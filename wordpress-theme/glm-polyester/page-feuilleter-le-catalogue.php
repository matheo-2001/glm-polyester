<?php
/**
 * Page « Feuilleter le catalogue » (identifiant feuilleter-le-catalogue) :
 * le catalogue PDF présenté comme un livre dont on tourne les pages.
 * Sans JavaScript, les pages s'affichent simplement les unes sous les autres.
 */
get_header();

$glm_pages = glm_catalogue_pages();
$glm_pdf   = glm_catalogue_pdf_url();
$glm_total = count( $glm_pages );

?>

<section class="flip-section">
	<div class="container">
		<div class="flip-tete">
			<nav class="fil" aria-label="Fil d'Ariane"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a> / <a href="<?php echo esc_url( glm_catalogue_url() ); ?>">Catalogue</a> / Feuilleter</nav>
			<h1 class="display display--s">Feuilleter le catalogue</h1>
		</div>
		<?php if ( $glm_pages ) : ?>
			<div class="flip" id="flip" data-total="<?php echo esc_attr( $glm_total ); ?>" data-ratio="<?php echo esc_attr( round( $glm_pages[0]['w'] / $glm_pages[0]['h'], 4 ) ); ?>">
				<div class="flip__barre">
					<div class="flip__nav">
						<button type="button" class="flip__btn" data-flip="prev" aria-label="Page précédente">
							<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.4"/></svg>
						</button>
						<span class="flip__compteur" aria-live="polite">Couverture</span>
						<button type="button" class="flip__btn" data-flip="next" aria-label="Page suivante">
							<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.4"/></svg>
						</button>
					</div>
					<div class="flip__actions">
						<?php if ( $glm_pdf ) : ?>
							<a class="flip__action" href="<?php echo esc_url( $glm_pdf ); ?>" download>
								<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false"><path d="M8 1v9M4 6.5L8 10.5l4-4M2 14h12" fill="none" stroke="currentColor" stroke-width="2"/></svg>
								<span>Télécharger</span>
							</a>
						<?php endif; ?>
						<button type="button" class="flip__action" data-flip="plein" aria-pressed="false">
							<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false"><path d="M1.5 6V1.5H6M10 1.5h4.5V6M14.5 10v4.5H10M6 14.5H1.5V10" fill="none" stroke="currentColor" stroke-width="2"/></svg>
							<span class="flip__plein-label">Plein écran</span>
						</button>
					</div>
				</div>

				<div class="flip__scene">
					<div class="flip__livre" id="flip-livre">
						<?php foreach ( $glm_pages as $glm_index => $glm_page ) : ?>
							<?php $glm_hard = 0 === $glm_index || $glm_total - 1 === $glm_index; ?>
							<div class="flip__page"<?php echo $glm_hard ? ' data-density="hard"' : ''; ?>>
								<img
									src="<?php echo esc_url( $glm_page['grand'] ); ?>"
									<?php if ( $glm_page['petit'] ) : ?>
										srcset="<?php echo esc_url( $glm_page['petit'] ); ?> 900w, <?php echo esc_url( $glm_page['grand'] ); ?> 1600w"
										sizes="(min-width: 1024px) 50vw, 100vw"
									<?php endif; ?>
									width="<?php echo esc_attr( $glm_page['w'] ); ?>"
									height="<?php echo esc_attr( $glm_page['h'] ); ?>"
									alt="<?php echo esc_attr( sprintf( 'Catalogue GLM Polyester, page %d sur %d', $glm_index + 1, $glm_total ) ); ?>"
									<?php echo $glm_index > 3 ? 'loading="lazy"' : ''; ?>
									draggable="false">
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<p class="flip__aide">Faites glisser un coin de page ou utilisez les flèches pour tourner les pages. Sur téléphone, passez en plein écran et tournez-le à l’horizontale pour lire plus confortablement.</p>
			</div>
		<?php else : ?>
			<p class="vide">Le catalogue à feuilleter arrive bientôt.
				<?php if ( $glm_pdf ) : ?>
					<a class="lien-fleche" href="<?php echo esc_url( $glm_pdf ); ?>" download>Télécharger le PDF</a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
glm_cta( 'Un sujet vous intéresse ?', 'Dites-nous lequel et vos couleurs, nous revenons vers vous avec un devis.' );
get_footer();
