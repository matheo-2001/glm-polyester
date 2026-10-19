<?php
/**
 * Page Contact et devis (page dont l'identifiant est « contact »).
 */
get_header();

glm_page_hero( get_the_title() ? get_the_title() : 'Contact et devis', glm_t( 'contact_intro' ) );

$glm_tel      = glm_opt( 'telephone' );
$glm_email    = glm_opt( 'email' );
$glm_horaires = glm_opt( 'horaires' );
$glm_facebook = glm_opt( 'facebook', 'https://www.facebook.com/glmpolyester/' );
$glm_adresse  = glm_opt( 'adresse_1', '46 rue de Montdidier' ) . ', ' . glm_opt( 'adresse_2', '60120 Breteuil' );
$glm_privacy  = glm_privacy_url();
$glm_erreurs  = glm_contact_champs_en_erreur();
$glm_messages = glm_contact_messages();

// Pré-remplissage depuis une fiche modèle.
$glm_pre_modele = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : ''; // phpcs:ignore
$glm_pre_type   = isset( $_GET['demande'] ) ? sanitize_key( $_GET['demande'] ) : ''; // phpcs:ignore

/** Attributs d'un champ en erreur (signalé par le serveur). */
$glm_err = function ( $champ, $cible = '' ) use ( $glm_erreurs ) {
	$cible = $cible ? $cible : $champ;
	$err   = in_array( $champ, $glm_erreurs, true );
	return ' aria-describedby="err-' . esc_attr( $cible ) . '"' . ( $err ? ' aria-invalid="true"' : '' );
};
$glm_msg = function ( $champ ) use ( $glm_erreurs, $glm_messages ) {
	$err = in_array( $champ, $glm_erreurs, true );
	return '<p class="champ__erreur" id="err-' . esc_attr( $champ ) . '"' . ( $err ? '' : ' hidden' ) . '>' . ( $err ? esc_html( $glm_messages[ $champ ] ) : '' ) . '</p>';
};
?>

<section class="section">
	<div class="container contact__grid">

		<div id="formulaire">
			<form class="form" id="form-devis" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data"
				data-photos-max="<?php echo esc_attr( (string) GLM_PHOTOS_MAX ); ?>"
				data-poids-max="<?php echo esc_attr( (string) glm_contact_limite() ); ?>">
				<h2 class="display display--s">Votre demande</h2>
				<div id="form-avis"><?php glm_contact_notice(); ?></div>
				<input type="hidden" name="action" value="glm_contact">
				<?php wp_nonce_field( 'glm_contact', 'glm_contact_nonce' ); ?>
				<div class="champ champ--piege" aria-hidden="true">
					<label for="site_web">Ne pas remplir</label>
					<input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">
				</div>

				<div class="form__row">
					<div class="champ">
						<label for="nom">Nom et prénom</label>
						<input type="text" id="nom" name="nom" autocomplete="name" required<?php echo $glm_err( 'nom' ); // phpcs:ignore ?>>
						<?php echo $glm_msg( 'nom' ); // phpcs:ignore ?>
					</div>
					<div class="champ">
						<label for="societe">Société ou collectivité <span>(facultatif)</span></label>
						<input type="text" id="societe" name="societe" autocomplete="organization">
					</div>
				</div>

				<fieldset class="champ-groupe">
					<legend>Pour vous répondre <span>: un téléphone ou un e-mail, au moins l'un des deux</span></legend>
					<div class="form__row">
						<div class="champ">
							<label for="tel">Téléphone</label>
							<input type="tel" id="tel" name="tel" autocomplete="tel" inputmode="tel"<?php echo $glm_err( 'joindre' ); // phpcs:ignore ?>>
						</div>
						<div class="champ">
							<label for="email">E-mail</label>
							<input type="email" id="email" name="email" autocomplete="email"<?php echo in_array( 'email', $glm_erreurs, true ) ? ' aria-invalid="true" aria-describedby="err-email"' : ' aria-describedby="err-joindre err-email"'; // phpcs:ignore ?>>
						</div>
					</div>
					<?php echo $glm_msg( 'joindre' ); // phpcs:ignore ?>
					<?php echo $glm_msg( 'email' ); // phpcs:ignore ?>
				</fieldset>

				<div class="form__row">
					<div class="champ">
						<label for="type">Type de demande</label>
						<select id="type" name="type">
							<option value="" <?php selected( $glm_pre_type, '' ); ?>>Choisissez…</option>
							<?php foreach ( glm_contact_types() as $glm_value => $glm_label ) : ?>
								<option value="<?php echo esc_attr( $glm_value ); ?>" <?php selected( $glm_pre_type, $glm_value ); ?>><?php echo esc_html( $glm_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="champ">
						<label for="modele">Modèle concerné <span>(facultatif)</span></label>
						<input type="text" id="modele" name="modele" value="<?php echo esc_attr( $glm_pre_modele ); ?>">
					</div>
				</div>
				<div class="champ">
					<label for="message">Votre message</label>
					<textarea id="message" name="message" rows="6" required<?php echo $glm_err( 'message' ); // phpcs:ignore ?>></textarea>
					<?php echo $glm_msg( 'message' ); // phpcs:ignore ?>
				</div>
				<div class="champ">
					<label for="photos">Photos <span>(facultatif, utile pour une réparation : <?php echo esc_html( (string) GLM_PHOTOS_MAX ); ?> photos au maximum)</span></label>
					<input type="file" id="photos" name="photos[]" accept="image/*" multiple aria-describedby="photos-aide err-photos">
					<p class="champ__aide" id="photos-aide">Les photos sont réduites automatiquement avant l'envoi.</p>
					<ul class="photos-liste" id="photos-liste" aria-live="polite"></ul>
					<p class="champ__erreur" id="err-photos" hidden></p>
				</div>
				<div class="form__fin">
					<button type="submit" class="btn btn--rouge">Envoyer la demande</button>
					<span>Vos coordonnées servent uniquement à vous répondre.<?php if ( $glm_privacy ) : ?> <a href="<?php echo esc_url( $glm_privacy ); ?>">Politique de confidentialité</a><?php endif; ?></span>
				</div>
			</form>
		</div>

		<aside>
			<div class="coordonnees">
				<h2 class="display display--s">Coordonnées</h2>
				<?php if ( $glm_tel ) : ?>
					<div>
						<div class="eyebrow">Téléphone</div>
						<a class="coordonnees__valeur" href="<?php echo esc_attr( glm_tel_href( $glm_tel ) ); ?>"><?php echo esc_html( $glm_tel ); ?></a>
					</div>
				<?php endif; ?>
				<?php if ( $glm_email ) : ?>
					<div>
						<div class="eyebrow">E-mail</div>
						<a class="coordonnees__valeur" href="mailto:<?php echo esc_attr( antispambot( $glm_email ) ); ?>"><?php echo esc_html( antispambot( $glm_email ) ); ?></a>
					</div>
				<?php endif; ?>
				<div>
					<div class="eyebrow">Atelier</div>
					<div><?php echo esc_html( glm_opt( 'adresse_1', '46 rue de Montdidier' ) ); ?><br><?php echo esc_html( glm_opt( 'adresse_2', '60120 Breteuil' ) ); ?></div>
				</div>
				<?php if ( $glm_horaires ) : ?>
					<div>
						<div class="eyebrow">Horaires</div>
						<div><?php echo esc_html( $glm_horaires ); ?></div>
					</div>
				<?php endif; ?>
				<a class="lien-fleche" href="<?php echo esc_url( 'https://www.openstreetmap.org/search?query=' . rawurlencode( $glm_adresse ) ); ?>" target="_blank" rel="noopener">Voir l'atelier sur la carte<span class="screen-reader-text"> (nouvel onglet)</span></a>
				<?php if ( $glm_facebook ) : ?>
					<a class="lien-fleche" href="<?php echo esc_url( $glm_facebook ); ?>" target="_blank" rel="noopener">Suivre l'atelier sur Facebook<span class="screen-reader-text"> (nouvel onglet)</span></a>
				<?php endif; ?>
			</div>
		</aside>

	</div>
</section>

<?php
get_footer();
