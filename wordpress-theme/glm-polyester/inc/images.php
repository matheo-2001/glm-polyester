<?php
/**
 * Images plus légères : tailles intermédiaires créées en WebP, outil de conversion des photos existantes.
 * Les fichiers d'origine (JPEG, PNG) ne sont jamais modifiés ni supprimés.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Le serveur sait-il écrire du WebP ? */
function glm_webp_possible() {
	static $ok = null;
	if ( null === $ok ) {
		$ok = function_exists( 'wp_image_editor_supports' ) && wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
	}
	return $ok;
}

// Les tailles créées par WordPress (cartes, grandes vues…) sont écrites en WebP.
add_filter( 'image_editor_output_format', function ( $formats ) {
	if ( glm_webp_possible() ) {
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
	}
	return $formats;
} );

// Qualité WebP : bon compromis pour des photos (80 par défaut chez WordPress).
add_filter( 'wp_editor_set_quality', function ( $quality, $mime ) {
	return 'image/webp' === $mime ? 78 : $quality;
}, 10, 2 );

/** Photos de la médiathèque dont les tailles ne sont pas encore en WebP (ou dont une taille du thème manque). */
function glm_images_a_convertir() {
	$ids    = get_posts( array(
		'post_type'      => 'attachment',
		'post_mime_type' => array( 'image/jpeg', 'image/png' ),
		'post_status'    => 'inherit',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	$tailles = array( 'glm-carte', 'glm-carte-s', 'glm-large' );
	return array_values( array_filter( $ids, function ( $id ) use ( $tailles ) {
		$meta = wp_get_attachment_metadata( $id );
		if ( empty( $meta['sizes'] ) ) {
			return ! empty( $meta['width'] );
		}
		foreach ( $meta['sizes'] as $size ) {
			if ( isset( $size['mime-type'] ) && 'image/webp' !== $size['mime-type'] ) {
				return true;
			}
		}
		foreach ( $tailles as $taille ) {
			// Une taille du thème absente alors que la photo est assez grande pour la produire.
			$dims = wp_get_registered_image_subsizes()[ $taille ] ?? null;
			if ( $dims && ! isset( $meta['sizes'][ $taille ] ) && $meta['width'] >= $dims['width'] && $meta['height'] >= $dims['height'] ) {
				return true;
			}
		}
		return false;
	} ) );
}

/* ---------- Outil : Outils > Images WebP ---------- */
add_action( 'admin_menu', function () {
	add_management_page( 'Images WebP', 'Images WebP', 'manage_options', 'glm-images', 'glm_images_page' );
} );

function glm_images_page() {
	$reste = glm_images_a_convertir();
	$fait  = isset( $_GET['fait'] ) ? absint( $_GET['fait'] ) : 0; // phpcs:ignore
	?>
	<div class="wrap">
		<h1>Images WebP</h1>
		<p>Les photos envoyées dans la médiathèque sont désormais déclinées en WebP, un format plus léger. Cet outil fait la même chose pour les photos déjà présentes. Les fichiers d'origine ne sont ni modifiés ni supprimés.</p>
		<?php if ( ! glm_webp_possible() ) : ?>
			<div class="notice notice-error inline"><p>Le serveur ne sait pas créer d'images WebP : l'outil est désactivé.</p></div>
		<?php elseif ( $fait ) : ?>
			<div class="notice notice-success inline"><p><?php echo esc_html( sprintf( '%d photo(s) converties.', $fait ) ); ?></p></div>
		<?php endif; ?>
		<p><strong><?php echo esc_html( sprintf( '%d photo(s) à convertir.', count( $reste ) ) ); ?></strong></p>
		<?php if ( $reste && glm_webp_possible() ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="glm_images">
				<?php wp_nonce_field( 'glm_images', 'glm_images_nonce' ); ?>
				<input type="hidden" name="fait" value="0">
				<?php submit_button( 'Convertir les photos', 'primary', 'submit', false ); ?>
				<p class="description">La conversion se fait par lots de 6 photos ; la page se recharge jusqu'à la fin.</p>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

add_action( 'admin_post_glm_images', function () {
	$nonce = isset( $_REQUEST['glm_images_nonce'] ) ? sanitize_key( $_REQUEST['glm_images_nonce'] ) : '';
	if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $nonce, 'glm_images' ) || ! glm_webp_possible() ) {
		wp_die( 'Action non autorisée.' );
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 120 );
	}
	$fait  = isset( $_REQUEST['fait'] ) ? absint( $_REQUEST['fait'] ) : 0;
	$apres = isset( $_REQUEST['apres'] ) ? absint( $_REQUEST['apres'] ) : 0;
	// Toujours vers l'avant (identifiants croissants) : une photo impossible à convertir n'arrête pas l'outil.
	$suite = array_values( array_filter( glm_images_a_convertir(), function ( $id ) use ( $apres ) {
		return $id > $apres;
	} ) );
	sort( $suite );
	$lot = array_slice( $suite, 0, 6 );
	foreach ( $lot as $id ) {
		$file = get_attached_file( $id );
		if ( $file && file_exists( $file ) ) {
			$meta = wp_generate_attachment_metadata( $id, $file );
			if ( $meta && ! is_wp_error( $meta ) ) {
				wp_update_attachment_metadata( $id, $meta );
				$fait++;
			}
		}
		$apres = $id;
	}
	if ( count( $suite ) > count( $lot ) ) {
		// Lot suivant.
		wp_safe_redirect( add_query_arg( array(
			'action'           => 'glm_images',
			'glm_images_nonce' => wp_create_nonce( 'glm_images' ),
			'fait'             => $fait,
			'apres'            => $apres,
		), admin_url( 'admin-post.php' ) ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'glm-images', 'fait' => $fait ), admin_url( 'tools.php' ) ) );
	exit;
} );
