<?php
/**
 * Formulaires de saisie dans l'administration : fiche modèle et réalisation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Champs texte de la fiche modèle : clé => (libellé, aide). */
function glm_modele_fields() {
	return array(
		'_glm_dimensions' => array( 'Dimensions', 'Exemple : L 1,80 × l 1,15 × H 1,05 m' ),
		'_glm_poids'      => array( 'Poids', 'Exemple : 75 kg' ),
		'_glm_places'     => array( 'Places', 'Exemple : 4 à 6' ),
		'_glm_materiau'   => array( 'Matériau', 'Exemple : Polyester' ),
		'_glm_finition'   => array( 'Finition', 'Exemple : Décor peint, éclairage LED' ),
		'_glm_options'    => array( 'Options', 'Exemple : Couleurs au choix, sonorisation' ),
	);
}

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'glm_modele_specs', 'Caractéristiques du modèle', 'glm_modele_specs_box', 'modele', 'normal', 'high' );
	add_meta_box( 'glm_modele_galerie', 'Photos supplémentaires', 'glm_modele_galerie_box', 'modele', 'normal', 'high' );
	add_meta_box( 'glm_realisation_infos', 'Détails de la réalisation', 'glm_realisation_box', 'realisation', 'normal', 'high' );
} );

function glm_modele_specs_box( $post ) {
	wp_nonce_field( 'glm_save_meta', 'glm_meta_nonce' );
	echo '<p>Les lignes laissées vides ne sont pas affichées sur le site.</p><table class="form-table"><tbody>';
	foreach ( glm_modele_fields() as $key => $field ) {
		printf(
			'<tr><th><label for="%1$s">%2$s</label></th><td><input type="text" class="regular-text" id="%1$s" name="%1$s" value="%3$s" placeholder="%4$s"></td></tr>',
			esc_attr( $key ),
			esc_html( $field[0] ),
			esc_attr( get_post_meta( $post->ID, $key, true ) ),
			esc_attr( $field[1] )
		);
	}
	printf(
		'<tr><th><label for="_glm_construction">Construction</label></th><td><textarea class="large-text" rows="8" id="_glm_construction" name="_glm_construction" placeholder="Une ligne par élément, par exemple : Monocoque polyester indéformable">%s</textarea></td></tr>',
		esc_textarea( get_post_meta( $post->ID, '_glm_construction', true ) )
	);
	printf(
		'<tr><th>Étiquette</th><td><label><input type="checkbox" name="_glm_sujet_glm" value="1" %s> Afficher l\'étiquette « Sujet GLM » (sujet fabriqué par GLM)</label></td></tr>',
		checked( get_post_meta( $post->ID, '_glm_sujet_glm', true ), '1', false )
	);
	printf(
		'<tr><th><label for="_glm_note_interne">Note interne</label></th><td><textarea class="large-text" rows="2" id="_glm_note_interne" name="_glm_note_interne" placeholder="Visible seulement dans l\'administration">%s</textarea></td></tr>',
		esc_textarea( get_post_meta( $post->ID, '_glm_note_interne', true ) )
	);
	echo '</tbody></table>';
}

// Rappel visible en haut de la fiche quand une note interne existe.
add_action( 'edit_form_after_title', function ( $post ) {
	$note = 'modele' === $post->post_type ? get_post_meta( $post->ID, '_glm_note_interne', true ) : '';
	if ( $note ) {
		echo '<div class="notice notice-warning inline" style="margin:12px 0"><p><strong>Note interne :</strong> ' . esc_html( $note ) . '</p></div>';
	}
} );

/** Sélecteur d'images réutilisable (une ou plusieurs). */
function glm_media_picker( $name, $ids, $multiple ) {
	$ids = array_filter( array_map( 'absint', (array) $ids ) );
	echo '<div class="glm-picker" data-multiple="' . ( $multiple ? '1' : '0' ) . '">';
	echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
	echo '<div class="glm-picker__preview" style="display:flex;flex-wrap:wrap;gap:8px;margin:8px 0;">';
	foreach ( $ids as $id ) {
		$src = wp_get_attachment_image_url( $id, 'thumbnail' );
		if ( $src ) {
			echo '<img src="' . esc_url( $src ) . '" data-id="' . esc_attr( $id ) . '" style="width:90px;height:90px;object-fit:cover;" alt="">';
		}
	}
	echo '</div>';
	echo '<button type="button" class="button glm-picker__choose">' . ( $multiple ? 'Choisir les photos' : 'Choisir la photo' ) . '</button> ';
	echo '<button type="button" class="button-link glm-picker__clear">Retirer</button>';
	echo '</div>';
}

function glm_modele_galerie_box( $post ) {
	echo '<p>La photo principale se choisit dans le bloc « Photo principale ». Ajoutez ici les autres vues du modèle (3 conseillées).</p>';
	glm_media_picker( '_glm_galerie', explode( ',', (string) get_post_meta( $post->ID, '_glm_galerie', true ) ), true );
}

function glm_realisation_box( $post ) {
	wp_nonce_field( 'glm_save_meta', 'glm_meta_nonce' );
	?>
	<table class="form-table"><tbody>
		<tr>
			<th><label for="_glm_legende">Légende</label></th>
			<td><input type="text" class="large-text" id="_glm_legende" name="_glm_legende" value="<?php echo esc_attr( get_post_meta( $post->ID, '_glm_legende', true ) ); ?>" placeholder="Exemple : Sujet de manège, décor peint et éclairage LED"></td>
		</tr>
		<tr>
			<th>Photo « avant »</th>
			<td>
				<p>Pour une rénovation : choisissez ici la photo avant travaux. La photo « après » est la photo principale.</p>
				<?php glm_media_picker( '_glm_avant', array( get_post_meta( $post->ID, '_glm_avant', true ) ), false ); ?>
			</td>
		</tr>
		<tr>
			<th><label for="_glm_large">Mise en avant</label></th>
			<td><label><input type="checkbox" id="_glm_large" name="_glm_large" value="1" <?php checked( get_post_meta( $post->ID, '_glm_large', true ), '1' ); ?>> Afficher en grand format dans la galerie</label></td>
		</tr>
	</tbody></table>
	<?php
}

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['glm_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['glm_meta_nonce'] ), 'glm_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type = get_post_type( $post_id );

	if ( 'modele' === $type ) {
		foreach ( array_keys( glm_modele_fields() ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		if ( isset( $_POST['_glm_construction'] ) ) {
			update_post_meta( $post_id, '_glm_construction', sanitize_textarea_field( wp_unslash( $_POST['_glm_construction'] ) ) );
		}
		if ( isset( $_POST['_glm_note_interne'] ) ) {
			update_post_meta( $post_id, '_glm_note_interne', sanitize_textarea_field( wp_unslash( $_POST['_glm_note_interne'] ) ) );
		}
		update_post_meta( $post_id, '_glm_sujet_glm', isset( $_POST['_glm_sujet_glm'] ) ? '1' : '' );
		if ( isset( $_POST['_glm_galerie'] ) ) {
			$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['_glm_galerie'] ) ) ) ) );
			update_post_meta( $post_id, '_glm_galerie', implode( ',', $ids ) );
		}
	}

	if ( 'realisation' === $type ) {
		if ( isset( $_POST['_glm_legende'] ) ) {
			update_post_meta( $post_id, '_glm_legende', sanitize_text_field( wp_unslash( $_POST['_glm_legende'] ) ) );
		}
		if ( isset( $_POST['_glm_avant'] ) ) {
			update_post_meta( $post_id, '_glm_avant', absint( $_POST['_glm_avant'] ) );
		}
		update_post_meta( $post_id, '_glm_large', isset( $_POST['_glm_large'] ) ? '1' : '' );
	}
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'modele', 'realisation' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'glm-admin', get_template_directory_uri() . '/assets/js/admin.js', array( 'jquery' ), GLM_VERSION, true );
} );

// Colonne « Photo » dans la liste des modèles et des réalisations.
foreach ( array( 'modele', 'realisation' ) as $glm_type ) {
	add_filter( "manage_{$glm_type}_posts_columns", function ( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$new['glm_photo'] = 'Photo';
			}
			$new[ $key ] = $label;
		}
		return $new;
	} );
	add_action( "manage_{$glm_type}_posts_custom_column", function ( $column, $post_id ) {
		if ( 'glm_photo' === $column ) {
			echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
		}
	}, 10, 2 );
}
