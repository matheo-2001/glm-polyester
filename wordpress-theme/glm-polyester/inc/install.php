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
	update_option( 'blogdescription', 'Constructeur de manèges, sujets et pièces en polyester' );
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
		"<h2>Éditeur du site</h2>\n<p>GLM Polyester, société par actions simplifiée au capital de 5 000 €.<br>Siège social : 46 rue de Montdidier, 60120 Breteuil.<br>RCS Beauvais 977 989 318. SIRET 977 989 318 00026.<br>TVA intracommunautaire : FR42977989318.</p>\n<p>Directeur de la publication : [NOM À COMPLÉTER].<br>Contact : [TÉLÉPHONE ET E-MAIL À COMPLÉTER].</p>\n<h2>Hébergement</h2>\n<p>Le site est hébergé par OVH SAS, au capital de 50 000 000 €, RCS Lille Métropole 424 761 419 00045.<br>Siège social : 2 rue Kellermann, 59100 Roubaix, France.<br>Téléphone : 1007. Site : <a href=\"https://www.ovhcloud.com/fr/\">www.ovhcloud.com</a></p>\n<h2>Propriété intellectuelle</h2>\n<p>Les textes, photos et modèles présentés sur ce site sont la propriété de GLM Polyester. Toute reproduction sans autorisation est interdite.</p>"
	);
	$confidentialite = glm_ensure_page( 'politique-de-confidentialite', 'Politique de confidentialité', glm_texte_confidentialite() );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $accueil );
	update_option( 'wp_page_for_privacy_policy', $confidentialite );

	/* ----- Familles du catalogue ----- */
	$familles = array(
		'maneges-complets'   => array( 'Manèges complets', 'Manèges enfantins livrés prêts à tourner, décor et éclairage compris.' ),
		'voitures-et-sujets' => array( 'Voitures et sujets', 'Voitures, camions, traîneaux : à l\'unité ou en série.' ),
		'pieces-polyester'   => array( 'Pièces et thermoformage', 'Décors, éléments de carrosserie et pièces de remplacement, en polyester ou thermoformés.' ),
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
	foreach ( array( 'fabrication' => 'Fabrication', 'sur-mesure' => 'Personnalisable', 'renovation' => 'Rénovation' ) as $slug => $name ) {
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
			'content' => 'Sujet de manège en forme de camion de pompiers, avec ses personnages.',
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

	glm_creer_pages_services();
	update_option( 'glm_services_pages_v1', 1 );
	update_option( 'glm_services_textes_v2', 1 );
	update_option( 'glm_mentions_ovh_v1', 1 );
	update_option( 'glm_installed', GLM_VERSION );
}

/** Texte de la politique de confidentialité de GLM Polyester. */
function glm_texte_confidentialite() {
	return "<h2>Données collectées</h2>\n<p>Les informations saisies dans le formulaire de contact (nom, société, téléphone, e-mail, message et photos jointes) sont transmises par e-mail à GLM Polyester. Elles servent uniquement à répondre à votre demande et à établir un devis.</p>\n<h2>Conservation</h2>\n<p>[DURÉE DE CONSERVATION À PRÉCISER]</p>\n<h2>Vos droits</h2>\n<p>Vous pouvez demander l'accès à vos données, leur rectification ou leur suppression en nous écrivant à l'adresse indiquée sur la page Contact.</p>\n<h2>Cookies</h2>\n<p>Ce site n'utilise pas de cookies publicitaires ni d'outil de suivi.</p>";
}

/**
 * Une seule fois : si la page de confidentialité contient encore le modèle de WordPress
 * (« Texte suggéré »), elle reçoit le texte de GLM Polyester et elle est publiée.
 * Un texte déjà rédigé n'est jamais remplacé.
 */
add_action( 'admin_init', function () {
	if ( get_option( 'glm_confidentialite_v1' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$id   = (int) get_option( 'wp_page_for_privacy_policy' );
	$page = $id ? get_post( $id ) : null;
	if ( ! $page || 'page' !== $page->post_type ) {
		$page = get_page_by_path( 'politique-de-confidentialite' );
	}
	if ( $page && ( false !== strpos( $page->post_content, 'Texte suggéré' ) || '' === trim( $page->post_content ) ) ) {
		wp_update_post( array(
			'ID'           => $page->ID,
			'post_status'  => 'publish',
			'post_content' => glm_texte_confidentialite(),
			'post_name'    => 'politique-de-confidentialite',
		) );
		update_option( 'wp_page_for_privacy_policy', $page->ID );
	}
	update_option( 'glm_confidentialite_v1', 1 );
} );

/** Texte court des pages des services (version 1), gardé pour savoir si une page a été modifiée depuis sa création. */
function glm_texte_service( $slug ) {
	$p  = function ( $texte ) {
		return "<!-- wp:paragraph -->\n<p>" . $texte . "</p>\n<!-- /wp:paragraph -->\n\n";
	};
	$h2 = function ( $texte ) {
		return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . $texte . "</h2>\n<!-- /wp:heading -->\n\n";
	};
	$ul = function ( $items, $ordered = false ) {
		$tag  = $ordered ? 'ol' : 'ul';
		$html = $ordered ? "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">" : "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
		foreach ( $items as $item ) {
			$html .= "<!-- wp:list-item -->\n<li>" . $item . "</li>\n<!-- /wp:list-item -->";
		}
		return $html . '</' . $tag . ">\n<!-- /wp:list -->\n\n";
	};
	switch ( $slug ) {
		case 'fabrication':
			return $p( 'GLM Polyester fabrique dans son atelier de Breteuil des manèges, des sujets roulants et volants et des pièces en polyester, à partir des modèles de son catalogue.' )
				. $h2( 'Ce que nous fabriquons' )
				. $ul( array( 'Manèges complets', 'Sujets roulants : voitures, camions, motos, bateaux…', 'Sujets volants : hélicoptères et autres sujets du catalogue', 'Pièces en polyester : caisses, carénages, éléments de décor', 'Pièces thermoformées : cabochons, optiques, caches' ) )
				. $h2( 'À partir de votre moule' )
				. $p( 'Vous avez votre propre moule ? Nous pouvons aussi réaliser vos pièces à partir de celui-ci.' )
				. $h2( 'Couleurs et décor' )
				. $p( 'Les sujets roulants et volants sont personnalisables : couleurs au choix, décor peint ou covering.' );
		case 'personnalisable':
			return $p( 'Les sujets roulants et volants du catalogue se personnalisent. Vous choisissez le modèle, nous l\'habillons à vos couleurs.' )
				. $h2( 'Les possibilités' )
				. $ul( array( 'Couleurs : teintes unies, en dégradé ou pailletées', 'Décor peint : motifs, noms et numéros peints à la main ou à l\'aérographe', 'Covering : un film imprimé à vos couleurs', 'Éclairage : leds et feux intégrés au sujet' ) )
				. $h2( 'Sujets volants' )
				. $p( 'Pour les sujets volants, le parallélogramme est fourni par le client.' )
				. $h2( 'Sujets de Noël' )
				. $p( 'Pour les sujets de Noël, nous ajoutons bruiteur, marche et éclairage.' );
		case 'thermoformage':
			return $p( 'Le thermoformage consiste à chauffer une plaque de plastique, puis à la former sur un moule. Nous réalisons ainsi des pièces pour les sujets et les manèges.' )
				. $h2( 'Nos pièces thermoformées' )
				. $ul( array( 'Cabochons d\'éclairage de couleur', 'Feux et optiques', 'Caches et carénages', 'Pièces diverses pour sujets et manèges' ) )
				. $p( 'Les pièces disponibles sont présentées dans le catalogue. Pour une autre pièce, envoyez-nous une photo et ses dimensions.' );
		case 'reparation':
			return $p( 'Nous réparons les sujets, les pièces en polyester et les manèges, quel que soit leur fabricant.' )
				. $h2( 'Ce que nous réparons' )
				. $ul( array( 'Sujets et pièces en polyester cassés ou fissurés', 'Pièces usées à remplacer', 'Sujets et manèges de toutes marques' ) )
				. $h2( 'Commencez par une photo' )
				. $p( 'Envoyez-nous des photos de la pièce avec le formulaire de devis : nous vous disons ce qui est possible et nous chiffrons la réparation.' );
		case 'renovation':
			return $p( 'Un sujet ou un manège ancien n\'est pas forcément à remplacer : il peut être remis à neuf, avec un nouveau décor si vous le souhaitez.' )
				. $h2( 'Les étapes d\'une rénovation' )
				. $ul( array( 'Examen du sujet ou du manège, sur photos ou à l\'atelier', 'Reprise de la structure et du polyester', 'Nouvelle peinture et nouveau décor', 'Éclairage modernisé' ), true );
	}
	return '';
}

/** Crée les pages détaillées des services (enfants de la page « Services ») qui n'existent pas encore. */
function glm_creer_pages_services() {
	$parent = get_page_by_path( 'services' );
	if ( ! $parent ) {
		return;
	}
	$ordre = 1;
	foreach ( glm_services() as $slug => $titre ) {
		if ( ! get_page_by_path( 'services/' . $slug ) ) {
			wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_parent'  => $parent->ID,
				'post_name'    => $slug,
				'post_title'   => $titre,
				'post_content' => wp_slash( glm_texte_service_v2( $slug ) ),
				'menu_order'   => $ordre,
			) );
		}
		$ordre++;
	}
}

// Une seule fois, à la prochaine visite d'un administrateur : pages détaillées des services.
add_action( 'admin_init', function () {
	if ( get_option( 'glm_services_pages_v1' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	update_option( 'glm_services_pages_v1', 1 );
	glm_creer_pages_services();
} );

// Une seule fois : hébergeur (OVH) dans les mentions légales, seulement si le texte à compléter est encore là.
add_action( 'admin_init', function () {
	if ( get_option( 'glm_mentions_ovh_v1' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	update_option( 'glm_mentions_ovh_v1', 1 );
	$page = get_page_by_path( 'mentions-legales' );
	if ( ! $page ) {
		return;
	}
	$ovh     = 'Le site est hébergé par OVH SAS, au capital de 50 000 000 €, RCS Lille Métropole 424 761 419 00045.<br>Siège social : 2 rue Kellermann, 59100 Roubaix, France.<br>Téléphone : 1007. Site : <a href="https://www.ovhcloud.com/fr/">www.ovhcloud.com</a>';
	$contenu = str_replace(
		array( '[NOM, ADRESSE ET TÉLÉPHONE DE L\'HÉBERGEUR À COMPLÉTER]', '[NOM, ADRESSE ET TÉLÉPHONE DE L’HÉBERGEUR À COMPLÉTER]' ),
		$ovh,
		$page->post_content,
		$fois
	);
	if ( $fois ) {
		wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => $contenu ) ) );
	}
} );
