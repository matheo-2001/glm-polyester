<?php
/**
 * Formulaire de contact et de demande de devis.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Types de demande proposés dans le formulaire. */
function glm_contact_types() {
	return array(
		'catalogue'     => 'Un modèle du catalogue',
		'sur-mesure'    => 'Un sujet personnalisé',
		'thermoformage' => 'Des pièces thermoformées',
		'reparation'    => 'Une réparation',
		'renovation'    => 'Une rénovation',
		'autre'         => 'Autre',
	);
}

/** Photos jointes : nombre maximum, poids maximum par photo. */
define( 'GLM_PHOTOS_MAX', 5 );
define( 'GLM_PHOTO_POIDS_MAX', 8 * MB_IN_BYTES );

/** Poids total qu'un envoi peut atteindre sur ce serveur (limite PHP), plafonné à 20 Mo pour l'e-mail. */
function glm_contact_limite() {
	$post = wp_convert_hr_to_bytes( (string) ini_get( 'post_max_size' ) );
	$max  = 20 * MB_IN_BYTES;
	return $post > 0 ? min( $post - 256 * KB_IN_BYTES, $max ) : $max;
}

add_action( 'admin_post_nopriv_glm_contact', 'glm_handle_contact' );
add_action( 'admin_post_glm_contact', 'glm_handle_contact' );

function glm_contact_redirect( $status, $champs = array() ) {
	$args = array( 'contact' => $status );
	if ( $champs ) {
		$args['champs'] = implode( ',', $champs );
	}
	wp_safe_redirect( add_query_arg( $args, glm_page_url( 'contact' ) ) . '#formulaire' );
	exit;
}

function glm_handle_contact() {
	// Envoi plus lourd que ce qu'accepte le serveur : PHP vide alors tout le formulaire.
	if ( empty( $_POST ) && ! empty( $_SERVER['CONTENT_LENGTH'] ) ) {
		glm_contact_redirect( 'trop-lourd' );
	}
	if ( ! isset( $_POST['glm_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['glm_contact_nonce'] ), 'glm_contact' ) ) {
		glm_contact_redirect( 'erreur' );
	}
	// Champ piège : rempli uniquement par les robots. On fait comme si tout allait bien.
	if ( ! empty( $_POST['site_web'] ) ) {
		glm_contact_redirect( 'ok' );
	}

	$nom     = isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '';
	$societe = isset( $_POST['societe'] ) ? sanitize_text_field( wp_unslash( $_POST['societe'] ) ) : '';
	$tel     = isset( $_POST['tel'] ) ? sanitize_text_field( wp_unslash( $_POST['tel'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$brut    = isset( $_POST['email'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['email'] ) ) ) : '';
	$type    = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
	$modele  = isset( $_POST['modele'] ) ? sanitize_text_field( wp_unslash( $_POST['modele'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$manque = array();
	if ( '' === $nom ) {
		$manque[] = 'nom';
	}
	if ( '' !== $brut && ! is_email( $email ) ) {
		$manque[] = 'email';
	} elseif ( '' === $tel && '' === $email ) {
		$manque[] = 'joindre';
	}
	if ( '' === $message ) {
		$manque[] = 'message';
	}
	if ( $manque ) {
		glm_contact_redirect( 'incomplet', $manque );
	}

	// Photos jointes : images uniquement, 5 fichiers de 8 Mo au maximum.
	$attachments = array();
	if ( ! empty( $_FILES['photos'] ) && is_array( $_FILES['photos']['name'] ) ) {
		$allowed = array( 'image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif' );
		$count   = min( GLM_PHOTOS_MAX, count( $_FILES['photos']['name'] ) );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( UPLOAD_ERR_OK !== (int) $_FILES['photos']['error'][ $i ] ) {
				continue;
			}
			$tmp  = $_FILES['photos']['tmp_name'][ $i ]; // phpcs:ignore
			$name = sanitize_file_name( wp_unslash( $_FILES['photos']['name'][ $i ] ) );
			if ( ! is_uploaded_file( $tmp ) || (int) $_FILES['photos']['size'][ $i ] > GLM_PHOTO_POIDS_MAX ) {
				continue;
			}
			$check = wp_check_filetype_and_ext( $tmp, $name );
			$mime  = $check['type'] ? $check['type'] : (string) mime_content_type( $tmp );
			if ( in_array( $mime, $allowed, true ) ) {
				$attachments[ $name ] = $tmp;
			}
		}
	}

	$types   = glm_contact_types();
	$to      = glm_opt( 'email', get_option( 'admin_email' ) );
	$subject = sprintf( '[GLM Polyester] Demande de devis : %s', $nom );
	$lines   = array(
		'Nom : ' . $nom,
		'Société ou collectivité : ' . ( $societe ? $societe : '-' ),
		'Téléphone : ' . ( $tel ? $tel : '-' ),
		'E-mail : ' . ( $email ? $email : '-' ),
		'Type de demande : ' . ( isset( $types[ $type ] ) ? $types[ $type ] : '-' ),
		'Modèle concerné : ' . ( $modele ? $modele : '-' ),
		'',
		'Message :',
		$message,
		'',
		count( $attachments ) ? count( $attachments ) . ' photo(s) en pièce jointe.' : 'Aucune photo jointe.',
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $email ) {
		$headers[] = 'Reply-To: ' . $nom . ' <' . $email . '>';
	}

	$sent = wp_mail( $to, $subject, implode( "\n", $lines ), $headers, $attachments );
	glm_contact_redirect( $sent ? 'ok' : 'erreur' );
}

/** Champs signalés par le serveur après un envoi incomplet. */
function glm_contact_champs_en_erreur() {
	if ( ! isset( $_GET['contact'] ) || 'incomplet' !== sanitize_key( $_GET['contact'] ) || empty( $_GET['champs'] ) ) { // phpcs:ignore
		return array();
	}
	$champs = array_map( 'sanitize_key', explode( ',', sanitize_text_field( wp_unslash( $_GET['champs'] ) ) ) ); // phpcs:ignore
	return array_values( array_intersect( $champs, array( 'nom', 'joindre', 'email', 'message' ) ) );
}

/** Messages d'erreur, par champ. */
function glm_contact_messages() {
	return array(
		'nom'     => 'Indiquez votre nom.',
		'joindre' => 'Indiquez un téléphone ou un e-mail pour que nous puissions vous répondre.',
		'email'   => 'Cette adresse e-mail n\'est pas valide (exemple : nom@domaine.fr).',
		'message' => 'Décrivez votre demande en quelques mots.',
	);
}

/** Message affiché au-dessus du formulaire après l'envoi. */
function glm_contact_notice() {
	$status = isset( $_GET['contact'] ) ? sanitize_key( $_GET['contact'] ) : ''; // phpcs:ignore
	if ( 'ok' === $status ) {
		$texte = glm_t( 'contact_ok' );
		$point = strpos( $texte, '.' );
		$debut = false !== $point ? substr( $texte, 0, $point + 1 ) : $texte;
		$fin   = false !== $point ? substr( $texte, $point + 1 ) : '';
		echo '<div class="avis avis--ok" role="status"><strong>' . esc_html( $debut ) . '</strong>' . esc_html( $fin ) . '</div>';
	} elseif ( 'incomplet' === $status ) {
		$champs   = glm_contact_champs_en_erreur();
		$messages = glm_contact_messages();
		echo '<div class="avis avis--erreur" role="alert"><strong>Il manque une information.</strong>';
		if ( $champs ) {
			echo '<ul>';
			foreach ( $champs as $champ ) {
				echo '<li>' . esc_html( $messages[ $champ ] ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo ' Indiquez votre nom, un téléphone ou un e-mail valide, et votre message.';
		}
		echo '</div>';
	} elseif ( 'trop-lourd' === $status ) {
		echo '<div class="avis avis--erreur" role="alert"><strong>Les photos sont trop lourdes pour être envoyées.</strong> Réessayez avec moins de photos, ou envoyez votre demande sans photo : nous vous les demanderons ensuite.</div>';
	} elseif ( 'erreur' === $status ) {
		echo '<div class="avis avis--erreur" role="alert"><strong>L\'envoi n\'a pas fonctionné.</strong> Réessayez dans un instant' . ( glm_opt( 'telephone' ) ? ', ou appelez-nous au ' . esc_html( glm_opt( 'telephone' ) ) : '' ) . '.</div>';
	}
}
