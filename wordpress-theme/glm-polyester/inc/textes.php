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
			'perso_1_texte'           => array( 'Choix 1 : texte', 'Les teintes de votre choix, unies, en dégradé ou pailletées.', 'text', '' ),
			'perso_2_titre'           => array( 'Choix 2 : titre', 'Décor peint', 'text', '' ),
			'perso_2_texte'           => array( 'Choix 2 : texte', 'Motifs, noms et numéros peints à la main ou à l\'aérographe.', 'text', '' ),
			'perso_3_titre'           => array( 'Choix 3 : titre', 'Éclairage', 'text', '' ),
			'perso_3_texte'           => array( 'Choix 3 : texte', 'Leds et feux intégrés au sujet.', 'text', '' ),
			'perso_lien'              => array( 'Personnalisable : lien', 'Parler de votre projet', 'text', '' ),
			'realisations_titre'      => array( 'Réalisations : titre', 'Sortis de l\'atelier', 'text', '' ),
		) ),
		'glm_txt_catalogue' => array( 'Catalogue et fiches', array(
			'catalogue_intro'        => array( 'Introduction du catalogue', 'Manèges, sujets roulants et volants, pièces en polyester et pièces thermoformées. Les sujets roulants et volants sont personnalisables : couleurs, décor peint ou covering.', 'textarea', '' ),
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
			'service_fabrication_court' => array( 'Fabrication : texte court (listes des services)', 'Sujets, manèges et pièces neufs', 'text', '' ),
			'service_fabrication_plus'  => array( 'Fabrication : texte en gras', 'Vous avez votre propre moule ? Nous pouvons aussi réaliser vos pièces à partir de celui-ci.', 'textarea', 'Laissez vide pour masquer.' ),
			'service_fabrication_lien'  => array( 'Fabrication : lien', 'Voir le catalogue', 'text', '' ),
			'service_fabrication_devis' => array( 'Fabrication : ce qu\'il faut nous envoyer', "Le modèle qui vous intéresse (nom ou photo du catalogue)\nLe nombre de sujets\nLes couleurs souhaitées\nVotre délai\nLe lieu de livraison", 'textarea', 'Page détaillée du service. Un élément par ligne. Laissez vide pour masquer.' ),
			'service_fabrication_faq'   => array( 'Fabrication : questions fréquentes', "Quels modèles proposez-vous ?\nSujets roulants, sujets volants, manèges enfantins complets et pièces en polyester : tous les modèles sont présentés dans le catalogue, à consulter en ligne ou à télécharger en PDF.\n\nLe circuit électrique est-il protégé ?\nOui : sur nos sujets, le circuit électrique est noyé dans le polyester.\n\nLes sujets volants sont-ils livrés avec leur parallélogramme ?\nNon : le parallélogramme est fourni par le client.\n\nOù se trouve l'atelier ?\nÀ Breteuil, dans l'Oise : {adresse}, 60120 Breteuil.\n\nPouvez-vous fabriquer à partir de notre moule ?\nOui. Si vous avez votre propre moule, nous pouvons réaliser vos pièces à partir de celui-ci.\n\nLes sujets sont-ils personnalisables ?\nOui, les sujets roulants et volants : couleurs au choix, décor peint ou covering.\n\nQuel est le délai de fabrication ?\n\n\nLivrez-vous les sujets ?\n", 'textarea', 'Page détaillée du service. La question sur une ligne, la réponse en dessous, une ligne vide entre deux questions. Une question sans réponse n\'est pas affichée.' ),
			'service_personnalisable_titre' => array( 'Personnalisable : titre', 'Personnalisable', 'text', '' ),
			'service_personnalisable_texte' => array( 'Personnalisable : texte', 'Nos sujets roulants et volants se personnalisent entièrement : couleurs au choix, décor peint ou covering. Pour les sujets de Noël, nous ajoutons bruiteur, marche et éclairage.', 'textarea', '' ),
			'service_personnalisable_court' => array( 'Personnalisable : texte court (listes des services)', 'Couleurs, décor, éclairage', 'text', '' ),
			'service_personnalisable_plus'  => array( 'Personnalisable : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_personnalisable_lien'  => array( 'Personnalisable : lien', 'Parler de votre projet', 'text', '' ),
			'service_personnalisable_devis' => array( 'Personnalisable : ce qu\'il faut nous envoyer', "Le modèle choisi\nVos couleurs (référence ou photo)\nLe décor souhaité : dessin, photo ou idée\nLe nombre de sujets\nVotre délai", 'textarea', 'Page détaillée du service. Un élément par ligne. Laissez vide pour masquer.' ),
			'service_personnalisable_faq'   => array( 'Personnalisable : questions fréquentes', "Proposez-vous le covering ?\nOui : un film imprimé à vos couleurs peut être posé sur la caisse du sujet.\n\nPeut-on mettre le nom de notre manège sur les sujets ?\nOui : le décor peint peut reprendre un nom, un numéro ou un thème.\n\nPeut-on décorer tout le manège dans le même thème ?\nOui : panneaux, frontons, barrières et plateaux peuvent recevoir le même décor que les sujets.\n\nQuels sujets peut-on personnaliser ?\nLes sujets roulants et volants du catalogue.\n\nLe parallélogramme des sujets volants est-il fourni ?\nNon : pour les sujets volants, le parallélogramme est fourni par le client.\n\nPouvez-vous équiper un sujet de Noël ?\nOui : nous ajoutons bruiteur, marche et éclairage.\n\nPeut-on voir le projet de décor avant la peinture ?\n", 'textarea', 'Page détaillée du service. La question sur une ligne, la réponse en dessous, une ligne vide entre deux questions. Une question sans réponse n\'est pas affichée.' ),
			'service_thermoformage_titre' => array( 'Thermoformage : titre', 'Thermoformage', 'text', '' ),
			'service_thermoformage_texte' => array( 'Thermoformage : texte', 'Pièces plastiques formées à chaud sur moule : cabochons, optiques, caches et pièces diverses pour sujets et manèges.', 'textarea', '' ),
			'service_thermoformage_court' => array( 'Thermoformage : texte court (listes des services)', 'Cabochons, optiques, caches', 'text', '' ),
			'service_thermoformage_plus'  => array( 'Thermoformage : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_thermoformage_lien'  => array( 'Thermoformage : lien', 'Voir les pièces', 'text', '' ),
			'service_thermoformage_devis' => array( 'Thermoformage : ce qu\'il faut nous envoyer', "Une photo de la pièce\nSes dimensions\nLa couleur et la quantité\nLa référence de la pièce, si vous l'avez", 'textarea', 'Page détaillée du service. Un élément par ligne. Laissez vide pour masquer.' ),
			'service_thermoformage_faq'   => array( 'Thermoformage : questions fréquentes', "Qu'est-ce que le thermoformage ?\nUn procédé qui consiste à chauffer une plaque de plastique, puis à la former sur un moule.\n\nEn quelles couleurs sont les cabochons ?\nEn plusieurs teintes : rouge, orange, jaune, vert, bleu et transparent.\n\nQue faut-il envoyer pour un devis ?\nUne photo de la pièce, ses dimensions, la couleur et la quantité, et sa référence si vous l'avez.\n\nQuelles pièces réalisez-vous en thermoformage ?\nCabochons, optiques, caches et pièces diverses pour sujets et manèges.\n\nFaites-vous des petites quantités ?\n\n\nPouvez-vous refaire une pièce qui n'existe plus ?\n", 'textarea', 'Page détaillée du service. La question sur une ligne, la réponse en dessous, une ligne vide entre deux questions. Une question sans réponse n\'est pas affichée.' ),
			'service_reparation_titre' => array( 'Réparation : titre', 'Réparation', 'text', '' ),
			'service_reparation_texte' => array( 'Réparation : texte', 'Pièce cassée, fissurée ou usée : nous la remettons en état, quelle que soit son origine, pour que le manège reparte en saison.', 'textarea', '' ),
			'service_reparation_court' => array( 'Réparation : texte court (listes des services)', 'Sujets et pièces de toutes marques', 'text', '' ),
			'service_reparation_plus'  => array( 'Réparation : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_reparation_lien'  => array( 'Réparation : lien', 'Envoyer une photo de la pièce', 'text', '' ),
			'service_reparation_devis' => array( 'Réparation : ce qu\'il faut nous envoyer', "Des photos de la pièce : vue d'ensemble et gros plan sur le défaut\nSes dimensions\nLa marque ou le modèle du sujet, si vous le connaissez\nVotre délai (date de reprise de la saison)", 'textarea', 'Page détaillée du service. Un élément par ligne. Laissez vide pour masquer.' ),
			'service_reparation_faq'   => array( 'Réparation : questions fréquentes', "Peut-on réparer un sujet fissuré ou cassé ?\nOui : le polyester fissuré ou cassé peut être repris, renforcé, puis repeint.\n\nLe décor est-il refait après la réparation ?\nSi besoin, oui : peinture et décor sont refaits pour que la réparation ne se voie pas.\n\nRemplacez-vous les cabochons et les optiques cassés ?\nOui : nous réalisons des cabochons, des feux et des optiques thermoformés.\n\nRéparez-vous les sujets d'autres fabricants ?\nOui : nous remettons la pièce en état, quelle que soit son origine.\n\nComment obtenir un devis ?\nEnvoyez-nous des photos de la pièce avec le formulaire de devis : nous revenons vers vous avec un devis.\n\nFaut-il apporter la pièce à l'atelier ?\n\n\nCombien de temps dure une réparation ?\n", 'textarea', 'Page détaillée du service. La question sur une ligne, la réponse en dessous, une ligne vide entre deux questions. Une question sans réponse n\'est pas affichée.' ),
			'service_renovation_titre' => array( 'Rénovation : titre', 'Rénovation', 'text', '' ),
			'service_renovation_texte' => array( 'Rénovation : texte', 'Sujets et manèges anciens remis à neuf : reprise de la structure, nouvelle peinture, nouveau décor, éclairage modernisé.', 'textarea', '' ),
			'service_renovation_court' => array( 'Rénovation : texte court (listes des services)', 'Remise à neuf des sujets et manèges', 'text', '' ),
			'service_renovation_plus'  => array( 'Rénovation : texte en gras', '', 'textarea', 'Laissez vide pour masquer.' ),
			'service_renovation_lien'  => array( 'Rénovation : lien', 'Voir des réalisations', 'text', '' ),
			'service_renovation_devis' => array( 'Rénovation : ce qu\'il faut nous envoyer', "Des photos du sujet ou du manège, sous plusieurs angles\nCe que vous souhaitez refaire : structure, peinture, décor, éclairage\nVos idées de couleurs ou de décor\nVotre délai", 'textarea', 'Page détaillée du service. Un élément par ligne. Laissez vide pour masquer.' ),
			'service_renovation_faq'   => array( 'Rénovation : questions fréquentes', "Peut-on garder les couleurs d'origine ?\nOui : le sujet peut être repeint dans ses couleurs d'origine ou dans de nouvelles couleurs.\n\nL'éclairage peut-il être modernisé ?\nOui : nouvel éclairage, nouveaux cabochons et nouvelles optiques.\n\nPouvez-vous rénover tout un manège ?\nOui : un sujet seul, plusieurs sujets ou un ensemble complet avec ses panneaux et ses décors.\n\nQue comprend une rénovation ?\nSelon l'état du sujet : reprise de la structure, nouvelle peinture, nouveau décor et éclairage modernisé.\n\nPeut-on changer complètement le décor ?\nOui, la rénovation peut comprendre un nouveau décor.\n\nCombien de temps dure une rénovation ?\n", 'textarea', 'Page détaillée du service. La question sur une ligne, la réponse en dessous, une ligne vide entre deux questions. Une question sans réponse n\'est pas affichée.' ),
			'services_devis_titre'     => array( 'Liste « pour un devis » : titre', 'Pour un devis rapide, envoyez-nous', 'text', '' ),
			'services_devis'           => array( 'Liste « pour un devis » : page Services', "Des photos du sujet, de la pièce ou du manège\nLes dimensions\nVos couleurs ou votre idée de décor\nLa quantité\nVotre délai", 'textarea', 'Un élément par ligne. Laissez vide pour masquer.' ),
			'service_plus_lien'        => array( 'Lien vers la page détaillée', 'En savoir plus', 'text', '' ),
			'service_photos_titre'     => array( 'Page détaillée : titre des photos', 'Exemples sortis de l\'atelier', 'text', '' ),
			'service_faq_titre'        => array( 'Page détaillée : titre des questions', 'Questions fréquentes', 'text', '' ),
			'service_atelier_titre'    => array( 'Page détaillée : titre du paragraphe sur l\'atelier', 'Un atelier dans l\'Oise', 'text', '' ),
			'service_atelier_texte'    => array( 'Page détaillée : paragraphe sur l\'atelier', "Notre atelier est installé à Breteuil, dans l'Oise ({adresse}, 60120 Breteuil), entre Beauvais et Amiens. Les devis se préparent à distance, à partir de vos photos : où que se trouve votre manège, envoyez-nous votre demande.", 'textarea', 'Laissez vide pour masquer.' ),
			'service_autres_titre'     => array( 'Page détaillée : titre des autres services', 'Nos autres services', 'text', '' ),
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
		) ),
		'glm_txt_atelier_plus' => array( 'Page L\'atelier : contenu détaillé', array(
			'atelier_metier_titre'    => array( 'Notre métier : titre', 'Constructeur de manèges et de sujets en polyester', 'text', '' ),
			'atelier_metier_texte'    => array( 'Notre métier : texte', "GLM Polyester est un atelier de construction de manèges installé à Breteuil, dans l'Oise, entre Beauvais et Amiens. Nous fabriquons, personnalisons, réparons et rénovons des sujets de manège, des manèges enfantins et des pièces en polyester, et nous réalisons des pièces thermoformées.\n\nTout se fait à l'atelier : le moulage des pièces, le thermoformage, la peinture et le décor, la pose de l'éclairage. Nous suivons ainsi chaque sujet du début à la fin, qu'il s'agisse d'une commande de sujets neufs ou de la réparation d'une seule pièce.\n\nNos sujets sont faits pour le travail en fête foraine et en parc : des journées entières de rotation, les montages et démontages, les transports et les intempéries.", 'textarea', 'Une ligne vide entre deux paragraphes. Laissez vide pour masquer.' ),
			'atelier_savoirs_titre'   => array( 'Savoir-faire : titre', 'Nos savoir-faire', 'text', '' ),
			'atelier_savoirs'         => array( 'Savoir-faire : liste', "Moulage polyester : caisses de sujets, carénages et éléments de décor, moulés à l'atelier.\nThermoformage : cabochons, optiques, feux et caches formés à chaud sur moule.\nPeinture et décor : couleurs unies, dégradés et paillettes, décors peints à la main ou à l'aérographe, covering.\nÉclairage : leds, feux, phares et clignotants intégrés aux sujets, en 12 ou 24 V.\nMécanique : pièces de rotation, collecteurs électriques, équipements de sécurité.\nRéparation et rénovation : reprise du polyester, renforcement, remise en peinture et nouveau décor.", 'textarea', 'Un savoir-faire par ligne, sous la forme « Titre : texte ». Laissez vide pour masquer.' ),
			'atelier_clients_titre'   => array( 'Clients : titre', 'Pour qui travaillons-nous ?', 'text', '' ),
			'atelier_clients'         => array( 'Clients : liste', "Forains : sujets neufs, réparations entre deux saisons, rénovation de manèges qui ont beaucoup tourné.\nParcs de loisirs : sujets, pièces et décors pour des manèges qui tournent toute la saison.\nCollectivités : manèges de fête, de place ou de marché de Noël.\nParticuliers : un sujet à restaurer, une pièce à remplacer, un projet de manège.", 'textarea', 'Un client par ligne, sous la forme « Titre : texte ». Laissez vide pour masquer.' ),
			'atelier_services_titre'  => array( 'Services : titre', 'Nos services', 'text', '' ),
			'atelier_photos_titre'    => array( 'Photos : titre', 'Sortis de l\'atelier', 'text', '' ),
			'atelier_faq_titre'       => array( 'Questions : titre', 'Questions fréquentes', 'text', '' ),
			'atelier_faq'             => array( 'Questions fréquentes', "Où se trouve l'atelier GLM Polyester ?\nÀ Breteuil, dans l'Oise : {adresse}, 60120 Breteuil.\n\nQue fabrique GLM Polyester ?\nDes sujets de manège roulants et volants, des manèges enfantins complets, des pièces en polyester et des pièces thermoformées.\n\nLes sujets sont-ils personnalisables ?\nOui, les sujets roulants et volants : couleurs au choix, décor peint ou covering.\n\nRéparez-vous les manèges d'autres marques ?\nOui : nous remettons les pièces en état, quelle que soit leur origine.\n\nComment demander un devis ?\nPar le formulaire de devis, avec des photos si possible, ou par téléphone.\n\nPeut-on visiter l'atelier ?\n", 'textarea', 'La question sur une ligne, la réponse en dessous, une ligne vide entre deux questions. Une question sans réponse n\'est pas affichée.' ),
		) ),
		'glm_txt_pages'     => array( 'Réalisations, contact et bandeaux', array(
			'realisations_intro' => array( 'Réalisations : introduction', 'Sujets, décors, pièces et rénovations sortis de l\'atelier.', 'textarea', '' ),
			'realisations_vide'  => array( 'Réalisations : galerie vide', 'Les photos de nos réalisations arrivent bientôt.', 'text', '' ),
			'contact_intro'      => array( 'Contact : introduction', 'Décrivez votre projet ou votre pièce à réparer. Plus la demande est précise, plus le devis est rapide.', 'textarea', '' ),
			'contact_ok'         => array( 'Contact : message après envoi', 'Votre demande est bien envoyée. Nous revenons vers vous rapidement.', 'text', '' ),
			'footer_texte'       => array( 'Pied de page : présentation', 'Constructeur de manèges à Breteuil, dans l\'Oise : sujets roulants et volants, pièces en polyester et thermoformées, réparation et rénovation.', 'textarea', '' ),
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
			'seo_titre_services'        => array( 'Titre d\'onglet : page Services', 'Services : fabrication, réparation et rénovation de manèges', 'text', 'Titre affiché dans l\'onglet et dans les résultats de Google (suivi de « – GLM Polyester »).' ),
			'seo_titre_fabrication'     => array( 'Titre d\'onglet : Fabrication', 'Fabrication de sujets de manège en polyester', 'text', '' ),
			'seo_titre_personnalisable' => array( 'Titre d\'onglet : Personnalisable', 'Sujets de manège personnalisables : couleurs, décor, éclairage', 'text', '' ),
			'seo_titre_thermoformage'   => array( 'Titre d\'onglet : Thermoformage', 'Pièces thermoformées pour manèges : cabochons, optiques, caches', 'text', '' ),
			'seo_titre_reparation'      => array( 'Titre d\'onglet : Réparation', 'Réparation de sujets de manège en polyester', 'text', '' ),
			'seo_titre_renovation'      => array( 'Titre d\'onglet : Rénovation', 'Rénovation de sujets et de manèges', 'text', '' ),
			'seo_titre_atelier'         => array( 'Titre d\'onglet : L\'atelier', 'L\'atelier : constructeur de manèges à Breteuil (Oise)', 'text', '' ),
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
