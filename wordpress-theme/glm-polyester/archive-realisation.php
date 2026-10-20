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
		<?php glm_fil( array(), 'Réalisations' ); ?>
		<h1 class="display display--l">Réalisations</h1>
		<p class="page-hero__lead"><?php echo esc_html( glm_t( 'realisations_intro' ) ); ?></p>
		<?php
		$glm_themes_r = glm_themes( 'realisation' );
		// Nombre de réalisations affichées pour un type ou un thème (photos sous licence comprises ou non, selon le réglage).
		$glm_nb = function ( $taxonomy, $term_id ) {
			$args = array( 'post_type' => 'realisation', 'posts_per_page' => 1, 'fields' => 'ids', 'tax_query' => array( array( 'taxonomy' => $taxonomy, 'terms' => $term_id ) ) );
			if ( ! glm_licences_visibles() ) {
				$args['meta_query'] = glm_sans_licence();
			}
			$q = new WP_Query( $args );
			return (int) $q->found_posts;
		};
		global $wp_query;
		?>
		<?php if ( count( $glm_types ) > 1 || $glm_themes_r ) : ?>
			<div class="cat-filtres" role="group" aria-label="Filtrer les réalisations">
				<div class="cat-filtres__ligne">
					<span class="cat-filtres__label">Catégories</span>
					<div class="cat-filtres__puces">
					<button type="button" class="is-active" data-filtre="" aria-pressed="true">Tout <span><?php echo esc_html( (string) $wp_query->found_posts ); ?></span></button>
					<?php foreach ( $glm_types as $glm_type ) : ?>
						<button type="button" data-filtre="<?php echo esc_attr( $glm_type->slug ); ?>" aria-pressed="false"><?php echo esc_html( $glm_type->name ); ?> <span><?php echo esc_html( (string) $glm_nb( 'type_realisation', $glm_type->term_id ) ); ?></span></button>
					<?php endforeach; ?>
					</div>
				</div>
				<?php if ( $glm_themes_r ) : ?>
					<div class="cat-filtres__ligne">
						<span class="cat-filtres__label">Thèmes</span>
						<div class="cat-filtres__puces">
						<?php foreach ( $glm_themes_r as $glm_t ) : ?>
							<button type="button" data-filtre="<?php echo esc_attr( 'theme-' . $glm_t->slug ); ?>" aria-pressed="false"><?php echo esc_html( $glm_t->name ); ?> <span><?php echo esc_html( (string) $glm_nb( 'theme_glm', $glm_t->term_id ) ); ?></span></button>
						<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<p class="screen-reader-text" id="galerie-compte" aria-live="polite"></p>
			<div class="galerie galerie--mosaique" id="galerie" data-glm-groupe>
				<?php
				$glm_n = 0;
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/realisation', null, array(
						'post_id'  => get_the_ID(),
						'large'    => false,
						'eager'    => $glm_n++ < 3,
						'mosaique' => true,
					) );
				}
				?>
			</div>
		<?php else : ?>
			<p class="vide"><?php echo esc_html( glm_t( 'realisations_vide' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
glm_cta();
get_footer();
