<?php
/**
 * Galerie des réalisations, avec filtres par type.
 */
get_header();

$glm_types = get_terms( array(
	'taxonomy'   => 'type_realisation',
	'hide_empty' => true,
	'orderby'    => 'term_id',
) );
if ( is_wp_error( $glm_types ) ) {
	$glm_types = array();
}
?>

<section class="page-hero">
	<div class="container">
		<nav class="fil" aria-label="Fil d'Ariane"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a> / Réalisations</nav>
		<h1 class="display display--l">Réalisations</h1>
		<p class="page-hero__lead">Sujets, décors, pièces et rénovations sortis de l'atelier.</p>
		<?php if ( count( $glm_types ) > 1 ) : ?>
			<div class="chips" role="group" aria-label="Filtrer les réalisations">
				<button type="button" class="btn btn--contour btn--petit is-active" data-filtre="" aria-pressed="true">Tout</button>
				<?php foreach ( $glm_types as $glm_type ) : ?>
					<button type="button" class="btn btn--contour btn--petit" data-filtre="<?php echo esc_attr( $glm_type->slug ); ?>" aria-pressed="false"><?php echo esc_html( $glm_type->name ); ?></button>
				<?php endforeach; ?>
				<?php $glm_themes_r = glm_themes( 'realisation' ); ?>
				<?php if ( $glm_themes_r ) : ?>
					<span class="chips__label">Thèmes</span>
					<?php foreach ( $glm_themes_r as $glm_t ) : ?>
						<button type="button" class="btn btn--contour btn--petit" data-filtre="<?php echo esc_attr( 'theme-' . $glm_t->slug ); ?>" aria-pressed="false"><?php echo esc_html( $glm_t->name ); ?></button>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="galerie" id="galerie">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/realisation', null, array(
						'post_id' => get_the_ID(),
						'large'   => null,
					) );
				}
				?>
			</div>
		<?php else : ?>
			<p class="vide">Les photos de nos réalisations arrivent bientôt.</p>
		<?php endif; ?>
	</div>
</section>

<?php
glm_cta();
get_footer();
