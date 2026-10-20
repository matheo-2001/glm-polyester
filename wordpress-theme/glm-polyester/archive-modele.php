<?php
/**
 * Catalogue : tous les modèles, classés par famille.
 * Sert aussi pour la page d'une seule famille (taxonomy-famille.php)
 * et pour les pages par thème (/catalogue/theme/noel/).
 */
get_header();

$glm_current  = is_tax( 'famille' ) ? get_queried_object() : null;
$glm_familles = glm_familles();
$glm_themes   = glm_themes( 'modele' );
$glm_theme    = $glm_current ? null : glm_theme_courant();
$glm_pdf      = glm_catalogue_pdf_url();
$glm_feuille  = glm_feuilleter_url();
$glm_total    = (int) wp_count_posts( 'modele' )->publish;
$glm_accueil  = ! $glm_current && ! $glm_theme; // Catalogue complet.

if ( $glm_current ) {
	$glm_titre = $glm_current->name;
	$glm_intro = $glm_current->description;
	$glm_fil   = array( 'Catalogue' => glm_catalogue_url() );
} elseif ( $glm_theme ) {
	$glm_titre = 'Thème : ' . $glm_theme->name;
	$glm_intro = $glm_theme->description;
	$glm_fil   = array( 'Catalogue' => glm_catalogue_url() );
} else {
	$glm_titre = 'Catalogue';
	$glm_intro = glm_t( 'catalogue_intro' );
	$glm_fil   = array();
}

/** Modèles d'une famille ou d'un thème, par ordre alphabétique. */
$glm_modeles = function ( $taxonomy, $term_id ) {
	return get_posts( array(
		'post_type'      => 'modele',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'fields'         => 'ids',
		'tax_query'      => array( array( 'taxonomy' => $taxonomy, 'terms' => $term_id ) ),
	) );
};
?>

<section class="cat-tete">
	<div class="container cat-tete__grid">
		<div class="cat-tete__texte">
			<?php glm_fil( $glm_fil, $glm_theme ? 'Thème ' . $glm_theme->name : $glm_titre ); ?>
			<h1 class="display display--l"><?php echo esc_html( $glm_titre ); ?></h1>
			<?php if ( $glm_intro ) : ?>
				<p><?php echo esc_html( $glm_intro ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $glm_pdf || $glm_feuille ) : ?>
			<div class="cat-tete__actions">
				<?php if ( $glm_feuille ) : ?>
					<a class="btn btn--rouge" href="<?php echo esc_url( $glm_feuille ); ?>">Feuilleter le catalogue</a>
				<?php endif; ?>
				<?php if ( $glm_pdf ) : ?>
					<a class="btn <?php echo $glm_feuille ? 'btn--contour' : 'btn--rouge'; ?>" href="<?php echo esc_url( $glm_pdf ); ?>" download>Télécharger le PDF</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<nav class="container cat-filtres" aria-label="Catégories et thèmes du catalogue">
		<div class="cat-filtres__ligne">
			<span class="cat-filtres__label">Catégories</span>
			<div class="cat-filtres__puces">
			<a href="<?php echo esc_url( glm_catalogue_url() ); ?>"<?php echo $glm_accueil ? ' aria-current="page"' : ''; ?>>Tout <span><?php echo esc_html( (string) $glm_total ); ?></span></a>
			<?php foreach ( $glm_familles as $glm_term ) : ?>
				<?php
				$glm_actif = $glm_current && $glm_current->term_id === $glm_term->term_id;
				// Sur le catalogue complet, les familles sont des ancres de la page (pas de rechargement).
				$glm_href = $glm_accueil ? '#' . $glm_term->slug : get_term_link( $glm_term );
				?>
				<a href="<?php echo esc_url( $glm_href ); ?>"<?php echo $glm_actif ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $glm_term->name ); ?> <span><?php echo esc_html( (string) $glm_term->count ); ?></span></a>
			<?php endforeach; ?>
			</div>
		</div>
		<?php if ( $glm_themes ) : ?>
			<div class="cat-filtres__ligne">
				<span class="cat-filtres__label">Thèmes</span>
				<div class="cat-filtres__puces">
				<?php foreach ( $glm_themes as $glm_t ) : ?>
					<a href="<?php echo esc_url( glm_theme_url( $glm_t ) ); ?>"<?php echo ( $glm_theme && $glm_theme->term_id === $glm_t->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $glm_t->name ); ?> <span><?php echo esc_html( (string) count( $glm_modeles( 'theme_glm', $glm_t->term_id ) ) ); ?></span></a>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</nav>
</section>

<section class="section catalogue">
	<div class="container">
		<?php
		if ( $glm_theme ) :
			$glm_ids = $glm_modeles( 'theme_glm', $glm_theme->term_id );
			?>
			<div class="famille2">
				<div class="famille2__tete"><h2 class="display display--s"><?php echo esc_html( $glm_theme->name ); ?></h2><span><?php echo esc_html( glm_nb_modeles( count( $glm_ids ) ) ); ?></span></div>
				<div class="cards2">
					<?php
					foreach ( $glm_ids as $glm_n => $glm_id ) {
						glm_modele_card( $glm_id, array( 'eager' => $glm_n < 2 ) );
					}
					?>
				</div>
			</div>
			<?php
		elseif ( $glm_current ) :
			$glm_ids = $glm_modeles( 'famille', $glm_current->term_id );
			?>
			<div class="famille2">
				<div class="famille2__tete"><h2 class="display display--s"><?php echo esc_html( glm_nb_modeles( count( $glm_ids ) ) ); ?></h2></div>
				<?php if ( $glm_ids ) : ?>
					<div class="cards2">
						<?php
						foreach ( $glm_ids as $glm_n => $glm_id ) {
							glm_modele_card( $glm_id, array( 'eager' => $glm_n < 2 ) );
						}
						?>
					</div>
				<?php else : ?>
					<p class="vide"><?php echo esc_html( glm_t( 'vide_famille' ) ); ?> <a class="lien-fleche" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Nous contacter</a></p>
				<?php endif; ?>
			</div>
			<?php
		else :
			$glm_n = 0;
			foreach ( $glm_familles as $glm_term ) :
				$glm_ids = $glm_modeles( 'famille', $glm_term->term_id );
				?>
				<div class="famille2<?php echo 1 === count( $glm_ids ) ? ' famille2--seul' : ''; ?>" id="<?php echo esc_attr( $glm_term->slug ); ?>">
					<div class="famille2__tete">
						<h2 class="display display--s"><a href="<?php echo esc_url( get_term_link( $glm_term ) ); ?>"><?php echo esc_html( $glm_term->name ); ?></a></h2>
						<span><?php echo esc_html( glm_nb_modeles( count( $glm_ids ) ) ); ?></span>
					</div>
					<?php if ( $glm_term->description ) : ?>
						<p class="famille2__desc"><?php echo esc_html( $glm_term->description ); ?></p>
					<?php endif; ?>
					<?php if ( $glm_ids ) : ?>
						<div class="cards2">
							<?php
							foreach ( $glm_ids as $glm_id ) {
								glm_modele_card( $glm_id, array( 'eager' => $glm_n++ < 2, 'extrait' => 1 === count( $glm_ids ) ) );
							}
							?>
						</div>
					<?php else : ?>
						<p class="vide"><?php echo esc_html( glm_t( 'vide_famille' ) ); ?> <a class="lien-fleche" href="<?php echo esc_url( glm_page_url( 'contact' ) ); ?>">Nous contacter</a></p>
					<?php endif; ?>
				</div>
				<?php
			endforeach;
		endif;
		?>
	</div>
</section>

<?php
if ( $glm_theme && 'noel' === $glm_theme->slug ) {
	glm_cta( glm_t( 'cta_noel_titre' ), glm_t( 'cta_noel_texte' ) );
} elseif ( $glm_current && ! in_array( $glm_current->slug, array( 'sujets-roulants', 'sujets-volants' ), true ) ) {
	glm_cta();
} else {
	glm_cta( glm_t( 'cta_catalogue_titre' ), glm_t( 'cta_catalogue_texte' ) );
}
get_footer();
