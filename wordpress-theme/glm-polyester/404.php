<?php
/**
 * Page introuvable.
 */
get_header();
?>
<section class="section">
	<div class="container" style="display: flex; flex-direction: column; align-items: flex-start; gap: 24px;">
		<p class="eyebrow">Erreur 404</p>
		<h1 class="display display--l">Cette page n'existe pas</h1>
		<p class="page-hero__lead">Le lien est peut-être ancien, ou l'adresse mal saisie.</p>
		<div class="hero__actions">
			<a class="btn btn--rouge" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l'accueil</a>
			<a class="btn btn--contour" href="<?php echo esc_url( glm_catalogue_url() ); ?>">Voir le catalogue</a>
		</div>
	</div>
</section>
<?php
get_footer();
