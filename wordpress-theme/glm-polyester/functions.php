<?php
/**
 * GLM Polyester : fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLM_VERSION', '1.0.0' );

require get_template_directory() . '/inc/content-types.php';
require get_template_directory() . '/inc/admin.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/contact.php';
require get_template_directory() . '/inc/install.php';
require get_template_directory() . '/inc/import.php';

/* ---------- Réglages du thème ---------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'glm-carte', 800, 600, true );
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

// Marque la page comme « avec JavaScript » le plus tôt possible (pour les apparitions au défilement).
add_action( 'wp_head', function () {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}, 1 );

// Allège l'en-tête : pas d'emojis ni de styles de blocs inutiles sur les gabarits du thème.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/* ---------- Aides ---------- */

/** Valeur d'un réglage du thème (Apparence > Personnaliser). */
function glm_opt( $key, $default = '' ) {
	$value = get_theme_mod( 'glm_' . $key, $default );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/** URL d'un fichier du dossier assets. */
function glm_asset( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/** URL d'une page par son identifiant court, avec repli sur l'accueil. */
function glm_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function glm_catalogue_url() {
	$url = get_post_type_archive_link( 'modele' );
	return $url ? $url : home_url( '/catalogue/' );
}

function glm_realisations_url() {
	$url = get_post_type_archive_link( 'realisation' );
	return $url ? $url : home_url( '/realisations/' );
}

/** Éléments du menu principal : libellé, URL, actif ou non. */
function glm_nav_items() {
	return array(
		array( 'Accueil', home_url( '/' ), is_front_page() ),
		array( 'Catalogue', glm_catalogue_url(), is_post_type_archive( 'modele' ) || is_singular( 'modele' ) || is_tax( 'famille' ) || is_page( 'feuilleter-le-catalogue' ) ),
		array( 'Services', glm_page_url( 'services' ), is_page( 'services' ) ),
		array( 'Réalisations', glm_realisations_url(), is_post_type_archive( 'realisation' ) ),
		array( 'L\'atelier', glm_page_url( 'atelier' ), is_page( 'atelier' ) ),
		array( 'Contact', glm_page_url( 'contact' ), is_page( 'contact' ) ),
	);
}

/**
 * Pages du catalogue à feuilleter : images wp-content/uploads/glm-catalogue/page-NN-1600.jpg
 * (et page-NN-900.jpg pour les petits écrans), tirées du PDF du catalogue.
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
	foreach ( $files as $file ) {
		$name  = basename( $file );
		$small = str_replace( '-1600.jpg', '-900.jpg', $name );
		$size  = getimagesize( $file );
		$pages[] = array(
			'grand' => $url . $name . '?v=' . filemtime( $file ),
			'petit' => file_exists( $dir . $small ) ? $url . $small . '?v=' . filemtime( $dir . $small ) : '',
			'w'     => $size ? (int) $size[0] : 1600,
			'h'     => $size ? (int) $size[1] : 1131,
		);
	}
	return $pages;
}

/** Adresse de la page « Feuilleter le catalogue », ou vide s'il n'y a rien à feuilleter. */
function glm_feuilleter_url() {
	if ( ! glm_catalogue_pages() ) {
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

// Crée une fois la page « Feuilleter le catalogue » (modèle page-feuilleter-le-catalogue.php).
add_action( 'admin_init', function () {
	if ( get_option( 'glm_page_feuilleter' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( glm_ensure_page( 'feuilleter-le-catalogue', 'Feuilleter le catalogue' ) ) {
		update_option( 'glm_page_feuilleter', 1 );
	}
} );

/** Blocs de la page Services, repris dans le menu déroulant (ancre = titre). */
function glm_services_menu() {
	return array(
		'Fabrication'     => 'Manèges, sujets et pièces du catalogue.',
		'Personnalisable' => 'Couleurs, décor peint ou covering.',
		'Thermoformage'   => 'Cabochons, optiques et pièces formées à chaud.',
		'Réparation'      => 'Pièces cassées ou usées remises en état.',
		'Rénovation'      => 'Sujets et manèges anciens remis à neuf.',
	);
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
	$html .= '<li><a class="dropdown__tout" href="' . esc_url( glm_catalogue_url() ) . '">Tout le catalogue <span aria-hidden="true">→</span></a></li>';
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
	$url  = glm_page_url( 'services' );
	$html = '<div class="dropdown" id="mega-services"><ul>';
	foreach ( array_keys( glm_services_menu() ) as $titre ) {
		$html .= '<li><a href="' . esc_url( $url . '#' . sanitize_title( $titre ) ) . '">' . esc_html( $titre ) . '</a></li>';
	}
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
		$url = glm_page_url( 'services' );
		foreach ( array_keys( glm_services_menu() ) as $titre ) {
			$liens[] = array( $titre, $url . '#' . sanitize_title( $titre ) );
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

/** Bloc « photo à venir ». */
function glm_placeholder( $label = 'Photo à venir' ) {
	return '<div class="placeholder">' . esc_html( $label ) . '</div>';
}

/** Numéro de téléphone au format lien. */
function glm_tel_href( $tel ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $tel );
}

/** Ligne de résumé d'un modèle (dimensions, places). */
function glm_modele_resume( $post_id ) {
	$parts = array();
	foreach ( array( '_glm_dimensions', '_glm_places' ) as $key ) {
		$value = get_post_meta( $post_id, $key, true );
		if ( $value ) {
			$parts[] = '_glm_places' === $key && is_numeric( substr( $value, 0, 1 ) ) ? $value . ' places' : $value;
		}
	}
	return implode( ' · ', $parts );
}

/** Carte d'un modèle du catalogue. */
function glm_modele_card( $post_id ) {
	$resume = glm_modele_resume( $post_id );
	$badge  = '1' === get_post_meta( $post_id, '_glm_sujet_glm', true );
	?>
	<a class="card reveal" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<div class="card__media">
			<?php if ( $badge ) : ?>
				<span class="badge-glm">Sujet GLM</span>
			<?php endif; ?>
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'glm-carte', array( 'loading' => 'lazy' ) );
			} else {
				echo glm_placeholder(); // phpcs:ignore
			}
			?>
		</div>
		<div class="card__body">
			<h3 class="titre-carte"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
			<?php if ( $resume ) : ?>
				<p><?php echo esc_html( $resume ); ?></p>
			<?php endif; ?>
			<span class="card__more">Voir la fiche</span>
		</div>
	</a>
	<?php
}

/** Familles du catalogue, dans l'ordre choisi (réglage « glm_ordre » de chaque famille). */
function glm_familles() {
	$terms = get_terms( array(
		'taxonomy'   => 'famille',
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	) );
	if ( is_wp_error( $terms ) ) {
		return array();
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

/** Thèmes utilisés par au moins un contenu du type donné. */
function glm_themes( $post_type ) {
	$terms = get_terms( array( 'taxonomy' => 'theme_glm', 'hide_empty' => false, 'orderby' => 'name' ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return array_values( array_filter( $terms, function ( $term ) use ( $post_type ) {
		return (bool) get_posts( array(
			'post_type'      => $post_type,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'theme_glm', 'terms' => $term->term_id ) ),
		) );
	} ) );
}

/** Encadré sous la fiche : personnalisation (sujets roulants et volants) ou options (Noël). */
function glm_modele_encart( $post_id, $famille ) {
	if ( has_term( 'noel', 'theme_glm', $post_id ) ) {
		return '<strong>Options.</strong> Bruiteur, marche et éclairage peuvent être ajoutés à ce sujet.';
	}
	if ( $famille && 'sujets-volants' === $famille->slug ) {
		return '<strong>Personnalisable.</strong> Couleurs au choix, décor peint ou covering. Le parallélogramme est fourni par le client.';
	}
	if ( $famille && 'sujets-roulants' === $famille->slug ) {
		return '<strong>Personnalisable.</strong> Couleurs au choix, décor peint ou covering.';
	}
	return '';
}

/** Image représentant une famille : celle de son modèle le plus récent. */
function glm_famille_image( $term, $size = 'glm-carte', $alt = null ) {
	$posts = get_posts( array(
		'post_type'      => 'modele',
		'posts_per_page' => 1,
		'meta_key'       => '_thumbnail_id',
		'tax_query'      => array( array( 'taxonomy' => 'famille', 'terms' => $term->term_id ) ),
		'fields'         => 'ids',
	) );
	return $posts ? get_the_post_thumbnail( $posts[0], $size, array( 'loading' => 'lazy', 'alt' => null === $alt ? $term->name : $alt ) ) : '';
}

/** Bandeau rouge « Demander un devis ». */
function glm_cta( $titre = 'Un projet, une réparation, une rénovation ?', $texte = 'Décrivez votre besoin, nous revenons vers vous avec un devis.' ) {
	$tel = glm_opt( 'telephone' );
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

/** En-tête gris des pages intérieures. */
function glm_page_hero( $titre, $intro = '', $fil = array() ) {
	?>
	<section class="page-hero">
		<div class="container">
			<nav class="fil" aria-label="Fil d'Ariane">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
				<?php foreach ( $fil as $label => $url ) : ?>
					/ <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
				/ <?php echo esc_html( $titre ); ?>
			</nav>
			<h1 class="display display--l"><?php echo esc_html( $titre ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="page-hero__lead"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/** Image d'un réglage (identifiant de média) ou image de repli du thème. */
function glm_image_opt( $key, $fallback_asset, $alt, $size = 'glm-large' ) {
	$id = absint( get_theme_mod( 'glm_' . $key ) );
	if ( $id ) {
		$own  = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		$html = wp_get_attachment_image( $id, $size, false, array( 'alt' => '' !== $own ? $own : $alt ) );
		if ( $html ) {
			return $html;
		}
	}
	if ( $fallback_asset ) {
		return '<img src="' . esc_url( glm_asset( $fallback_asset ) ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">';
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
} );

// Titres d'onglet plus propres pour les archives.
add_filter( 'document_title_parts', function ( $parts ) {
	if ( is_post_type_archive( 'modele' ) ) {
		$parts['title'] = 'Catalogue';
	} elseif ( is_post_type_archive( 'realisation' ) ) {
		$parts['title'] = 'Réalisations';
	}
	return $parts;
} );
