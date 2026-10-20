/* GLM Polyester : menu mobile, menus déroulants, apparitions, catalogue, galeries, visionneuse, formulaire de devis. */
(function () {
	'use strict';

	var reduit = window.matchMedia('(prefers-reduced-motion: reduce)');

	/* ----- Ombre de l'en-tête au défilement ----- */
	var header = document.querySelector('.site-header');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* ----- Bouton « revenir en haut » ----- */
	var hautPage = document.querySelector('.haut-page');
	if (hautPage) {
		var montrerHaut = function () {
			hautPage.classList.toggle('is-visible', window.scrollY > window.innerHeight * 0.8);
		};
		montrerHaut();
		window.addEventListener('scroll', montrerHaut, { passive: true });
		hautPage.addEventListener('click', function (event) {
			event.preventDefault();
			window.scrollTo({ top: 0, behavior: reduit.matches ? 'auto' : 'smooth' });
			// Le clavier repart du haut de la page.
			var lien = document.querySelector('.site-logo');
			if (lien) { lien.focus({ preventScroll: true }); }
		});
	}

	/* ----- Menu mobile : panneau à hauteur d'écran, page bloquée derrière ----- */
	var burger = document.querySelector('.burger');
	var panel = document.getElementById('menu-mobile');
	if (burger && panel && header) {
		var arriere = [document.querySelector('main'), document.querySelector('.site-footer')];
		var setOpen = function (open) {
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			burger.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
			if (open) {
				header.style.setProperty('--bas-entete', Math.round(header.getBoundingClientRect().bottom) + 'px');
			}
			panel.classList.toggle('is-open', open);
			document.documentElement.classList.toggle('menu-ouvert', open);
			// Le contenu derrière le menu n'est plus atteignable au clavier ni lu par les lecteurs d'écran.
			arriere.forEach(function (el) {
				if (el) {
					el.inert = open;
				}
			});
		};
		burger.addEventListener('click', function () {
			setOpen(burger.getAttribute('aria-expanded') !== 'true');
		});
		// Le panneau est fixé sous l'en-tête : sa position suit l'en-tête si la page bouge (barre d'adresse, barre d'administration).
		var recaler = function () {
			if (burger.getAttribute('aria-expanded') === 'true') {
				header.style.setProperty('--bas-entete', Math.max(0, Math.round(header.getBoundingClientRect().bottom)) + 'px');
			}
		};
		window.addEventListener('scroll', recaler, { passive: true });
		window.addEventListener('resize', recaler);
		// Un lien (vers une ancre de la même page, par exemple) referme le menu.
		panel.addEventListener('click', function (event) {
			if (event.target.closest('a')) {
				setOpen(false);
			}
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && burger.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
				burger.focus();
			}
		});
		window.addEventListener('resize', function () {
			if (window.innerWidth > 1100 && burger.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
			}
		});
	}

	/* Sous-menu mobile : il s'ouvre et se ferme en douceur (hauteur animée). */
	var deplier = function (el, open) {
		if (reduit.matches) {
			el.hidden = !open;
			return;
		}
		clearTimeout(el._glmFin);
		if (open) {
			el.hidden = false;
			var h = el.scrollHeight;
			el.style.height = '0px';
			el.offsetHeight; // eslint-disable-line no-unused-expressions
			el.style.height = h + 'px';
			el._glmFin = setTimeout(function () { el.style.height = ''; }, 320);
		} else {
			el.style.height = el.scrollHeight + 'px';
			el.offsetHeight; // eslint-disable-line no-unused-expressions
			el.style.height = '0px';
			el._glmFin = setTimeout(function () { el.hidden = true; el.style.height = ''; }, 320);
		}
	};

	/* ----- Menus déroulants (Catalogue, Services) ----- */
	var subs = document.querySelectorAll('.has-sub');
	var canHover = window.matchMedia('(hover: hover) and (pointer: fine)');
	var setSub = function (li, open) {
		var btn = li.querySelector('.sub-toggle');
		var target = btn ? document.getElementById(btn.getAttribute('aria-controls')) : null;
		li.classList.toggle('is-open', open);
		if (btn) {
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		}
		if (target && target.classList.contains('mobile-sub')) {
			deplier(target, open);
		}
	};
	var closeOthers = function (keep) {
		subs.forEach(function (li) {
			if (li !== keep && li.classList.contains('is-open')) {
				setSub(li, false);
			}
		});
	};
	subs.forEach(function (li) {
		var btn = li.querySelector('.sub-toggle');
		var timer = null;
		var inDesktop = !!li.closest('.site-nav');
		if (btn) {
			btn.addEventListener('click', function () {
				var open = !li.classList.contains('is-open');
				closeOthers(li);
				setSub(li, open);
			});
		}
		if (inDesktop) {
			li.addEventListener('mouseenter', function () {
				if (!canHover.matches) { return; }
				clearTimeout(timer);
				closeOthers(li);
				setSub(li, true);
			});
			li.addEventListener('mouseleave', function () {
				if (!canHover.matches) { return; }
				timer = setTimeout(function () { setSub(li, false); }, 180);
			});
			li.addEventListener('focusout', function (event) {
				if (!li.contains(event.relatedTarget)) {
					setSub(li, false);
				}
			});
		}
	});
	document.addEventListener('keydown', function (event) {
		if (event.key !== 'Escape') { return; }
		subs.forEach(function (li) {
			if (li.classList.contains('is-open') && li.closest('.site-nav')) {
				setSub(li, false);
				var btn = li.querySelector('.sub-toggle');
				if (btn && li.contains(document.activeElement)) { btn.focus(); }
			}
		});
	});
	document.addEventListener('click', function (event) {
		subs.forEach(function (li) {
			if (li.closest('.site-nav') && li.classList.contains('is-open') && !li.contains(event.target)) {
				setSub(li, false);
			}
		});
	});

	/* ----- Apparition discrète des sections ----- */
	var reveals = document.querySelectorAll('.reveal');
	if ('IntersectionObserver' in window && reveals.length && !reduit.matches) {
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		reveals.forEach(function (element) {
			observer.observe(element);
		});
	} else {
		reveals.forEach(function (element) {
			element.classList.add('is-visible');
		});
	}

	/* ----- Galerie : la dernière rangée est toujours pleine (quelques photos passent en grand format) ----- */
	var galerie = document.getElementById('galerie');
	var combler = function () {
		if (!galerie || galerie.classList.contains('galerie--mosaique')) { return; }
		var visibles = Array.prototype.filter.call(galerie.querySelectorAll('.galerie__item'), function (el) {
			el.classList.remove('galerie__item--comble');
			return !el.hidden;
		});
		var colonnes = getComputedStyle(galerie).gridTemplateColumns.split(' ').length;
		if (colonnes < 2) { return; }
		var cases = visibles.reduce(function (s, el) {
			return s + (el.classList.contains('galerie__item--large') ? 2 : 1);
		}, 0);
		var manque = (colonnes - (cases % colonnes)) % colonnes;
		var petites = visibles.filter(function (el) { return !el.classList.contains('galerie__item--large'); });
		for (var i = 0; i < manque && i < petites.length; i++) {
			petites[petites.length - 1 - i].classList.add('galerie__item--comble');
		}
	};
	combler();
	window.addEventListener('resize', combler);

	/* ----- Filtres de la galerie de réalisations ----- */
	var filtres = document.querySelectorAll('[data-filtre]');
	var items = document.querySelectorAll('#galerie .galerie__item');
	var compte = document.getElementById('galerie-compte');
	filtres.forEach(function (bouton) {
		bouton.addEventListener('click', function () {
			var valeur = bouton.getAttribute('data-filtre');
			var visibles = 0;
			filtres.forEach(function (autre) {
				var actif = autre === bouton;
				autre.classList.toggle('is-active', actif);
				autre.setAttribute('aria-pressed', actif ? 'true' : 'false');
			});
			items.forEach(function (item) {
				var types = (item.getAttribute('data-types') || '').split(' ');
				item.hidden = valeur !== '' && types.indexOf(valeur) === -1;
				item.classList.add('is-visible');
				if (!item.hidden) { visibles++; }
			});
			combler();
			if (compte) {
				compte.textContent = visibles + (visibles > 1 ? ' réalisations affichées' : ' réalisation affichée');
			}
		});
	});

	/* Lien vers un type précis (#type-pieces, #theme-noel) : le filtre correspondant est activé. */
	var filtreAncre = function () {
		var ancreFiltre = (window.location.hash || '').replace(/^#(type-)?/, '');
		if (!ancreFiltre) { return; }
		filtres.forEach(function (bouton) {
			if (bouton.getAttribute('data-filtre') === ancreFiltre) { bouton.click(); }
		});
	};
	if (filtres.length) {
		filtreAncre();
		window.addEventListener('hashchange', filtreAncre);
	}

	/* Lignes de puces (catégories, thèmes) qui défilent sur téléphone : la puce active reste visible. */
	document.querySelectorAll('.cat-filtres__puces').forEach(function (ligne) {
		var actif = ligne.querySelector('[aria-current], [aria-pressed="true"]');
		if (actif && ligne.scrollWidth > ligne.clientWidth) {
			ligne.scrollLeft = actif.offsetLeft - 18;
		}
	});

	/* ----- Vignettes de la fiche modèle ----- */
	var grande = document.getElementById('fiche-photo-img');
	var zoomFiche = document.querySelector('.fiche2__zoom');
	var vignettes = document.querySelectorAll('.fiche__vignettes button');
	vignettes.forEach(function (vignette) {
		vignette.addEventListener('click', function () {
			if (!grande || !vignette.getAttribute('data-src')) {
				return;
			}
			grande.removeAttribute('srcset');
			grande.removeAttribute('sizes');
			grande.src = vignette.getAttribute('data-src');
			grande.alt = vignette.getAttribute('data-alt') || '';
			if (zoomFiche) {
				zoomFiche.setAttribute('data-index', vignette.getAttribute('data-index') || '0');
			}
			vignettes.forEach(function (autre) {
				autre.setAttribute('aria-pressed', autre === vignette ? 'true' : 'false');
			});
		});
	});

	/* ----- Fiche : barre « Demander un devis » en bas d'écran sur téléphone ----- */
	var barreDevis = document.getElementById('barre-devis');
	var actionsFiche = document.querySelector('.fiche2__actions');
	if (barreDevis && actionsFiche && 'IntersectionObserver' in window) {
		new IntersectionObserver(function (entries) {
			var e = entries[0];
			// Visible une fois le bouton de la fiche passé (au-dessus de l'écran), masquée sinon.
			barreDevis.classList.toggle('is-visible', !e.isIntersecting && e.boundingClientRect.top < 0);
		}).observe(actionsFiche);
	}

	/* ----- Visionneuse (carrousel plein écran) : réalisations et photos des fiches ----- */
	var lightbox = document.getElementById('lightbox');
	if (lightbox && typeof lightbox.showModal === 'function') {
		var lbImg = lightbox.querySelector('.lightbox__img');
		var lbCaption = lightbox.querySelector('.lightbox__caption');
		var lbCount = lightbox.querySelector('.lightbox__count');
		var slides = [];
		var current = 0;
		var lastTrigger = null;

		var collect = function (groupe) {
			var list = [];
			groupe.querySelectorAll('[data-slides]').forEach(function (item) {
				if (item.hidden) {
					return;
				}
				var data = [];
				try {
					data = JSON.parse(item.getAttribute('data-slides') || '[]');
				} catch (e) {
					data = [];
				}
				data.forEach(function (slide, n) {
					list.push({ src: slide.src, alt: slide.alt, label: slide.label, caption: item.getAttribute('data-caption') || '', item: item, n: n });
				});
			});
			return list;
		};

		var preload = function (index) {
			if (slides[index]) {
				var image = new Image();
				image.src = slides[index].src;
			}
		};

		var show = function (index) {
			if (!slides.length) {
				return;
			}
			current = (index + slides.length) % slides.length;
			var slide = slides[current];
			lbImg.classList.add('is-loading');
			lbImg.onload = function () {
				lbImg.classList.remove('is-loading');
			};
			lbImg.src = slide.src;
			lbImg.alt = slide.alt || '';
			lbCaption.textContent = '';
			if (slide.label) {
				var tag = document.createElement('span');
				tag.className = 'lightbox__tag';
				tag.textContent = slide.label;
				lbCaption.appendChild(tag);
			}
			var parts = slide.caption.split(' — ');
			var strong = document.createElement('strong');
			strong.textContent = parts[0];
			lbCaption.appendChild(strong);
			if (parts[1]) {
				lbCaption.appendChild(document.createTextNode(' — ' + parts.slice(1).join(' — ')));
			}
			lbCount.textContent = (current + 1) + ' / ' + slides.length;
			preload(current + 1);
			preload(current - 1);
		};

		var open = function (trigger) {
			var item = trigger.closest('[data-slides]');
			var groupe = trigger.closest('[data-glm-groupe]') || item;
			if (!item || !groupe) {
				return;
			}
			slides = collect(groupe);
			var depart = parseInt(trigger.getAttribute('data-index') || '0', 10) || 0;
			var start = 0;
			for (var i = 0; i < slides.length; i++) {
				if (slides[i].item === item && slides[i].n === depart) {
					start = i;
					break;
				}
			}
			lightbox.classList.toggle('lightbox--seule', slides.length < 2);
			lastTrigger = trigger;
			show(start);
			document.documentElement.classList.add('lightbox-open');
			lightbox.showModal();
			lightbox.querySelector(slides.length < 2 ? '.lightbox__close' : '.lightbox__next').focus({ preventScroll: true });
		};

		document.addEventListener('click', function (event) {
			var trigger = event.target.closest('[data-glm-zoom]');
			if (trigger) {
				event.preventDefault();
				open(trigger);
			}
		});

		lightbox.querySelector('.lightbox__prev').addEventListener('click', function () { show(current - 1); });
		lightbox.querySelector('.lightbox__next').addEventListener('click', function () { show(current + 1); });
		lightbox.querySelector('.lightbox__close').addEventListener('click', function () { lightbox.close(); });

		// Clic sur le fond sombre : fermeture.
		lightbox.addEventListener('click', function (event) {
			if (event.target === lightbox) {
				lightbox.close();
			}
		});

		// Flèches du clavier (Échap ferme nativement).
		lightbox.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') {
				event.preventDefault();
				show(current - 1);
			} else if (event.key === 'ArrowRight') {
				event.preventDefault();
				show(current + 1);
			}
		});

		// Balayage au doigt.
		var touchX = null;
		lightbox.addEventListener('touchstart', function (event) {
			touchX = event.changedTouches[0].clientX;
		}, { passive: true });
		lightbox.addEventListener('touchend', function (event) {
			if (touchX === null) {
				return;
			}
			var dx = event.changedTouches[0].clientX - touchX;
			if (Math.abs(dx) > 50) {
				show(dx < 0 ? current + 1 : current - 1);
			}
			touchX = null;
		}, { passive: true });

		lightbox.addEventListener('close', function () {
			document.documentElement.classList.remove('lightbox-open');
			if (lastTrigger) {
				lastTrigger.focus({ preventScroll: true });
			}
		});
	}

	/* ----- Formulaire de devis ----- */
	var form = document.getElementById('form-devis');
	if (form) {
		var CLE = 'glm-devis';
		var champs = ['nom', 'societe', 'tel', 'email', 'type', 'modele', 'message'];
		var statut = new URLSearchParams(window.location.search).get('contact');
		var messages = {
			nom: 'Indiquez votre nom.',
			joindre: 'Indiquez un téléphone ou un e-mail pour que nous puissions vous répondre.',
			email: 'Cette adresse e-mail n\'est pas valide (exemple : nom@domaine.fr).',
			message: 'Décrivez votre demande en quelques mots.'
		};
		var stockage = function (action, valeur) {
			try {
				if (action === 'lire') { return JSON.parse(window.sessionStorage.getItem(CLE) || 'null'); }
				if (action === 'ecrire') { window.sessionStorage.setItem(CLE, JSON.stringify(valeur)); }
				if (action === 'effacer') { window.sessionStorage.removeItem(CLE); }
			} catch (e) {
				return null;
			}
			return null;
		};

		// Après un retour d'erreur du serveur, on remet ce qui avait été saisi ; après un envoi réussi, on oublie tout.
		if (statut === 'ok') {
			stockage('effacer');
		} else if (statut) {
			var saisie = stockage('lire');
			if (saisie) {
				champs.forEach(function (nom) {
					var el = form.elements[nom];
					if (el && saisie[nom] && !el.value) {
						el.value = saisie[nom];
					}
				});
			}
		}

		form.noValidate = true;
		var erreur = function (cle, texte) {
			var p = document.getElementById('err-' + cle);
			if (p) {
				p.textContent = texte || '';
				p.hidden = !texte;
			}
		};
		var marquer = function (el, invalide) {
			if (invalide) {
				el.setAttribute('aria-invalid', 'true');
			} else {
				el.removeAttribute('aria-invalid');
			}
		};
		var valider = function () {
			var e = form.elements;
			var premier = null;
			var nomVide = !e.nom.value.trim();
			var mail = e.email.value.trim();
			var mailFaux = mail !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(mail);
			var joindre = !e.tel.value.trim() && !mail;
			var messageVide = !e.message.value.trim();
			erreur('nom', nomVide ? messages.nom : '');
			marquer(e.nom, nomVide);
			erreur('email', mailFaux ? messages.email : '');
			erreur('joindre', joindre ? messages.joindre : '');
			marquer(e.tel, joindre);
			marquer(e.email, joindre || mailFaux);
			erreur('message', messageVide ? messages.message : '');
			marquer(e.message, messageVide);
			if (nomVide) { premier = e.nom; } else if (joindre) { premier = e.tel; } else if (mailFaux) { premier = e.email; } else if (messageVide) { premier = e.message; }
			return premier;
		};

		// Les erreurs disparaissent dès que le champ est corrigé.
		form.addEventListener('input', function () {
			if (form.classList.contains('a-verifier')) {
				valider();
			}
		});

		form.addEventListener('submit', function (event) {
			form.classList.add('a-verifier');
			var premier = valider();
			if (premier) {
				event.preventDefault();
				premier.focus();
				return;
			}
			var saisie = {};
			champs.forEach(function (nom) {
				if (form.elements[nom]) { saisie[nom] = form.elements[nom].value; }
			});
			stockage('ecrire', saisie);
			if (photosEnCours) {
				event.preventDefault();
				envoiDiffere = true;
				return;
			}
			form.classList.add('is-envoi');
		});

		/* Photos : réduites dans le navigateur avant l'envoi (2 000 px, JPEG), dans la limite du serveur. */
		var input = document.getElementById('photos');
		var liste = document.getElementById('photos-liste');
		var max = parseInt(form.getAttribute('data-photos-max') || '5', 10);
		var poidsMax = parseInt(form.getAttribute('data-poids-max') || '0', 10);
		var photosEnCours = false;
		var envoiDiffere = false;
		var poids = function (o) {
			return o > 1048576 ? (o / 1048576).toFixed(1).replace('.', ',') + ' Mo' : Math.max(1, Math.round(o / 1024)) + ' Ko';
		};
		var reduire = function (fichier) {
			return new Promise(function (resolve) {
				if (!/^image\/(jpeg|png|webp)$/.test(fichier.type) || !window.createImageBitmap || !window.DataTransfer) {
					resolve(fichier);
					return;
				}
				window.createImageBitmap(fichier).then(function (bitmap) {
					var cote = 2000;
					var echelle = Math.min(1, cote / Math.max(bitmap.width, bitmap.height));
					if (echelle === 1 && fichier.size < 1.5 * 1048576) {
						resolve(fichier);
						return;
					}
					var canvas = document.createElement('canvas');
					canvas.width = Math.round(bitmap.width * echelle);
					canvas.height = Math.round(bitmap.height * echelle);
					var ctx = canvas.getContext('2d');
					ctx.fillStyle = '#fff';
					ctx.fillRect(0, 0, canvas.width, canvas.height);
					ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
					canvas.toBlob(function (blob) {
						if (!blob || blob.size >= fichier.size) {
							resolve(fichier);
							return;
						}
						var nom = fichier.name.replace(/\.[^.]+$/, '') + '.jpg';
						resolve(new File([blob], nom, { type: 'image/jpeg', lastModified: Date.now() }));
					}, 'image/jpeg', 0.82);
				}).catch(function () {
					resolve(fichier);
				});
			});
		};
		var afficher = function (fichiers) {
			liste.textContent = '';
			if (!fichiers.length) {
				return;
			}
			fichiers.forEach(function (f) {
				var li = document.createElement('li');
				if (/^image\//.test(f.type) && window.URL) {
					var img = document.createElement('img');
					img.alt = '';
					img.src = URL.createObjectURL(f);
					li.appendChild(img);
				}
				li.appendChild(document.createTextNode(f.name + ' (' + poids(f.size) + ')'));
				liste.appendChild(li);
			});
			var li = document.createElement('li');
			var vider = document.createElement('button');
			vider.type = 'button';
			vider.className = 'photos-liste__vider';
			vider.textContent = 'Retirer les photos';
			vider.addEventListener('click', function () {
				input.value = '';
				afficher([]);
				erreur('photos', '');
				input.focus();
			});
			li.appendChild(vider);
			liste.appendChild(li);
		};
		if (input && liste) {
			input.addEventListener('change', function () {
				var fichiers = Array.prototype.slice.call(input.files || []);
				var note = '';
				if (fichiers.length > max) {
					note = 'Seules les ' + max + ' premières photos seront envoyées.';
					fichiers = fichiers.slice(0, max);
				}
				photosEnCours = true;
				liste.textContent = '';
				var attente = document.createElement('li');
				attente.textContent = 'Préparation des photos…';
				liste.appendChild(attente);
				Promise.all(fichiers.map(reduire)).then(function (prets) {
					var total = prets.reduce(function (s, f) { return s + f.size; }, 0);
					if (poidsMax && total > poidsMax) {
						note = 'Les photos pèsent ' + poids(total) + ', au-delà de la limite de ' + poids(poidsMax) + '. Retirez-en une ou deux.';
					}
					if (window.DataTransfer) {
						try {
							var dt = new DataTransfer();
							prets.forEach(function (f) { dt.items.add(f); });
							input.files = dt.files;
						} catch (e) {
							prets = fichiers;
						}
					}
					afficher(prets);
					erreur('photos', note);
					photosEnCours = false;
					if (envoiDiffere) {
						envoiDiffere = false;
						if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
					}
				});
			});
		}

		// Après le rechargement : le message de confirmation ou d'erreur est amené à l'écran et annoncé.
		var avis = form.querySelector('.avis');
		if (avis) {
			avis.setAttribute('tabindex', '-1');
			// Après le chargement complet (le navigateur replace le focus en arrivant sur #formulaire).
			var focusAvis = function () {
				window.setTimeout(function () { avis.focus(); }, 60);
			};
			if (document.readyState === 'complete') {
				focusAvis();
			} else {
				window.addEventListener('load', focusAvis);
			}
		}
	}
})();
