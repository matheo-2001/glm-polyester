<?php
/**
 * Page Services (page dont l'identifiant est « services »).
 */
get_header();

glm_page_hero( 'Services', 'Fabrication, personnalisation, thermoformage, réparation et rénovation : tout se fait à l\'atelier, pour les forains, les parcs, les collectivités et les particuliers.' );

$glm_blocs = array(
	array(
		'titre' => 'Fabrication',
		'texte' => 'Manèges complets, sujets roulants et volants, pièces en polyester, à partir des modèles du catalogue.',
		'plus'  => 'Vous avez votre propre moule ? Nous pouvons aussi réaliser vos pièces à partir de celui-ci.',
		'lien'  => array( 'Voir le catalogue', glm_catalogue_url() ),
		'image' => glm_image_opt( 'img_fabrication', 'img/traineau.jpg', 'Sujet de manège terminé dans l\'atelier' ),
		'vide'  => 'Photo à venir',
	),
	array(
		'titre' => 'Personnalisable',
		'texte' => 'Nos sujets roulants et volants se personnalisent entièrement : couleurs au choix, décor peint ou covering. Pour les sujets de Noël, nous ajoutons bruiteur, marche et éclairage.',
		'plus'  => '',
		'lien'  => array( 'Parler de votre projet', glm_page_url( 'contact' ) ),
		'image' => glm_image_opt( 'img_sur_mesure', 'img/sujet-pompier.jpg', 'Sujet de manège personnalisé' ),
		'vide'  => 'Photo à venir',
	),
	array(
		'titre' => 'Thermoformage',
		'texte' => 'Pièces plastiques formées à chaud sur moule : cabochons, optiques, caches et pièces diverses pour sujets et manèges.',
		'plus'  => '',
		'lien'  => array( 'Voir les pièces', glm_catalogue_url() ),
		'image' => glm_image_opt( 'img_thermoformage', '', 'Pièces thermoformées' ),
		'vide'  => 'Photo à venir : pièces thermoformées',
	),
	array(
		'titre' => 'Réparation',
		'texte' => 'Pièce cassée, fissurée ou usée : nous la remettons en état, quelle que soit son origine, pour que le manège reparte en saison.',
		'plus'  => '',
		'lien'  => array( 'Envoyer une photo de la pièce', glm_page_url( 'contact' ) ),
		'image' => glm_image_opt( 'img_reparation', '', 'Réparation d\'une pièce de manège' ),
		'vide'  => 'Photo à venir : réparation en cours',
	),
	array(
		'titre' => 'Rénovation',
		'texte' => 'Sujets et manèges anciens remis à neuf : reprise de la structure, nouvelle peinture, nouveau décor, éclairage modernisé.',
		'plus'  => '',
		'lien'  => array( 'Voir des réalisations', glm_realisations_url() ),
		'image' => glm_image_opt( 'img_renovation', '', 'Sujet de manège rénové' ),
		'vide'  => 'Photo à venir : rénovation avant / après',
	),
);
?>

<section class="section">
	<div class="container blocs">
		<?php foreach ( $glm_blocs as $glm_index => $glm_bloc ) : ?>
			<div class="split<?php echo ( 1 === $glm_index % 2 ) ? ' split--inverse' : ''; ?>" id="<?php echo esc_attr( sanitize_title( $glm_bloc['titre'] ) ); ?>">
				<div class="split__media reveal">
					<?php echo $glm_bloc['image'] ? $glm_bloc['image'] : glm_placeholder( $glm_bloc['vide'] ); // phpcs:ignore ?>
				</div>
				<div class="split__text reveal">
					<h2 class="display display--s famille-titre"><?php echo esc_html( $glm_bloc['titre'] ); ?></h2>
					<p class="muted"><?php echo esc_html( $glm_bloc['texte'] ); ?></p>
					<?php if ( $glm_bloc['plus'] ) : ?>
						<p class="muted"><strong style="color: var(--encre); font-weight: 600;"><?php echo esc_html( $glm_bloc['plus'] ); ?></strong></p>
					<?php endif; ?>
					<a class="lien-fleche" href="<?php echo esc_url( $glm_bloc['lien'][1] ); ?>"><?php echo esc_html( $glm_bloc['lien'][0] ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="section section--fond">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="display display--m">Comment ça se passe</h2>
		</div>
		<ol class="process">
			<li class="reveal" style="background: var(--blanc);"><span class="process__num">01</span><strong>Votre demande</strong><span>Par téléphone ou par le formulaire, avec photos si possible.</span></li>
			<li class="reveal" style="background: var(--blanc);"><span class="process__num">02</span><strong>Le devis</strong><span>Nous chiffrons le travail et le délai.</span></li>
			<li class="reveal" style="background: var(--blanc);"><span class="process__num">03</span><strong>Le travail à l'atelier</strong><span>Fabrication, personnalisation, thermoformage, réparation ou rénovation.</span></li>
			<li class="reveal" style="background: var(--blanc);"><span class="process__num">04</span><strong>La remise</strong><span>Nous convenons ensemble de la remise de la pièce.</span></li>
		</ol>
	</div>
</section>

<?php
glm_cta();
get_footer();
