<?php
/**
 * Mise en place automatique à la première activation du thème :
 * pages, page d'accueil, permaliens, familles, modèles et réalisations d'exemple.
 * Ne s'exécute qu'une seule fois (option glm_installed).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'glm_install' );

/** Importe une image du thème dans la médiathèque et renvoie son identifiant. */
function glm_import_image( $filename, $alt ) {
	$path = get_template_directory() . '/assets/img/' . $filename;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( $filename, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type = wp_check_filetype( $upload['file'] );
	$id   = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => $alt,
		'post_status'    => 'inherit',
	), $upload['file'] );
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

/** Crée une page si elle n'existe pas encore et renvoie son identifiant. */
function glm_ensure_page( $slug, $title, $content = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return (int) $page->ID;
	}
	return (int) wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
	) );
}

function glm_install() {
	if ( get_option( 'glm_installed' ) ) {
		return;
	}

	glm_register_content_types();

	/* ----- Réglages généraux ----- */
	update_option( 'blogname', 'GLM Polyester' );
	update_option( 'blogdescription', 'Constructeur de manèges, voitures de manège et pièces en polyester' );
	update_option( 'timezone_string', 'Europe/Paris' );
	update_option( 'date_format', 'j F Y' );
	update_option( 'time_format', 'H:i' );
	update_option( 'start_of_week', 1 );

	// Administration en français si le pack de langue peut être téléchargé.
	if ( 'fr_FR' !== get_option( 'WPLANG' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		if ( function_exists( 'wp_download_language_pack' ) && wp_download_language_pack( 'fr_FR' ) ) {
			update_option( 'WPLANG', 'fr_FR' );
		}
	}

	/* ----- Pages ----- */
	$accueil = glm_ensure_page( 'accueil', 'Accueil' );
	glm_ensure_page( 'services', 'Services' );
	glm_ensure_page(
		'atelier',
		'L\'atelier',
		"<p>[Présentation de l'entreprise et de son dirigeant : parcours, années d'expérience dans le métier, ce qui fait la différence de l'atelier. Texte à rédiger, 5 à 8 lignes.]</p>\n<p>Nous travaillons pour les forains, les parcs de loisirs, les collectivités et les particuliers.</p>"
	);
	glm_ensure_page( 'contact', 'Contact et devis' );
	glm_ensure_page(
		'mentions-legales',
		'Mentions légales',
		"<h2>Éditeur du site</h2>\n<p>GLM Polyester, société par actions simplifiée au capital de 5 000 €.<br>Siège social : 46 rue de Montdidier, 60120 Breteuil.<br>RCS Beauvais 977 989 318. SIRET 977 989 318 00026.<br>TVA intracommunautaire : FR42977989318.</p>\n<p>Directeur de la publication : [NOM À COMPLÉTER].<br>Contact : [TÉLÉPHONE ET E-MAIL À COMPLÉTER].</p>\n<h2>Hébergement</h2>\n<p>[NOM, ADRESSE ET TÉLÉPHONE DE L'HÉBERGEUR À COMPLÉTER]</p>\n<h2>Propriété intellectuelle</h2>\n<p>Les textes, photos et modèles présentés sur ce site sont la propriété de GLM Polyester. Toute reproduction sans autorisation est interdite.</p>"
	);
	$confidentialite = glm_ensure_page(
		'politique-de-confidentialite',
		'Politique de confidentialité',
		"<h2>Données collectées</h2>\n<p>Les informations saisies dans le formulaire de contact (nom, société, téléphone, e-mail, message et photos jointes) sont transmises par e-mail à GLM Polyester. Elles servent uniquement à répondre à votre demande et à établir un devis.</p>\n<h2>Conservation</h2>\n<p>[DURÉE DE CONSERVATION À PRÉCISER]</p>\n<h2>Vos droits</h2>\n<p>Vous pouvez demander l'accès à vos données, leur rectification ou leur suppression en nous écrivant à l'adresse indiquée sur la page Contact.</p>\n<h2>Cookies</h2>\n<p>Ce site n'utilise pas de cookies publicitaires ni d'outil de suivi.</p>"
	);

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $accueil );
	update_option( 'wp_page_for_privacy_policy', $confidentialite );

	/* ----- Familles du catalogue ----- */
	$familles = array(
		'maneges-complets'   => array( 'Manèges complets', 'Manèges enfantins livrés prêts à tourner, décor et éclairage compris.' ),
		'voitures-et-sujets' => array( 'Voitures et sujets', 'Voitures, camions, traîneaux, personnages : à l\'unité ou en série.' ),
		'pieces-polyester'   => array( 'Pièces polyester et résine', 'Décors, éléments de carrosserie et pièces de remplacement.' ),
	);
	$famille_ids = array();
	foreach ( $familles as $slug => $data ) {
		$term = term_exists( $slug, 'famille' );
		if ( ! $term ) {
			$term = wp_insert_term( $data[0], 'famille', array( 'slug' => $slug, 'description' => $data[1] ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$famille_ids[ $slug ] = (int) $term['term_id'];
		}
	}

	$types = array();
	foreach ( array( 'fabrication' => 'Fabrication', 'sur-mesure' => 'Sur mesure', 'renovation' => 'Rénovation' ) as $slug => $name ) {
		$term = term_exists( $slug, 'type_realisation' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'type_realisation', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$types[ $slug ] = (int) $term['term_id'];
		}
	}

	/* ----- Photos, modèles et réalisations d'exemple ----- */
	$img_traineau = glm_import_image( 'traineau.jpg', 'Traîneau de Noël avec Père Noël, ourson et sapin, éclairé de LED' );
	$img_detail   = glm_import_image( 'traineau-serre.jpg', 'Détail du traîneau de Noël : ourson, cadeaux et patins dorés' );
	$img_pompier  = glm_import_image( 'sujet-pompier.jpg', 'Camion de pompiers de manège avec son personnage' );
	$img_manege   = glm_import_image( 'manege-lumieres.jpg', 'Manège enfantin illuminé sous son chapiteau rouge et jaune' );

	$modeles = array(
		array(
			'title'   => 'Traîneau de Noël',
			'content' => 'Sujet de manège sur le thème de Noël : Père Noël, ourson, sapin et cadeaux. Décor peint à la main et éclairage LED intégré.',
			'famille' => 'voitures-et-sujets',
			'thumb'   => $img_traineau,
			'galerie' => $img_detail,
			'meta'    => array( '_glm_materiau' => 'Polyester', '_glm_finition' => 'Décor peint, éclairage LED' ),
		),
		array(
			'title'   => 'Camion de pompiers',
			'content' => 'Voiture de manège en forme de camion de pompiers, avec ses personnages.',
			'famille' => 'voitures-et-sujets',
			'thumb'   => $img_pompier,
			'galerie' => 0,
			'meta'    => array( '_glm_materiau' => 'Polyester' ),
		),
		array(
			'title'   => 'Manège enfantin',
			'content' => 'Manège enfantin complet avec chapiteau, éclairage et sujets.',
			'famille' => 'maneges-complets',
			'thumb'   => $img_manege,
			'galerie' => 0,
			'meta'    => array(),
		),
	);
	foreach ( $modeles as $modele ) {
		$id = wp_insert_post( array(
			'post_type'    => 'modele',
			'post_status'  => 'publish',
			'post_title'   => $modele['title'],
			'post_content' => $modele['content'],
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		if ( isset( $famille_ids[ $modele['famille'] ] ) ) {
			wp_set_object_terms( $id, array( $famille_ids[ $modele['famille'] ] ), 'famille' );
		}
		if ( $modele['thumb'] ) {
			set_post_thumbnail( $id, $modele['thumb'] );
		}
		if ( $modele['galerie'] ) {
			update_post_meta( $id, '_glm_galerie', (string) $modele['galerie'] );
		}
		foreach ( $modele['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	$realisations = array(
		array( 'Traîneau de Noël', 'Sujet de manège, décor peint et éclairage LED.', $img_traineau, 'fabrication', '1' ),
		array( 'Camion de pompiers', 'Voiture de manège avec personnages.', $img_pompier, 'fabrication', '' ),
		array( 'Manège enfantin', 'Manège complet en fête foraine.', $img_manege, 'fabrication', '' ),
	);
	foreach ( $realisations as $index => $item ) {
		$id = wp_insert_post( array(
			'post_type'   => 'realisation',
			'post_status' => 'publish',
			'post_title'  => $item[0],
			'post_date'   => gmdate( 'Y-m-d H:i:s', time() - $index * 60 ),
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_glm_legende', $item[1] );
		update_post_meta( $id, '_glm_large', $item[4] );
		if ( $item[2] ) {
			set_post_thumbnail( $id, $item[2] );
		}
		if ( isset( $types[ $item[3] ] ) ) {
			wp_set_object_terms( $id, array( $types[ $item[3] ] ), 'type_realisation' );
		}
	}

	/* ----- Permaliens lisibles ----- */
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	flush_rewrite_rules();

	update_option( 'glm_installed', GLM_VERSION );
}
