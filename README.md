# GLM Polyester — site web

Site de GLM Polyester, constructeur de manèges, sujets et pièces en polyester (Breteuil, Oise).

## Aperçu en ligne

La racine de ce dépôt est une **copie statique** du site WordPress, publiée avec GitHub Pages :
<https://matheo-2001.github.io/glm-polyester/>

C'est un aperçu pour le client : le formulaire de contact n'envoie rien et les moteurs de recherche
ne l'indexent pas (`robots.txt` et balise `noindex`).

## Contenu

| Dossier | Rôle |
| --- | --- |
| `/` (racine) | Copie statique du site (pages, `wp-content/`, `wp-includes/`) servie par GitHub Pages |
| `wordpress-theme/glm-polyester/` | Thème WordPress sur mesure (PHP, CSS, JS) |
| `outils/convertir-export.py` | Transforme une copie brute du site local en site statique |
| `outils/catalogue-build.py`, `outils/sujets.json` | Génération du catalogue PDF des sujets |

L'ancienne maquette est conservée sur la branche `ancienne-maquette`.

## Thème WordPress (version 1.1)

Thème classique, sans constructeur de pages :

- types de contenu **Modèles** (catalogue, familles, thèmes) et **Réalisations** (galerie avec visionneuse) ;
- réglages dans *Apparence > Personnaliser > GLM Polyester* : coordonnées, photos des sections, PDF du catalogue,
  et tous les textes des pages (accueil, catalogue et fiches, services, atelier, contact, bandeaux de devis, référencement) ;
- réglages de chaque famille dans *Catalogue > Familles* : ordre d'affichage, photo de la famille, présentation
  (section complète ou regroupée avec les petites familles) ;
- formulaire de devis : vérification champ par champ, saisie conservée en cas d'erreur, photos réduites dans le navigateur
  avant l'envoi ;
- images : tailles créées en WebP ; *Outils > Images WebP* convertit les photos déjà présentes (les originaux ne sont pas touchés) ;
- référencement : descriptions, aperçus de partage (Open Graph), données structurées (entreprise locale, produits,
  fil d'Ariane), plan du site nettoyé, pages par thème en `/catalogue/theme/<thème>/` ;
- page *Feuilleter le catalogue* (livre feuilletable, bibliothèque StPageFlip incluse dans `assets/vendor/`).
  Les pages affichées sont des images placées dans `wp-content/uploads/glm-catalogue/page-NN-1600.jpg` (et `-900.jpg`),
  avec une version `.webp` à côté, à régénérer quand le PDF change.

Installation : copier `wordpress-theme/glm-polyester` dans `wp-content/themes/` puis activer le thème.

## Refaire l'aperçu statique

1. Copier `outils/export/glm-export-temp.php` dans `wp-content/mu-plugins/` du site local.
2. Dans l'administration, ouvrir *Outils > Copie GLM* et lancer `await glmCopie()` dans la console du navigateur :
   le site est parcouru en visiteur et la copie est enregistrée dans `wp-content/uploads/glm-export/`.
3. `python3 outils/convertir-export.py COPIE SORTIE`, puis remplacer la racine du dépôt par `SORTIE`.
4. Revider le fichier `glm-export-temp.php` du site local.
