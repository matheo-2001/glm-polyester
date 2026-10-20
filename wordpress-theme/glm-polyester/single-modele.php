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
	$glm_tel     = glm_opt( 'telephone' );

	// Photos : la principale, puis la galerie.
	$glm_photos = array();
	if ( has_post_thumbnail() ) {
		$glm_photos[] = (int) get_post_thumbnail_id();
	}
	foreach ( array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $glm_id, '_glm_galerie', true ) ) ) ) as $glm_photo ) {
		if ( ! in_array( $glm_photo, $glm_photos, true ) && wp_attachment_is_image( $glm_photo ) ) {
			$glm_photos[] = $glm_photo;
		}
	}

	// Photos pour la visionneuse (grand format).
	$glm_slides = array();
	foreach ( $glm_photos as $glm_photo ) {
		$glm_src = wp_get_attachment_image_url( $glm_photo, 'full' );
		if ( $glm_src ) {
			$glm_slides[] = array(
				'src'   => $glm_src,
				'alt'   => (string) get_post_meta( $glm_photo, '_wp_attachment_image_alt', true ),
				'label' => '',
			);
		}
	}

	$glm_specs = array();
	foreach ( glm_modele_fields() as $glm_key => $glm_field ) {
		$glm_value = get_post_meta( $glm_id, $glm_key, true );
		if ( $glm_value ) {
			$glm_specs[ $glm_key ] = array( $glm_field[0], $glm_value );
		}
	}

	$glm_construction = array_filter( array_map( 'trim', explode( "\n", (string) get_post_meta( $glm_id, '_glm_construction', true ) ) ) );
	$glm_perso        = glm_modele_perso( $glm_id, $glm_famille );

	$glm_devis_url = add_query_arg(
		array(
			'ref'     => rawurlencode( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) ),
			'demande' => 'catalogue',
		),
		glm_page_url( 'contact' )
	) . '#formulaire';
	?>

	<section class="fiche2">
		<div class="container">
			<?php
			$glm_fil = array( 'Catalogue' => glm_catalogue_url() );
			if ( $glm_famille ) {
				$glm_fil[ $glm_famille->name ] = get_term_link( $glm_famille );
			}
			glm_fil( $glm_fil, get_the_title() );
			?>

			<div class="fiche2__grid">
				<div class="fiche2__galerie" data-glm-groupe>
					<div class="fiche2__photo" data-slides="<?php echo esc_attr( wp_json_encode( $glm_slides ) ); ?>" data-caption="<?php echo esc_attr( get_the_title() ); ?>">
						<?php if ( $glm_photos ) : ?>
							<?php
							echo wp_get_attachment_image( $glm_photos[0], 'glm-large', false, array( // phpcs:ignore
								'id'            => 'fiche-photo-img',
								'loading'       => 'eager',
								'fetchpriority' => 'high',
								'sizes'         => '(min-width: 1240px) 700px, (min-width: 1024px) 56vw, 100vw',
							) );
							?>
							<button type="button" class="fiche2__zoom" data-glm-zoom data-index="0">
								<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"></path></svg>
								Agrandir la photo
							</button>
						<?php else : ?>
							<span class="card2__vide" aria-hidden="true"></span>
						<?php endif; ?>
					</div>
					<?php if ( count( $glm_photos ) > 1 ) : ?>
						<div class="fiche__vignettes">
							<?php foreach ( $glm_photos as $glm_index => $glm_photo ) : ?>
								<?php $glm_full = wp_get_attachment_image_src( $glm_photo, 'glm-large' ); ?>
								<button type="button" aria-pressed="<?php echo 0 === $glm_index ? 'true' : 'false'; ?>" aria-label="Afficher la photo <?php echo (int) $glm_index + 1; ?>" data-index="<?php echo (int) $glm_index; ?>" data-src="<?php echo esc_url( $glm_full ? $glm_full[0] : '' ); ?>" data-alt="<?php echo esc_attr( get_post_meta( $glm_photo, '_wp_attachment_image_alt', true ) ); ?>">
									<?php echo wp_get_attachment_image( $glm_photo, 'glm-carte-s', false, array( 'loading' => 'lazy', 'alt' => '', 'sizes' => '160px' ) ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="fiche2__infos">
					<div class="fiche2__tete">
						<?php if ( $glm_famille ) : ?>
							<a class="fiche2__famille" href="<?php echo esc_url( get_term_link( $glm_famille ) ); ?>"><?php echo esc_html( $glm_famille->name ); ?></a>
						<?php endif; ?>
					</div>
					<h1 class="display display--l"><?php the_title(); ?></h1>
					<?php if ( get_the_content() ) : ?>
						<div class="fiche2__desc"><?php the_content(); ?></div>
					<?php endif; ?>
					<?php if ( $glm_perso ) : ?>
						<p class="fiche2__perso"><strong><?php echo esc_html( $glm_perso['titre'] ); ?> :</strong> <?php echo esc_html( trim( $glm_perso['texte'] . ' ' . $glm_perso['note'] ) ); ?></p>
					<?php endif; ?>
					<div class="fiche2__actions">
						<a class="btn btn--rouge" href="<?php echo esc_url( $glm_devis_url ); ?>">Demander un devis pour ce modèle</a>
						<?php if ( $glm_tel ) : ?>
							<a class="btn btn--contour" href="<?php echo esc_attr( glm_tel_href( $glm_tel ) ); ?>">Appeler le <?php echo esc_html( $glm_tel ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $glm_specs || $glm_construction ) : ?>
		<section class="section section--fond fiche2__details">
			<div class="container plaque-zone<?php echo ( $glm_specs && $glm_construction ) ? '' : ' plaque-zone--seule'; ?>">
				<?php if ( $glm_specs ) : ?>
					<div class="plaque reveal">
						<h2 class="plaque__marque">Caractéristiques</h2>
						<dl>
							<div><dt>Modèle</dt><dd><?php the_title(); ?></dd></div>
							<?php if ( $glm_famille ) : ?>
								<div><dt>Famille</dt><dd><?php echo esc_html( $glm_famille->name ); ?></dd></div>
							<?php endif; ?>
							<?php foreach ( $glm_specs as $glm_key => $glm_spec ) : ?>
								<div><dt><?php echo esc_html( $glm_spec[0] ); ?></dt><dd><?php echo esc_html( $glm_spec[1] ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					</div>
				<?php endif; ?>
				<?php if ( $glm_construction ) : ?>
					<div class="construction2 reveal">
						<h2>Construction</h2>
						<ul>
							<?php foreach ( $glm_construction as $glm_ligne ) : ?>
								<li><?php echo esc_html( $glm_ligne ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php
	if ( $glm_famille ) :
		$glm_autres = get_posts( array(
			'post_type'      => 'modele',
			'posts_per_page' => 4,
			'post__not_in'   => array( $glm_id ),
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'famille', 'terms' => $glm_famille->term_id ) ),
		) );
		if ( $glm_autres ) :
			?>
			<section class="section">
				<div class="container">
					<div class="section-head reveal">
						<h2 class="display display--s"><?php echo esc_html( glm_t( 'fiche_meme_famille' ) ); ?></h2>
						<a class="lien-fleche" href="<?php echo esc_url( get_term_link( $glm_famille ) ); ?>"><?php echo esc_html( $glm_famille->count > 1 ? sprintf( 'Voir les %d modèles : %s', $glm_famille->count, $glm_famille->name ) : 'Voir la famille' ); ?></a>
					</div>
					<div class="cards2">
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

	if ( glm_est_noel( $glm_id ) ) {
		glm_cta( glm_t( 'cta_noel_titre' ), glm_t( 'cta_noel_texte' ) );
	} else {
		glm_cta();
	}
	?>

	<div class="barre-devis" id="barre-devis">
		<a class="btn btn--rouge" href="<?php echo esc_url( $glm_devis_url ); ?>">Demander un devis</a>
		<?php if ( $glm_tel ) : ?>
			<a class="btn btn--contour" href="<?php echo esc_attr( glm_tel_href( $glm_tel ) ); ?>">Appeler</a>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
