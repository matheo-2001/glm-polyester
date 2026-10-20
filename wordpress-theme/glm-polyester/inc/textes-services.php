<?php
/**
 * Textes de départ des pages détaillées des services (version 2, plus complète pour le référencement).
 * Ils ne servent qu'à la création des pages, ou à remplacer le texte de la version 1 s'il n'a pas été modifié.
 * Ensuite, tout se modifie dans l'éditeur de chaque page (Pages > Services > …).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Petits assembleurs de blocs de l'éditeur. */
function glm_bloc_p( $texte ) {
	return "<!-- wp:paragraph -->\n<p>" . $texte . "</p>\n<!-- /wp:paragraph -->\n\n";
}
function glm_bloc_h( $texte, $niveau = 2 ) {
	$attrs = 2 === $niveau ? '' : ' {"level":' . (int) $niveau . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . (int) $niveau . ' class="wp-block-heading">' . $texte . '</h' . (int) $niveau . ">\n<!-- /wp:heading -->\n\n";
}
function glm_bloc_liste( $items, $numerotee = false ) {
	$tag  = $numerotee ? 'ol' : 'ul';
	$html = $numerotee ? "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">" : "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$html .= "<!-- wp:list-item -->\n<li>" . $item . "</li>\n<!-- /wp:list-item -->";
	}
	return $html . '</' . $tag . ">\n<!-- /wp:list -->\n\n";
}

/** Texte complet (version 2) de la page d'un service. */
function glm_texte_service_v2( $slug ) {
	$p = 'glm_bloc_p';
	$h = 'glm_bloc_h';
	$l = 'glm_bloc_liste';
	switch ( $slug ) {
		case 'fabrication':
			return $p( 'GLM Polyester est un <strong>fabricant de sujets de manège et de pièces en polyester</strong>, installé à Breteuil, dans l\'Oise. Dans notre atelier, nous fabriquons des sujets roulants, des sujets volants, des manèges enfantins complets et des pièces en polyester, à partir des modèles de notre catalogue. Chaque sujet peut ensuite être personnalisé à vos couleurs.' )
				. $h( 'Ce que nous fabriquons' )
				. $h( 'Sujets roulants', 3 )
				. $p( 'Voitures, camions, motos, bateaux, véhicules de pompiers ou de police : les sujets roulants sont la base des manèges enfantins. Selon les modèles, ils accueillent un ou plusieurs enfants, avec volants, klaxon et boutons poussoirs pour le jeu.' )
				. $h( 'Sujets volants', 3 )
				. $p( 'Hélicoptères, vaisseaux et autres sujets qui montent et descendent pendant le tour. Ils sont conçus pour être fixés sur un parallélogramme, fourni par le client.' )
				. $h( 'Manèges enfantins complets', 3 )
				. $p( 'Un manège prêt à tourner, avec son chapiteau, son éclairage et ses sujets.' )
				. $h( 'Pièces en polyester et pièces thermoformées', 3 )
				. $p( 'Caisses, carénages, éléments de décor, cabochons et optiques d\'éclairage : des pièces neuves pour compléter un manège ou remplacer une pièce abîmée.' )
				. $h( 'Une construction faite pour durer' )
				. $p( 'Nos sujets sont construits pour le travail en fête foraine et en parc : des journées entières de rotation, les montages et démontages, les transports et les intempéries. Selon les modèles, on retrouve :' )
				. $l( array(
					'une <strong>monocoque en polyester indéformable</strong> ;',
					'un <strong>circuit électrique noyé dans le polyester</strong>, à l\'abri des chocs et de l\'humidité ;',
					'des feux et des phares branchés en 12 ou 24 V, avec centrale clignotante sur certains modèles ;',
					'un klaxon et des boutons poussoirs pour les enfants ;',
					'un pare-brise en plexi transparent, des accessoires en inox poli ou en ABS chromé ;',
					'pour les sujets roulants : suspension à lames, roues gonflées, moyeux à billes ;',
					'pour les sujets volants : châssis métallique noyé dans le polyester, fixation par parallélogramme, commande de levée mécanique ou par électrovanne 24 V en option.',
				) )
				. $p( 'Le détail de chaque modèle (dimensions, poids, nombre de places, équipements) figure sur sa fiche dans le catalogue.' )
				. $h( 'Pourquoi le polyester ?' )
				. $p( 'Le polyester est léger, solide et facile à entretenir. Il se moule en formes arrondies, idéales pour des sujets de manège, résiste bien aux chocs et aux intempéries, et se répare : une caisse fissurée peut être reprise plutôt que remplacée.' )
				. $h( 'À partir de votre moule' )
				. $p( 'Vous avez votre propre moule ? Nous pouvons aussi réaliser vos pièces à partir de celui-ci. Envoyez-nous des photos du moule et de la pièce attendue, nous vous disons ce qui est possible.' )
				. $h( 'Couleurs, décor et éclairage' )
				. $p( 'Les sujets roulants et volants sont personnalisables : couleurs au choix, décor peint ou covering, éclairage. Vous choisissez le modèle dans le catalogue, nous l\'habillons à vos couleurs.' )
				. $h( 'Pour qui ?' )
				. $p( 'Nous travaillons pour les <strong>forains</strong>, les <strong>parcs de loisirs</strong>, les <strong>collectivités</strong> et les <strong>particuliers</strong> : remplacement d\'un sujet usé, ajout de nouveaux sujets sur un manège existant, ou équipement d\'un manège complet.' )
				. $h( 'Comment commander ?' )
				. $l( array(
					'Choisissez un ou plusieurs modèles dans le catalogue, en ligne ou dans le PDF à télécharger.',
					'Envoyez-nous votre demande par le formulaire de devis ou par téléphone : modèle, quantité, couleurs souhaitées.',
					'Nous vous répondons avec un devis et un délai.',
					'Le sujet est fabriqué à l\'atelier de Breteuil, puis nous convenons ensemble de la remise.',
				), true );
		case 'personnalisable':
			return $p( 'Un manège se reconnaît à ses sujets. Chez GLM Polyester, les <strong>sujets roulants et volants du catalogue sont personnalisables</strong> : vous choisissez le modèle, nous l\'habillons à vos couleurs, avec le décor de votre choix. Votre manège ne ressemble à aucun autre.' )
				. $h( 'Les possibilités de personnalisation' )
				. $h( 'Les couleurs', 3 )
				. $p( 'Teintes unies, dégradés, finitions pailletées : la couleur de la caisse est la première façon de donner une identité à un sujet. Indiquez-nous vos couleurs, ou envoyez-nous une photo ou une référence de teinte.' )
				. $h( 'Le décor peint', 3 )
				. $p( 'Motifs, étoiles, flammes, filets, noms et numéros : le décor est peint à la main ou à l\'aérographe. Il peut reprendre le nom de votre manège, vos couleurs ou un thème (l\'espace, la mer, la jungle, la police, les pompiers…).' )
				. $h( 'Le covering', 3 )
				. $p( 'Un film imprimé à vos couleurs, posé sur la caisse du sujet. Une autre façon d\'obtenir un décor précis.' )
				. $h( 'L\'éclairage', 3 )
				. $p( 'Leds, feux, phares et clignotants intégrés au sujet : l\'éclairage fait vivre le manège, surtout en soirée.' )
				. $h( 'Quels sujets peut-on personnaliser ?' )
				. $p( 'La personnalisation concerne les <strong>sujets roulants</strong> (voitures, camions, motos, bateaux…) et les <strong>sujets volants</strong> (hélicoptères, vaisseaux…) du catalogue. Pour les sujets volants, le parallélogramme est fourni par le client.' )
				. $h( 'Sujets de Noël' )
				. $p( 'Pour les sujets de Noël (traîneau du Père Noël, décors de fêtes), nous ajoutons un <strong>bruiteur</strong>, une <strong>marche</strong> et un <strong>éclairage</strong>, pour un manège prêt pour la saison des fêtes.' )
				. $h( 'Un thème pour tout le manège' )
				. $p( 'La personnalisation peut aller au-delà d\'un seul sujet : panneaux, frontons, barrières, plateaux et cadres peuvent recevoir le même décor, pour un manège cohérent du sol au chapiteau. Vous en trouverez des exemples dans nos réalisations.' )
				. $h( 'Comment se passe un projet personnalisé ?' )
				. $l( array(
					'Vous choisissez le modèle dans le catalogue.',
					'Vous nous envoyez vos couleurs et votre idée de décor : un dessin, une photo, un thème, un nom.',
					'Nous chiffrons le sujet et vous envoyons un devis.',
					'Le sujet est préparé, peint et équipé à l\'atelier de Breteuil.',
				), true );
		case 'thermoformage':
			return $p( 'GLM Polyester réalise des <strong>pièces thermoformées pour les manèges et les sujets</strong> : cabochons d\'éclairage, feux et optiques, caches et carénages. Ces pièces sont formées dans notre atelier de Breteuil, dans l\'Oise.' )
				. $h( 'Qu\'est-ce que le thermoformage ?' )
				. $p( 'Le thermoformage consiste à chauffer une plaque de plastique jusqu\'à ce qu\'elle devienne souple, puis à la plaquer sur un moule. En refroidissant, la plaque garde la forme du moule. Le procédé permet de produire des pièces légères, régulières, transparentes ou colorées, à l\'unité comme en série.' )
				. $h( 'Nos pièces thermoformées' )
				. $h( 'Cabochons d\'éclairage', 3 )
				. $p( 'Les cabochons recouvrent les ampoules et les leds des sujets et des manèges. Nous les réalisons en plusieurs teintes (rouge, orange, jaune, vert, bleu, transparent) et en plusieurs formats.' )
				. $h( 'Feux et optiques', 3 )
				. $p( 'Feux arrière, clignotants, optiques de phares : les pièces qui donnent leur caractère aux sujets roulants, et qu\'il faut souvent remplacer après une saison chargée.' )
				. $h( 'Caches et carénages', 3 )
				. $p( 'Carénages et caches techniques pour protéger un mécanisme, habiller un sujet ou un élément du manège.' )
				. $h( 'Pièces diverses', 3 )
				. $p( 'D\'autres pièces pour les sujets et les manèges, selon vos besoins. Envoyez-nous une photo et les dimensions : nous vous disons ce qui est possible.' )
				. $h( 'Remplacer une pièce cassée ou introuvable' )
				. $p( 'Un cabochon fendu, une optique jaunie, un cache perdu : ces petites pièces abîment l\'aspect d\'un sujet. Le thermoformage permet de les remplacer. Indiquez-nous si vous avez la pièce d\'origine, même cassée : elle aide à préparer la nouvelle.' )
				. $h( 'Pour qui ?' )
				. $p( 'Forains, parcs de loisirs, collectivités et particuliers qui entretiennent ou restaurent un manège. Les pièces disponibles sont présentées dans la famille « Pièces et thermoformage » du catalogue, avec les luminaires.' )
				. $h( 'Demander des pièces' )
				. $l( array(
					'Une photo de la pièce, ou sa référence si vous l\'avez.',
					'Ses dimensions.',
					'La couleur et la quantité.',
				) )
				. $p( 'Nous revenons vers vous avec un devis.' );
		case 'reparation':
			return $p( 'GLM Polyester assure la <strong>réparation de sujets de manège et de pièces en polyester</strong>, quel que soit leur fabricant. Sujet fissuré, caisse cassée, pièce usée : nous remettons en état à l\'atelier de Breteuil, dans l\'Oise, pour que le manège reparte en saison.' )
				. $h( 'Ce que nous réparons' )
				. $l( array(
					'<strong>Sujets en polyester</strong> fissurés, cassés ou enfoncés après un choc ou un transport ;',
					'<strong>pièces en polyester</strong> : caisses, carénages, éléments de décor ;',
					'<strong>pièces usées</strong> à remplacer : pièces de rotation, raccords, ressorts, collecteurs ;',
					'<strong>éclairage</strong> : cabochons, feux et optiques cassés, remplacés par des pièces thermoformées ;',
					'sujets et manèges <strong>de toutes marques</strong>.',
				) )
				. $h( 'Réparer plutôt que remplacer' )
				. $p( 'Le polyester se répare bien : une fissure ou une casse peut être reprise, renforcée, puis repeinte. Réparer un sujet peut revenir moins cher que d\'en acheter un neuf, et permet de garder un manège homogène, avec tous ses sujets d\'origine.' )
				. $h( 'Les étapes d\'une réparation' )
				. $l( array(
					'Vous nous envoyez des photos de la pièce : une vue d\'ensemble et un gros plan sur le défaut.',
					'Nous vous disons ce qui est possible et nous vous envoyons un devis.',
					'La pièce est réparée à l\'atelier : reprise du polyester, renforcement, finition.',
					'Peinture et décor sont refaits si besoin, pour que la réparation ne se voie pas.',
					'Nous convenons ensemble de la remise de la pièce.',
				), true )
				. $h( 'Avant la reprise de la saison' )
				. $p( 'Le plus souvent, les réparations se préparent entre deux saisons. Pour être prêt à temps, envoyez-nous vos photos dès que possible et indiquez-nous la date à laquelle le manège doit repartir.' )
				. $h( 'Quand la réparation ne suffit plus' )
				. $p( 'Si un sujet est trop abîmé, nous pouvons le rénover entièrement (structure, peinture, décor, éclairage) ou vous proposer un sujet neuf du catalogue.' );
		case 'renovation':
			return $p( 'Un sujet ou un manège ancien n\'est pas forcément à remplacer : il peut être <strong>remis à neuf</strong>. GLM Polyester rénove les sujets et les manèges dans son atelier de Breteuil, dans l\'Oise : reprise de la structure, nouvelle peinture, nouveau décor et éclairage modernisé.' )
				. $h( 'Ce que comprend une rénovation' )
				. $h( 'La structure', 3 )
				. $p( 'Le polyester fatigué, fissuré ou réparé plusieurs fois est repris et renforcé. Les pièces manquantes ou trop abîmées sont refaites ou remplacées.' )
				. $h( 'La peinture', 3 )
				. $p( 'Le sujet est préparé, puis entièrement repeint : les couleurs d\'origine, ou de nouvelles couleurs.' )
				. $h( 'Le décor', 3 )
				. $p( 'Le décor peut être refait à l\'identique ou changé complètement : motifs peints à la main ou à l\'aérographe, nom du manège, nouveau thème.' )
				. $h( 'L\'éclairage', 3 )
				. $p( 'Les anciennes ampoules peuvent laisser place à un éclairage modernisé, avec de nouveaux cabochons et de nouvelles optiques.' )
				. $h( 'Pourquoi rénover ?' )
				. $l( array(
					'<strong>Garder un manège homogène</strong>, avec tous ses sujets d\'origine.',
					'<strong>Changer de thème</strong> sans changer de manège.',
					'<strong>Redonner de l\'attrait</strong> à un manège qui a beaucoup tourné.',
					'<strong>Prolonger la vie</strong> de sujets solides, construits pour durer.',
				) )
				. $h( 'Les étapes d\'une rénovation' )
				. $l( array(
					'Examen du sujet ou du manège, sur photos ou à l\'atelier.',
					'Devis selon l\'état et vos souhaits : structure, peinture, décor, éclairage.',
					'Reprise de la structure et du polyester.',
					'Nouvelle peinture et nouveau décor.',
					'Éclairage modernisé, puis remise du sujet.',
				), true )
				. $h( 'Pour qui ?' )
				. $p( 'Forains, parcs de loisirs, collectivités et particuliers : pour un sujet seul, quelques sujets d\'un manège, ou un ensemble complet avec ses panneaux et ses décors.' );
	}
	return '';
}

/** Texte « tel qu'il a été créé » (sans balises ni espaces) : sert à savoir si une page a été modifiée. */
function glm_texte_compare( $html ) {
	return preg_replace( '/\s+/', ' ', trim( wp_strip_all_tags( str_replace( array( '’', '&#8217;', '&rsquo;' ), "'", (string) $html ) ) ) );
}

// Une seule fois : les pages des services créées avec le texte court (version 1), et jamais modifiées depuis,
// reçoivent le texte complet (version 2). Une page déjà modifiée n'est pas touchée.
add_action( 'admin_init', function () {
	if ( get_option( 'glm_services_textes_v2' ) || ! current_user_can( 'manage_options' ) || ! function_exists( 'glm_texte_service' ) ) {
		return;
	}
	update_option( 'glm_services_textes_v2', 1 );
	foreach ( array_keys( glm_services() ) as $slug ) {
		$page = get_page_by_path( 'services/' . $slug );
		if ( $page && glm_texte_compare( $page->post_content ) === glm_texte_compare( glm_texte_service( $slug ) ) ) {
			wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => glm_texte_service_v2( $slug ) ) ) );
		}
	}
} );

// Une seule fois : retire la mention du catalogue à feuilleter du texte de la page Fabrication (s'il est encore là).
add_action( 'admin_init', function () {
	if ( get_option( 'glm_services_sans_feuilleter' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	update_option( 'glm_services_sans_feuilleter', 1 );
	$page = get_page_by_path( 'services/fabrication' );
	if ( $page && false !== strpos( $page->post_content, 'ou feuilletez le catalogue en ligne' ) ) {
		$contenu = str_replace( 'Choisissez un ou plusieurs modèles dans le catalogue, ou feuilletez le catalogue en ligne.', 'Choisissez un ou plusieurs modèles dans le catalogue, en ligne ou dans le PDF à télécharger.', $page->post_content );
		wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => $contenu ) ) );
	}
} );
