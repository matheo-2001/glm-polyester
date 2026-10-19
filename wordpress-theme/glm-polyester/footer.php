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
				<div><?php echo esc_html( glm_opt( 'adresse_1', '46 rue de Montdidier' ) ); ?><br><?php echo esc_html( glm_opt( 'adresse_2', '60120 Breteuil' ) ); ?></div>
				<?php if ( $glm_tel || $glm_email ) : ?>
					<div>
						<?php if ( $glm_tel ) : ?>
							<a href="<?php echo esc_attr( glm_tel_href( $glm_tel ) ); ?>"><?php echo esc_html( $glm_tel ); ?></a>
						<?php endif; ?>
						<?php echo ( $glm_tel && $glm_email ) ? ' · ' : ''; ?>
						<?php if ( $glm_email ) : ?>
							<a href="mailto:<?php echo esc_attr( antispambot( $glm_email ) ); ?>"><?php echo esc_html( antispambot( $glm_email ) ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="site-footer__col">
				<h2>Catalogue</h2>
				<?php foreach ( glm_familles() as $glm_term ) : ?>
					<a href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>"><?php echo esc_html( $glm_term->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="site-footer__col">
				<h2>GLM Polyester</h2>
				<a href="<?php echo esc_url( glm_page_url( 'services' ) ); ?>">Services</a>
				<a href="<?php echo esc_url( glm_realisations_url() ); ?>">Réalisations</a>
				<a href="<?php echo esc_url( glm_page_url( 'atelier' ) ); ?>">L'atelier</a>
				<a href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Contact</a>
				<?php if ( $glm_facebook ) : ?>
					<a href="<?php echo esc_url( $glm_facebook ); ?>" target="_blank" rel="noopener">Facebook</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="site-footer__bas">
			<div>GLM Polyester SAS · SIRET <?php echo esc_html( glm_opt( 'siret', '977 989 318 00026' ) ); ?></div>
			<div>
				<a href="<?php echo esc_url( glm_page_url( 'mentions-legales' ) ); ?>">Mentions légales</a>
				<?php if ( $glm_privacy ) : ?>
					<a href="<?php echo esc_url( $glm_privacy ); ?>">Politique de confidentialité</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>

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
