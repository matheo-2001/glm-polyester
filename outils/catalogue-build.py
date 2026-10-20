"""Génère le catalogue PDF des sujets GLM Polyester (A4 paysage) à partir de sujets.json."""
import html
import json
import sys
from pathlib import Path

from playwright.sync_api import sync_playwright

BASE = Path(sys.argv[1])          # dossier scratchpad
OUT = Path(sys.argv[2])           # chemin du PDF à produire
FICHES = BASE / "fiches"
THEME = BASE / "theme" / "glm-polyester" / "assets"

sujets = json.loads((FICHES / "sujets.json").read_text(encoding="utf-8"))
volants = [s for s in sujets if s["famille"] == "sujets-volants"]
roulants = [s for s in sujets if s["famille"] == "sujets-roulants"]

FAMILLES = {"sujets-volants": "Sujet volant", "sujets-roulants": "Sujet roulant"}
ADRESSE = "46 rue de Montdidier · 60120 Breteuil"
TELEPHONE = "07 60 16 26 39"
EMAIL = "contact@glm-polyester.com"
e = html.escape


def font(name, weight, style):
    return (f'@font-face{{font-family:"{name}";font-weight:{weight};font-style:{style};'
            f'src:url("{(THEME / "fonts").as_uri()}/{"barlow-condensed" if "Condensed" in name else "barlow"}'
            f'-latin-{weight}-{style}.woff2") format("woff2");}}')


CSS = "".join([
    font("Barlow", 400, "normal"), font("Barlow", 500, "normal"), font("Barlow", 600, "normal"),
    font("Barlow Condensed", 700, "italic"), font("Barlow Condensed", 800, "italic"),
]) + """
@page { size: A4 landscape; margin: 0; }
:root { --rouge:#d10a0a; --rouge-logo:#f70404; --encre:#17181b; --gris:#4a4d54; --trait:#e4e2dd; --fond:#f4f3f0; --jaune:#ffc61a; }
* { box-sizing:border-box; }
html, body { margin:0; padding:0; }
body { font-family:"Barlow", Arial, sans-serif; color:var(--encre); font-size:10.5pt; line-height:1.4; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
.page { width:297mm; height:210mm; position:relative; overflow:hidden; page-break-after:always; background:#fff; }
.page:last-child { page-break-after:auto; }
.titre { font-family:"Barlow Condensed", "Arial Narrow", sans-serif; font-style:italic; font-weight:800; text-transform:uppercase; line-height:.92; margin:0; }
.eyebrow { font-size:9pt; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--rouge); }
.pied { position:absolute; left:14mm; right:14mm; bottom:8mm; display:flex; justify-content:space-between; align-items:center; font-size:8.5pt; color:var(--gris); border-top:.3mm solid var(--trait); padding-top:2.5mm; }
.pied img { height:7mm; }
.pied span b { color:var(--encre); font-weight:600; }

/* Couverture */
.couv { display:grid; grid-template-columns:118mm 1fr; }
.couv__gauche { background:var(--encre); color:#fff; padding:16mm 14mm; display:flex; flex-direction:column; justify-content:space-between; }
.couv__logo { background:#fff; padding:5mm 6mm; align-self:flex-start; }
.couv__logo img { height:30mm; display:block; }
.couv__gauche .titre { font-size:46pt; }
.couv__gauche .titre span { color:var(--jaune); }
.couv__gauche p { margin:0; color:#c2c4ca; font-size:11pt; max-width:80mm; }
.couv__adr { font-size:9.5pt; color:#c2c4ca; border-top:.3mm solid #3a3c42; padding-top:4mm; }
.couv__droite { position:relative; background:var(--fond); display:flex; align-items:center; justify-content:center; }
.couv__droite::before { content:""; position:absolute; left:10mm; top:22mm; right:18mm; bottom:26mm; background:var(--rouge-logo); clip-path:polygon(9% 0,100% 0,91% 100%,0 100%); }
.couv__droite img { position:relative; width:150mm; height:150mm; object-fit:cover; clip-path:polygon(9% 0,100% 0,91% 100%,0 100%); }

/* Présentation et sommaire */
.intro { padding:16mm 14mm 0; display:grid; grid-template-columns:1fr 1fr; gap:14mm; }
.intro .titre { font-size:30pt; margin:2mm 0 5mm; }
.intro p { margin:0 0 3mm; color:var(--gris); }
.encadre { background:var(--fond); padding:4mm 5mm; margin-top:4mm; }
.encadre strong { color:var(--encre); }
.sommaire h3 { font-family:"Barlow Condensed", sans-serif; font-style:italic; font-weight:800; text-transform:uppercase; font-size:15pt; margin:0 0 2mm; border-top:1mm solid var(--rouge); padding-top:2.5mm; }
.sommaire ul { list-style:none; margin:0 0 6mm; padding:0; }
.sommaire li { display:flex; justify-content:space-between; gap:4mm; padding:1.3mm 0; border-bottom:.3mm dotted #b9b7b1; font-size:10.5pt; }
.sommaire li span:last-child { font-variant-numeric:tabular-nums; color:var(--gris); }

/* Fiche sujet */
.fiche { display:grid; grid-template-columns:166mm 1fr; height:100%; }
.fiche__photo { background:var(--fond); display:flex; flex-direction:column; justify-content:center; padding:12mm 10mm 27mm 14mm; position:relative; }
.fiche__photo img { width:100%; height:150mm; object-fit:contain; }
.fiche__photo::after { content:""; position:absolute; left:14mm; right:10mm; bottom:22mm; height:1.2mm; background:var(--rouge-logo); }
.fiche__infos { padding:12mm 14mm 22mm 9mm; display:flex; flex-direction:column; gap:3mm; }
.fiche__tete { display:flex; justify-content:space-between; align-items:flex-start; gap:3mm; }
.badge { display:inline-block; background:var(--rouge); color:#fff; font-size:8pt; font-weight:600; letter-spacing:.08em; text-transform:uppercase; padding:1.2mm 2.5mm; white-space:nowrap; }
.fiche .titre { font-size:30pt; margin-top:1mm; }
.carac { display:grid; grid-template-columns:repeat(5,1fr); gap:1.5mm; margin-top:1mm; }
.carac div { background:var(--fond); padding:2mm 2.2mm; }
.carac small { display:block; font-size:7.5pt; color:var(--gris); text-transform:uppercase; letter-spacing:.06em; }
.carac strong { font-size:11pt; font-weight:600; font-variant-numeric:tabular-nums; white-space:nowrap; }
.sous-titre { font-family:"Barlow Condensed", sans-serif; font-style:italic; font-weight:800; text-transform:uppercase; font-size:12.5pt; margin:1.5mm 0 0; }
.construction { list-style:none; margin:0; padding:0; columns:1; font-size:10pt; line-height:1.32; }
.construction li { padding-left:3.5mm; position:relative; margin-bottom:.7mm; break-inside:avoid; }
.construction li::before { content:""; position:absolute; left:0; top:1.6mm; width:1.6mm; height:1.6mm; background:var(--rouge); }
.notes { font-size:9pt; color:var(--gris); margin:0; }
.perso { margin-top:auto; border-left:1.2mm solid var(--jaune); background:var(--fond); padding:2.5mm 3.5mm; font-size:9pt; }
.perso strong { font-weight:600; }

/* Dos */
.dos { background:var(--encre); color:#fff; display:flex; flex-direction:column; justify-content:center; padding:0 30mm; gap:6mm; }
.dos .titre { font-size:40pt; }
.dos .titre span { color:var(--jaune); }
.dos p { margin:0; color:#c2c4ca; font-size:12pt; max-width:170mm; }
.dos__contact { display:grid; grid-template-columns:repeat(3, auto); gap:4mm 14mm; justify-content:start; margin-top:4mm; }
.dos__contact small { display:block; color:var(--jaune); font-size:8.5pt; font-weight:600; letter-spacing:.09em; text-transform:uppercase; }
.dos__contact strong { font-size:13pt; font-weight:600; }
.dos__logo { background:#fff; padding:4mm 5mm; align-self:flex-start; }
.dos__logo img { height:20mm; display:block; }
"""


def pied(num):
    return (f'<div class="pied"><img src="{(THEME / "img" / "logo-glm.png").as_uri()}" alt="GLM Polyester">'
            f'<span><b>GLM Polyester</b> · {ADRESSE} · {TELEPHONE} · {EMAIL}</span><span>{num}</span></div>')


def page_sujet(s, num):
    carac = [("Longueur", s["longueur"]), ("Largeur", s["largeur"]), ("Hauteur", s["hauteur"]),
             ("Poids", s["poids"]), ("Places", s["places"])]
    carac_html = "".join(f"<div><small>{k}</small><strong>{e(v)}</strong></div>" for k, v in carac)
    cons = "".join(f"<li>{e(c)}</li>" for c in s["construction"])
    notes = "".join(f'<p class="notes">{e(n)}</p>' for n in s["notes"])
    if s["famille"] == "sujets-volants":
        perso = ("<strong>Personnalisable</strong> : couleurs au choix, décor peint ou covering. "
                 "Sujet volant : le parallélogramme est fourni par le client.")
    else:
        perso = "<strong>Personnalisable</strong> : couleurs au choix, décor peint ou covering."
    photo = (FICHES / "photos-pdf" / s["photo"]).as_uri()
    return f"""
<section class="page"><div class="fiche">
  <div class="fiche__photo"><img src="{photo}" alt="{e(s['nom'])}"></div>
  <div class="fiche__infos">
    <div class="fiche__tete"><span class="eyebrow">{FAMILLES[s['famille']]}</span></div>
    <h2 class="titre">{e(s['nom'])}</h2>
    <div class="carac">{carac_html}</div>
    <h3 class="sous-titre">Construction</h3>
    <ul class="construction">{cons}</ul>
    {notes}
    <div class="perso">{perso}</div>
  </div>
</div>{pied(num)}</section>"""


ordre = volants + roulants
premiere = 3  # couverture = 1, présentation = 2
num = {s["slug"]: premiere + i for i, s in enumerate(ordre)}


def liste(groupe):
    return "".join(f"<li><span>{e(s['nom'])}</span><span>{num[s['slug']]}</span></li>" for s in groupe)


couverture = f"""
<section class="page couv">
  <div class="couv__gauche">
    <div class="couv__logo"><img src="{(THEME / 'img' / 'logo-glm.png').as_uri()}" alt="GLM Polyester"></div>
    <div>
      <p class="eyebrow" style="color:var(--jaune);margin-bottom:4mm">Catalogue</p>
      <h1 class="titre">Sujets<br>de manège<br><span>volants et roulants</span></h1>
    </div>
    <p>Des sujets fabriqués en polyester dans notre atelier de Breteuil, dans l'Oise.</p>
    <div class="couv__adr">GLM Polyester · {ADRESSE}<br>{TELEPHONE} · {EMAIL}</div>
  </div>
  <div class="couv__droite"><img src="{(FICHES / 'photos-pdf' / 'spacio.jpg').as_uri()}" alt="Sujet volant Spacio"></div>
</section>"""

presentation = f"""
<section class="page">
  <div class="intro">
    <div>
      <p class="eyebrow">GLM Polyester</p>
      <h2 class="titre">Des sujets solides, faits pour tourner</h2>
      <p>Chaque sujet est construit autour d'une monocoque en polyester indéformable, avec le circuit électrique noyé dans le polyester.</p>
      <p>Les sujets volants se montent sur parallélogramme ; les sujets roulants se posent sur deux essieux à lames, avec des roues gonflées.</p>
      <div class="encadre"><strong>Personnalisable.</strong> Couleurs au choix, décor peint ou covering, sur tous les sujets volants et roulants.</div>
      <div class="encadre"><strong>Sujets volants.</strong> Le parallélogramme est fourni par le client.</div>
      <div class="encadre"><strong>Sur demande.</strong> Dimensions, places et équipements sont donnés à titre indicatif : demandez-nous un devis pour votre manège.</div>
    </div>
    <div class="sommaire">
      <h3>Sujets volants</h3><ul>{liste(volants)}</ul>
      <h3>Sujets roulants</h3><ul>{liste(roulants)}</ul>
    </div>
  </div>{pied(2)}
</section>"""

dos = f"""
<section class="page dos">
  <div class="dos__logo"><img src="{(THEME / 'img' / 'logo-glm.png').as_uri()}" alt="GLM Polyester"></div>
  <h2 class="titre">Un sujet vous intéresse ?<br><span>Demandez un devis.</span></h2>
  <p>Fabrication, personnalisation, réparation et rénovation de manèges, de sujets et de pièces en polyester.</p>
  <div class="dos__contact">
    <div><small>Atelier</small><strong>{ADRESSE}</strong></div>
    <div><small>Téléphone</small><strong>{TELEPHONE}</strong></div>
    <div><small>E-mail</small><strong>{EMAIL}</strong></div>
  </div>
</section>"""

pages = [couverture, presentation] + [page_sujet(s, num[s["slug"]]) for s in ordre] + [dos]
doc = f'<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Catalogue GLM Polyester</title><style>{CSS}</style></head><body>{"".join(pages)}</body></html>'
html_path = BASE / "catalogue" / "catalogue.html"
html_path.write_text(doc, encoding="utf-8")

with sync_playwright() as p:
    browser = p.chromium.launch(executable_path=sys.argv[3] if len(sys.argv) > 3 else None)
    page = browser.new_page()
    page.goto(html_path.as_uri(), wait_until="networkidle")
    page.evaluate("document.fonts.ready")
    page.pdf(path=str(OUT), width="297mm", height="210mm", print_background=True, prefer_css_page_size=True)
    browser.close()
print("pages:", len(pages))
