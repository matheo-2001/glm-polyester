<?php
/**
 * Référencement : titres, descriptions, aperçus de partage (Open Graph), données structurées,
 * plan du site et discrétion sur le compte administrateur.
 * Si une extension de référencement est activée (Yoast, Rank Math, SEOPress, AIOSEO),
 * le thème lui laisse les titres, descriptions, aperçus et données structurées.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Une extension de référencement est-elle active ? */
function glm_seo_extension() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/** Texte ramené à une description d'environ 155 caractères. */
function glm_seo_court( $text, $max = 155 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $text ) ) ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max - 1 );
	$pos = strrpos( $cut, ' ' ); // Position en octets : l'espace tient sur un octet, la coupe reste valide.
	return rtrim( $pos ? substr( $cut, 0, $pos ) : $cut, ' ,;:' ) . '…';
}

/** Famille principale d'un modèle. */
function glm_seo_famille( $post_id ) {
	$terms = get_the_terms( $post_id, 'famille' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/** Description de la page courante. */
function glm_seo_description() {
	if ( is_front_page() ) {
		$d = glm_t( 'seo_accueil' );
		return glm_seo_court( $d ? $d : glm_opt( 'hero_texte', 'GLM Polyester fabrique, répare et rénove des manèges, des sujets et des pièces en polyester, et réalise des pièces thermoformées. Des modèles du catalogue, personnalisables.' ) );
	}
	if ( is_singular( 'modele' ) ) {
		$id      = get_queried_object_id();
		$content = get_post_field( 'post_content', $id );
		if ( trim( wp_strip_all_tags( $content ) ) ) {
			return glm_seo_court( $content );
		}
		$famille = glm_seo_famille( $id );
		return glm_seo_court( get_the_title( $id ) . ( $famille ? ', ' . $famille->name : '' ) . ' par GLM Polyester, constructeur de manèges à Breteuil. ' . glm_modele_resume( $id ) );
	}
	if ( is_tax( 'famille' ) ) {
		$term = get_queried_object();
		$base = $term->description ? $term->description : $term->name . ' du catalogue GLM Polyester.';
		return glm_seo_court( $base . ' ' . glm_nb_modeles( $term->count ) . '.' );
	}
	if ( is_post_type_archive( 'modele' ) ) {
		$theme = glm_theme_courant();
		if ( $theme ) {
			$titres = get_posts( array( 'post_type' => 'modele', 'posts_per_page' => 6, 'fields' => 'ids', 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array( array( 'taxonomy' => 'theme_glm', 'terms' => $theme->term_id ) ) ) );
			$noms   = implode( ', ', array_map( 'get_the_title', $titres ) );
			return glm_seo_court( 'Catalogue GLM Polyester, thème ' . $theme->name . ( $noms ? ' : ' . $noms . '.' : '.' ) );
		}
		$d = glm_t( 'seo_catalogue' );
		return glm_seo_court( $d ? $d : glm_t( 'catalogue_intro' ) );
	}
	if ( is_post_type_archive( 'realisation' ) ) {
		$d = glm_t( 'seo_realisations' );
		return glm_seo_court( $d ? $d : glm_t( 'realisations_intro' ) );
	}
	if ( is_page( 'feuilleter-le-catalogue' ) ) {
		$d = glm_t( 'seo_catalogue' );
		return glm_seo_court( 'Feuilletez le catalogue des sujets de manège de GLM Polyester. ' . ( $d ? $d : glm_t( 'catalogue_intro' ) ) );
	}
	if ( glm_service_courant() ) {
		return glm_seo_court( glm_t( 'service_' . glm_service_courant() . '_texte' ) );
	}
	foreach ( array( 'services', 'atelier', 'contact' ) as $slug ) {
		if ( is_page( $slug ) ) {
			$d = glm_t( 'seo_' . $slug );
			return glm_seo_court( $d ? $d : glm_t( $slug . '_intro' ) );
		}
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		return glm_seo_court( has_excerpt( $post ) ? $post->post_excerpt : $post->post_content );
	}
	return '';
}

/** Adresse canonique de la page courante. */
function glm_seo_canonical() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return (string) wp_get_canonical_url();
	}
	if ( is_post_type_archive( 'modele' ) ) {
		$theme = glm_theme_courant();
		return $theme ? glm_theme_url( $theme ) : glm_catalogue_url();
	}
	if ( is_post_type_archive( 'realisation' ) ) {
		return glm_realisations_url();
	}
	if ( is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? '' : $link;
	}
	return '';
}

/** Image de partage : array( url, largeur, hauteur, texte ). */
function glm_seo_image() {
	$id = 0;
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$id = get_post_thumbnail_id( get_queried_object_id() );
	} elseif ( is_tax( 'famille' ) ) {
		$id = glm_famille_image_id( get_queried_object() );
	} elseif ( is_front_page() ) {
		$id = absint( get_theme_mod( 'glm_img_hero' ) );
	} elseif ( is_page( 'atelier' ) || is_page( 'services' ) ) {
		$id = absint( get_theme_mod( 'glm_img_atelier' ) );
	}
	if ( $id ) {
		$src = wp_get_attachment_image_src( $id, 'glm-large' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2], (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		}
	}
	return array( glm_asset( 'img/traineau.jpg' ), 1920, 1080, 'Sujet de manège de Noël dans l\'atelier GLM Polyester' );
}

/* ---------- Titres d'onglet ---------- */
add_filter( 'document_title_parts', function ( $parts ) {
	if ( is_post_type_archive( 'modele' ) ) {
		$theme          = glm_theme_courant();
		$parts['title'] = $theme ? 'Thème ' . $theme->name . ' – Catalogue' : 'Catalogue';
	} elseif ( is_post_type_archive( 'realisation' ) ) {
		$parts['title'] = 'Réalisations';
	} elseif ( glm_service_courant() && ! glm_seo_extension() ) {
		$parts['title'] = glm_t( 'seo_titre_' . glm_service_courant() );
	} elseif ( ( is_page( 'services' ) || is_page( 'atelier' ) ) && ! glm_seo_extension() ) {
		$parts['title'] = glm_t( 'seo_titre_' . ( is_page( 'services' ) ? 'services' : 'atelier' ) );
	} elseif ( is_singular( 'modele' ) && ! glm_seo_extension() ) {
		$famille = glm_seo_famille( get_queried_object_id() );
		if ( $famille ) {
			$parts['title'] = get_the_title( get_queried_object_id() ) . ' – ' . $famille->name;
		}
	}
	return $parts;
} );

/* ---------- Description, canonique et aperçus de partage ---------- */
add_action( 'wp_head', function () {
	if ( glm_seo_extension() || is_404() || is_search() ) {
		return;
	}
	$desc      = glm_seo_description();
	$canonical = glm_seo_canonical();
	$image     = glm_seo_image();
	$titre     = wp_get_document_title();

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	// WordPress écrit déjà l'adresse canonique des pages et des fiches ; on l'ajoute pour les listes.
	if ( $canonical && ! is_singular() && ! is_front_page() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	}
	$og = array(
		'og:locale'    => 'fr_FR',
		'og:type'      => 'website',
		'og:site_name' => get_bloginfo( 'name' ),
		'og:title'     => $titre,
		'og:description' => $desc,
		'og:url'       => $canonical,
		'og:image'     => $image[0],
		'og:image:width'  => $image[1],
		'og:image:height' => $image[2],
		'og:image:alt' => $image[3],
	);
	foreach ( $og as $property => $value ) {
		if ( '' !== (string) $value ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $value ) );
		}
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}, 5 );

/* ---------- Données structurées (schema.org) ---------- */

/** Fiche « entreprise locale ». */
function glm_schema_entreprise() {
	$adresse2 = glm_opt( 'adresse_2', '60120 Breteuil' );
	$cp       = preg_match( '/\b(\d{5})\b/', $adresse2, $m ) ? $m[1] : '';
	$ville    = trim( str_replace( $cp, '', $adresse2 ) );
	$data     = array(
		'@type'       => 'LocalBusiness',
		'@id'         => home_url( '/#entreprise' ),
		'name'        => get_bloginfo( 'name' ),
		'url'         => home_url( '/' ),
		'logo'        => glm_asset( 'img/logo-glm.png' ),
		'image'       => glm_seo_image()[0],
		'description' => glm_seo_court( glm_opt( 'hero_texte', 'GLM Polyester fabrique, répare et rénove des manèges, des sujets et des pièces en polyester, et réalise des pièces thermoformées.' ), 300 ),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => glm_opt( 'adresse_1', '46 rue de Montdidier' ),
			'postalCode'      => $cp,
			'addressLocality' => $ville,
			'addressRegion'   => 'Oise',
			'addressCountry'  => 'FR',
		),
		'identifier'  => array(
			'@type'      => 'PropertyValue',
			'propertyID' => 'SIRET',
			'value'      => str_replace( ' ', '', glm_opt( 'siret', '977 989 318 00026' ) ),
		),
	);
	if ( glm_opt( 'telephone' ) ) {
		$data['telephone'] = glm_opt( 'telephone' );
	}
	if ( glm_opt( 'email' ) ) {
		$data['email'] = glm_opt( 'email' );
	}
	if ( glm_opt( 'facebook', 'https://www.facebook.com/glmpolyester/' ) ) {
		$data['sameAs'] = array( glm_opt( 'facebook', 'https://www.facebook.com/glmpolyester/' ) );
	}
	return $data;
}

/** Fil d'Ariane de la page courante : liste de array( nom, url ). */
function glm_schema_fil() {
	$fil = array( array( 'Accueil', home_url( '/' ) ) );
	if ( is_singular( 'modele' ) ) {
		$fil[]   = array( 'Catalogue', glm_catalogue_url() );
		$famille = glm_seo_famille( get_queried_object_id() );
		if ( $famille ) {
			$fil[] = array( $famille->name, get_term_link( $famille ) );
		}
		$fil[] = array( get_the_title( get_queried_object_id() ), get_permalink( get_queried_object_id() ) );
	} elseif ( is_tax( 'famille' ) ) {
		$fil[] = array( 'Catalogue', glm_catalogue_url() );
		$fil[] = array( get_queried_object()->name, get_term_link( get_queried_object() ) );
	} elseif ( is_post_type_archive( 'modele' ) ) {
		$fil[] = array( 'Catalogue', glm_catalogue_url() );
		$theme = glm_theme_courant();
		if ( $theme ) {
			$fil[] = array( 'Thème ' . $theme->name, glm_theme_url( $theme ) );
		}
	} elseif ( is_post_type_archive( 'realisation' ) ) {
		$fil[] = array( 'Réalisations', glm_realisations_url() );
	} elseif ( is_page() && ! is_front_page() ) {
		if ( is_page( 'feuilleter-le-catalogue' ) ) {
			$fil[] = array( 'Catalogue', glm_catalogue_url() );
		}
		if ( glm_service_courant() ) {
			$fil[] = array( 'Services', glm_page_url( 'services' ) );
		}
		$fil[] = array( get_the_title( get_queried_object_id() ), get_permalink( get_queried_object_id() ) );
	}
	return count( $fil ) > 1 ? $fil : array();
}

/** Fiche « produit » d'un modèle (sans prix). */
function glm_schema_produit( $id ) {
	$famille = glm_seo_famille( $id );
	$images  = array();
	if ( has_post_thumbnail( $id ) ) {
		$images[] = wp_get_attachment_image_url( get_post_thumbnail_id( $id ), 'glm-large' );
	}
	foreach ( array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $id, '_glm_galerie', true ) ) ) ) as $photo ) {
		$url = wp_get_attachment_image_url( $photo, 'glm-large' );
		if ( $url && ! in_array( $url, $images, true ) ) {
			$images[] = $url;
		}
	}
	$data = array(
		'@type'       => 'Product',
		'name'        => get_the_title( $id ),
		'url'         => get_permalink( $id ),
		'description' => glm_seo_description(),
	);
	if ( $images ) {
		$data['image'] = $images;
	}
	if ( $famille ) {
		$data['category'] = $famille->name;
	}
	// Seuls les sujets fabriqués par GLM portent sa marque.
	if ( '1' === get_post_meta( $id, '_glm_sujet_glm', true ) ) {
		$data['brand']        = array( '@type' => 'Brand', 'name' => get_bloginfo( 'name' ) );
		$data['manufacturer'] = array( '@id' => home_url( '/#entreprise' ) );
	}
	if ( get_post_meta( $id, '_glm_materiau', true ) ) {
		$data['material'] = get_post_meta( $id, '_glm_materiau', true );
	}
	$props = array();
	foreach ( glm_modele_fields() as $key => $field ) {
		$value = get_post_meta( $id, $key, true );
		if ( $value && '_glm_materiau' !== $key ) {
			$props[] = array( '@type' => 'PropertyValue', 'name' => $field[0], 'value' => '_glm_places' === $key ? glm_places( $value ) : $value );
		}
	}
	if ( $props ) {
		$data['additionalProperty'] = $props;
	}
	return $data;
}

add_action( 'wp_head', function () {
	if ( glm_seo_extension() || is_404() || is_search() ) {
		return;
	}
	$graph = array();
	if ( is_front_page() || is_page( 'contact' ) || is_page( 'atelier' ) ) {
		$graph[] = glm_schema_entreprise();
	}
	if ( is_singular( 'modele' ) ) {
		$graph[] = glm_schema_produit( get_queried_object_id() );
	}
	$service = glm_service_courant();
	if ( $service ) {
		$graph[] = array(
			'@type'       => 'Service',
			'name'        => get_the_title( get_queried_object_id() ),
			'description' => glm_t( 'service_' . $service . '_texte' ),
			'url'         => get_permalink( get_queried_object_id() ),
			'provider'    => array( '@id' => home_url( '/#entreprise' ) ),
			'areaServed'  => 'FR',
		);
		$questions = array();
		foreach ( glm_service_faq( $service ) as $qr ) {
			$questions[] = array( '@type' => 'Question', 'name' => $qr[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qr[1] ) );
		}
		if ( $questions ) {
			$graph[] = array( '@type' => 'FAQPage', 'mainEntity' => $questions );
		}
	}
	if ( is_page( 'atelier' ) ) {
		$questions = array();
		foreach ( glm_faq( 'atelier_faq' ) as $qr ) {
			$questions[] = array( '@type' => 'Question', 'name' => $qr[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qr[1] ) );
		}
		if ( $questions ) {
			$graph[] = array( '@type' => 'FAQPage', 'mainEntity' => $questions );
		}
	}
	$fil = glm_schema_fil();
	if ( $fil ) {
		$items = array();
		foreach ( $fil as $i => $etape ) {
			$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $etape[0], 'item' => $etape[1] );
		}
		$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $items );
	}
	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
	}
}, 6 );

/* ---------- Pages à ne pas indexer (le site n'a pas de blog) ---------- */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() || is_search() || is_page( array( 'page-d-exemple', 'sample-page' ) ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );

/* ---------- Plan du site (wp-sitemap.xml) ---------- */
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['post'], $types['realisation'] ); // Pas de blog ; les réalisations n'ont pas de page.
	return $types;
} );
add_filter( 'wp_sitemaps_taxonomies', function ( $taxonomies ) {
	unset( $taxonomies['category'], $taxonomies['post_tag'] );
	return $taxonomies;
} );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	if ( 'page' === $post_type ) {
		$exclure = array();
		foreach ( array_merge( array( 'page-d-exemple', 'sample-page' ), glm_feuilleter_actif() ? array() : array( 'feuilleter-le-catalogue' ) ) as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$exclure[] = $page->ID;
			}
		}
		if ( $exclure ) {
			$args['post__not_in'] = array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array(), $exclure );
		}
	}
	return $args;
}, 10, 2 );

// Pages « thème » du catalogue dans le plan du site.
add_action( 'init', function () {
	if ( ! class_exists( 'WP_Sitemaps_Provider' ) || ! function_exists( 'wp_register_sitemap_provider' ) ) {
		return;
	}
	if ( ! class_exists( 'GLM_Sitemap_Themes' ) ) {
		class GLM_Sitemap_Themes extends WP_Sitemaps_Provider { // phpcs:ignore
			public function __construct() {
				$this->name        = 'themes';
				$this->object_type = 'themes';
			}
			public function get_url_list( $page_num, $object_subtype = '' ) {
				$urls = array();
				foreach ( glm_themes( 'modele' ) as $term ) {
					$urls[] = array( 'loc' => glm_theme_url( $term ) );
				}
				return $urls;
			}
			public function get_max_num_pages( $object_subtype = '' ) {
				return glm_themes( 'modele' ) ? 1 : 0;
			}
		}
	}
	wp_register_sitemap_provider( 'themes', new GLM_Sitemap_Themes() );
}, 30 );

/* ---------- Discrétion sur le compte administrateur ---------- */

// Pas de page auteur (elle affiche l'identifiant de connexion).
add_action( 'template_redirect', function () {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
	// Pas de flux RSS : le site n'a pas de blog.
	if ( is_feed() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 1 );

// Liste des utilisateurs de l'API réservée aux personnes connectées.
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		foreach ( array_keys( $endpoints ) as $route ) {
			if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
				unset( $endpoints[ $route ] );
			}
		}
	}
	return $endpoints;
} );

// Pas de nom d'auteur dans les aperçus intégrés (oEmbed).
add_filter( 'oembed_response_data', function ( $data ) {
	unset( $data['author_name'], $data['author_url'] );
	return $data;
} );

// En-tête plus sobre.
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
