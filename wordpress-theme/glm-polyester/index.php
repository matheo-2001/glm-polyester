<?php
/**
 * Gabarit de repli (articles, recherche, etc.).
 */
get_header();

glm_page_hero( is_search() ? 'Recherche' : ( is_singular() ? get_the_title() : ( is_home() ? 'Actualités' : wp_strip_all_tags( get_the_archive_title() ) ) ) );
?>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="contenu">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php is_singular() ? the_content() : the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="vide">Aucun contenu pour le moment.</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
