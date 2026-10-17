<?php
/**
 * Page standard (mentions légales, confidentialité, etc.).
 */
get_header();

while ( have_posts() ) :
	the_post();
	glm_page_hero( get_the_title() );
	?>
	<section class="section">
		<div class="container">
			<div class="contenu"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
