<?php
/**
 * Fiche d'un modèle du catalogue.
 */
get_header();

while ( have_posts() ) :
	the_post();
	$glm_id      = get_the_ID();
	$glm_terms   = get_the_terms( $glm_id, 'famille' );
	$glm_famille = ( $glm_terms && ! is_wp_error( $glm_terms ) ) ? $glm_terms[0] : null;

	// Photos : la principale, puis la galerie.
	$glm_photos = array();
	if ( has_post_thumbnail() ) {
		$glm_photos[] = get_post_thumbnail_id();
	}
	foreach ( array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $glm_id, '_glm_galerie', true ) ) ) ) as $glm_photo ) {
		if ( ! in_array( $glm_photo, $glm_photos, true ) ) {
			$glm_photos[] = $glm_photo;
		}
	}

	$glm_specs = array();
	foreach ( glm_modele_fields() as $glm_key => $glm_field ) {
		$glm_value = get_post_meta( $glm_id, $glm_key, true );
		if ( $glm_value ) {
			$glm_specs[ $glm_field[0] ] = $glm_value;
		}
	}

	$glm_construction = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $glm_id, '_glm_construction', true ) ) ) );
	$glm_badge        = '1' === get_post_meta( $glm_id, '_glm_sujet_glm', true );
	$glm_encart       = glm_modele_encart( $glm_id, $glm_famille );

	$glm_devis_url = add_query_arg(
		array(
			'ref'     => rawurlencode( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) ),
			'demande' => 'catalogue',
		),
		glm_page_url( 'contact' )
	) . '#formulaire';
	?>

	<section class="fiche">
		<div class="container">
			<nav class="fil" aria-label="Fil d'Ariane">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
				/ <a href="<?php echo esc_url( glm_catalogue_url() ); ?>">Catalogue</a>
				<?php if ( $glm_famille ) : ?>
					/ <a href="<?php echo esc_url( get_term_link( $glm_famille ) ); ?>"><?php echo esc_html( $glm_famille->name ); ?></a>
				<?php endif; ?>
				/ <?php the_title(); ?>
			</nav>

			<div class="fiche__grid">
				<div class="fiche__galerie">
					<div class="fiche__photo" id="fiche-photo">
						<?php
						if ( $glm_photos ) {
							echo wp_get_attachment_image( $glm_photos[0], 'glm-large', false, array( 'id' => 'fiche-photo-img' ) );
						} else {
							echo glm_placeholder(); // phpcs:ignore
						}
						?>
					</div>
					<?php if ( count( $glm_photos ) > 1 ) : ?>
						<div class="fiche__vignettes">
							<?php foreach ( $glm_photos as $glm_index => $glm_photo ) : ?>
								<?php $glm_full = wp_get_attachment_image_src( $glm_photo, 'glm-large' ); ?>
								<button type="button" aria-pressed="<?php echo 0 === $glm_index ? 'true' : 'false'; ?>" aria-label="Afficher la photo <?php echo (int) $glm_index + 1; ?>" data-src="<?php echo esc_url( $glm_full ? $glm_full[0] : '' ); ?>" data-alt="<?php echo esc_attr( get_post_meta( $glm_photo, '_wp_attachment_image_alt', true ) ); ?>">
									<?php echo wp_get_attachment_image( $glm_photo, 'glm-carte', false, array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="fiche__infos">
					<div class="fiche__tete">
						<?php if ( $glm_famille ) : ?>
							<p class="eyebrow"><?php echo esc_html( $glm_famille->name ); ?></p>
						<?php endif; ?>
						<?php if ( $glm_badge ) : ?>
							<span class="badge-glm badge-glm--statique">Sujet GLM</span>
						<?php endif; ?>
					</div>
					<h1 class="display display--m"><?php the_title(); ?></h1>
					<?php if ( get_the_content() ) : ?>
						<div class="fiche__desc"><?php the_content(); ?></div>
					<?php endif; ?>
					<?php if ( $glm_specs ) : ?>
						<table class="specs">
							<tbody>
								<?php foreach ( $glm_specs as $glm_label => $glm_value ) : ?>
									<tr><th scope="row"><?php echo esc_html( $glm_label ); ?></th><td><?php echo esc_html( $glm_value ); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
					<?php if ( $glm_construction ) : ?>
						<div class="construction">
							<h2>Construction</h2>
							<ul>
								<?php foreach ( $glm_construction as $glm_ligne ) : ?>
									<li><?php echo esc_html( $glm_ligne ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $glm_encart ) : ?>
						<div class="encart"><?php echo wp_kses( $glm_encart, array( 'strong' => array() ) ); ?></div>
					<?php endif; ?>
					<a class="btn btn--rouge" href="<?php echo esc_url( $glm_devis_url ); ?>">Demander un devis pour ce modèle</a>
				</div>
			</div>
		</div>
	</section>

	<?php
	if ( $glm_famille ) :
		$glm_autres = get_posts( array(
			'post_type'      => 'modele',
			'posts_per_page' => 3,
			'post__not_in'   => array( $glm_id ),
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'famille', 'terms' => $glm_famille->term_id ) ),
		) );
		if ( $glm_autres ) :
			?>
			<section class="section section--fond">
				<div class="container">
					<div class="section-head reveal">
						<h2 class="display display--s">Dans la même famille</h2>
						<a class="lien-fleche" href="<?php echo esc_url( get_term_link( $glm_famille ) ); ?>">Voir toute la famille</a>
					</div>
					<div class="cards">
						<?php
						foreach ( $glm_autres as $glm_autre ) {
							glm_modele_card( $glm_autre );
						}
						?>
					</div>
				</div>
			</section>
			<?php
		endif;
	endif;
endwhile;

glm_cta();
get_footer();
