<?php
/**
 * Import ponctuel de photos : lit wp-content/uploads/glm-import/manifest.json
 * à la prochaine visite d'un administrateur, une seule fois par manifeste.
 * Sans dossier glm-import, ce fichier ne fait rien.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', 'glm_run_import' );

/** Copie un fichier dans la médiathèque et renvoie l'identifiant de la pièce jointe. */
function glm_import_file( $path, $alt ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore
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

/** Pièce jointe déjà présente dans la médiathèque pour ce nom de fichier, ou 0. */
function glm_find_attachment( $name ) {
	global $wpdb;
	$name = basename( $name );
	$id   = $wpdb->get_var( $wpdb->prepare( // phpcs:ignore
		"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND ( meta_value = %s OR meta_value LIKE %s ) ORDER BY post_id ASC LIMIT 1",
		$name,
		'%/' . $wpdb->esc_like( $name )
	) );
	return (int) $id;
}

/** Contenu (modèle ou réalisation) par titre exact, ou 0. */
function glm_find_post( $post_type, $title ) {
	$posts = get_posts( array( 'post_type' => $post_type, 'title' => $title, 'posts_per_page' => 1, 'fields' => 'ids', 'post_status' => 'any' ) );
	return $posts ? (int) $posts[0] : 0;
}

/** Thèmes d'un contenu, à partir de leurs identifiants (slug). */
function glm_set_themes( $post_id, $slugs ) {
	$ids = array();
	foreach ( (array) $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'theme_glm' );
		if ( $term ) {
			$ids[] = (int) $term->term_id;
		}
	}
	wp_set_object_terms( $post_id, $ids, 'theme_glm' );
}

/** Réglages et champs d'un modèle (création ou mise à jour). */
function glm_apply_modele( $id, $item, $get ) {
	if ( ! empty( $item['famille'] ) ) {
		$term = get_term_by( 'slug', $item['famille'], 'famille' );
		if ( $term ) {
			wp_set_object_terms( $id, array( (int) $term->term_id ), 'famille' );
		}
	}
	if ( isset( $item['themes'] ) ) {
		glm_set_themes( $id, $item['themes'] );
	}
	if ( isset( $item['badge'] ) ) {
		update_post_meta( $id, '_glm_sujet_glm', $item['badge'] ? '1' : '' );
	}
	if ( isset( $item['construction'] ) ) {
		update_post_meta( $id, '_glm_construction', implode( "\n", (array) $item['construction'] ) );
	}
	foreach ( (array) ( isset( $item['meta'] ) ? $item['meta'] : array() ) as $key => $value ) {
		update_post_meta( $id, '_glm_' . sanitize_key( $key ), $value );
	}
	foreach ( (array) ( isset( $item['delete_meta'] ) ? $item['delete_meta'] : array() ) as $key ) {
		delete_post_meta( $id, '_glm_' . sanitize_key( $key ) );
	}
	if ( ! empty( $item['thumb'] ) ) {
		$thumb = $get( $item['thumb'], isset( $item['alt'] ) ? $item['alt'] : $item['title'] );
		if ( $thumb ) {
			set_post_thumbnail( $id, $thumb );
		}
	}
	if ( isset( $item['galerie'] ) ) {
		$galerie = array();
		foreach ( (array) $item['galerie'] as $name ) {
			$photo = $get( $name, $item['title'] );
			if ( $photo ) {
				$galerie[] = $photo;
			}
		}
		update_post_meta( $id, '_glm_galerie', implode( ',', $galerie ) );
	}
	if ( ! empty( $item['thumb_alt'] ) && get_post_thumbnail_id( $id ) ) {
		update_post_meta( get_post_thumbnail_id( $id ), '_wp_attachment_image_alt', $item['thumb_alt'] );
	}
}

function glm_run_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'glm-import/';
	$file    = $dir . 'manifest.json';
	if ( ! file_exists( $file ) ) {
		return;
	}
	$hash = md5_file( $file );
	if ( get_option( 'glm_import_done' ) === $hash ) {
		return;
	}
	$data = json_decode( file_get_contents( $file ), true ); // phpcs:ignore
	if ( ! is_array( $data ) ) {
		return;
	}
	update_option( 'glm_import_done', $hash ); // posé tout de suite : pas de double import si la page est rechargée.

	$ids  = array();
	$alts = isset( $data['alts'] ) ? $data['alts'] : array();
	$get  = function ( $name, $alt ) use ( &$ids, $dir ) {
		if ( ! isset( $ids[ $name ] ) ) {
			$existing     = glm_find_attachment( $name );
			$ids[ $name ] = $existing ? $existing : glm_import_file( $dir . basename( $name ), $alt );
		}
		return $ids[ $name ];
	};

	// Familles du catalogue : création, renommage (ancien slug) et ordre d'affichage.
	foreach ( (array) ( isset( $data['familles'] ) ? $data['familles'] : array() ) as $item ) {
		$term = get_term_by( 'slug', $item['slug'], 'famille' );
		if ( ! $term && ! empty( $item['from'] ) ) {
			$term = get_term_by( 'slug', $item['from'], 'famille' );
		}
		$args = array( 'name' => $item['name'], 'slug' => $item['slug'], 'description' => $item['description'] );
		if ( $term ) {
			$res = wp_update_term( $term->term_id, 'famille', $args );
		} else {
			$res = wp_insert_term( $item['name'], 'famille', $args );
		}
		if ( ! is_wp_error( $res ) ) {
			update_term_meta( (int) $res['term_id'], 'glm_ordre', (int) $item['ordre'] );
		}
	}

	// Thèmes (Noël, Espace…).
	foreach ( (array) ( isset( $data['themes'] ) ? $data['themes'] : array() ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'theme_glm' ) ) {
			wp_insert_term( $name, 'theme_glm', array( 'slug' => $slug ) );
		}
	}

	// Noms des types de réalisation existants.
	foreach ( (array) ( isset( $data['rename_types'] ) ? $data['rename_types'] : array() ) as $slug => $name ) {
		$term = get_term_by( 'slug', $slug, 'type_realisation' );
		if ( $term ) {
			wp_update_term( $term->term_id, 'type_realisation', array( 'name' => $name ) );
		}
	}

	// Modèles existants à mettre à jour.
	foreach ( (array) ( isset( $data['update_modeles'] ) ? $data['update_modeles'] : array() ) as $item ) {
		$id = glm_find_post( 'modele', $item['find'] );
		if ( ! $id ) {
			continue;
		}
		$update = array( 'ID' => $id );
		if ( ! empty( $item['title'] ) ) {
			$update['post_title'] = $item['title'];
			$update['post_name']  = sanitize_title( $item['title'] );
		} else {
			$item['title'] = $item['find'];
		}
		if ( isset( $item['content'] ) ) {
			$update['post_content'] = $item['content'];
		}
		wp_update_post( $update );
		glm_apply_modele( $id, $item, $get );
	}

	// Réalisations existantes à mettre à jour.
	foreach ( (array) ( isset( $data['update_realisations'] ) ? $data['update_realisations'] : array() ) as $item ) {
		$id = glm_find_post( 'realisation', $item['find'] );
		if ( ! $id ) {
			continue;
		}
		if ( ! empty( $item['title'] ) ) {
			wp_update_post( array( 'ID' => $id, 'post_title' => $item['title'], 'post_name' => sanitize_title( $item['title'] ) ) );
		}
		if ( isset( $item['legende'] ) ) {
			update_post_meta( $id, '_glm_legende', $item['legende'] );
		}
		if ( isset( $item['themes'] ) ) {
			glm_set_themes( $id, $item['themes'] );
		}
		if ( ! empty( $item['alt'] ) && get_post_thumbnail_id( $id ) ) {
			update_post_meta( get_post_thumbnail_id( $id ), '_wp_attachment_image_alt', $item['alt'] );
		}
	}

	// Types de réalisation.
	$types = array();
	foreach ( (array) ( isset( $data['types'] ) ? $data['types'] : array() ) as $slug => $name ) {
		$term = term_exists( $slug, 'type_realisation' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'type_realisation', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$types[ $slug ] = (int) $term['term_id'];
		}
	}

	// Nouveau type pour les réalisations existantes.
	foreach ( (array) ( isset( $data['retype'] ) ? $data['retype'] : array() ) as $title => $slug ) {
		$posts = get_posts( array( 'post_type' => 'realisation', 'title' => $title, 'posts_per_page' => 1, 'fields' => 'ids' ) );
		if ( $posts && isset( $types[ $slug ] ) ) {
			wp_set_object_terms( $posts[0], array( $types[ $slug ] ), 'type_realisation' );
		}
	}

	// Réalisations : la première du manifeste apparaît en premier dans la galerie.
	$items = isset( $data['realisations'] ) ? $data['realisations'] : array();
	$now   = time();
	foreach ( $items as $index => $item ) {
		$id = wp_insert_post( array(
			'post_type'   => 'realisation',
			'post_status' => 'publish',
			'post_title'  => $item['title'],
			'post_date'   => wp_date( 'Y-m-d H:i:s', $now - $index * 60 ),
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$photo = $get( $item['file'], $item['alt'] );
		if ( $photo ) {
			set_post_thumbnail( $id, $photo );
		}
		update_post_meta( $id, '_glm_legende', $item['legende'] );
		update_post_meta( $id, '_glm_large', ! empty( $item['large'] ) ? '1' : '' );
		if ( isset( $types[ $item['type'] ] ) ) {
			wp_set_object_terms( $id, array( $types[ $item['type'] ] ), 'type_realisation' );
		}
	}

	// Modèles du catalogue : le premier du manifeste apparaît en premier. Ignoré s'il existe déjà.
	foreach ( (array) ( isset( $data['modeles'] ) ? $data['modeles'] : array() ) as $index => $item ) {
		if ( glm_find_post( 'modele', $item['title'] ) ) {
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'modele',
			'post_status'  => 'publish',
			'post_title'   => $item['title'],
			'post_content' => $item['content'],
			'post_date'    => wp_date( 'Y-m-d H:i:s', $now - $index * 60 ),
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		glm_apply_modele( $id, $item, $get );
	}

	// Photos des sections (Apparence > Personnaliser).
	foreach ( (array) ( isset( $data['theme_mods'] ) ? $data['theme_mods'] : array() ) as $key => $name ) {
		$photo = $get( $name, isset( $alts[ $name ] ) ? $alts[ $name ] : 'Photo GLM Polyester' );
		if ( $photo ) {
			set_theme_mod( 'glm_' . sanitize_key( $key ), $photo );
		}
	}
}
