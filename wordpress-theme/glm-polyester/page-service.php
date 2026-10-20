<?php
/**
 * Page détaillée d'un service : page enfant de « Services » (fabrication, personnalisable,
 * thermoformage, reparation, renovation). Le texte principal se modifie dans l'éditeur de la page ;
 * la liste « pour un devis » et les questions fréquentes dans Apparence > Personnaliser > Page Services.
 * Les photos d'exemple viennent automatiquement des réalisations du même type.
 */
get_header();

$glm_slug  = glm_service_courant();
$glm_infos = glm_service_infos( $glm_slug );
$glm_devis = glm_t_lignes( 'service_' . $glm_slug . '_devis' );
$glm_faq   = glm_service_faq( $glm_slug );
$glm_ex    = $glm_infos['types'] ? glm_realisations_choisies( 7, $glm_infos['types'], true ) : array();
// La photo principale n'est pas répétée dans les exemples.
$glm_cle   = glm_service_photo_cle( $glm_slug );
$glm_main  = absint( get_theme_mod( 'glm_' . $glm_cle[0] ) );
if ( ! glm_service_photo_choisie( $glm_slug ) && $glm_ex ) {
	$glm_main = (int) get_post_thumbnail_id( $glm_ex[0] );
}
$glm_ex    = array_slice( array_values( array_filter( $glm_ex, function ( $id ) use ( $glm_main ) {
	return ! $glm_main || (int) get_post_thumbnail_id( $id ) !== $glm_main;
} ) ), 0, 6 );
$glm_url   = glm_service_devis_url( $glm_slug );

while ( have_posts() ) :
	the_post();
	glm_page_hero( get_the_title(), glm_t( 'service_' . $glm_slug . '_texte' ), array( 'Services' => glm_page_url( 'services' ) ) );
	$glm_photo = glm_service_image( $glm_slug, 'glm-large', array( 'sizes' => '(min-width: 1240px) 560px, (min-width: 1024px) 45vw, 100vw', 'loading' => 'eager' ) );
	?>

	<section class="section">
		<div class="container service">
			<div class="service__texte">
				<?php if ( $glm_photo ) : ?>
					<div class="service__photo"><?php echo $glm_photo; // phpcs:ignore ?></div>
				<?php endif; ?>
				<div class="contenu"><?php the_content(); ?></div>
			</div>
			<aside class="service__devis" aria-labelledby="service-devis-titre">
				<?php if ( $glm_devis ) : ?>
					<h2 class="titre-carte" id="service-devis-titre"><?php echo esc_html( glm_t( 'services_devis_titre' ) ); ?></h2>
					<ul class="coche">
						<?php foreach ( $glm_devis as $glm_ligne ) : ?>
							<li><?php echo esc_html( $glm_ligne ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<h2 class="titre-carte" id="service-devis-titre"><?php echo esc_html( glm_t( 'cta_titre' ) ); ?></h2>
				<?php endif; ?>
				<a class="btn btn--rouge" href="<?php echo esc_url( $glm_url ); ?>">Demander un devis</a>
				<?php if ( glm_opt( 'telephone' ) ) : ?>
					<a class="btn btn--contour" href="<?php echo esc_attr( glm_tel_href( glm_opt( 'telephone' ) ) ); ?>">Appeler le <?php echo esc_html( glm_opt( 'telephone' ) ); ?></a>
				<?php endif; ?>
				<?php
				$glm_liees = array_filter( array_map( function ( $slug ) {
					$term = get_term_by( 'slug', $slug, 'famille' );
					return ( $term && $term->count ) ? $term : null;
				}, $glm_infos['familles'] ) );
				?>
				<?php if ( $glm_liees ) : ?>
					<div class="service__liees">
						<h2 class="service__sous-titre">Dans le catalogue</h2>
						<ul>
							<?php foreach ( $glm_liees as $glm_term ) : ?>
								<li><a class="lien-fleche" href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>"><?php echo esc_html( $glm_term->name ); ?> <span>(<?php echo esc_html( glm_nb_modeles( $glm_term->count ) ); ?>)</span></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</aside>
		</div>
	</section>

	<?php if ( $glm_ex ) : ?>
		<section class="section section--fond">
			<div class="container">
				<div class="section-head reveal">
					<h2 class="display display--m"><?php echo esc_html( glm_t( 'service_photos_titre' ) ); ?></h2>
					<a class="lien-fleche" href="<?php echo esc_url( glm_realisations_url() . ( 'personnalisable' === $glm_slug && get_term_by( 'slug', 'personnalisation', 'theme_glm' ) ? '#theme-personnalisation' : '#type-' . $glm_infos['types'][0] ) ); ?>">Toutes les réalisations</a>
				</div>
				<div class="galerie galerie--mosaique" data-glm-groupe>
					<?php
					foreach ( $glm_ex as $glm_id ) {
						get_template_part( 'template-parts/realisation', null, array(
							'post_id'  => $glm_id,
							'large'    => false,
							'mosaique' => true,
						) );
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $glm_faq ) : ?>
		<section class="section">
			<div class="container">
				<h2 class="display display--m"><?php echo esc_html( glm_t( 'service_faq_titre' ) ); ?></h2>
				<div class="faq">
					<?php foreach ( $glm_faq as $glm_qr ) : ?>
						<details class="faq__item">
							<summary><?php echo esc_html( $glm_qr[0] ); ?></summary>
							<p><?php echo esc_html( $glm_qr[1] ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="section<?php echo $glm_faq ? ' section--fond' : ''; ?>">
		<div class="container">
			<?php if ( glm_t( 'service_atelier_texte' ) ) : ?>
				<div class="service__atelier">
					<h2 class="display display--s"><?php echo esc_html( glm_t( 'service_atelier_titre' ) ); ?></h2>
					<p><?php echo esc_html( glm_t( 'service_atelier_texte' ) ); ?> <a href="<?php echo esc_url( glm_page_url( 'atelier' ) ); ?>">Découvrir l'atelier</a></p>
				</div>
			<?php endif; ?>
			<h2 class="display display--s"><?php echo esc_html( glm_t( 'service_autres_titre' ) ); ?></h2>
			<?php glm_liste_services( $glm_slug ); ?>
		</div>
	</section>

	<?php
endwhile;

glm_cta();
get_footer();
