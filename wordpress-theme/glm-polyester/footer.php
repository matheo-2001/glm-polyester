<?php
/**
 * Pied de page.
 */
$glm_tel      = glm_opt( 'telephone' );
$glm_email    = glm_opt( 'email' );
$glm_facebook = glm_opt( 'facebook', 'https://www.facebook.com/glmpolyester/' );
$glm_privacy  = glm_privacy_url();
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__grid">
			<div class="site-footer__brand">
				<?php echo glm_logo( 'lazy' ); // phpcs:ignore ?>
				<p><?php echo esc_html( glm_t( 'footer_texte' ) ); ?></p>
				<address class="site-footer__adresse">
					<span><?php echo esc_html( glm_opt( 'adresse_1' ) ); ?><br><?php echo esc_html( glm_opt( 'adresse_2' ) ); ?></span>
					<?php if ( $glm_tel ) : ?>
						<a href="<?php echo esc_attr( glm_tel_href( $glm_tel ) ); ?>"><?php echo esc_html( $glm_tel ); ?></a>
					<?php endif; ?>
					<?php if ( $glm_email ) : ?>
						<a href="mailto:<?php echo esc_attr( antispambot( $glm_email ) ); ?>"><?php echo esc_html( antispambot( $glm_email ) ); ?></a>
					<?php endif; ?>
				</address>
				<?php if ( $glm_facebook ) : ?>
					<a class="site-footer__facebook" href="<?php echo esc_url( $glm_facebook ); ?>" target="_blank" rel="noopener">
						<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.2H7.8v3h2.6V21h3.1z"/></svg>
						Suivez l'atelier sur Facebook
					</a>
				<?php endif; ?>
			</div>
			<nav class="site-footer__col" aria-label="Catalogue">
				<h2>Catalogue</h2>
				<?php foreach ( glm_familles() as $glm_term ) : ?>
					<a href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>"><?php echo esc_html( $glm_term->name ); ?></a>
				<?php endforeach; ?>
			</nav>
			<nav class="site-footer__col" aria-label="Services">
				<h2>Services</h2>
				<?php foreach ( glm_services() as $glm_slug => $glm_titre ) : ?>
					<a href="<?php echo esc_url( glm_service_url( $glm_slug ) ); ?>"><?php echo esc_html( $glm_titre ); ?></a>
				<?php endforeach; ?>
			</nav>
			<nav class="site-footer__col" aria-label="GLM Polyester">
				<h2>GLM Polyester</h2>
				<a href="<?php echo esc_url( glm_realisations_url() ); ?>">Réalisations</a>
				<a href="<?php echo esc_url( glm_page_url( 'atelier' ) ); ?>">L'atelier</a>
				<?php if ( glm_feuilleter_url() ) : ?>
					<a href="<?php echo esc_url( glm_feuilleter_url() ); ?>">Feuilleter le catalogue</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Contact et devis</a>
			</nav>
			<?php $glm_pied_photos = glm_realisations_choisies( 4, array(), true ); ?>
			<?php if ( $glm_pied_photos ) : ?>
				<div class="site-footer__photos">
					<h2>Dernières réalisations</h2>
					<div>
						<?php foreach ( $glm_pied_photos as $glm_pied_id ) : ?>
							<?php if ( has_post_thumbnail( $glm_pied_id ) ) : ?>
								<a href="<?php echo esc_url( glm_realisations_url() ); ?>" aria-label="<?php echo esc_attr( 'Réalisation : ' . get_the_title( $glm_pied_id ) ); ?>"><?php echo wp_get_attachment_image( get_post_thumbnail_id( $glm_pied_id ), 'glm-carte-s', false, array( 'loading' => 'lazy', 'alt' => '', 'sizes' => '120px' ) ); ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<a class="site-footer__tout" href="<?php echo esc_url( glm_realisations_url() ); ?>">Toutes les réalisations</a>
				</div>
			<?php endif; ?>
		</div>

		<nav class="site-footer__pages" aria-label="Pages du site">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
			<a href="<?php echo esc_url( glm_catalogue_url() ); ?>">Catalogue</a>
			<a href="<?php echo esc_url( glm_page_url( 'services' ) ); ?>">Services</a>
			<a href="<?php echo esc_url( glm_realisations_url() ); ?>">Réalisations</a>
			<a href="<?php echo esc_url( glm_page_url( 'atelier' ) ); ?>">L'atelier</a>
			<a href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Contact</a>
		</nav>

		<div class="site-footer__bas">
			<div>© <?php echo esc_html( wp_date( 'Y' ) ); ?> GLM Polyester</div>
			<div>
				<a href="<?php echo esc_url( glm_page_url( 'mentions-legales' ) ); ?>">Mentions légales</a>
				<?php if ( $glm_privacy ) : ?>
					<a href="<?php echo esc_url( $glm_privacy ); ?>">Politique de confidentialité</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>

<a class="haut-page" href="#haut" aria-label="Revenir en haut de la page">
	<svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M6 14l6-6 6 6" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
</a>

<dialog class="lightbox" id="lightbox" aria-label="Visionneuse de photos">
	<span class="lightbox__count" aria-live="polite"></span>
	<button type="button" class="lightbox__btn lightbox__close" aria-label="Fermer la visionneuse">
		<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg>
	</button>
	<button type="button" class="lightbox__btn lightbox__prev" aria-label="Photo précédente">
		<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"></path></svg>
	</button>
	<figure class="lightbox__figure">
		<img class="lightbox__img" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="">
		<figcaption class="lightbox__caption"></figcaption>
	</figure>
	<button type="button" class="lightbox__btn lightbox__next" aria-label="Photo suivante">
		<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"></path></svg>
	</button>
</dialog>

<?php wp_footer(); ?>
</body>
</html>
