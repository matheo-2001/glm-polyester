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
		'catalogue'  => 'Un modèle du catalogue',
		'sur-mesure' => 'Un sujet personnalisé',
		'thermoformage' => 'Des pièces thermoformées',
		'reparation' => 'Une réparation',
		'renovation' => 'Une rénovation',
		'autre'      => 'Autre',
	);
}

add_action( 'admin_post_nopriv_glm_contact', 'glm_handle_contact' );
add_action( 'admin_post_glm_contact', 'glm_handle_contact' );

function glm_contact_redirect( $status ) {
	wp_safe_redirect( add_query_arg( 'contact', $status, glm_page_url( 'contact' ) ) . '#formulaire' );
	exit;
}

function glm_handle_contact() {
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
	$type    = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
	$modele  = isset( $_POST['modele'] ) ? sanitize_text_field( wp_unslash( $_POST['modele'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$types = glm_contact_types();
	if ( '' === $nom || '' === $message || ( '' === $tel && '' === $email ) ) {
		glm_contact_redirect( 'incomplet' );
	}
	if ( '' !== $email && ! is_email( $email ) ) {
		glm_contact_redirect( 'incomplet' );
	}

	// Photos jointes : images uniquement, 5 fichiers de 8 Mo au maximum.
	$attachments = array();
	if ( ! empty( $_FILES['photos'] ) && is_array( $_FILES['photos']['name'] ) ) {
		$allowed = array( 'image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif' );
		$count   = min( 5, count( $_FILES['photos']['name'] ) );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( UPLOAD_ERR_OK !== (int) $_FILES['photos']['error'][ $i ] ) {
				continue;
			}
			$tmp  = $_FILES['photos']['tmp_name'][ $i ]; // phpcs:ignore
			$name = sanitize_file_name( wp_unslash( $_FILES['photos']['name'][ $i ] ) );
			if ( ! is_uploaded_file( $tmp ) || (int) $_FILES['photos']['size'][ $i ] > 8 * MB_IN_BYTES ) {
				continue;
			}
			$check = wp_check_filetype_and_ext( $tmp, $name );
			$mime  = $check['type'] ? $check['type'] : (string) mime_content_type( $tmp );
			if ( in_array( $mime, $allowed, true ) ) {
				$attachments[ $name ] = $tmp;
			}
		}
	}

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

/** Message affiché au-dessus du formulaire après l'envoi. */
function glm_contact_notice() {
	$status = isset( $_GET['contact'] ) ? sanitize_key( $_GET['contact'] ) : ''; // phpcs:ignore
	if ( 'ok' === $status ) {
		echo '<div class="avis avis--ok" role="status"><strong>Votre demande est bien envoyée.</strong> Nous revenons vers vous rapidement.</div>';
	} elseif ( 'incomplet' === $status ) {
		echo '<div class="avis avis--erreur" role="alert"><strong>Il manque une information.</strong> Indiquez votre nom, un téléphone ou un e-mail valide, et votre message.</div>';
	} elseif ( 'erreur' === $status ) {
		echo '<div class="avis avis--erreur" role="alert"><strong>L\'envoi n\'a pas fonctionné.</strong> Réessayez, ou contactez-nous par téléphone.</div>';
	}
}
