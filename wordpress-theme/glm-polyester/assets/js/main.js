/* GLM Polyester : menu mobile, apparitions au défilement, filtres, galerie de fiche. */
(function () {
	'use strict';

	/* ----- Ombre de l'en-tête au défilement ----- */
	var header = document.querySelector('.site-header');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* ----- Menu mobile ----- */
	var burger = document.querySelector('.burger');
	var panel = document.getElementById('menu-mobile');
	if (burger && panel) {
		var setOpen = function (open) {
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			burger.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
			panel.classList.toggle('is-open', open);
		};
		burger.addEventListener('click', function () {
			setOpen(burger.getAttribute('aria-expanded') !== 'true');
		});
		// Un lien vers une ancre de la même page referme le menu.
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
			if (window.innerWidth > 1100) {
				setOpen(false);
			}
		});
	}

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
			target.hidden = !open;
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
	if ('IntersectionObserver' in window && reveals.length) {
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

	/* ----- Filtres de la galerie de réalisations ----- */
	var filtres = document.querySelectorAll('[data-filtre]');
	var items = document.querySelectorAll('#galerie .galerie__item');
	filtres.forEach(function (bouton) {
		bouton.addEventListener('click', function () {
			var valeur = bouton.getAttribute('data-filtre');
			filtres.forEach(function (autre) {
				var actif = autre === bouton;
				autre.classList.toggle('is-active', actif);
				autre.setAttribute('aria-pressed', actif ? 'true' : 'false');
			});
			items.forEach(function (item) {
				var types = (item.getAttribute('data-types') || '').split(' ');
				item.hidden = valeur !== '' && types.indexOf(valeur) === -1;
				item.classList.add('is-visible');
			});
		});
	});

	/* ----- Vignettes de la fiche modèle ----- */
	var grande = document.getElementById('fiche-photo-img');
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
			vignettes.forEach(function (autre) {
				autre.setAttribute('aria-pressed', autre === vignette ? 'true' : 'false');
			});
		});
	});

	/* ----- Visionneuse des réalisations (carrousel plein écran) ----- */
	var lightbox = document.getElementById('lightbox');
	if (lightbox && typeof lightbox.showModal === 'function') {
		var lbImg = lightbox.querySelector('.lightbox__img');
		var lbCaption = lightbox.querySelector('.lightbox__caption');
		var lbCount = lightbox.querySelector('.lightbox__count');
		var slides = [];
		var current = 0;
		var lastTrigger = null;

		var collect = function (galerie) {
			var list = [];
			galerie.querySelectorAll('.galerie__item').forEach(function (item) {
				if (item.hidden) {
					return;
				}
				var data = [];
				try {
					data = JSON.parse(item.getAttribute('data-slides') || '[]');
				} catch (e) {
					data = [];
				}
				data.forEach(function (slide) {
					list.push({ src: slide.src, alt: slide.alt, label: slide.label, caption: item.getAttribute('data-caption') || '', item: item });
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
			var item = trigger.closest('.galerie__item');
			var galerie = trigger.closest('.galerie');
			if (!item || !galerie) {
				return;
			}
			slides = collect(galerie);
			var start = 0;
			for (var i = 0; i < slides.length; i++) {
				if (slides[i].item === item) {
					start = i;
					break;
				}
			}
			lightbox.classList.toggle('lightbox--seule', slides.length < 2);
			lastTrigger = trigger;
			show(start);
			document.documentElement.classList.add('lightbox-open');
			lightbox.showModal();
			lightbox.querySelector('.lightbox__next').focus({ preventScroll: true });
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

	/* ----- Confirmation du formulaire : on amène le message à l'écran ----- */
	var avis = document.querySelector('.avis');
	if (avis) {
		avis.setAttribute('tabindex', '-1');
		avis.focus({ preventScroll: true });
	}
})();
