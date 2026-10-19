<?php
/**
 * Plugin temporaire (à revider après usage) : reçoit la copie du site préparée dans le navigateur
 * et l'enregistre dans wp-content/uploads/glm-export/. Réservé aux administrateurs, avec jeton de sécurité.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Script de copie (parcourt le site en visiteur, rassemble pages et fichiers dans un zip).
const GLM_COPIE_JS = <<<'JS'
window.glmCopie = async () => {
	if (!window.JSZip) {
		await new Promise((ok, ko) => {
			const s = document.createElement('script');
			s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
			s.onload = ok; s.onerror = ko;
			document.head.appendChild(s);
		});
	}
	const origin = location.origin;
	const cfg = document.getElementById('glm-export');
	const zip = new JSZip();
	const pages = {};
	const vus = new Set();
	const file = ['/'];
	const assets = new Set();
	const exclus = /^\/(wp-admin|wp-json|wp-login|wp-content|wp-includes|feed|comments|xmlrpc)/;
	const estPage = (u) => u.origin === origin && !u.search && !exclus.test(u.pathname) && !/\.[a-z0-9]{2,5}$/i.test(u.pathname);
	const ajouterAsset = (brut, base) => {
		if (!brut) { return; }
		try {
			const u = new URL(brut.trim().replace(/\\\//g, '/'), base);
			if (u.origin === origin && /^\/wp-(content|includes)\//.test(u.pathname)) { assets.add(u.pathname); }
		} catch (e) { /* adresse invalide */ }
	};
	let n = 0;
	while (file.length && n < 300) {
		const chemin = file.shift();
		if (vus.has(chemin)) { continue; }
		vus.add(chemin);
		const r = await fetch(chemin, { credentials: 'omit' });
		const final = new URL(r.url);
		if (!r.ok || !(r.headers.get('content-type') || '').includes('text/html')) { continue; }
		if (final.pathname !== chemin) {
			if (vus.has(final.pathname)) { continue; }
			vus.add(final.pathname);
		}
		const html = await r.text();
		const nom = 'html/' + String(n).padStart(3, '0') + '.html';
		pages[origin + final.pathname] = nom;
		zip.file(nom, html);
		n++;
		const doc = new DOMParser().parseFromString(html, 'text/html');
		const base = origin + final.pathname;
		doc.querySelectorAll('a[href]').forEach((a) => {
			try {
				const u = new URL(a.getAttribute('href'), base);
				if (estPage(u) && !vus.has(u.pathname)) { file.push(u.pathname); }
				ajouterAsset(a.getAttribute('href'), base);
			} catch (e) { /* lien invalide */ }
		});
		doc.querySelectorAll('[src]').forEach((el) => ajouterAsset(el.getAttribute('src'), base));
		doc.querySelectorAll('[srcset]').forEach((el) => el.getAttribute('srcset').split(',').forEach((c) => ajouterAsset(c.trim().split(/\s+/)[0], base)));
		doc.querySelectorAll('link[href]').forEach((el) => ajouterAsset(el.getAttribute('href'), base));
		doc.querySelectorAll('[data-src]').forEach((el) => ajouterAsset(el.getAttribute('data-src'), base));
		doc.querySelectorAll('meta[content^="http"]').forEach((el) => ajouterAsset(el.getAttribute('content'), base));
		doc.querySelectorAll('[data-slides]').forEach((el) => {
			try { JSON.parse(el.getAttribute('data-slides')).forEach((s) => ajouterAsset(s.src, base)); } catch (e) { /* JSON invalide */ }
		});
		doc.querySelectorAll('[style]').forEach((el) => (el.getAttribute('style').match(/url\(([^)]+)\)/g) || []).forEach((m) => ajouterAsset(m.slice(4, -1).replace(/["']/g, ''), base)));
		(html.match(/https?:\\?\/\\?\/[^"'\s<>]+\/wp-content\/[^"'\s<>)]+/g) || []).forEach((m) => ajouterAsset(m, base));
	}
	// Feuilles de style : polices et images qu'elles appellent.
	const css = [...assets].filter((p) => p.endsWith('.css'));
	for (const p of css) {
		const t = await (await fetch(p, { credentials: 'omit' })).text();
		(t.match(/url\(([^)]+)\)/g) || []).forEach((m) => ajouterAsset(m.slice(4, -1).replace(/["']/g, ''), origin + p));
	}
	let poids = 0;
	let manquants = 0;
	for (const p of assets) {
		const r = await fetch(p, { credentials: 'omit' });
		if (!r.ok) { manquants++; continue; }
		const b = await r.blob();
		poids += b.size;
		zip.file('site' + decodeURIComponent(p), b);
	}
	zip.file('manifest.json', JSON.stringify({ pages: pages, date: new Date().toISOString() }, null, 1));
	const blob = await zip.generateAsync({ type: 'blob', compression: 'STORE' });
	const nomZip = 'copie-' + new Date().toISOString().slice(0, 19).replace(/[^0-9]/g, '') + '.zip';
	const fd = new FormData();
	fd.append('action', 'glm_export_recevoir');
	fd.append('nonce', cfg.dataset.nonce);
	fd.append('nom', nomZip);
	fd.append('zip', blob, nomZip);
	const rep = await fetch(cfg.dataset.action, { method: 'POST', body: fd, credentials: 'include' });
	return JSON.stringify({ pages: n, fichiers: assets.size, manquants: manquants, mo: Math.round(blob.size / 1048576), reponse: (await rep.text()).slice(0, 80) });
};
JS;

// Page d'administration qui fournit le jeton au script de copie.
add_action( 'admin_menu', function () {
	add_management_page( 'Copie GLM', 'Copie GLM', 'manage_options', 'glm-export', function () {
		echo '<div class="wrap"><h1>Copie GLM</h1><p id="glm-export" data-nonce="' . esc_attr( wp_create_nonce( 'glm_export' ) ) . '" data-action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">Prêt.</p></div>';
		echo '<script>' . GLM_COPIE_JS . '</script>'; // phpcs:ignore
	} );
} );

add_action( 'admin_post_glm_export_recevoir', function () {
	if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'glm_export' ) ) {
		status_header( 403 );
		exit( 'refus' );
	}
	if ( empty( $_FILES['zip']['tmp_name'] ) || ! is_uploaded_file( $_FILES['zip']['tmp_name'] ) ) { // phpcs:ignore
		status_header( 400 );
		exit( 'aucun fichier' );
	}
	$nom = isset( $_POST['nom'] ) ? sanitize_file_name( wp_unslash( $_POST['nom'] ) ) : 'copie.zip';
	if ( ! preg_match( '/^[a-z0-9-]+\.zip$/', $nom ) ) {
		$nom = 'copie-' . gmdate( 'Ymd-His' ) . '.zip';
	}
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'glm-export/';
	wp_mkdir_p( $dir );
	if ( ! file_exists( $dir . 'index.php' ) ) {
		file_put_contents( $dir . 'index.php', '<?php // Silence.' ); // phpcs:ignore
	}
	$ok = move_uploaded_file( $_FILES['zip']['tmp_name'], $dir . $nom ); // phpcs:ignore
	status_header( $ok ? 200 : 500 );
	exit( $ok ? 'ok ' . $nom . ' ' . filesize( $dir . $nom ) : 'echec' );
} );
