#!/usr/bin/env python3
"""
Transforme la copie brute du site WordPress (zip généré depuis le navigateur :
manifest.json + html/NNN.html + site/wp-content/...) en site statique pour GitHub Pages.

Usage : python3 convertir-export.py DOSSIER_COPIE DOSSIER_SORTIE [URL_LOCALE]
"""
import json, os, re, shutil, sys
from urllib.parse import urlsplit, parse_qs

src, out = sys.argv[1], sys.argv[2]
base = (sys.argv[3] if len(sys.argv) > 3 else 'http://localhost:10013').rstrip('/')
host = base.split('://', 1)[1]

manifest = json.load(open(os.path.join(src, 'manifest.json')))
pages = manifest['pages']

def chemin(url):
    """URL du site (chemin + requête) -> dossier de sortie, ou None si la page n'est pas copiée telle quelle."""
    p = urlsplit(url)
    path = p.path
    if p.query:
        q = parse_qs(p.query)
        if path == '/catalogue/' and 'theme' in q:
            return 'catalogue/theme/' + q['theme'][0] + '/'
        return None  # ex. contact/?ref=… : la page contact/ suffit
    return path.lstrip('/')

ANALYSE = ('<script>/* Aperçu statique : le formulaire n\'envoie rien. */document.addEventListener("submit",function(e){'
           'var f=e.target;if(!f.matches||!f.matches("form"))return;e.preventDefault();'
           'var m=document.createElement("div");m.className="avis avis--ok";m.setAttribute("role","status");'
           'm.innerHTML="<strong>Aperçu du site.</strong> Le formulaire sera actif une fois le site en ligne.";'
           'f.parentNode.insertBefore(m,f);m.scrollIntoView({block:"center"});},true);'
           # Pré-remplissage du modèle depuis ?ref= (comme sur le vrai site).
           'document.addEventListener("DOMContentLoaded",function(){var q=new URLSearchParams(location.search);'
           'var r=q.get("ref"),d=q.get("demande");var c=document.querySelector("[name=modele]");if(r&&c&&!c.value)c.value=r;'
           'var t=document.querySelector("[name=type]");if(d&&t)t.value=d;});</script>')

if os.path.exists(out):
    shutil.rmtree(out)
os.makedirs(out)
shutil.copytree(os.path.join(src, 'site'), out, dirs_exist_ok=True)

head_junk = re.compile(r'<link[^>]+(?:rel="(?:https://api\.w\.org/|EditURI|shortlink|pingback|alternate)"|wp-json)[^>]*>\s*', re.I)

ecrites = 0
for url, fichier in pages.items():
    dest = chemin(url)
    if dest is None:
        continue
    html = open(os.path.join(src, fichier), encoding='utf-8').read()
    profondeur = dest.count('/')
    prefixe = '../' * profondeur if profondeur else './'

    html = head_junk.sub('', html)

    # Liens vers les filtres de thème du catalogue.
    def theme(m):
        return prefixe + 'catalogue/theme/' + m.group(1) + '/'
    html = re.sub(re.escape(base) + r'/catalogue/\?theme=([a-z0-9-]+)', theme, html)
    html = re.sub(re.escape(base) + r'/catalogue/\?(?:amp;|#038;)?theme=([a-z0-9-]+)', theme, html)

    # Adresses absolues -> relatives (forme normale et forme échappée des JSON).
    html = html.replace(base + '/', prefixe)
    html = html.replace(base, prefixe.rstrip('/') or '.')
    esc = base.replace('/', '\\/')
    html = html.replace(esc + '\\/', prefixe.replace('/', '\\/'))
    html = html.replace('//' + host + '/', prefixe)

    # Pas d'indexation par les moteurs de recherche pour l'aperçu.
    html = html.replace('<head>', '<head>\n<meta name="robots" content="noindex, nofollow">', 1)
    html = html.replace('</body>', ANALYSE + '\n</body>', 1)

    target = os.path.join(out, dest, 'index.html')
    os.makedirs(os.path.dirname(target), exist_ok=True)
    open(target, 'w', encoding='utf-8').write(html)
    ecrites += 1

# Feuilles de style : adresses absolues éventuelles.
for racine, _, fichiers in os.walk(out):
    for f in fichiers:
        if f.endswith('.css'):
            p = os.path.join(racine, f)
            t = open(p, encoding='utf-8', errors='ignore').read()
            if base in t:
                rel = os.path.relpath(out, racine).replace(os.sep, '/') + '/'
                open(p, 'w', encoding='utf-8').write(t.replace(base + '/', rel))

open(os.path.join(out, 'robots.txt'), 'w').write('User-agent: *\nDisallow: /\n')
open(os.path.join(out, '.nojekyll'), 'w').write('')
reste = sum(open(os.path.join(r, f), encoding='utf-8', errors='ignore').read().count(host)
            for r, _, fs in os.walk(out) for f in fs if f.endswith(('.html', '.css', '.js')))
print(f'{ecrites} pages écrites, {reste} adresse(s) locale(s) restante(s)')
