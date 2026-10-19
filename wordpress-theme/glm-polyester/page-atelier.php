<?php
/**
 * Page L'atelier (page dont l'identifiant est « atelier »).
 * Le texte de présentation se modifie dans l'éditeur de la page ;
 * les autres textes dans Apparence > Personnaliser > GLM Polyester > Page L'atelier et étapes.
 */
get_header();

glm_page_hero( get_the_title() ? get_the_title() : 'L\'atelier', glm_t( 'atelier_intro' ) );

$glm_experience = glm_opt( 'experience' );
$glm_etapes     = array();
foreach ( array( 1 => 'Moule d\'une pièce de manège', 2 => 'Pièce thermoformée', 3 => 'Détail d\'un traîneau peint et éclairé' ) as $glm_i => $glm_alt ) {
	$glm_etapes[ $glm_i ] = array(
		glm_t( 'etape_' . $glm_i . '_titre' ),
		glm_t( 'etape_' . $glm_i . '_texte' ),
		glm_image_opt( 'img_etape_' . $glm_i, 3 === $glm_i ? 'img/traineau-serre.jpg' : '', $glm_alt, 'glm-carte', array( 'sizes' => '(min-width: 1240px) 390px, (min-width: 640px) 33vw, 100vw' ) ),
	);
}
?>

<section class="section">
	<div class="container split">
		<div class="split__media slant slant--large reveal">
			<?php echo glm_image_opt( 'img_atelier', 'img/traineau.jpg', 'Vue de l\'atelier : établis, outillage et un traîneau de manège terminé', 'glm-large', array( 'sizes' => '(min-width: 1240px) 640px, (min-width: 1024px) 52vw, 100vw' ) ); // phpcs:ignore ?>
		</div>
		<div class="split__text reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'atelier_titre' ) ); ?></h2>
			<div class="contenu">
				<?php
				while ( have_posts() ) {
					the_post();
					the_content();
				}
				?>
			</div>
		</div>
	</div>
</section>

<section class="section section--sombre">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'etapes_titre' ) ); ?></h2>
		</div>
		<ol class="etapes-photos">
			<?php foreach ( $glm_etapes as $glm_index => $glm_etape ) : ?>
				<li class="reveal">
					<?php if ( $glm_etape[2] ) : ?>
						<div class="etapes-photos__media"><?php echo $glm_etape[2]; // phpcs:ignore ?></div>
					<?php else : ?>
						<div class="etapes-photos__media etapes-photos__media--chiffre" aria-hidden="true"><span><?php echo esc_html( sprintf( '%02d', $glm_index ) ); ?></span></div>
					<?php endif; ?>
					<div class="etapes-photos__titre"><span><?php echo esc_html( sprintf( '%02d', $glm_index ) ); ?></span><strong><?php echo esc_html( $glm_etape[0] ); ?></strong></div>
					<p><?php echo esc_html( $glm_etape[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="section">
	<div class="container chiffres">
		<div class="reveal">
			<strong><?php echo esc_html( glm_t( 'chiffre_lieu_titre' ) ); ?></strong>
			<p><?php echo esc_html( glm_t( 'chiffre_lieu_texte' ) ); ?></p>
		</div>
		<?php if ( $glm_experience ) : ?>
			<div class="reveal">
				<strong><?php echo esc_html( $glm_experience ); ?></strong>
				<p><?php echo esc_html( glm_opt( 'experience_texte', 'D\'expérience dans la construction de manèges.' ) ); ?></p>
			</div>
		<?php endif; ?>
		<div class="reveal">
			<strong><?php echo esc_html( glm_t( 'chiffre_metiers_titre' ) ); ?></strong>
			<p><?php echo esc_html( glm_t( 'chiffre_metiers_texte' ) ); ?></p>
		</div>
	</div>
</section>

<?php
glm_cta();
get_footer();
