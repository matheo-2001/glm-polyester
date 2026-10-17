<?php
/**
 * Catalogue : tous les modèles, classés par famille.
 * Sert aussi pour la page d'une seule famille (taxonomy-famille.php)
 * et pour le tri par thème (?theme=noel, par exemple).
 */
get_header();

$glm_current  = is_tax( 'famille' ) ? get_queried_object() : null;
$glm_familles = glm_familles();
$glm_themes   = glm_themes( 'modele' );
$glm_theme    = null;
if ( ! $glm_current && isset( $_GET['theme'] ) ) { // phpcs:ignore
	$glm_theme = get_term_by( 'slug', sanitize_key( $_GET['theme'] ), 'theme_glm' ); // phpcs:ignore
}
$glm_pdf = absint( get_theme_mod( 'glm_catalogue_pdf' ) );
$glm_pdf = $glm_pdf ? wp_get_attachment_url( $glm_pdf ) : '';

if ( $glm_current ) {
	glm_page_hero( $glm_current->name, $glm_current->description, array( 'Catalogue' => glm_catalogue_url() ) );
} elseif ( $glm_theme ) {
	glm_page_hero( 'Thème : ' . $glm_theme->name, '', array( 'Catalogue' => glm_catalogue_url() ) );
} else {
	glm_page_hero( 'Catalogue', 'Manèges, sujets roulants et volants, pièces en polyester et pièces thermoformées. Les sujets roulants et volants sont personnalisables : couleurs, décor peint ou covering.' );
}
?>

<section class="section">
	<div class="container">
		<?php $glm_feuille = glm_feuilleter_url(); ?>
		<?php if ( $glm_pdf || $glm_feuille ) : ?>
			<div class="page-hero__actions" style="margin: 0 0 28px;">
				<?php if ( $glm_feuille ) : ?>
					<a class="btn btn--rouge" href="<?php echo esc_url( $glm_feuille ); ?>">Feuilleter le catalogue</a>
				<?php endif; ?>
				<?php if ( $glm_pdf ) : ?>
					<a class="btn <?php echo $glm_feuille ? 'btn--contour' : 'btn--rouge'; ?>" href="<?php echo esc_url( $glm_pdf ); ?>" download>Télécharger le PDF</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="chips" style="margin: 0 0 16px;">
			<a class="btn btn--contour btn--petit<?php echo ( $glm_current || $glm_theme ) ? '' : ' is-active'; ?>" href="<?php echo esc_url( glm_catalogue_url() ); ?>">Tout</a>
			<?php foreach ( $glm_familles as $glm_term ) : ?>
				<a class="btn btn--contour btn--petit<?php echo ( $glm_current && $glm_current->term_id === $glm_term->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>"><?php echo esc_html( $glm_term->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php if ( $glm_themes ) : ?>
			<div class="chips" style="margin: 0 0 48px;">
				<span class="chips__label">Thèmes</span>
				<?php foreach ( $glm_themes as $glm_t ) : ?>
					<a class="btn btn--contour btn--petit<?php echo ( $glm_theme && $glm_theme->term_id === $glm_t->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'theme', $glm_t->slug, glm_catalogue_url() ) ); ?>"><?php echo esc_html( $glm_t->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( $glm_theme ) :
			$glm_modeles = get_posts( array(
				'post_type'      => 'modele',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
				'tax_query'      => array( array( 'taxonomy' => 'theme_glm', 'terms' => $glm_theme->term_id ) ),
			) );
			?>
			<div class="cards">
				<?php
				foreach ( $glm_modeles as $glm_id ) {
					glm_modele_card( $glm_id );
				}
				?>
			</div>
			<?php
		else :
			foreach ( $glm_familles as $glm_term ) :
				if ( $glm_current && $glm_current->term_id !== $glm_term->term_id ) {
					continue;
				}
				$glm_modeles = get_posts( array(
					'post_type'      => 'modele',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'fields'         => 'ids',
					'tax_query'      => array( array( 'taxonomy' => 'famille', 'terms' => $glm_term->term_id ) ),
				) );
				?>
				<div class="famille" id="<?php echo esc_attr( $glm_term->slug ); ?>">
					<?php if ( ! $glm_current ) : ?>
						<h2 class="display display--s famille-titre reveal"><?php echo esc_html( $glm_term->name ); ?></h2>
						<?php if ( $glm_term->description ) : ?>
							<p class="famille__desc"><?php echo esc_html( $glm_term->description ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( $glm_modeles ) : ?>
						<div class="cards">
							<?php
							foreach ( $glm_modeles as $glm_id ) {
								glm_modele_card( $glm_id );
							}
							?>
						</div>
					<?php else : ?>
						<p class="vide">Les modèles de cette famille arrivent bientôt. <a class="lien-fleche" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Nous contacter</a></p>
					<?php endif; ?>
				</div>
				<?php
			endforeach;
		endif;
		?>
	</div>
</section>

<?php
glm_cta( 'Un modèle à personnaliser ?', 'Nos sujets roulants et volants sont personnalisables : couleurs, décor peint ou covering.' );
get_footer();
