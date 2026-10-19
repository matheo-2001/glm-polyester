<?php
/**
 * Textes du site modifiables dans Apparence > Personnaliser > GLM Polyester.
 * Chaque texte a une valeur par défaut : tant que rien n'est saisi, c'est elle qui s'affiche.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registre des textes : section => array( titre de la section, champs ).
 * Champ : clé => array( libellé, valeur par défaut, type « text » ou « textarea », aide ).
 */
function glm_textes() {
	static $textes = null;
	if ( null !== $textes ) {
		return $textes;
	}
	$textes = array(
		'glm_txt_accueil'   => array( 'Accueil : textes', array(
			'publics_titre'           => array( 'Bandeau des clients : titre', 'Nous travaillons pour', 'text', '' ),
			'publics'                 => array( 'Bandeau des clients : liste', "les forains\nles parcs de loisirs\nles collectivités\nles particuliers", 'textarea', 'Un client par ligne.' ),
			'metier_1_titre'          => array( 'Métier 1 : titre', 'Fabrication', 'text', '' ),
			'metier_1_texte'          => array( 'Métier 1 : texte', 'Manèges complets, sujets, pièces en polyester et pièces thermoformées.', 'textarea', '' ),
			'metier_2_titre'          => array( 'Métier 2 : titre', 'Personnalisable', 'text', '' ),
			'metier_2_texte'          => array( 'Métier 2 : texte', 'Sujets roulants et volants : couleurs au choix, décor peint ou covering.', 'textarea', '' ),
			'metier_3_titre'          => array( 'Métier 3 : titre', 'Réparation', 'text', '' ),
			'metier_3_texte'          => array( 'Métier 3 : texte', 'Pièces cassées ou fissurées remises en état, pour repartir en saison.', 'textarea', '' ),
			'metier_4_titre'          => array( 'Métier 4 : titre', 'Rénovation', 'text', '' ),
			'metier_4_texte'          => array( 'Métier 4 : texte', 'Sujets et manèges anciens remis à neuf : structure, peinture, éclairage.', 'textarea', '' ),
			'accueil_catalogue_titre' => array( 'Catalogue : titre', 'Le catalogue', 'text', '' ),
			'perso_titre'             => array( 'Personnalisable : titre', 'Personnalisable', 'text', '' ),
			'perso_texte'             => array( 'Personnalisable : texte', 'Nos sujets roulants et volants se personnalisent : couleurs au choix, décor peint ou covering. Dites-nous vos couleurs, nous chiffrons le sujet.', 'textarea', '' ),
			'perso_note'              => array( 'Personnalisable : remarque', 'Pour les sujets volants, le parallélogramme est fourni par le client.', 'textarea', 'Laissez vide pour masquer.' ),
			'perso_1_titre'           => array( 'Choix 1 : titre', 'Couleurs', 'text', '' ),
			'perso_1_texte'           => array( 'Choix 1 : texte', 'La teinte de votre choix sur toute la caisse.', 'text', '' ),
			'perso_2_titre'           => array( 'Choix 2 : titre', 'Décor peint', 'text', '' ),
			'perso_2_texte'           => array( 'Choix 2 : texte', 'Motifs peints à la main ou à l\'aérographe.', 'text', '' ),
			'perso_3_titre'           => array( 'Choix 3 : titre', 'Covering', 'text', '' ),
			'perso_3_texte'           => array( 'Choix 3 : texte', 'Un film imprimé à vos couleurs.', 'text', '' ),
			'perso_lien'              => array( 'Personnalisable : lien', 'Parler de votre projet', 'text', '' ),
			'realisations_titre'      => array( 'Réalisations : titre', 'Sortis de l\'atelier', 'text', '' ),
		) ),
		'glm_txt_catalogue' => array( 'Catalogue et fiches', array(
			'catalogue_intro'        => array( 'Introduction du catalogue', 'Manèges, sujets roulants et volants, pièces en polyester et pièces thermoformées. Les sujets roulants et volants sont personnalisables : couleurs, décor peint ou covering.', 'textarea', '' ),
			'badge_explication'      => array( 'Explication de l\'étiquette « Sujet GLM »', 'Les modèles marqués « Sujet GLM » sont fabriqués dans notre atelier de Breteuil.', 'textarea', 'Affichée sous l\'introduction du catalogue. Laissez vide pour masquer.' ),
			'badge_court'            => array( 'Étiquette sur les cartes', 'Sujet GLM', 'text', '' ),
			'badge_long'             => array( 'Étiquette sur les fiches', 'Sujet GLM, fabriqué à Breteuil', 'text', '' ),
			'catalogue_groupe_titre' => array( 'Titre du groupe des petites familles', 'Pièces, luminaires et équipements', 'text', '' ),
			'perso_fiche_texte'      => array( 'Fiche : texte « Personnalisable »', 'Couleurs au choix, décor peint ou covering.', 'text', '' ),
			'perso_fiche_volant'     => array( 'Fiche : remarque des sujets volants', 'Le parallélogramme est fourni par le client.', 'text', '' ),
			'noel_fiche_titre'       => array( 'Fiche Noël : titre de l\'encadré', 'Options', 'text', '' ),
			'noel_fiche_texte'       => array( 'Fiche Noël : texte de l\'encadré', 'Bruiteur, marche et éclairage peuvent être ajoutés à ce sujet.', 'text', '' ),
			'fiche_meme_famille'     => array( 'Fiche : titre des autres modèles', 'Dans la même famille', 'text', '' ),
			'vide_famille'           => array( 'Famille sans modèle', 'Les modèles de cette famille arrivent bientôt.', 'text', '' ),
		) ),
		'glm_txt_services'  => array( 'Page Services', array(
			'services_intro'           => array( 'Introduction', 'Fabrication, personnalisation, thermoformage, réparation et rénovation : tout se fait à l\'atelier, pour les forains, les parcs, les collectivités et les particuliers.', 'textarea', '' ),
			'service_fabrication_titre' => array( 'Fabrication : titre', 'Fabrication', 'text', '' ),
			'service_fabrication_texte' => array( 'Fabrication : texte', 'Manèges complets, sujets roulants et volants, pièces en polyester, à partir des modèles du catalogue.', 'textarea', '' ),
			'service_fabrication_plus'  => array( 'Fabrication : texte en gras', 'Vous avez votre propre moule ? Nous pouvons aussi réaliser vos pièces à partir de celui-ci.', 'textarea', 'Laissez vide pour masquer.' ),
			'service_fabrication_lien'  => array( 'Fabrication : lien', 'Voir le catalogue', 'text', '' ),
			'service_personnalisable_titre' => array( 'Personnalisable : titre', 'Personnalisable', 'text', '' ),
			'service_personnalisable_texte' => array( 'Personnalisable : texte', 'Nos sujets roulants et volants se personnalisent entièrement : couleurs au choix, décor peint ou covering. Pour les sujets de Noël, nous ajoutons bruiteur, marche et éclairage.', 'textarea', '' ),
			'service_personnalisable_plus'  => array( 'Personnalisable : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_personnalisable_lien'  => array( 'Personnalisable : lien', 'Parler de votre projet', 'text', '' ),
			'service_thermoformage_titre' => array( 'Thermoformage : titre', 'Thermoformage', 'text', '' ),
			'service_thermoformage_texte' => array( 'Thermoformage : texte', 'Pièces plastiques formées à chaud sur moule : cabochons, optiques, caches et pièces diverses pour sujets et manèges.', 'textarea', '' ),
			'service_thermoformage_plus'  => array( 'Thermoformage : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_thermoformage_lien'  => array( 'Thermoformage : lien', 'Voir les pièces', 'text', '' ),
			'service_reparation_titre' => array( 'Réparation : titre', 'Réparation', 'text', '' ),
			'service_reparation_texte' => array( 'Réparation : texte', 'Pièce cassée, fissurée ou usée : nous la remettons en état, quelle que soit son origine, pour que le manège reparte en saison.', 'textarea', '' ),
			'service_reparation_plus'  => array( 'Réparation : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_reparation_lien'  => array( 'Réparation : lien', 'Envoyer une photo de la pièce', 'text', '' ),
			'service_renovation_titre' => array( 'Rénovation : titre', 'Rénovation', 'text', '' ),
			'service_renovation_texte' => array( 'Rénovation : texte', 'Sujets et manèges anciens remis à neuf : reprise de la structure, nouvelle peinture, nouveau décor, éclairage modernisé.', 'textarea', '' ),
			'service_renovation_plus'  => array( 'Rénovation : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_renovation_lien'  => array( 'Rénovation : lien', 'Voir des réalisations', 'text', '' ),
			'process_titre'            => array( 'Déroulement : titre', 'Comment ça se passe', 'text', '' ),
			'process_1_titre'          => array( 'Étape 1 : titre', 'Votre demande', 'text', '' ),
			'process_1_texte'          => array( 'Étape 1 : texte', 'Par téléphone ou par le formulaire, avec photos si possible.', 'text', '' ),
			'process_2_titre'          => array( 'Étape 2 : titre', 'Le devis', 'text', '' ),
			'process_2_texte'          => array( 'Étape 2 : texte', 'Nous chiffrons le travail et le délai.', 'text', '' ),
			'process_3_titre'          => array( 'Étape 3 : titre', 'Le travail à l\'atelier', 'text', '' ),
			'process_3_texte'          => array( 'Étape 3 : texte', 'Fabrication, personnalisation, thermoformage, réparation ou rénovation.', 'text', '' ),
			'process_4_titre'          => array( 'Étape 4 : titre', 'La remise', 'text', '' ),
			'process_4_texte'          => array( 'Étape 4 : texte', 'Nous convenons ensemble de la remise de la pièce.', 'text', '' ),
		) ),
		'glm_txt_atelier'   => array( 'Page L\'atelier et étapes', array(
			'atelier_intro'       => array( 'Introduction', 'GLM Polyester est un atelier de construction de manèges installé à Breteuil, dans l\'Oise.', 'textarea', '' ),
			'atelier_titre'       => array( 'Titre à côté de la photo', 'Un savoir-faire de constructeur', 'text', '' ),
			'etapes_titre'        => array( 'Étapes : titre (accueil et atelier)', 'De la forme brute à la pièce finie', 'text', '' ),
			'etape_1_titre'       => array( 'Étape 1 : titre', 'Choix du modèle et moulage', 'text', '' ),
			'etape_1_texte'       => array( 'Étape 1 : texte', 'Le client choisit son modèle, puis la pièce est moulée.', 'text', '' ),
			'etape_2_titre'       => array( 'Étape 2 : titre', 'Thermoformage', 'text', '' ),
			'etape_2_texte'       => array( 'Étape 2 : texte', 'Les pièces plastiques sont formées à chaud sur leur moule.', 'text', '' ),
			'etape_3_titre'       => array( 'Étape 3 : titre', 'Peinture et éclairage', 'text', '' ),
			'etape_3_texte'       => array( 'Étape 3 : texte', 'Décor peint ou covering, puis pose de l\'éclairage.', 'text', '' ),
			'chiffre_lieu_titre'  => array( 'Repère 1 : titre', 'Breteuil', 'text', '' ),
			'chiffre_lieu_texte'  => array( 'Repère 1 : texte', 'Atelier situé dans l\'Oise, {adresse}.', 'text', '{adresse} est remplacé par l\'adresse des coordonnées.' ),
			'chiffre_metiers_titre' => array( 'Repère 3 : titre', '5 métiers', 'text', '' ),
			'chiffre_metiers_texte' => array( 'Repère 3 : texte', 'Fabrication, personnalisation, thermoformage, réparation et rénovation.', 'text', '' ),
		) ),
		'glm_txt_pages'     => array( 'Réalisations, contact et bandeaux', array(
			'realisations_intro' => array( 'Réalisations : introduction', 'Sujets, décors, pièces et rénovations sortis de l\'atelier.', 'textarea', '' ),
			'realisations_vide'  => array( 'Réalisations : galerie vide', 'Les photos de nos réalisations arrivent bientôt.', 'text', '' ),
			'contact_intro'      => array( 'Contact : introduction', 'Décrivez votre projet ou votre pièce à réparer. Plus la demande est précise, plus le devis est rapide.', 'textarea', '' ),
			'contact_ok'         => array( 'Contact : message après envoi', 'Votre demande est bien envoyée. Nous revenons vers vous rapidement.', 'text', '' ),
			'cta_titre'          => array( 'Bandeau rouge : titre', 'Un projet, une réparation, une rénovation ?', 'text', 'Bandeau en bas de la plupart des pages.' ),
			'cta_texte'          => array( 'Bandeau rouge : texte', 'Décrivez votre besoin, nous revenons vers vous avec un devis.', 'text', '' ),
			'cta_catalogue_titre' => array( 'Bandeau du catalogue : titre', 'Un modèle à personnaliser ?', 'text', '' ),
			'cta_catalogue_texte' => array( 'Bandeau du catalogue : texte', 'Nos sujets roulants et volants sont personnalisables : couleurs, décor peint ou covering.', 'text', '' ),
			'cta_noel_titre'     => array( 'Bandeau des sujets de Noël : titre', 'Un sujet de Noël pour votre manège ?', 'text', '' ),
			'cta_noel_texte'     => array( 'Bandeau des sujets de Noël : texte', 'Bruiteur, marche et éclairage peuvent être ajoutés. Dites-nous lequel vous intéresse.', 'text', '' ),
			'cta_flip_titre'     => array( 'Bandeau du catalogue à feuilleter : titre', 'Un sujet vous intéresse ?', 'text', '' ),
			'cta_flip_texte'     => array( 'Bandeau du catalogue à feuilleter : texte', 'Dites-nous lequel et vos couleurs, nous revenons vers vous avec un devis.', 'text', '' ),
		) ),
		'glm_txt_seo'       => array( 'Référencement', array(
			'seo_accueil'      => array( 'Description de l\'accueil pour Google', '', 'textarea', 'Environ 150 caractères. Vide : le texte d\'introduction de l\'accueil est utilisé.' ),
			'seo_catalogue'    => array( 'Description du catalogue', '', 'textarea', 'Vide : l\'introduction du catalogue est utilisée.' ),
			'seo_realisations' => array( 'Description des réalisations', '', 'textarea', 'Vide : l\'introduction des réalisations est utilisée.' ),
			'seo_services'     => array( 'Description de la page Services', '', 'textarea', 'Vide : l\'introduction de la page est utilisée.' ),
			'seo_atelier'      => array( 'Description de la page L\'atelier', '', 'textarea', 'Vide : l\'introduction de la page est utilisée.' ),
			'seo_contact'      => array( 'Description de la page Contact', '', 'textarea', 'Vide : l\'introduction de la page est utilisée.' ),
		) ),
	);
	return $textes;
}

/** Valeur par défaut d'un texte du registre. */
function glm_texte_defaut( $key ) {
	foreach ( glm_textes() as $section ) {
		if ( isset( $section[1][ $key ] ) ) {
			return $section[1][ $key ][1];
		}
	}
	return '';
}

/** Texte modifiable (réglage enregistré, sinon valeur par défaut). */
function glm_t( $key ) {
	$value = get_theme_mod( 'glm_' . $key, null );
	if ( null === $value || false === $value || ( '' === trim( (string) $value ) && ! glm_texte_masquable( $key ) ) ) {
		$value = glm_texte_defaut( $key );
	}
	return str_replace( '{adresse}', glm_opt( 'adresse_1', '46 rue de Montdidier' ), (string) $value );
}

/** Un texte que l'on peut vider pour le masquer (aide « vide pour masquer », ou sans valeur par défaut). */
function glm_texte_masquable( $key ) {
	foreach ( glm_textes() as $section ) {
		if ( isset( $section[1][ $key ] ) ) {
			return '' === $section[1][ $key ][1] || false !== stripos( $section[1][ $key ][3], 'masquer' );
		}
	}
	return true;
}

/** Liste « une valeur par ligne ». */
function glm_t_lignes( $key ) {
	return array_values( array_filter( array_map( 'trim', explode( "\n", glm_t( $key ) ) ) ) );
}
