<?php
/**
 * Page Contact et devis (page dont l'identifiant est « contact »).
 */
get_header();

glm_page_hero( 'Contact et devis', 'Décrivez votre projet ou votre pièce à réparer. Plus la demande est précise, plus le devis est rapide.' );

$glm_tel      = glm_opt( 'telephone' );
$glm_email    = glm_opt( 'email' );
$glm_horaires = glm_opt( 'horaires' );
$glm_facebook = glm_opt( 'facebook', 'https://www.facebook.com/glmpolyester/' );
$glm_adresse  = glm_opt( 'adresse_1', '46 rue de Montdidier' ) . ', ' . glm_opt( 'adresse_2', '60120 Breteuil' );

// Pré-remplissage depuis une fiche modèle.
$glm_pre_modele = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : ''; // phpcs:ignore
$glm_pre_type   = isset( $_GET['demande'] ) ? sanitize_key( $_GET['demande'] ) : ''; // phpcs:ignore
?>

<section class="section">
	<div class="container contact__grid">

		<div id="formulaire">
			<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<h2 class="display display--s">Votre demande</h2>
				<?php glm_contact_notice(); ?>
				<input type="hidden" name="action" value="glm_contact">
				<?php wp_nonce_field( 'glm_contact', 'glm_contact_nonce' ); ?>
				<div class="champ champ--piege" aria-hidden="true">
					<label for="site_web">Ne pas remplir</label>
					<input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">
				</div>

				<div class="form__row">
					<div class="champ">
						<label for="nom">Nom et prénom</label>
						<input type="text" id="nom" name="nom" autocomplete="name" required>
					</div>
					<div class="champ">
						<label for="societe">Société ou collectivité <span>(facultatif)</span></label>
						<input type="text" id="societe" name="societe" autocomplete="organization">
					</div>
				</div>
				<div class="form__row">
					<div class="champ">
						<label for="tel">Téléphone</label>
						<input type="tel" id="tel" name="tel" autocomplete="tel">
					</div>
					<div class="champ">
						<label for="email">E-mail</label>
						<input type="email" id="email" name="email" autocomplete="email">
					</div>
				</div>
				<div class="form__row">
					<div class="champ">
						<label for="type">Type de demande</label>
						<select id="type" name="type">
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
					<textarea id="message" name="message" rows="6" required></textarea>
				</div>
				<div class="champ">
					<label for="photos">Photos <span>(facultatif, utile pour une réparation : 5 photos de 8 Mo au maximum)</span></label>
					<input type="file" id="photos" name="photos[]" accept="image/*" multiple>
				</div>
				<div class="form__fin">
					<button type="submit" class="btn btn--rouge">Envoyer la demande</button>
					<span>Vos coordonnées servent uniquement à vous répondre.</span>
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
				<a class="lien-fleche" href="<?php echo esc_url( 'https://www.openstreetmap.org/search?query=' . rawurlencode( $glm_adresse ) ); ?>" target="_blank" rel="noopener">Voir l'atelier sur la carte</a>
				<?php if ( $glm_facebook ) : ?>
					<a class="lien-fleche" href="<?php echo esc_url( $glm_facebook ); ?>" target="_blank" rel="noopener">Suivre l'atelier sur Facebook</a>
				<?php endif; ?>
			</div>
		</aside>

	</div>
</section>

<?php
get_footer();
