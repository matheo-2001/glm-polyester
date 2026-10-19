<?php
/**
 * Une vignette de la galerie de réalisations, cliquable pour l'agrandir.
 * Arguments : post_id, large (booléen ou null pour suivre le réglage de la réalisation).
 */
$glm_id      = (int) $args['post_id'];
$glm_avant   = absint( get_post_meta( $glm_id, '_glm_avant', true ) );
$glm_legende = get_post_meta( $glm_id, '_glm_legende', true );
$glm_large   = isset( $args['large'] ) && null !== $args['large'] ? (bool) $args['large'] : (bool) get_post_meta( $glm_id, '_glm_large', true );
$glm_terms   = get_the_terms( $glm_id, 'type_realisation' );
$glm_slugs   = ( $glm_terms && ! is_wp_error( $glm_terms ) ) ? wp_list_pluck( $glm_terms, 'slug' ) : array();
$glm_th      = get_the_terms( $glm_id, 'theme_glm' );
if ( $glm_th && ! is_wp_error( $glm_th ) ) {
	foreach ( $glm_th as $glm_t ) {
		$glm_slugs[] = 'theme-' . $glm_t->slug;
	}
}
$glm_thumb   = get_post_thumbnail_id( $glm_id );
$glm_eager   = ! empty( $args['eager'] );
$glm_title   = get_the_title( $glm_id );
if ( $glm_avant ) {
	$glm_large = true;
}

// Photos montrées dans la visionneuse : avant puis après, ou la photo seule.
$glm_slides = array();
foreach ( array_filter( array( $glm_avant, $glm_thumb ) ) as $glm_index => $glm_photo ) {
	$glm_src = wp_get_attachment_image_url( $glm_photo, 'full' );
	if ( $glm_src ) {
		$glm_label = '';
		if ( $glm_avant ) {
			$glm_label = $glm_photo === $glm_avant ? 'Avant' : 'Après';
		}
		$glm_slides[] = array(
			'src'   => $glm_src,
			'alt'   => (string) get_post_meta( $glm_photo, '_wp_attachment_image_alt', true ),
			'label' => $glm_label,
		);
	}
}
$glm_caption = $glm_title . ( $glm_legende ? ' — ' . $glm_legende : '' );
$glm_sizes   = $glm_large ? '(min-width: 1240px) 600px, (min-width: 640px) 50vw, 100vw' : '(min-width: 1240px) 290px, (min-width: 1024px) 23vw, (min-width: 640px) 50vw, 100vw';
$glm_demi    = $glm_large ? '(min-width: 1240px) 300px, (min-width: 640px) 25vw, 50vw' : '(min-width: 1240px) 145px, (min-width: 640px) 25vw, 50vw';
$glm_attrs   = array( 'loading' => $glm_eager ? 'eager' : 'lazy', 'sizes' => $glm_sizes );
if ( $glm_eager ) {
	$glm_attrs['fetchpriority'] = 'high';
}
$glm_overlay = '<span class="galerie__overlay" aria-hidden="true"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"></path></svg><span>Agrandir l\'image</span></span>';
?>
<figure class="galerie__item reveal<?php echo $glm_large ? ' galerie__item--large' : ''; ?>" data-types="<?php echo esc_attr( implode( ' ', $glm_slugs ) ); ?>" data-slides="<?php echo esc_attr( wp_json_encode( $glm_slides ) ); ?>" data-caption="<?php echo esc_attr( $glm_caption ); ?>">
	<?php if ( $glm_slides ) : ?>
		<button type="button" class="galerie__media galerie__zoom<?php echo $glm_avant ? ' galerie__media--avant-apres' : ''; ?>" aria-label="<?php echo esc_attr( 'Agrandir l\'image : ' . $glm_title ); ?>" data-glm-zoom>
			<?php if ( $glm_avant ) : ?>
				<span class="galerie__part">
					<?php echo wp_get_attachment_image( $glm_avant, 'glm-carte', false, array_merge( $glm_attrs, array( 'sizes' => $glm_demi ) ) ); ?>
					<span class="galerie__etiquette">Avant</span>
				</span>
				<span class="galerie__part">
					<?php echo $glm_thumb ? wp_get_attachment_image( $glm_thumb, 'glm-carte', false, array_merge( $glm_attrs, array( 'sizes' => $glm_demi ) ) ) : ''; // phpcs:ignore ?>
					<span class="galerie__etiquette">Après</span>
				</span>
			<?php else : ?>
				<?php echo wp_get_attachment_image( $glm_thumb, $glm_large ? 'glm-large' : 'glm-carte', false, $glm_attrs ); ?>
			<?php endif; ?>
			<?php echo $glm_overlay; // phpcs:ignore ?>
		</button>
	<?php else : ?>
		<div class="galerie__media"><span class="card2__vide" aria-hidden="true"></span></div>
	<?php endif; ?>
	<figcaption>
		<strong><?php echo esc_html( $glm_title ); ?>.</strong>
		<?php if ( $glm_legende ) : ?>
			<span><?php echo esc_html( $glm_legende ); ?></span>
		<?php endif; ?>
	</figcaption>
</figure>
