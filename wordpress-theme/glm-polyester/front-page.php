<?php
/**
 * Page d'accueil.
 */
get_header();

$glm_realisations = get_posts( array(
	'post_type'      => 'realisation',
	'posts_per_page' => 3,
) );
?>

<section class="hero">
	<div class="container hero__grid">
		<div class="hero__text">
			<p class="eyebrow"><?php echo esc_html( glm_opt( 'hero_surtitre', 'Constructeur de manèges · Breteuil, Oise' ) ); ?></p>
			<h1 class="display display--xl"><?php echo esc_html( glm_opt( 'hero_titre', 'Manèges, sujets et pièces en polyester' ) ); ?></h1>
			<p class="hero__lead"><?php echo esc_html( glm_opt( 'hero_texte', 'GLM Polyester fabrique, répare et rénove des manèges, des sujets et des pièces en polyester, et réalise des pièces thermoformées. Des modèles du catalogue, personnalisables.' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--rouge" href="<?php echo esc_url( glm_catalogue_url() ); ?>">Voir le catalogue</a>
				<a class="btn btn--contour" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Demander un devis</a>
			</div>
		</div>
		<div class="hero__media slant">
			<?php echo glm_image_opt( 'img_hero', 'img/traineau-serre.jpg', 'Sujet de manège fabriqué par GLM Polyester' ); // phpcs:ignore ?>
		</div>
	</div>
</section>

<section class="strip">
	<div class="container strip__grid">
		<div class="strip__item reveal">
			<h2 class="titre-carte">Fabrication</h2>
			<p>Manèges complets, sujets, pièces en polyester et pièces thermoformées.</p>
		</div>
		<div class="strip__item reveal">
			<h2 class="titre-carte">Personnalisable</h2>
			<p>Sujets roulants et volants : couleurs au choix, décor peint ou covering.</p>
		</div>
		<div class="strip__item reveal">
			<h2 class="titre-carte">Réparation</h2>
			<p>Pièces cassées ou fissurées remises en état, pour repartir en saison.</p>
		</div>
		<div class="strip__item reveal">
			<h2 class="titre-carte">Rénovation</h2>
			<p>Sujets et manèges anciens remis à neuf : structure, peinture, éclairage.</p>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<div class="section-head__titles">
				<p class="eyebrow">Catalogue</p>
				<h2 class="display display--m">Le catalogue</h2>
			</div>
			<a class="lien-fleche" href="<?php echo esc_url( glm_catalogue_url() ); ?>">Tout le catalogue</a>
		</div>
		<div class="cards cards--4">
			<?php foreach ( array_slice( glm_familles(), 0, 4 ) as $glm_term ) : ?>
				<?php $glm_image = glm_famille_image( $glm_term ); ?>
				<a class="card reveal" href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>">
					<div class="card__media">
						<?php echo $glm_image ? $glm_image : glm_placeholder(); // phpcs:ignore ?>
					</div>
					<div class="card__body">
						<h3 class="titre-carte"><?php echo esc_html( $glm_term->name ); ?></h3>
						<?php if ( $glm_term->description ) : ?>
							<p><?php echo esc_html( $glm_term->description ); ?></p>
						<?php endif; ?>
						<span class="card__more">Voir les modèles</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--sombre">
	<div class="container split">
		<div class="split__media reveal">
			<?php echo glm_image_opt( 'img_atelier', 'img/traineau.jpg', 'Vue de l\'atelier avec un traîneau de manège terminé' ); // phpcs:ignore ?>
		</div>
		<div class="split__text reveal">
			<p class="eyebrow">L'atelier</p>
			<h2 class="display display--m">De la forme brute à la pièce finie</h2>
			<ol class="etapes">
				<li><span class="etapes__num">01</span><span><strong>Choix du modèle et moulage.</strong> <span>Le client choisit son modèle, puis la pièce est moulée.</span></span></li>
				<li><span class="etapes__num">02</span><span><strong>Thermoformage.</strong> <span>Les pièces plastiques sont formées à chaud sur leur moule.</span></span></li>
				<li><span class="etapes__num">03</span><span><strong>Peinture et éclairage.</strong> <span>Décor peint ou covering, puis pose de l'éclairage.</span></span></li>
			</ol>
			<a class="lien-fleche" href="<?php echo esc_url( glm_page_url( 'atelier' ) ); ?>">Découvrir l'atelier</a>
		</div>
	</div>
</section>

<?php if ( $glm_realisations ) : ?>
<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<div class="section-head__titles">
				<p class="eyebrow">Réalisations</p>
				<h2 class="display display--m">Sortis de l'atelier</h2>
			</div>
			<a class="lien-fleche" href="<?php echo esc_url( glm_realisations_url() ); ?>">Toutes les réalisations</a>
		</div>
		<div class="galerie">
			<?php
			foreach ( $glm_realisations as $glm_index => $glm_post ) {
				get_template_part( 'template-parts/realisation', null, array(
					'post_id' => $glm_post->ID,
					'large'   => 0 === $glm_index,
				) );
			}
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
glm_cta();
get_footer();
