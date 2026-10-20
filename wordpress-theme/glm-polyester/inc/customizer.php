<?php
/**
 * Réglages modifiables dans Apparence > Personnaliser.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', function ( $wp_customize ) {

	$wp_customize->add_panel( 'glm', array(
		'title'    => 'GLM Polyester',
		'priority' => 20,
	) );

	/* ----- Coordonnées ----- */
	$wp_customize->add_section( 'glm_coordonnees', array(
		'title' => 'Coordonnées',
		'panel' => 'glm',
	) );

	$texts = array(
		'telephone' => array( 'Téléphone', '07 60 16 26 39', 'Affiché dans le pied de page, la page contact et les bandeaux de devis.' ),
		'email'     => array( 'E-mail', 'contact@glm-polyester.com', 'Adresse affichée et destinataire des demandes de devis.' ),
		'adresse_1' => array( 'Adresse, ligne 1', '46 rue de Montdidier', '' ),
		'adresse_2' => array( 'Adresse, ligne 2', '60120 Breteuil', '' ),
		'horaires'  => array( 'Horaires', '', 'Exemple : Du lundi au vendredi, 8 h – 18 h' ),
		'facebook'  => array( 'Lien Facebook', 'https://www.facebook.com/glmpolyester/', '' ),
		'siret'     => array( 'SIRET', '977 989 318 00026', '' ),
	);
	foreach ( $texts as $key => $field ) {
		$wp_customize->add_setting( 'glm_' . $key, array(
			'default'           => $field[1],
			'sanitize_callback' => 'facebook' === $key ? 'esc_url_raw' : ( 'email' === $key ? 'sanitize_email' : 'sanitize_text_field' ),
		) );
		$wp_customize->add_control( 'glm_' . $key, array(
			'label'       => $field[0],
			'description' => $field[2],
			'section'     => 'glm_coordonnees',
			'type'        => 'facebook' === $key ? 'url' : 'text',
		) );
	}

	/* ----- Accueil ----- */
	$wp_customize->add_section( 'glm_accueil', array(
		'title' => 'Page d\'accueil',
		'panel' => 'glm',
	) );
	$accueil = array(
		'hero_surtitre' => array( 'Sur-titre', 'Constructeur de manèges · Breteuil, Oise', 'text' ),
		'hero_titre'    => array( 'Titre principal', 'Manèges, sujets et pièces en polyester', 'text' ),
		'hero_texte'    => array( 'Texte d\'introduction', 'GLM Polyester fabrique, répare et rénove des manèges, des sujets et des pièces en polyester, et réalise des pièces thermoformées. Des modèles du catalogue, personnalisables.', 'textarea' ),
	);
	foreach ( $accueil as $key => $field ) {
		$wp_customize->add_setting( 'glm_' . $key, array(
			'default'           => $field[1],
			'sanitize_callback' => 'sanitize_textarea_field',
		) );
		$wp_customize->add_control( 'glm_' . $key, array(
			'label'   => $field[0],
			'section' => 'glm_accueil',
			'type'    => $field[2],
		) );
	}

	/* ----- Photos des sections ----- */
	$wp_customize->add_section( 'glm_photos', array(
		'title'       => 'Photos des sections',
		'description' => 'Sans photo choisie, le site affiche la photo par défaut, ou une mise en page sans photo.',
		'panel'       => 'glm',
	) );
	$photos = array(
		'img_hero'        => 'Accueil : grande photo du haut',
		'img_atelier'     => 'Photo de l\'atelier',
		'img_fabrication' => 'Services : fabrication',
		'img_sur_mesure'  => 'Services : personnalisable',
		'img_thermoformage' => 'Services : thermoformage',
		'img_reparation'  => 'Services : réparation',
		'img_renovation'  => 'Services : rénovation',
		'img_etape_1'     => 'Atelier : étape 1, choix du modèle et moulage',
		'img_etape_2'     => 'Atelier : étape 2, thermoformage',
		'img_etape_3'     => 'Atelier : étape 3, peinture et éclairage',
		'img_perso_1'     => 'Accueil, Personnalisable : photo du choix 1 (couleurs)',
		'img_perso_2'     => 'Accueil, Personnalisable : photo du choix 2 (décor peint)',
		'img_perso_3'     => 'Accueil, Personnalisable : photo du choix 3 (éclairage)',
	);
	foreach ( $photos as $key => $label ) {
		$wp_customize->add_setting( 'glm_' . $key, array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'glm_' . $key, array(
			'label'     => $label,
			'section'   => 'glm_photos',
			'mime_type' => 'image',
		) ) );
	}

	/* ----- Textes des pages (registre inc/textes.php) ----- */
	$priorite = 40;
	foreach ( glm_textes() as $section_id => $section ) {
		$wp_customize->add_section( $section_id, array(
			'title'    => $section[0],
			'panel'    => 'glm',
			'priority' => $priorite++,
		) );
		foreach ( $section[1] as $key => $field ) {
			$wp_customize->add_setting( 'glm_' . $key, array(
				'default'           => $field[1],
				'sanitize_callback' => 'textarea' === $field[2] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			) );
			$wp_customize->add_control( 'glm_' . $key, array(
				'label'       => $field[0],
				'description' => $field[3],
				'section'     => $section_id,
				'type'        => $field[2],
			) );
		}
	}

	/* ----- Réalisations ----- */
	$wp_customize->add_section( 'glm_realisations', array(
		'title' => 'Réalisations',
		'panel' => 'glm',
	) );
	$wp_customize->add_setting( 'glm_licences', array(
		'default'           => '1',
		'sanitize_callback' => function ( $value ) {
			return $value ? '1' : '0';
		},
	) );
	$wp_customize->add_control( 'glm_licences', array(
		'label'       => 'Afficher les photos avec des personnages sous licence',
		'description' => 'Disney, Nintendo, Pat\'Patrouille… Décochez pour retirer ces photos du site d\'un seul coup (elles restent dans l\'administration).',
		'section'     => 'glm_realisations',
		'type'        => 'checkbox',
	) );

} );
