/* GLM Polyester : catalogue à feuilleter (bibliothèque StPageFlip, assets/vendor/page-flip.browser.js). */
(function () {
	'use strict';

	var root = document.getElementById('flip');
	var livre = document.getElementById('flip-livre');
	if (!root || !livre || !window.St || !window.St.PageFlip) {
		return;
	}

	var ratio = parseFloat(root.getAttribute('data-ratio')) || 1.4142;
	var prev = root.querySelector('[data-flip="prev"]');
	var next = root.querySelector('[data-flip="next"]');
	var plein = root.querySelector('[data-flip="plein"]');
	var pleinLabel = root.querySelector('.flip__plein-label');
	var compteur = root.querySelector('.flip__compteur');

	root.style.setProperty('--spread', String(ratio * 2));
	root.classList.add('is-ready');

	var largeur = 1100;
	var book = new window.St.PageFlip(livre, {
		width: largeur,
		height: Math.round(largeur / ratio),
		size: 'stretch',
		minWidth: 420,
		maxWidth: 1100,
		minHeight: 150,
		maxHeight: 1600,
		showCover: true,
		usePortrait: true,
		flippingTime: 800,
		maxShadowOpacity: 0.35,
		mobileScrollSupport: true
	});

	var maj = function () {
		var i = book.getCurrentPageIndex();
		var n = book.getPageCount();
		var paysage = book.getOrientation() === 'landscape';
		var texte;
		if (i === 0) {
			texte = 'Couverture';
		} else if (!paysage || i >= n - 1) {
			texte = 'Page ' + (i + 1) + ' / ' + n;
		} else {
			texte = 'Pages ' + (i + 1) + '–' + Math.min(i + 2, n) + ' / ' + n;
		}
		compteur.textContent = texte;
		prev.disabled = i === 0;
		next.disabled = i >= n - 1 || (paysage && i !== 0 && i + 1 >= n - 1);
	};

	book.on('init', maj);
	book.on('flip', maj);
	book.on('changeOrientation', maj);
	book.loadFromHTML(livre.querySelectorAll('.flip__page'));
	maj();

	prev.addEventListener('click', function () { book.flipPrev(); });
	next.addEventListener('click', function () { book.flipNext(); });

	document.addEventListener('keydown', function (event) {
		var cible = event.target;
		if (cible && (cible.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(cible.tagName))) {
			return;
		}
		if (event.key === 'ArrowRight') {
			book.flipNext();
		} else if (event.key === 'ArrowLeft') {
			book.flipPrev();
		} else if (event.key === 'Escape' && root.classList.contains('is-plein')) {
			setPlein(false);
		}
	});

	/* ----- Plein écran (API du navigateur, sinon affichage par-dessus la page) ----- */
	var redimensionner = function () {
		window.dispatchEvent(new Event('resize'));
	};
	var etiquette = function (actif) {
		plein.setAttribute('aria-pressed', actif ? 'true' : 'false');
		pleinLabel.textContent = actif ? 'Quitter le plein écran' : 'Plein écran';
	};
	var setPlein = function (actif) {
		root.classList.toggle('is-plein', actif);
		document.documentElement.classList.toggle('flip-ouvert', actif);
		etiquette(actif);
		redimensionner();
	};
	var natif = !!(root.requestFullscreen && document.fullscreenEnabled);

	plein.addEventListener('click', function () {
		if (document.fullscreenElement) {
			document.exitFullscreen();
			return;
		}
		if (root.classList.contains('is-plein')) {
			setPlein(false);
			return;
		}
		if (natif) {
			root.requestFullscreen().catch(function () { setPlein(true); });
		} else {
			setPlein(true);
		}
	});
	document.addEventListener('fullscreenchange', function () {
		var actif = document.fullscreenElement === root;
		root.classList.toggle('is-natif', actif);
		etiquette(actif);
		setTimeout(redimensionner, 50);
	});
})();
