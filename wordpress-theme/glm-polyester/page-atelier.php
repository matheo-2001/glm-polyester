<?php
/**
 * Page L'atelier (page dont l'identifiant est « atelier »).
 * Le texte de présentation se modifie dans l'éditeur de la page ;
 * les autres textes dans Apparence > Personnaliser > GLM Polyester > Page L'atelier et étapes.
 */
get_header();

glm_page_hero( get_the_title() ? get_the_title() : 'L\'atelier', glm_t( 'atelier_intro' ) );

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

<?php $glm_metier = glm_t_paragraphes( 'atelier_metier_texte' ); ?>
<?php if ( $glm_metier ) : ?>
	<section class="section section--fond">
		<div class="container bloc-texte reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'atelier_metier_titre' ) ); ?></h2>
			<div class="bloc-texte__corps">
				<?php foreach ( $glm_metier as $glm_para ) : ?>
					<p><?php echo esc_html( $glm_para ); ?></p>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

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

<?php $glm_savoirs = glm_t_paires( 'atelier_savoirs' ); ?>
<?php if ( $glm_savoirs ) : ?>
	<section class="section">
		<div class="container">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'atelier_savoirs_titre' ) ); ?></h2>
			<ul class="cartes-texte">
				<?php foreach ( $glm_savoirs as $glm_paire ) : ?>
					<li class="reveal"><h3><?php echo esc_html( $glm_paire[0] ); ?></h3><p><?php echo esc_html( $glm_paire[1] ); ?></p></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php $glm_clients = glm_t_paires( 'atelier_clients' ); ?>
<?php if ( $glm_clients ) : ?>
	<section class="section section--fond">
		<div class="container">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'atelier_clients_titre' ) ); ?></h2>
			<ul class="clients-pictos">
				<?php foreach ( $glm_clients as $glm_index => $glm_paire ) : ?>
					<li class="reveal"><?php echo glm_picto_client( $glm_index ); // phpcs:ignore ?><h3><?php echo esc_html( $glm_paire[0] ); ?></h3><p><?php echo esc_html( $glm_paire[1] ); ?></p></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php $glm_photos = glm_realisations_choisies( 6, array(), true ); ?>
<?php if ( $glm_photos ) : ?>
	<section class="section">
		<div class="container">
			<div class="section-head reveal">
				<h2 class="display display--m"><?php echo esc_html( glm_t( 'atelier_photos_titre' ) ); ?></h2>
				<a class="lien-fleche" href="<?php echo esc_url( glm_realisations_url() ); ?>">Toutes les réalisations</a>
			</div>
			<div class="galerie galerie--mosaique" data-glm-groupe>
				<?php
				foreach ( $glm_photos as $glm_id ) {
					get_template_part( 'template-parts/realisation', null, array( 'post_id' => $glm_id, 'large' => false, 'mosaique' => true ) );
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section section--fond">
	<div class="container">
		<h2 class="display display--m"><?php echo esc_html( glm_t( 'atelier_services_titre' ) ); ?></h2>
		<?php glm_liste_services(); ?>
	</div>
</section>

<?php $glm_faq = glm_faq( 'atelier_faq' ); ?>
<?php if ( $glm_faq ) : ?>
	<section class="section">
		<div class="container">
			<h2 class="display display--m faq-titre"><?php echo esc_html( glm_t( 'atelier_faq_titre' ) ); ?></h2>
			<div class="faq faq--centre">
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

<?php
glm_cta();
get_footer();
