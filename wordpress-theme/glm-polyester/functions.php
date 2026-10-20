<?php
/**
 * GLM Polyester : fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLM_VERSION', '1.2.0' );

require get_template_directory() . '/inc/textes.php';
require get_template_directory() . '/inc/content-types.php';
require get_template_directory() . '/inc/admin.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/contact.php';
require get_template_directory() . '/inc/install.php';
require get_template_directory() . '/inc/textes-services.php';
require get_template_directory() . '/inc/import.php';
require get_template_directory() . '/inc/images.php';
require get_template_directory() . '/inc/seo.php';

/* ---------- Réglages du thème ---------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'glm-carte', 800, 600, true );
	add_image_size( 'glm-carte-s', 400, 300, true );
	add_image_size( 'glm-large', 1600, 1000, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'glm', get_stylesheet_uri(), array(), (string) filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'glm', get_template_directory_uri() . '/assets/js/main.js', array(), (string) filemtime( get_template_directory() . '/assets/js/main.js' ), true );
	if ( is_page( 'feuilleter-le-catalogue' ) && glm_catalogue_pages() ) {
		wp_enqueue_script( 'page-flip', get_template_directory_uri() . '/assets/vendor/page-flip.browser.js', array(), '2.0.7', true );
		wp_enqueue_script( 'glm-flip', get_template_directory_uri() . '/assets/js/flip.js', array( 'page-flip' ), (string) filemtime( get_template_directory() . '/assets/js/flip.js' ), true );
	}
} );

// Le thème n'utilise pas l'éditeur de blocs pour ses gabarits : on retire les styles de blocs chargés sur chaque page.
add_action( 'wp_enqueue_scripts', function () {
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}, 100 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

// Marque la page comme « avec JavaScript » le plus tôt possible, et précharge les polices des titres et du texte.
add_action( 'wp_head', function () {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
	foreach ( array( 'barlow-condensed-latin-800-italic', 'barlow-latin-400-normal', 'barlow-latin-600-normal' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( glm_asset( 'fonts/' . $font . '.woff2' ) ) );
	}
}, 1 );

// Icône d'onglet tirée du logo, tant qu'aucune icône n'est choisie dans Personnaliser > Identité du site.
add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" sizes="32x32" type="image/png">' . "\n", esc_url( glm_asset( 'img/favicon-32.png' ) ) );
	printf( '<link rel="icon" href="%s" sizes="192x192" type="image/png">' . "\n", esc_url( glm_asset( 'img/favicon-192.png' ) ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( glm_asset( 'img/apple-touch-icon.png' ) ) );
}, 3 );

// Allège l'en-tête : pas d'emojis.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/* ---------- Aides ---------- */

/** Valeur d'un réglage du thème (Apparence > Personnaliser). */
function glm_opt( $key, $default = '' ) {
	$value = get_theme_mod( 'glm_' . $key, '' );
	if ( '' === $value || null === $value || false === $value ) {
		$defauts = glm_coordonnees_defaut();
		return isset( $defauts[ $key ] ) ? $defauts[ $key ] : $default;
	}
	return $value;
}

/** Coordonnées par défaut (modifiables dans Apparence > Personnaliser > GLM Polyester > Coordonnées). */
function glm_coordonnees_defaut() {
	return array(
		'telephone' => '07 60 16 26 39',
		'email'     => 'contact@glm-polyester.com',
		'adresse_1' => '46 rue de Montdidier',
		'adresse_2' => '60120 Breteuil',
		'facebook'  => 'https://www.facebook.com/glmpolyester/',
		'siret'     => '977 989 318 00026',
	);
}

/** URL d'un fichier du dossier assets. */
function glm_asset( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/** URL d'une page par son identifiant court, avec repli sur l'accueil. */
function glm_page_url( $slug ) {
	static $cache = array();
	if ( ! isset( $cache[ $slug ] ) ) {
		$page           = get_page_by_path( $slug );
		$cache[ $slug ] = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
	}
	return $cache[ $slug ];
}

function glm_catalogue_url() {
	$url = get_post_type_archive_link( 'modele' );
	return $url ? $url : home_url( '/catalogue/' );
}

function glm_realisations_url() {
	$url = get_post_type_archive_link( 'realisation' );
	return $url ? $url : home_url( '/realisations/' );
}

/** Adresse de la page de confidentialité, seulement si elle est publiée. */
function glm_privacy_url() {
	$url = get_privacy_policy_url();
	if ( $url ) {
		return $url;
	}
	$page = get_page_by_path( 'politique-de-confidentialite' );
	return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/** Thème du catalogue affiché (page /catalogue/theme/…/), ou null. */
function glm_theme_courant() {
	static $theme = false;
	if ( false !== $theme ) {
		return $theme;
	}
	$theme = null;
	$slug  = get_query_var( 'glm_theme' );
	if ( $slug ) {
		$term  = get_term_by( 'slug', sanitize_key( $slug ), 'theme_glm' );
		$theme = $term ? $term : null;
	}
	return $theme;
}

/** Adresse d'une page « thème » du catalogue. */
function glm_theme_url( $term ) {
	return trailingslashit( glm_catalogue_url() ) . 'theme/' . $term->slug . '/';
}

/** Éléments du menu principal : libellé, URL, actif ou non. */
function glm_nav_items() {
	return array(
		array( 'Accueil', home_url( '/' ), is_front_page() ),
		array( 'Catalogue', glm_catalogue_url(), is_post_type_archive( 'modele' ) || is_singular( 'modele' ) || is_tax( 'famille' ) || is_page( 'feuilleter-le-catalogue' ) ),
		array( 'Services', glm_page_url( 'services' ), is_page( 'services' ) || '' !== glm_service_courant() ),
		array( 'Réalisations', glm_realisations_url(), is_post_type_archive( 'realisation' ) ),
		array( 'L\'atelier', glm_page_url( 'atelier' ), is_page( 'atelier' ) ),
		array( 'Contact', glm_page_url( 'contact' ), is_page( 'contact' ) ),
	);
}

/**
 * Pages du catalogue à feuilleter : images wp-content/uploads/glm-catalogue/page-NN-1600.jpg
 * (et page-NN-900.jpg pour les petits écrans), tirées du PDF du catalogue.
 * Une version .webp placée à côté est utilisée en priorité.
 */
function glm_catalogue_pages() {
	static $pages = null;
	if ( null !== $pages ) {
		return $pages;
	}
	$pages   = array();
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'glm-catalogue/';
	$url     = trailingslashit( $uploads['baseurl'] ) . 'glm-catalogue/';
	$files   = glob( $dir . 'page-*-1600.jpg' );
	if ( ! $files ) {
		return $pages;
	}
	sort( $files );
	$version = function ( $name ) use ( $dir, $url ) {
		return file_exists( $dir . $name ) ? $url . $name . '?v=' . filemtime( $dir . $name ) : '';
	};
	foreach ( $files as $file ) {
		$name    = basename( $file );
		$small   = str_replace( '-1600.jpg', '-900.jpg', $name );
		$size    = getimagesize( $file );
		$pages[] = array(
			'grand'      => $version( $name ),
			'petit'      => $version( $small ),
			'grand_webp' => $version( str_replace( '.jpg', '.webp', $name ) ),
			'petit_webp' => $version( str_replace( '.jpg', '.webp', $small ) ),
			'w'          => $size ? (int) $size[0] : 1600,
			'h'          => $size ? (int) $size[1] : 1131,
		);
	}
	return $pages;
}

/**
 * Adresse de la page « Feuilleter le catalogue », ou vide.
 * Le catalogue à feuilleter n'est plus proposé sur le site : la fonction renvoie vide,
 * sauf si on le réactive avec le filtre glm_feuilleter_actif.
 */
function glm_feuilleter_actif() {
	return (bool) apply_filters( 'glm_feuilleter_actif', false );
}
function glm_feuilleter_url() {
	if ( ! glm_feuilleter_actif() || ! glm_catalogue_pages() ) {
		return '';
	}
	$page = get_page_by_path( 'feuilleter-le-catalogue' );
	return $page && 'publish' === $page->post_status ? get_permalink( $page ) : '';
}

/** URL du PDF du catalogue (Apparence > Personnaliser), ou vide. */
function glm_catalogue_pdf_url() {
	$pdf = absint( get_theme_mod( 'glm_catalogue_pdf' ) );
	return $pdf ? (string) wp_get_attachment_url( $pdf ) : '';
}

// Page « Feuilleter le catalogue » désactivée : son adresse renvoie vers le catalogue.
add_action( 'template_redirect', function () {
	if ( ! glm_feuilleter_actif() && is_page( 'feuilleter-le-catalogue' ) ) {
		wp_safe_redirect( glm_catalogue_url(), 301 );
		exit;
	}
} );

// Crée une fois la page « Feuilleter le catalogue » (modèle page-feuilleter-le-catalogue.php), si elle est réactivée.
add_action( 'admin_init', function () {
	if ( ! glm_feuilleter_actif() || get_option( 'glm_page_feuilleter' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( glm_ensure_page( 'feuilleter-le-catalogue', 'Feuilleter le catalogue' ) ) {
		update_option( 'glm_page_feuilleter', 1 );
	}
} );

/** Blocs de la page Services : ancre => titre (modifiable). Les ancres ne changent pas. */
function glm_services() {
	$services = array();
	foreach ( array( 'fabrication', 'personnalisable', 'thermoformage', 'reparation', 'renovation' ) as $slug ) {
		$services[ $slug ] = glm_t( 'service_' . $slug . '_titre' );
	}
	return $services;
}

// Classes du corps de page pour la page L'atelier et les pages de services (mises en page sur téléphone).
add_filter( 'body_class', function ( $classes ) {
	if ( is_page( 'atelier' ) ) {
		$classes[] = 'page-atelier';
	}
	if ( is_page( 'services' ) ) {
		$classes[] = 'page-services';
	}
	if ( glm_service_courant() ) {
		$classes[] = 'page-service';
	}
	return $classes;
} );

/** Réglages de chaque service : type de demande du formulaire et types de réalisations montrés en exemple. */
function glm_service_infos( $slug ) {
	$infos = apply_filters( 'glm_service_infos', array(
		'fabrication'     => array( 'demande' => 'catalogue', 'types' => array( 'sujets', 'maneges' ), 'familles' => array( 'sujets-roulants', 'sujets-volants', 'maneges-complets', 'pieces-et-thermoformage' ) ),
		'personnalisable' => array( 'demande' => 'sur-mesure', 'types' => array( 'decors', 'sujets' ), 'familles' => array( 'sujets-roulants', 'sujets-volants' ) ),
		'thermoformage'   => array( 'demande' => 'thermoformage', 'types' => array( 'pieces' ), 'familles' => array( 'pieces-et-thermoformage', 'luminaires' ) ),
		'reparation'      => array( 'demande' => 'reparation', 'types' => array( 'mecanique' ), 'familles' => array( 'pieces-et-thermoformage', 'collecteurs', 'accessoires' ) ),
		'renovation'      => array( 'demande' => 'renovation', 'types' => array( 'renovation' ), 'familles' => array( 'sujets-roulants', 'sujets-volants', 'maneges-complets' ) ),
	) );
	$infos = isset( $infos[ $slug ] ) ? $infos[ $slug ] : array();
	return wp_parse_args( $infos, array( 'demande' => 'autre', 'types' => array(), 'familles' => array() ) );
}

/** Page détaillée d'un service (page enfant de « Services », publiée), ou null. */
function glm_service_page( $slug ) {
	static $cache = array();
	if ( ! array_key_exists( $slug, $cache ) ) {
		$page           = get_page_by_path( 'services/' . $slug );
		$cache[ $slug ] = ( $page && 'publish' === $page->post_status ) ? $page : null;
	}
	return $cache[ $slug ];
}

/** Adresse d'un service : sa page détaillée, sinon son paragraphe de la page Services. */
function glm_service_url( $slug ) {
	$page = glm_service_page( $slug );
	return $page ? get_permalink( $page ) : glm_page_url( 'services' ) . '#' . $slug;
}

/** Formulaire de devis pré-rempli pour un service. */
function glm_service_devis_url( $slug ) {
	$infos = glm_service_infos( $slug );
	return add_query_arg( 'demande', $infos['demande'], glm_page_url( 'contact' ) ) . '#formulaire';
}

/** Service de la page courante (page enfant de « Services »), ou chaîne vide. */
function glm_service_courant() {
	if ( ! is_page() ) {
		return '';
	}
	$page = get_queried_object();
	if ( ! $page || ! $page->post_parent ) {
		return '';
	}
	$parent = get_post( $page->post_parent );
	return ( $parent && 'services' === $parent->post_name && array_key_exists( $page->post_name, glm_services() ) ) ? $page->post_name : '';
}

// Les pages enfants de « Services » (fabrication, réparation…) utilisent le modèle page-service.php.
add_filter( 'page_template', function ( $template ) {
	if ( glm_service_courant() ) {
		$service = locate_template( 'page-service.php' );
		if ( $service ) {
			return $service;
		}
	}
	return $template;
} );

/**
 * Questions fréquentes d'un service : array( array( question, réponse ) ).
 * Une question sans réponse n'est pas affichée.
 */
function glm_service_faq( $slug ) {
	return glm_faq( 'service_' . $slug . '_faq' );
}

/** Questions fréquentes d'un texte du registre (question sur une ligne, réponse dessous, ligne vide entre deux). */
function glm_faq( $key ) {
	$texte = str_replace( "\r", '', glm_t( $key ) );
	$faq   = array();
	foreach ( preg_split( '/\n\s*\n/', trim( $texte ) ) as $bloc ) {
		$lignes   = array_values( array_filter( array_map( 'trim', explode( "\n", $bloc ) ) ) );
		$question = $lignes ? array_shift( $lignes ) : '';
		$reponse  = implode( ' ', $lignes );
		if ( '' !== $question && '' !== $reponse ) {
			$faq[] = array( $question, $reponse );
		}
	}
	return $faq;
}

/** Pictogramme d'un client de la page L'atelier (dans l'ordre : forains, parcs, collectivités, particuliers). */
function glm_picto_client( $index ) {
	$dessins = array(
		// Chapiteau.
		'<path d="M6 40V22L24 9l18 13v18M3 40h42M6 22q4.5 4 9 0q4.5 4 9 0q4.5 4 9 0q4.5 4 9 0M20 40v-8a4 4 0 0 1 8 0v8M24 9V3l6 2-6 2"/>',
		// Grande roue.
		'<circle cx="24" cy="19" r="14"/><circle cx="24" cy="19" r="2.5"/><path d="M24 5v28M10 19h28M14.1 9.1l19.8 19.8M33.9 9.1L14.1 28.9M22 21l-9 23M26 21l9 23M8 44h32"/>',
		// Mairie.
		'<path d="M6 18h36L24 8zM9 22v16M17 22v16M31 22v16M39 22v16M5 42h38M7 38h34M24 8V2l6 2-6 2"/><path d="M21 38v-9h6v9"/>',
		// Maison.
		'<path d="M6 23L24 8l18 15M11 19v23h26V19M20 42V31h8v11M3 42h42"/><rect x="29" y="23" width="5" height="5"/>',
	);
	$dessin = isset( $dessins[ $index ] ) ? $dessins[ $index ] : $dessins[0];
	return '<svg class="clients-pictos__picto" viewBox="0 0 48 48" width="64" height="64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $dessin . '</svg>';
}

/** Services en tuiles photo (page L'atelier, bas des pages services). $sauf : service à ne pas répéter. */
function glm_liste_services( $sauf = '' ) {
	$services = glm_services();
	unset( $services[ $sauf ] );
	echo '<ul class="tuiles-services tuiles-services--' . count( $services ) . '">';
	foreach ( $services as $slug => $titre ) {
		$photo = glm_service_image( $slug, 'glm-carte', array( 'sizes' => '(min-width: 1240px) 620px, (min-width: 640px) 50vw, 100vw', 'alt' => '' ) );
		printf(
			'<li><a class="tuile-service%s" href="%s">%s<span class="tuile-service__txt"><strong>%s</strong><span>%s</span></span></a></li>',
			$photo ? '' : ' tuile-service--sans-photo',
			esc_url( glm_service_url( $slug ) ),
			$photo, // phpcs:ignore
			esc_html( $titre ),
			esc_html( glm_t( 'service_' . $slug . '_court' ) )
		);
	}
	echo '</ul>';
}

/** Paragraphes d'un texte du registre (séparés par une ligne vide). */
function glm_t_paragraphes( $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\n\s*\n/', str_replace( "\r", '', glm_t( $key ) ) ) ) ) );
}

/** Lignes « Titre : texte » d'un texte du registre : array( array( titre, texte ) ). */
function glm_t_paires( $key ) {
	$paires = array();
	foreach ( glm_t_lignes( $key ) as $ligne ) {
		$morceaux = preg_split( '/\s+:\s+/u', $ligne, 2 );
		$texte    = isset( $morceaux[1] ) ? trim( $morceaux[1] ) : '';
		// Le texte commence par une majuscule, même s'il est écrit à la suite des deux-points.
		$texte    = '' === $texte ? '' : mb_strtoupper( mb_substr( $texte, 0, 1 ) ) . mb_substr( $texte, 1 );
		$paires[] = array( trim( $morceaux[0] ), $texte );
	}
	return $paires;
}

/** Réglage et photo du thème utilisés pour la photo d'un service. */
function glm_service_photo_cle( $slug ) {
	$cles = array(
		'fabrication'     => array( 'img_fabrication', 'img/traineau.jpg' ),
		'personnalisable' => array( 'img_sur_mesure', 'img/sujet-pompier.jpg' ),
		'thermoformage'   => array( 'img_thermoformage', '' ),
		'reparation'      => array( 'img_reparation', '' ),
		'renovation'      => array( 'img_renovation', '' ),
	);
	return isset( $cles[ $slug ] ) ? $cles[ $slug ] : array( '', '' );
}

/** La photo du service est-elle choisie (réglage ou photo du thème) plutôt que prise dans les réalisations ? */
function glm_service_photo_choisie( $slug ) {
	$cle = glm_service_photo_cle( $slug );
	return absint( get_theme_mod( 'glm_' . $cle[0] ) ) || $cle[1];
}

/** Photo d'un service : celle choisie dans Apparence > Personnaliser, sinon la première réalisation du même type. */
function glm_service_image( $slug, $size = 'glm-large', $attrs = array() ) {
	$cle   = glm_service_photo_cle( $slug );
	$titre = glm_t( 'service_' . $slug . '_titre' );
	if ( glm_service_photo_choisie( $slug ) ) {
		return glm_image_opt( $cle[0], $cle[1], $titre, $size, $attrs );
	}
	$infos = glm_service_infos( $slug );
	$ids   = $infos['types'] ? glm_realisations_choisies( 1, $infos['types'], true ) : array();
	if ( $ids && has_post_thumbnail( $ids[0] ) ) {
		return wp_get_attachment_image( get_post_thumbnail_id( $ids[0] ), $size, false, $attrs );
	}
	return '';
}

/** Flèche des menus déroulants. */
function glm_chevron() {
	return '<svg viewBox="0 0 12 8" width="12" height="8" aria-hidden="true" focusable="false"><path d="M1 1.5l5 5 5-5" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
}

/** Liste déroulante du catalogue (ordinateur). */
function glm_mega_catalogue() {
	$pdf      = glm_catalogue_pdf_url();
	$feuille  = glm_feuilleter_url();
	$courante = is_tax( 'famille' ) ? get_queried_object_id() : 0;
	$html     = '<div class="dropdown" id="mega-catalogue"><ul>';
	foreach ( glm_familles() as $famille ) {
		$html .= sprintf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( get_term_link( $famille ) ),
			$courante === $famille->term_id ? ' aria-current="page"' : '',
			esc_html( $famille->name )
		);
	}
	$html .= '</ul><ul class="dropdown__pied">';
	$html .= '<li><a class="dropdown__tout" href="' . esc_url( glm_catalogue_url() ) . '">Tout le catalogue</a></li>';
	if ( $feuille ) {
		$html .= '<li><a class="dropdown__pdf" href="' . esc_url( $feuille ) . '"' . ( is_page( 'feuilleter-le-catalogue' ) ? ' aria-current="page"' : '' ) . '><svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" focusable="false"><path d="M8 3.5C6.5 2.3 4.3 2 1.5 2v10.5c2.8 0 5 .3 6.5 1.5 1.5-1.2 3.7-1.5 6.5-1.5V2C11.7 2 9.5 2.3 8 3.5zM8 3.5V14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>Feuilleter le catalogue</a></li>';
	}
	if ( $pdf ) {
		$html .= '<li><a class="dropdown__pdf" href="' . esc_url( $pdf ) . '" download><svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" focusable="false"><path d="M8 1v9M4 6.5L8 10.5l4-4M2 14h12" fill="none" stroke="currentColor" stroke-width="2"/></svg>Catalogue PDF</a></li>';
	}
	return $html . '</ul></div>';
}

/** Liste déroulante des services (ordinateur). */
function glm_mega_services() {
	$html = '<div class="dropdown" id="mega-services"><ul>';
	foreach ( glm_services() as $slug => $titre ) {
		$html .= '<li><a href="' . esc_url( glm_service_url( $slug ) ) . '"' . ( glm_service_courant() === $slug ? ' aria-current="page"' : '' ) . '>' . esc_html( $titre ) . '</a></li>';
	}
	$html .= '</ul><ul class="dropdown__pied"><li><a class="dropdown__tout" href="' . esc_url( glm_page_url( 'services' ) ) . '"' . ( is_page( 'services' ) ? ' aria-current="page"' : '' ) . '>Tous les services</a></li>';
	return $html . '</ul></div>';
}

/** Liens d'un sous-menu mobile. */
function glm_sous_menu_mobile( $key ) {
	$liens = array();
	if ( 'catalogue' === $key ) {
		$liens[] = array( 'Tout le catalogue', glm_catalogue_url() );
		foreach ( glm_familles() as $famille ) {
			$liens[] = array( $famille->name, get_term_link( $famille ) );
		}
		if ( glm_feuilleter_url() ) {
			$liens[] = array( 'Feuilleter le catalogue', glm_feuilleter_url() );
		}
		if ( glm_catalogue_pdf_url() ) {
			$liens[] = array( 'Catalogue PDF', glm_catalogue_pdf_url() );
		}
	} else {
		$liens[] = array( 'Tous les services', glm_page_url( 'services' ) );
		foreach ( glm_services() as $slug => $titre ) {
			$liens[] = array( $titre, glm_service_url( $slug ) );
		}
	}
	$html = '<ul class="mobile-sub" id="mobile-sub-' . esc_attr( $key ) . '" hidden>';
	foreach ( $liens as $lien ) {
		$html .= '<li><a href="' . esc_url( $lien[1] ) . '">' . esc_html( $lien[0] ) . '</a></li>';
	}
	return $html . '</ul>';
}

/**
 * Menu principal. $contexte : « desktop » (panneaux déroulants) ou « mobile » (sous-listes dépliables).
 */
function glm_nav_list( $contexte = 'desktop' ) {
	$sous = array( 'Catalogue' => 'catalogue', 'Services' => 'services' );
	echo '<ul>';
	foreach ( glm_nav_items() as $item ) {
		$lien = sprintf(
			'<a href="%s"%s>%s</a>',
			esc_url( $item[1] ),
			$item[2] ? ' aria-current="page"' : '',
			esc_html( $item[0] )
		);
		if ( ! isset( $sous[ $item[0] ] ) ) {
			echo '<li>' . $lien . '</li>'; // phpcs:ignore
			continue;
		}
		$key    = $sous[ $item[0] ];
		$cible  = 'desktop' === $contexte ? 'mega-' . $key : 'mobile-sub-' . $key;
		$bouton = sprintf(
			'<button type="button" class="sub-toggle" aria-expanded="false" aria-controls="%s" aria-label="%s">%s</button>',
			esc_attr( $cible ),
			esc_attr( 'Afficher le sous-menu ' . $item[0] ),
			glm_chevron()
		);
		$panneau = 'desktop' === $contexte
			? ( 'catalogue' === $key ? glm_mega_catalogue() : glm_mega_services() )
			: glm_sous_menu_mobile( $key );
		echo '<li class="has-sub" data-sub="' . esc_attr( $key ) . '"><div class="has-sub__head">' . $lien . $bouton . '</div>' . $panneau . '</li>'; // phpcs:ignore
	}
	echo '</ul>';
}

/** Logo du site (version allégée WebP, dimensions fixées pour éviter les décalages). */
function glm_logo( $loading = 'eager' ) {
	return sprintf(
		'<img src="%s" alt="GLM Polyester" width="582" height="363"%s decoding="async">',
		esc_url( glm_asset( 'img/logo-glm.webp' ) ),
		'lazy' === $loading ? ' loading="lazy"' : ''
	);
}

/** Bloc « photo à venir » (conservé pour compatibilité ; les gabarits affichent désormais une mise en page sans photo). */
function glm_placeholder( $label = 'Photo à venir' ) {
	return '<div class="placeholder">' . esc_html( $label ) . '</div>';
}

/** Numéro de téléphone au format lien. */
function glm_tel_href( $tel ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $tel );
}

/** Nombre de places lisible (« 4 » devient « 4 places »). */
function glm_places( $value ) {
	$value = trim( (string) $value );
	return ( '' !== $value && is_numeric( substr( $value, 0, 1 ) ) && false === stripos( $value, 'place' ) ) ? $value . ' places' : $value;
}

/** Ligne de résumé d'un modèle (dimensions, places). */
function glm_modele_resume( $post_id ) {
	$parts = array();
	$dims  = get_post_meta( $post_id, '_glm_dimensions', true );
	$plac  = get_post_meta( $post_id, '_glm_places', true );
	if ( $dims ) {
		$parts[] = $dims;
	}
	if ( $plac ) {
		$parts[] = glm_places( $plac );
	}
	return implode( ' · ', $parts );
}

/** Repères courts d'une carte : places et longueur (« L 1,90 m »). */
function glm_modele_tags( $post_id ) {
	$tags   = array();
	$places = get_post_meta( $post_id, '_glm_places', true );
	if ( $places ) {
		$tags[] = glm_places( $places );
	}
	$dims = (string) get_post_meta( $post_id, '_glm_dimensions', true );
	if ( preg_match( '/\bL\s*([0-9]+(?:[,.][0-9]+)?)/u', $dims, $m ) ) {
		$unite  = preg_match( '/\bcm\b/u', $dims ) ? 'cm' : 'm';
		$tags[] = 'L ' . $m[1] . ' ' . $unite;
	}
	return $tags;
}

/** Image d'une carte : tailles adaptées à l'affichage, cadrage haut pour les photos en hauteur. */
function glm_image_carte( $attachment_id, $sizes, $eager = false ) {
	$meta     = wp_get_attachment_metadata( $attachment_id );
	$portrait = $meta && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['height'] > $meta['width'];
	$attrs    = array(
		'sizes'   => $sizes,
		'class'   => $portrait ? 'is-portrait' : '',
		'loading' => $eager ? 'eager' : 'lazy',
	);
	if ( $eager ) {
		$attrs['fetchpriority'] = 'high';
	}
	// Photo en hauteur : le recadrage 4:3 de WordPress couperait le haut (une tête, un toit) ; on garde la photo entière.
	return wp_get_attachment_image( $attachment_id, $portrait ? 'medium_large' : 'glm-carte', false, $attrs );
}

/** Tailles d'affichage des cartes du catalogue (4 colonnes, 3 sur tablette, 2 sur téléphone). */
function glm_sizes_carte() {
	return '(min-width: 1240px) 290px, (min-width: 1024px) 23vw, (min-width: 640px) 31vw, 48vw';
}

/** Carte d'un modèle du catalogue. */
function glm_modele_card( $post_id, $args = array() ) {
	$args  = wp_parse_args( $args, array( 'titre' => 'h3', 'eager' => false, 'extrait' => false ) );
	$tags  = glm_modele_tags( $post_id );
	$tag   = in_array( $args['titre'], array( 'h2', 'h3', 'h4' ), true ) ? $args['titre'] : 'h3';
	?>
	<a class="card2" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<div class="card2__media">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo glm_image_carte( get_post_thumbnail_id( $post_id ), glm_sizes_carte(), $args['eager'] ); // phpcs:ignore
			} else {
				echo '<span class="card2__vide" aria-hidden="true"></span>';
			}
			?>
		</div>
		<div class="card2__body">
			<<?php echo $tag; // phpcs:ignore ?> class="titre-carte"><?php echo esc_html( get_the_title( $post_id ) ); ?></<?php echo $tag; // phpcs:ignore ?>>
			<?php if ( $tags ) : ?>
				<ul class="tags">
					<?php foreach ( $tags as $glm_tag ) : ?>
						<li><?php echo esc_html( $glm_tag ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $args['extrait'] ) : ?>
				<?php $glm_extrait = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 28, '…' ); ?>
				<?php if ( $glm_extrait ) : ?>
					<p class="card2__extrait"><?php echo esc_html( $glm_extrait ); ?></p>
				<?php endif; ?>
				<span class="card2__plus">Voir la fiche</span>
			<?php endif; ?>
		</div>
	</a>
	<?php
}

/** Familles du catalogue, dans l'ordre choisi (réglage « glm_ordre » de chaque famille). */
function glm_familles() {
	static $terms = null;
	if ( null !== $terms ) {
		return $terms;
	}
	$terms = get_terms( array(
		'taxonomy'   => 'famille',
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	) );
	if ( is_wp_error( $terms ) ) {
		$terms = array();
		return $terms;
	}
	usort( $terms, function ( $a, $b ) {
		$oa = (int) get_term_meta( $a->term_id, 'glm_ordre', true );
		$ob = (int) get_term_meta( $b->term_id, 'glm_ordre', true );
		$oa = $oa ? $oa : 100 + $a->term_id;
		$ob = $ob ? $ob : 100 + $b->term_id;
		return $oa - $ob;
	} );
	return $terms;
}

/** Libellé « N modèle(s) ». */
function glm_nb_modeles( $count ) {
	$count = (int) $count;
	return $count . ' modèle' . ( $count > 1 ? 's' : '' );
}

/**
 * Petite famille, regroupée dans le catalogue (réglage « glm_compact » : 1 oui, 0 non).
 * Sans réglage : les familles après les trois premières.
 */
function glm_famille_compacte( $term ) {
	$reglage = get_term_meta( $term->term_id, 'glm_compact', true );
	if ( '1' === $reglage || '0' === $reglage ) {
		return '1' === $reglage;
	}
	$ids = wp_list_pluck( glm_familles(), 'term_id' );
	$pos = array_search( $term->term_id, $ids, true );
	return false !== $pos && $pos >= 3;
}

/** Thèmes utilisés par au moins un contenu du type donné. */
function glm_themes( $post_type ) {
	static $cache = array();
	if ( isset( $cache[ $post_type ] ) ) {
		return $cache[ $post_type ];
	}
	$terms = get_terms( array( 'taxonomy' => 'theme_glm', 'hide_empty' => false, 'orderby' => 'name' ) );
	if ( is_wp_error( $terms ) ) {
		$cache[ $post_type ] = array();
		return array();
	}
	$cache[ $post_type ] = array_values( array_filter( $terms, function ( $term ) use ( $post_type ) {
		return (bool) get_posts( array(
			'post_type'      => $post_type,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'theme_glm', 'terms' => $term->term_id ) ),
		) );
	} ) );
	return $cache[ $post_type ];
}

/** Un sujet de Noël (revendu par GLM, non personnalisable). */
function glm_est_noel( $post_id ) {
	return has_term( 'noel', 'theme_glm', $post_id );
}

/**
 * Encadré de la fiche : personnalisation (sujets roulants et volants) ou options (Noël).
 * Renvoie array( 'type' => 'perso'|'noel', 'titre', 'texte', 'note' ) ou un tableau vide.
 */
function glm_modele_perso( $post_id, $famille ) {
	if ( glm_est_noel( $post_id ) ) {
		return array( 'type' => 'noel', 'titre' => glm_t( 'noel_fiche_titre' ), 'texte' => glm_t( 'noel_fiche_texte' ), 'note' => '' );
	}
	if ( $famille && in_array( $famille->slug, array( 'sujets-roulants', 'sujets-volants' ), true ) ) {
		return array(
			'type'  => 'perso',
			'titre' => glm_t( 'perso_titre' ),
			'texte' => glm_t( 'perso_fiche_texte' ),
			'note'  => 'sujets-volants' === $famille->slug ? glm_t( 'perso_fiche_volant' ) : '',
		);
	}
	return array();
}

/** Ancien encadré sous forme de texte (compatibilité). */
function glm_modele_encart( $post_id, $famille ) {
	$perso = glm_modele_perso( $post_id, $famille );
	return $perso ? '<strong>' . esc_html( $perso['titre'] ) . '.</strong> ' . esc_html( trim( $perso['texte'] . ' ' . $perso['note'] ) ) : '';
}

/** Identifiant de la photo d'une famille : celle choisie dans l'admin (Catalogue > Familles), sinon celle d'un de ses modèles. */
function glm_famille_image_id( $term ) {
	$id = absint( get_term_meta( $term->term_id, 'glm_image', true ) );
	if ( $id && wp_attachment_is_image( $id ) ) {
		return $id;
	}
	// Sans photo choisie : celle du premier modèle de la famille (ordre alphabétique) qui en a une.
	$posts = get_posts( array(
		'post_type'      => 'modele',
		'posts_per_page' => 1,
		'meta_key'       => '_thumbnail_id',
		'orderby'        => 'title',
		'order'          => 'ASC',
		'tax_query'      => array( array( 'taxonomy' => 'famille', 'terms' => $term->term_id ) ),
		'fields'         => 'ids',
	) );
	return $posts ? (int) get_post_thumbnail_id( $posts[0] ) : 0;
}

/** Image représentant une famille. */
function glm_famille_image( $term, $size = 'glm-carte', $alt = null, $sizes = '' ) {
	$id = glm_famille_image_id( $term );
	if ( ! $id ) {
		return '';
	}
	$attrs = array( 'loading' => 'lazy', 'alt' => null === $alt ? $term->name : $alt );
	if ( $sizes ) {
		$attrs['sizes'] = $sizes;
	}
	return wp_get_attachment_image( $id, $size, false, $attrs );
}

/** Bandeau rouge « Demander un devis ». Sans argument : textes du réglage « Bandeau rouge ». */
function glm_cta( $titre = null, $texte = null ) {
	$tel   = glm_opt( 'telephone' );
	$titre = null === $titre ? glm_t( 'cta_titre' ) : $titre;
	$texte = null === $texte ? glm_t( 'cta_texte' ) : $texte;
	?>
	<section class="cta">
		<div class="container cta__grid">
			<div class="cta__text reveal">
				<h2 class="display display--m"><?php echo esc_html( $titre ); ?></h2>
				<p>
					<?php echo esc_html( $texte ); ?>
					<?php if ( $tel ) : ?>
						Téléphone : <a href="<?php echo esc_attr( glm_tel_href( $tel ) ); ?>"><?php echo esc_html( $tel ); ?></a>
					<?php endif; ?>
				</p>
			</div>
			<a class="btn btn--blanc" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Demander un devis</a>
		</div>
	</section>
	<?php
}

/** Fil d'Ariane : array( libellé => url ), le dernier élément sans lien. */
function glm_fil( $fil, $courant ) {
	?>
	<nav class="fil" aria-label="Fil d'Ariane">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
		<?php foreach ( $fil as $label => $url ) : ?>
			/ <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
		/ <span aria-current="page"><?php echo esc_html( $courant ); ?></span>
	</nav>
	<?php
}

/** En-tête gris des pages intérieures. */
function glm_page_hero( $titre, $intro = '', $fil = array() ) {
	?>
	<section class="page-hero">
		<div class="container">
			<?php glm_fil( $fil, $titre ); ?>
			<h1 class="display display--l"><?php echo esc_html( $titre ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="page-hero__lead"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/** Image d'un réglage (identifiant de média) ou image de repli du thème. */
function glm_image_opt( $key, $fallback_asset, $alt, $size = 'glm-large', $attrs = array() ) {
	$id = absint( get_theme_mod( 'glm_' . $key ) );
	if ( $id ) {
		$own  = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		$html = wp_get_attachment_image( $id, $size, false, array_merge( array( 'alt' => '' !== $own ? $own : $alt ), $attrs ) );
		if ( $html ) {
			return $html;
		}
	}
	if ( $fallback_asset ) {
		$loading = isset( $attrs['loading'] ) ? $attrs['loading'] : 'lazy';
		$extra   = isset( $attrs['fetchpriority'] ) ? ' fetchpriority="' . esc_attr( $attrs['fetchpriority'] ) . '"' : '';
		$path    = get_template_directory() . '/assets/' . $fallback_asset;
		$dims    = file_exists( $path ) ? getimagesize( $path ) : false;
		$wh      = $dims ? sprintf( ' width="%d" height="%d"', $dims[0], $dims[1] ) : '';
		return '<img src="' . esc_url( glm_asset( $fallback_asset ) ) . '" alt="' . esc_attr( $alt ) . '"' . $wh . ' loading="' . esc_attr( $loading ) . '"' . $extra . ' decoding="async">';
	}
	return '';
}

// Les réalisations n'ont pas de page individuelle : on renvoie vers la galerie.
add_action( 'template_redirect', function () {
	if ( is_singular( 'realisation' ) ) {
		wp_safe_redirect( glm_realisations_url(), 301 );
		exit;
	}
} );

// Le catalogue et la galerie affichent tout sur une seule page.
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'modele', 'realisation' ) ) || $query->is_tax( 'famille' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
	if ( $query->is_post_type_archive( 'realisation' ) && ! glm_licences_visibles() ) {
		$query->set( 'meta_query', glm_sans_licence() );
	}
} );

/** Les photos avec des personnages sous licence sont-elles affichées ? (Apparence > Personnaliser > Réalisations) */
function glm_licences_visibles() {
	return '0' !== (string) get_theme_mod( 'glm_licences', '1' );
}

/** Condition « sans personnage sous licence » pour les requêtes de réalisations. */
function glm_sans_licence() {
	return array(
		'relation' => 'OR',
		array( 'key' => '_glm_licence', 'compare' => 'NOT EXISTS' ),
		array( 'key' => '_glm_licence', 'value' => '1', 'compare' => '!=' ),
	);
}

/**
 * Réalisations à montrer : celles mises en avant d'abord, puis les plus récentes.
 * $types : identifiants (slug) de types de réalisation, ou vide pour toutes.
 * $sans_licence : écarte les photos de personnages sous licence (pages Services).
 */
function glm_realisations_choisies( $nombre, $types = array(), $sans_licence = false ) {
	$base = array(
		'post_type'      => 'realisation',
		'posts_per_page' => $nombre,
		'fields'         => 'ids',
	);
	if ( $types ) {
		$base['tax_query'] = array( array( 'taxonomy' => 'type_realisation', 'field' => 'slug', 'terms' => (array) $types ) );
	}
	$filtre = ( glm_licences_visibles() && ! $sans_licence ) ? array() : array( glm_sans_licence() );
	$avant  = get_posts( array_merge( $base, array( 'meta_query' => array_merge( array( 'relation' => 'AND', array( 'key' => '_glm_large', 'value' => '1' ) ), $filtre ) ) ) );
	$ids    = $avant;
	if ( count( $ids ) < $nombre ) {
		$reste = get_posts( array_merge( $base, array(
			'posts_per_page' => $nombre - count( $ids ),
			'post__not_in'   => $ids ? $ids : array( 0 ),
			'meta_query'     => $filtre,
		) ) );
		$ids   = array_merge( $ids, $reste );
	}
	return $ids;
}
