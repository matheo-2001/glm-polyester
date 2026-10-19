<?php
/**
 * Page Services (page dont l'identifiant est « services »).
 * Textes : Apparence > Personnaliser > GLM Polyester > Page Services.
 */
get_header();

glm_page_hero( get_the_title() ? get_the_title() : 'Services', glm_t( 'services_intro' ) );

$glm_liens  = array(
	'fabrication'     => glm_catalogue_url(),
	'personnalisable' => add_query_arg( 'demande', 'sur-mesure', glm_page_url( 'contact' ) ) . '#formulaire',
	'thermoformage'   => glm_catalogue_url(),
	'reparation'      => add_query_arg( 'demande', 'reparation', glm_page_url( 'contact' ) ) . '#formulaire',
	'renovation'      => glm_realisations_url(),
);
$glm_images = array(
	'fabrication'     => array( 'img_fabrication', 'img/traineau.jpg', 'Sujet de manège terminé dans l\'atelier' ),
	'personnalisable' => array( 'img_sur_mesure', 'img/sujet-pompier.jpg', 'Sujet de manège personnalisé' ),
	'thermoformage'   => array( 'img_thermoformage', '', 'Pièces thermoformées' ),
	'reparation'      => array( 'img_reparation', '', 'Réparation d\'une pièce de manège' ),
	'renovation'      => array( 'img_renovation', '', 'Sujet de manège rénové' ),
);
// Le lien des pièces thermoformées mène à la famille correspondante quand elle existe.
$glm_pieces = get_term_by( 'slug', 'pieces-et-thermoformage', 'famille' );
if ( $glm_pieces ) {
	$glm_liens['thermoformage'] = get_term_link( $glm_pieces );
}
?>

<section class="section">
	<div class="container blocs">
		<?php
		$glm_avec_photo = 0;
		foreach ( glm_services() as $glm_slug => $glm_titre ) :
			$glm_img   = $glm_images[ $glm_slug ];
			$glm_image = glm_image_opt( $glm_img[0], $glm_img[1], $glm_img[2], 'glm-large', array( 'sizes' => '(min-width: 1240px) 640px, (min-width: 1024px) 52vw, 100vw' ) );
			$glm_plus  = glm_t( 'service_' . $glm_slug . '_plus' );
			$glm_class = $glm_image ? ( 1 === $glm_avec_photo++ % 2 ? 'split split--inverse' : 'split' ) : 'split split--sans-photo';
			?>
			<div class="<?php echo esc_attr( $glm_class ); ?>" id="<?php echo esc_attr( $glm_slug ); ?>">
				<?php if ( $glm_image ) : ?>
					<div class="split__media reveal"><?php echo $glm_image; // phpcs:ignore ?></div>
				<?php endif; ?>
				<div class="split__text reveal">
					<h2 class="display display--s famille-titre"><?php echo esc_html( $glm_titre ); ?></h2>
					<p class="muted"><?php echo esc_html( glm_t( 'service_' . $glm_slug . '_texte' ) ); ?></p>
					<?php if ( $glm_plus ) : ?>
						<p class="split__plus"><?php echo esc_html( $glm_plus ); ?></p>
					<?php endif; ?>
					<a class="lien-fleche" href="<?php echo esc_url( $glm_liens[ $glm_slug ] ); ?>"><?php echo esc_html( glm_t( 'service_' . $glm_slug . '_lien' ) ); ?></a>
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
	</div>
</section>

<?php
glm_cta();
get_footer();
