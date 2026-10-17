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

## Thème WordPress

Thème classique, sans constructeur de pages :

- types de contenu **Modèles** (catalogue, familles, thèmes) et **Réalisations** (galerie avec visionneuse) ;
- réglages dans *Apparence > Personnaliser > GLM Polyester* (coordonnées, textes d'accueil, photos, PDF du catalogue) ;
- formulaire de devis avec pièces jointes ;
- page *Feuilleter le catalogue* (livre feuilletable, bibliothèque StPageFlip incluse dans `assets/vendor/`).
  Les pages affichées sont des images placées dans `wp-content/uploads/glm-catalogue/page-NN-1600.jpg` (et `-900.jpg`),
  à régénérer quand le PDF change.

Installation : copier `wordpress-theme/glm-polyester` dans `wp-content/themes/` puis activer le thème.
