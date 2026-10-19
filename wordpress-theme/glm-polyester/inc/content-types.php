<?php
/**
 * Types de contenu : Modèles (catalogue) et Réalisations (galerie).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'glm_register_content_types' );

function glm_register_content_types() {
	register_post_type( 'modele', array(
		'labels'        => array(
			'name'               => 'Modèles',
			'singular_name'      => 'Modèle',
			'add_new'            => 'Ajouter un modèle',
			'add_new_item'       => 'Ajouter un modèle',
			'edit_item'          => 'Modifier le modèle',
			'new_item'           => 'Nouveau modèle',
			'view_item'          => 'Voir le modèle',
			'search_items'       => 'Rechercher un modèle',
			'not_found'          => 'Aucun modèle',
			'all_items'          => 'Tous les modèles',
			'menu_name'          => 'Catalogue',
			'featured_image'     => 'Photo principale',
			'set_featured_image' => 'Choisir la photo principale',
		),
		'public'        => true,
		'has_archive'   => 'catalogue',
		'rewrite'       => array( 'slug' => 'catalogue', 'with_front' => false ),
		'menu_icon'     => 'dashicons-screenoptions',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest'  => false,
	) );

	register_taxonomy( 'famille', 'modele', array(
		'labels'            => array(
			'name'          => 'Familles',
			'singular_name' => 'Famille',
			'add_new_item'  => 'Ajouter une famille',
			'edit_item'     => 'Modifier la famille',
			'all_items'     => 'Toutes les familles',
			'menu_name'     => 'Familles',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'famille', 'with_front' => false ),
		'show_in_rest'      => false,
	) );

	register_post_type( 'realisation', array(
		'labels'        => array(
			'name'               => 'Réalisations',
			'singular_name'      => 'Réalisation',
			'add_new'            => 'Ajouter une réalisation',
			'add_new_item'       => 'Ajouter une réalisation',
			'edit_item'          => 'Modifier la réalisation',
			'new_item'           => 'Nouvelle réalisation',
			'search_items'       => 'Rechercher une réalisation',
			'not_found'          => 'Aucune réalisation',
			'all_items'          => 'Toutes les réalisations',
			'menu_name'          => 'Réalisations',
			'featured_image'     => 'Photo (ou photo « après »)',
			'set_featured_image' => 'Choisir la photo',
		),
		'public'        => true,
		'has_archive'   => 'realisations',
		'rewrite'       => array( 'slug' => 'realisations', 'with_front' => false ),
		'menu_icon'     => 'dashicons-format-gallery',
		'menu_position' => 6,
		'supports'      => array( 'title', 'thumbnail' ),
		'show_in_rest'  => false,
	) );

	register_taxonomy( 'theme_glm', array( 'modele', 'realisation' ), array(
		'labels'            => array(
			'name'          => 'Thèmes',
			'singular_name' => 'Thème',
			'add_new_item'  => 'Ajouter un thème',
			'edit_item'     => 'Modifier le thème',
			'all_items'     => 'Tous les thèmes',
			'menu_name'     => 'Thèmes',
		),
		'public'            => false,
		'show_ui'           => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => false,
	) );

	register_taxonomy( 'type_realisation', 'realisation', array(
		'labels'            => array(
			'name'          => 'Types de réalisation',
			'singular_name' => 'Type de réalisation',
			'add_new_item'  => 'Ajouter un type',
			'all_items'     => 'Tous les types',
			'menu_name'     => 'Types',
		),
		'public'            => false,
		'show_ui'           => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => false,
	) );
}

/* ---------- Pages « thème » du catalogue : /catalogue/theme/noel/ ---------- */
add_action( 'init', function () {
	add_rewrite_rule( '^catalogue/theme/([^/]+)/?$', 'index.php?post_type=modele&glm_theme=$matches[1]', 'top' );
}, 20 );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'glm_theme';
	return $vars;
} );

// Règles d'adresse recalculées une fois après une mise à jour du thème.
add_action( 'init', function () {
	if ( get_option( 'glm_rewrite_version' ) !== GLM_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'glm_rewrite_version', GLM_VERSION );
	}
}, 99 );

// Anciennes adresses « /catalogue/?theme=noel » : redirection vers la nouvelle.
add_action( 'template_redirect', function () {
	if ( ! is_post_type_archive( 'modele' ) || empty( $_GET['theme'] ) ) { // phpcs:ignore
		return;
	}
	$term = get_term_by( 'slug', sanitize_key( wp_unslash( $_GET['theme'] ) ), 'theme_glm' ); // phpcs:ignore
	if ( $term ) {
		wp_safe_redirect( glm_theme_url( $term ), 301 );
		exit;
	}
} );
