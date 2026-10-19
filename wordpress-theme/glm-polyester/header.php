<?php
/**
 * En-tête du site.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#contenu">Aller au contenu</a>

<header class="site-header" id="haut">
	<div class="container site-header__bar">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="GLM Polyester, accueil">
			<?php echo glm_logo(); // phpcs:ignore ?>
		</a>
		<nav class="site-nav" aria-label="Menu principal">
			<?php glm_nav_list(); ?>
		</nav>
		<div class="site-header__actions">
			<a class="btn btn--rouge" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Demander un devis</a>
			<button class="burger" type="button" aria-expanded="false" aria-controls="menu-mobile" aria-label="Ouvrir le menu">
				<span class="burger__lines"></span>
			</button>
		</div>
	</div>
	<nav class="mobile-nav" id="menu-mobile" aria-label="Menu mobile">
		<div class="container">
			<?php glm_nav_list( 'mobile' ); ?>
		</div>
	</nav>
</header>

<main id="contenu">
