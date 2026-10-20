<?php
/**
 * Page d'accueil.
 */
get_header();

$glm_realisations = glm_realisations_choisies( 3 );

// Familles : les familles principales en grand (les plus fournies d'abord), les petites familles en petit, comme dans le catalogue.
$glm_familles = glm_familles();
$glm_grandes  = array_values( array_filter( $glm_familles, function ( $term ) {
	return ! glm_famille_compacte( $term );
} ) );
$glm_petites  = array_values( array_filter( $glm_familles, 'glm_famille_compacte' ) );
usort( $glm_grandes, function ( $a, $b ) {
	return $b->count - $a->count;
} );
if ( count( $glm_grandes ) > 3 ) {
	$glm_petites = array_merge( array_slice( $glm_grandes, 3 ), $glm_petites );
	$glm_grandes = array_slice( $glm_grandes, 0, 3 );
}
$glm_total   = (int) wp_count_posts( 'modele' )->publish;
?>

<section class="hero2 hero2--clair">
	<div class="container hero2__grid">
		<div class="hero2__texte">
			<p class="hero2__lieu"><?php echo esc_html( glm_opt( 'hero_surtitre', 'Constructeur de manèges · Breteuil, Oise' ) ); ?></p>
			<h1 class="display"><?php echo esc_html( glm_opt( 'hero_titre', 'Manèges, sujets et pièces en polyester' ) ); ?></h1>
			<p class="hero2__lead"><?php echo esc_html( glm_opt( 'hero_texte', 'GLM Polyester fabrique, répare et rénove des manèges, des sujets et des pièces en polyester, et réalise des pièces thermoformées. Des modèles du catalogue, personnalisables.' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--rouge" href="<?php echo esc_url( glm_catalogue_url() ); ?>">Voir le catalogue</a>
				<a class="btn btn--contour" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Demander un devis</a>
			</div>
		</div>
		<div class="hero2__media">
			<?php
			echo glm_image_opt( 'img_hero', 'img/traineau-serre.jpg', 'Sujet de manège fabriqué par GLM Polyester', 'glm-large', array( // phpcs:ignore
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'sizes'         => '(min-width: 1240px) 700px, (min-width: 1024px) 55vw, 100vw',
			) );
			?>
		</div>
	</div>
	<a class="hero2__suite" href="#suite" aria-label="Voir la suite de la page"><svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path d="M5 9l7 7 7-7" fill="none" stroke="currentColor" stroke-width="2.4"/></svg></a>
</section>

<section class="metiers" id="suite">
	<div class="container metiers__grid">
		<?php
		// Chaque métier mène à la page du service ; sur téléphone, la phrase courte du service remplace le texte.
		$glm_metiers = array( 1 => 'fabrication', 2 => 'personnalisable', 3 => 'reparation', 4 => 'renovation' );
		foreach ( $glm_metiers as $glm_i => $glm_service ) :
			?>
			<a class="metier reveal" href="<?php echo esc_url( glm_service_url( $glm_service ) ); ?>">
				<h2 class="titre-carte"><?php echo esc_html( glm_t( 'metier_' . $glm_i . '_titre' ) ); ?></h2>
				<p class="metier__texte"><?php echo esc_html( glm_t( 'metier_' . $glm_i . '_texte' ) ); ?></p>
				<p class="metier__court"><?php echo esc_html( glm_t( 'service_' . $glm_service . '_court' ) ); ?></p>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<?php if ( $glm_familles ) : ?>
<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'accueil_catalogue_titre' ) ); ?></h2>
			<a class="lien-fleche" href="<?php echo esc_url( glm_catalogue_url() ); ?>"><?php echo esc_html( $glm_total > 1 ? sprintf( 'Voir les %d modèles', $glm_total ) : 'Voir le catalogue' ); ?></a>
		</div>
		<div class="mosaique">
			<?php foreach ( $glm_grandes as $glm_index => $glm_term ) : ?>
				<?php
				$glm_image = glm_famille_image( $glm_term, 0 === $glm_index ? 'glm-large' : 'glm-carte', '', 0 === $glm_index ? '(min-width: 1240px) 700px, (min-width: 640px) 58vw, 100vw' : '(min-width: 1240px) 500px, (min-width: 1024px) 40vw, (min-width: 640px) 50vw, 50vw' );
				?>
				<a class="tuile tuile--<?php echo esc_attr( (string) ( $glm_index + 1 ) ); ?><?php echo $glm_image ? '' : ' tuile--sans-photo'; ?>" href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>">
					<?php echo $glm_image; // phpcs:ignore ?>
					<span class="tuile__txt">
						<h3 class="titre-carte"><?php echo esc_html( $glm_term->name ); ?></h3>
						<span class="tuile__nb"><?php echo esc_html( glm_nb_modeles( $glm_term->count ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
			<?php foreach ( $glm_petites as $glm_term ) : ?>
				<?php $glm_image = glm_famille_image( $glm_term, 'glm-carte-s', '', '120px' ); ?>
				<a class="tuile tuile--petite<?php echo $glm_image ? '' : ' tuile--sans-photo'; ?>" href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>">
					<?php echo $glm_image ? $glm_image : '<span class="tuile__vide" aria-hidden="true"></span>'; // phpcs:ignore ?>
					<span class="tuile__txt">
						<h3 class="titre-carte"><?php echo esc_html( $glm_term->name ); ?></h3>
						<span class="tuile__nb"><?php echo esc_html( glm_nb_modeles( $glm_term->count ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
// Photos d'origine des trois choix (remplaçables dans Apparence > Personnaliser > Photos).
$glm_perso_photos = array(
	1 => array( 'img/perso-couleurs.webp', 'Bateau de manège peint en dégradé rose, vert et jaune pailleté' ),
	2 => array( 'img/perso-decor.webp', 'Hélicoptère de manège jaune avec armoiries et numéro peints' ),
	3 => array( 'img/perso-eclairage.webp', 'Motos de police de manège avec leurs feux allumés' ),
);
?>
<section class="section section--fond section-perso">
	<div class="container perso">
		<div class="perso__texte reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'perso_titre' ) ); ?></h2>
			<div class="perso__ligne">
				<p><?php echo esc_html( trim( glm_t( 'perso_texte' ) . ' ' . glm_t( 'perso_note' ) ) ); ?></p>
				<a class="btn btn--rouge" href="<?php echo esc_url( glm_service_devis_url( 'personnalisable' ) ); ?>"><?php echo esc_html( glm_t( 'perso_lien' ) ); ?></a>
			</div>
		</div>
		<ul class="perso__choix">
			<?php for ( $glm_i = 1; $glm_i <= 3; $glm_i++ ) : ?>
				<?php $glm_photo = glm_image_opt( 'img_perso_' . $glm_i, $glm_perso_photos[ $glm_i ][0], $glm_perso_photos[ $glm_i ][1], 'glm-carte', array( 'sizes' => '(min-width: 1240px) 400px, (min-width: 640px) 33vw, 100vw' ) ); ?>
				<li class="reveal">
					<div class="perso__visuel"><?php echo $glm_photo; // phpcs:ignore ?></div>
					<strong><?php echo esc_html( glm_t( 'perso_' . $glm_i . '_titre' ) ); ?></strong>
					<span><?php echo esc_html( glm_t( 'perso_' . $glm_i . '_texte' ) ); ?></span>
				</li>
			<?php endfor; ?>
		</ul>
	</div>
</section>

<section class="section section--sombre">
	<div class="container split">
		<div class="split__media reveal">
			<?php echo glm_image_opt( 'img_atelier', 'img/traineau.jpg', 'Vue de l\'atelier avec un traîneau de manège terminé', 'glm-large', array( 'sizes' => '(min-width: 1024px) 640px, 100vw' ) ); // phpcs:ignore ?>
		</div>
		<div class="split__text reveal">
			<h2 class="display display--m"><?php echo esc_html( glm_t( 'etapes_titre' ) ); ?></h2>
			<ol class="etapes">
				<?php for ( $glm_i = 1; $glm_i <= 3; $glm_i++ ) : ?>
					<li><span class="etapes__num"><?php echo esc_html( sprintf( '%02d', $glm_i ) ); ?></span><span><strong><?php echo esc_html( glm_t( 'etape_' . $glm_i . '_titre' ) ); ?>.</strong> <span><?php echo esc_html( glm_t( 'etape_' . $glm_i . '_texte' ) ); ?></span></span></li>
				<?php endfor; ?>
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
				<h2 class="display display--m"><?php echo esc_html( glm_t( 'realisations_titre' ) ); ?></h2>
			</div>
			<a class="lien-fleche" href="<?php echo esc_url( glm_realisations_url() ); ?>">Toutes les réalisations</a>
		</div>
		<div class="galerie" data-glm-groupe>
			<?php
			foreach ( $glm_realisations as $glm_index => $glm_post_id ) {
				get_template_part( 'template-parts/realisation', null, array(
					'post_id' => $glm_post_id,
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
