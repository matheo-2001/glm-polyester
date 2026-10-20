<?php
/**
 * Page Services (page dont l'identifiant est « services »).
 * Textes : Apparence > Personnaliser > GLM Polyester > Page Services.
 */
get_header();

glm_page_hero( get_the_title() ? get_the_title() : 'Services', glm_t( 'services_intro' ) );

$glm_liens = array(
	'fabrication'     => glm_catalogue_url(),
	'personnalisable' => glm_service_devis_url( 'personnalisable' ),
	'thermoformage'   => glm_catalogue_url(),
	'reparation'      => glm_service_devis_url( 'reparation' ),
	'renovation'      => glm_realisations_url(),
);
// Le lien des pièces thermoformées mène à la famille correspondante quand elle existe.
$glm_pieces = get_term_by( 'slug', 'pieces-et-thermoformage', 'famille' );
if ( $glm_pieces ) {
	$glm_liens['thermoformage'] = get_term_link( $glm_pieces );
}
$glm_devis = glm_t_lignes( 'services_devis' );
?>

<section class="section">
	<div class="container blocs blocs--services">
		<?php
		$glm_avec_photo = 0;
		foreach ( glm_services() as $glm_slug => $glm_titre ) :
			$glm_image = glm_service_image( $glm_slug, 'glm-large', array( 'sizes' => '(min-width: 1240px) 640px, (min-width: 1024px) 52vw, 100vw' ) );
			$glm_page  = glm_service_page( $glm_slug );
			$glm_plus  = glm_t( 'service_' . $glm_slug . '_plus' );
			$glm_class = $glm_image ? ( 1 === $glm_avec_photo++ % 2 ? 'split split--inverse' : 'split' ) : 'split split--sans-photo';
			?>
			<div class="<?php echo esc_attr( $glm_class ); ?>" id="<?php echo esc_attr( $glm_slug ); ?>">
				<?php if ( $glm_image ) : ?>
					<div class="split__media reveal"><?php echo $glm_image; // phpcs:ignore ?></div>
				<?php endif; ?>
				<div class="split__text reveal">
					<h2 class="display display--s famille-titre"><?php if ( $glm_page ) : ?><a href="<?php echo esc_url( get_permalink( $glm_page ) ); ?>"><?php echo esc_html( $glm_titre ); ?></a><?php else : ?><?php echo esc_html( $glm_titre ); ?><?php endif; ?></h2>
					<p class="muted"><?php echo esc_html( glm_t( 'service_' . $glm_slug . '_texte' ) ); ?></p>
					<?php if ( $glm_plus ) : ?>
						<p class="split__plus"><?php echo esc_html( $glm_plus ); ?></p>
					<?php endif; ?>
					<div class="split__liens">
						<?php if ( $glm_page ) : ?>
							<a class="btn btn--contour btn--petit" href="<?php echo esc_url( get_permalink( $glm_page ) ); ?>"><?php echo esc_html( glm_t( 'service_plus_lien' ) ); ?><span class="screen-reader-text"> : <?php echo esc_html( $glm_titre ); ?></span></a>
						<?php endif; ?>
						<a class="lien-fleche" href="<?php echo esc_url( $glm_liens[ $glm_slug ] ); ?>"><?php echo esc_html( glm_t( 'service_' . $glm_slug . '_lien' ) ); ?></a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="section section--fond">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'process_titre' ) ); ?></h2>
		</div>
		<ol class="process">
			<?php for ( $glm_i = 1; $glm_i <= 4; $glm_i++ ) : ?>
				<li class="reveal"><span class="process__num"><?php echo esc_html( sprintf( '%02d', $glm_i ) ); ?></span><strong><?php echo esc_html( glm_t( 'process_' . $glm_i . '_titre' ) ); ?></strong><span><?php echo esc_html( glm_t( 'process_' . $glm_i . '_texte' ) ); ?></span></li>
			<?php endfor; ?>
		</ol>
		<?php if ( $glm_devis ) : ?>
			<div class="devis-liste reveal">
				<h3 class="titre-carte"><?php echo esc_html( glm_t( 'services_devis_titre' ) ); ?></h3>
				<ul class="coche">
					<?php foreach ( $glm_devis as $glm_ligne ) : ?>
						<li><?php echo esc_html( $glm_ligne ); ?></li>
					<?php endforeach; ?>
				</ul>
				<a class="btn btn--rouge" href="<?php echo esc_url( glm_page_url( 'contact' ) . '#formulaire' ); ?>">Demander un devis</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
glm_cta();
get_footer();
