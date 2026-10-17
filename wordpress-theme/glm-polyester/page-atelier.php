<?php
/**
 * Page L'atelier (page dont l'identifiant est « atelier »).
 * Le texte de présentation se modifie dans l'éditeur de la page.
 */
get_header();

glm_page_hero( 'L\'atelier', 'GLM Polyester est un atelier de construction de manèges installé à Breteuil, dans l\'Oise.' );

$glm_experience = glm_opt( 'experience' );
$glm_etapes     = array(
	array( 'Choix du modèle et moulage', 'Le client choisit son modèle, puis la pièce est moulée.', glm_image_opt( 'img_etape_1', '', 'Moule d\'une pièce de manège', 'glm-carte' ), 'Photo à venir : moule ou modèle' ),
	array( 'Thermoformage', 'Les pièces plastiques sont formées à chaud sur leur moule.', glm_image_opt( 'img_etape_2', '', 'Pièce thermoformée', 'glm-carte' ), 'Photo à venir : thermoformage' ),
	array( 'Peinture et éclairage', 'Décor peint ou covering, puis pose de l\'éclairage.', glm_image_opt( 'img_etape_3', 'img/traineau-serre.jpg', 'Détail d\'un traîneau peint et éclairé', 'glm-carte' ), 'Photo à venir' ),
);
?>

<section class="section">
	<div class="container split">
		<div class="split__media slant slant--large reveal">
			<?php echo glm_image_opt( 'img_atelier', 'img/traineau.jpg', 'Vue de l\'atelier : établis, outillage et un traîneau de manège terminé' ); // phpcs:ignore ?>
		</div>
		<div class="split__text reveal">
			<h2 class="display display--m">Un savoir-faire de constructeur</h2>
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
			<div class="section-head__titles">
				<p class="eyebrow">Fabrication</p>
				<h2 class="display display--m">De la forme brute à la pièce finie</h2>
			</div>
		</div>
		<ol class="etapes-photos">
			<?php foreach ( $glm_etapes as $glm_index => $glm_etape ) : ?>
				<li class="reveal">
					<div class="etapes-photos__media"><?php echo $glm_etape[2] ? $glm_etape[2] : glm_placeholder( $glm_etape[3] ); // phpcs:ignore ?></div>
					<div class="etapes-photos__titre"><span><?php echo esc_html( sprintf( '%02d', $glm_index + 1 ) ); ?></span><strong><?php echo esc_html( $glm_etape[0] ); ?></strong></div>
					<p><?php echo esc_html( $glm_etape[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="section">
	<div class="container chiffres">
		<div class="reveal">
			<strong>Breteuil</strong>
			<p>Atelier situé dans l'Oise, <?php echo esc_html( glm_opt( 'adresse_1', '46 rue de Montdidier' ) ); ?>.</p>
		</div>
		<?php if ( $glm_experience ) : ?>
			<div class="reveal">
				<strong><?php echo esc_html( $glm_experience ); ?></strong>
				<p><?php echo esc_html( glm_opt( 'experience_texte', 'D\'expérience dans la construction de manèges.' ) ); ?></p>
			</div>
		<?php endif; ?>
		<div class="reveal">
			<strong>5 métiers</strong>
			<p>Fabrication, personnalisation, thermoformage, réparation et rénovation.</p>
		</div>
	</div>
</section>

<?php
glm_cta();
get_footer();
