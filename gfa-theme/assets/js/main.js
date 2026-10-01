/* GFA — interazioni e animazioni.
   Regola: nessun contenuto deve restare invisibile. Le animazioni partono da uno stato nascosto
   solo se tutto è pronto, e ogni errore riporta la pagina allo stato visibile. */
(function () {
  'use strict';

  var root = document.documentElement;

  function reduced() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  // Mostra tutto: usato quando le animazioni non possono partire o qualcosa va storto.
  function revealAll() {
    root.classList.remove('gfa-anim', 'gfa-preload');
    var pre = document.querySelector('.preloader');
    if (pre) { pre.remove(); }
    if (window.gsap) {
      window.gsap.set('.gfa-animated', { clearProps: 'opacity,visibility,transform,clipPath' });
    }
  }

  /* ---------- Menu mobile ---------- */
  function initMenu() {
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('site-nav');
    if (!toggle || !nav) { return; }
    function setOpen(open, focusToggle) {
      if (open) {
        var bottom = toggle.closest('.site-header').getBoundingClientRect().bottom;
        nav.style.setProperty('--nav-max', Math.max(200, window.innerHeight - bottom) + 'px');
      }
      nav.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.textContent = open ? 'Chiudi' : 'Menu';
      if (!open && focusToggle) { toggle.focus(); }
    }
    toggle.addEventListener('click', function () { setOpen(!nav.classList.contains('is-open')); });
    nav.addEventListener('click', function (e) { if (e.target.closest('a')) { setOpen(false); } });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) { setOpen(false, true); }
    });
    window.matchMedia('(min-width: 1181px)').addEventListener('change', function (mq) { if (mq.matches) { setOpen(false); } });
  }

  /* ---------- Header compatto ---------- */
  function initHeader() {
    var header = document.querySelector('.site-header');
    if (!header) { return; }
    var onScroll = function () { header.classList.toggle('is-compact', window.scrollY > 40); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- Logo: torna alla home e rivede il preload ---------- */
  function initBrand() {
    document.querySelectorAll('a.brand').forEach(function (a) {
      a.addEventListener('click', function () {
        try { window.sessionStorage.removeItem('gfaSeen'); } catch (e) { /* storage non disponibile */ }
      });
    });
  }

  /* ---------- Linea del percorso ---------- */
  function initRouteLines() {
    document.querySelectorAll('.route-line').forEach(function (line) {
      if (!line.querySelector('.route-progress')) {
        var bar = document.createElement('span');
        bar.className = 'route-progress';
        bar.setAttribute('aria-hidden', 'true');
        line.insertBefore(bar, line.firstChild);
      }
    });
  }

  /* ---------- Modulo preventivo a passi ---------- */
  function initForms() {
    document.querySelectorAll('[data-quote-form]').forEach(function (form) {
      var steps = Array.prototype.slice.call(form.querySelectorAll('fieldset[data-step]'));
      var marks = Array.prototype.slice.call(form.querySelectorAll('.form__steps li'));
      var back = form.querySelector('[data-back]');
      var next = form.querySelector('[data-next]');
      var send = form.querySelector('[data-send]');
      var error = form.querySelector('[data-step-error]');
      var current = 0;
      if (!steps.length || !back || !next || !send || !error) { return; }

      function show(i, focus) {
        current = i;
        steps.forEach(function (s, n) { s.hidden = n !== i; });
        marks.forEach(function (m, n) {
          m.classList.toggle('is-current', n === i);
          m.classList.toggle('is-done', n < i);
          if (n === i) { m.setAttribute('aria-current', 'step'); } else { m.removeAttribute('aria-current'); }
        });
        back.hidden = i === 0;
        next.hidden = i === steps.length - 1;
        send.hidden = i !== steps.length - 1;
        error.textContent = '';
        if (focus) {
          var first = steps[i].querySelector('input, select, textarea');
          if (first) { first.focus({ preventScroll: true }); }
        }
      }

      function valid(i) {
        var fields = steps[i].querySelectorAll('input, select, textarea');
        for (var k = 0; k < fields.length; k++) {
          if (!fields[k].checkValidity()) {
            var label = fields[k].getAttribute('data-label') || 'questo campo';
            error.textContent = 'Controlla ' + label + ' per continuare.';
            fields[k].focus();
            return false;
          }
        }
        return true;
      }

      next.addEventListener('click', function () { if (valid(current)) { show(current + 1, true); } });
      back.addEventListener('click', function () { show(current - 1, true); });

      form.addEventListener('submit', function (e) {
        if (!valid(current)) { e.preventDefault(); return; }
        if (form.hasAttribute('data-prototype')) {
          e.preventDefault();
          var ok = form.querySelector('.form__ok');
          steps.forEach(function (s) { s.hidden = true; });
          form.querySelector('.form__nav').hidden = true;
          form.querySelector('.form__steps').hidden = true;
          ok.hidden = false;
          ok.focus();
          return;
        }
        // Un solo invio.
        send.disabled = true;
        send.textContent = 'Invio in corso…';
      });

      show(0, false);
    });
  }

  initMenu();
  initHeader();
  initBrand();
  initRouteLines();
  initForms();

  /* ---------- Animazioni ---------- */
  if (!window.gsap || !window.ScrollTrigger || reduced()) { revealAll(); window.gfaReady = true; return; }

  try {
    initAnimations();
    window.gfaReady = true;
  } catch (err) {
    window.gfaReady = true;
    revealAll();
    if (window.console) { window.console.error('GFA: animazioni disattivate', err); }
  }

  function initAnimations() {
    var gsap = window.gsap;
    var ST = window.ScrollTrigger;
    var hasSplit = !!window.SplitText;
    gsap.registerPlugin(ST);
    if (hasSplit) { gsap.registerPlugin(window.SplitText); }
    var preloader = document.querySelector('.preloader');

    // Scroll morbido collegato a ScrollTrigger
    var lenis = null;
    if (window.Lenis) {
      lenis = new window.Lenis({ lerp: 0.1, anchors: { offset: -90 } });
      lenis.on('scroll', ST.update);
      gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
      gsap.ticker.lagSmoothing(0);
    }

    // Righe di testo che salgono: divise solo durante l'animazione, poi il testo torna normale.
    // Così un ridimensionamento della finestra non può lasciare parole fuori dalla maschera.
    function riseText(el, opts) {
      opts = opts || {};
      el.classList.add('gfa-animated');
      gsap.set(el, { visibility: 'visible' });
      if (!hasSplit) {
        return gsap.from(el, { y: 30, autoAlpha: 0, duration: 0.9, ease: 'power3.out', delay: opts.delay || 0 });
      }
      var split = window.SplitText.create(el, { type: 'lines,words', mask: 'lines', linesClass: 'split-line', aria: 'none' });
      return gsap.from(split.words, {
        yPercent: 115, duration: opts.duration || 1.1, stagger: opts.stagger || 0.05, ease: 'expo.out', delay: opts.delay || 0,
        onComplete: function () { split.revert(); }
      });
    }

    // Elementi che salgono e compaiono
    function rise(els, opts) {
      opts = opts || {};
      els = gsap.utils.toArray(els);
      if (!els.length) { return null; }
      els.forEach(function (el) { el.classList.add('gfa-animated'); });
      return gsap.fromTo(els, { y: opts.y === undefined ? 40 : opts.y, autoAlpha: 0 }, {
        y: 0, autoAlpha: 1, duration: opts.duration || 0.9, stagger: opts.stagger || 0.1, ease: 'power3.out', delay: opts.delay || 0,
        clearProps: 'transform', overwrite: true
      });
    }

    // 1. Hero
    function heroIntro() {
      var hero = document.querySelector('.hero');
      if (!hero) { return; }
      var h1 = hero.querySelector('h1');
      var hl = hero.querySelectorAll('mark.hl');
      var img = hero.querySelectorAll('.hero__visual img');
      if (h1) { riseText(h1, { duration: 1.3, stagger: 0.07 }); }
      gsap.fromTo(hl, { backgroundSize: '0% 100%' }, { backgroundSize: '100% 100%', duration: 0.9, ease: 'power2.inOut', delay: 0.8 });
      rise(hero.querySelectorAll('.eyebrow, .lead, .hero__cta .wp-block-button, .hero__proof li'), { y: 24, stagger: 0.08, delay: 0.35, duration: 1 });
      img.forEach(function (i) { i.classList.add('gfa-animated'); });
      gsap.fromTo(img, { clipPath: 'inset(100% 0% 0% 0%)', scale: 1.12 }, { clipPath: 'inset(0% 0% 0% 0%)', scale: 1, duration: 1.6, ease: 'expo.inOut', delay: 0.1, clearProps: 'clipPath,transform' });
      rise(hero.querySelectorAll('.ticket'), { y: 60, delay: 0.9, duration: 1.1 });
    }

    function startSite() {
      if (lenis) { lenis.start(); }
      heroIntro();
      ST.refresh();
    }

    // 2. Preload
    if (preloader && root.classList.contains('gfa-preload')) {
      try { window.sessionStorage.setItem('gfaSeen', '1'); } catch (e) { /* storage non disponibile */ }
      if (lenis) { lenis.stop(); }
      gsap.timeline({ onComplete: function () { root.classList.remove('gfa-preload'); preloader.remove(); } })
        .from('.preloader__box', { scale: 0.7, rotate: -6, autoAlpha: 0, duration: 0.5, ease: 'back.out(1.6)' })
        .from('.preloader__letter', { yPercent: 110, duration: 0.55, stagger: 0.07, ease: 'expo.out' }, 0.15)
        .fromTo('.preloader__route', { clipPath: 'inset(0% 100% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 0.7, ease: 'power2.inOut' }, 0.45)
        .from('.preloader__claim', { y: 12, autoAlpha: 0, duration: 0.45 }, 0.7)
        .to('.preloader__inner', { y: -24, autoAlpha: 0, duration: 0.4, ease: 'power2.in' }, '+=0.3')
        .to(preloader, { yPercent: -100, duration: 0.85, ease: 'expo.inOut', onStart: startSite }, '-=0.1');
    } else {
      root.classList.remove('gfa-preload');
      if (preloader) { preloader.remove(); }
      startSite();
    }

    // 3. Titoli di sezione
    gsap.utils.toArray('.section__head h2, .page-hero h1, .control__text h2, .quote-box__text h2').forEach(function (h) {
      if (h.closest('.hero')) { return; }
      var head = h.parentElement;
      ST.create({
        trigger: h, start: 'top 96%', once: true,
        onEnter: function () {
          riseText(h);
          rise(head.querySelectorAll(':scope > .eyebrow, :scope > .lead, :scope > .wp-block-buttons'), { y: 20, delay: 0.2, duration: 0.8 });
        }
      });
    });

    // 4. Card e blocchi: a gruppi
    var cards = '.path, .mode, .quote, .person, .zone-list li, .work, .route-step, .checks li, .faq details, .facts-list li, .price-table tr';
    ST.batch(cards, { start: 'top 97%', once: true, onEnter: function (els) { rise(els, { y: 50, stagger: 0.12 }); } });

    // 5. Report GPS
    var route = document.querySelector('.report__map .route-draw');
    if (route && route.getTotalLength) {
      var rlen = route.getTotalLength();
      gsap.set(route, { strokeDasharray: rlen, strokeDashoffset: rlen });
      gsap.timeline({ scrollTrigger: { trigger: '.report', start: 'top 75%', once: true } })
        .to(route, { strokeDashoffset: 0, duration: 2.6, ease: 'power1.inOut' })
        .from('.report__map .stop', { scale: 0, transformOrigin: 'center', duration: 0.45, stagger: 0.55, ease: 'back.out(2.5)' }, 0)
        .from('.report__rows li', { x: -20, autoAlpha: 0, duration: 0.5, stagger: 0.25 }, 0.6)
        .set(route, { strokeDasharray: '9 7', strokeDashoffset: 0 });
    }

    // 6. Linea dei passi legata allo scroll
    document.querySelectorAll('.route-progress').forEach(function (bar) {
      gsap.fromTo(bar, { scaleX: 0 }, { scaleX: 1, ease: 'none', scrollTrigger: { trigger: bar.parentElement, start: 'top 75%', end: 'bottom 50%', scrub: 0.6 } });
    });

    // 7. Mappa sedi
    if (document.querySelector('.north-map')) {
      gsap.timeline({ scrollTrigger: { trigger: '.north-map', start: 'top 80%', once: true } })
        .from('.north-map .dot', { scale: 0, transformOrigin: 'center', duration: 0.4, stagger: 0.12, ease: 'back.out(2.5)' })
        .from('.north-map text', { autoAlpha: 0, duration: 0.4, stagger: 0.12 }, 0.15);
    }

    // 8. Foto fuori dalla hero
    gsap.utils.toArray('.wp-block-image img').forEach(function (img) {
      if (img.closest('.hero')) { return; }
      gsap.from(img, { clipPath: 'inset(100% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut', clearProps: 'clipPath', scrollTrigger: { trigger: img, start: 'top 88%', once: true } });
    });

    // 9. Lavori: scorrimento orizzontale fissato solo se l'intero blocco sta nello schermo
    var pin = document.querySelector('.works-pin');
    var track = document.querySelector('.works');
    var pinTween = null;
    function setupPin() {
      if (pinTween) {
        if (pinTween.scrollTrigger) { pinTween.scrollTrigger.kill(true); }
        pinTween.kill();
        pinTween = null;
        track.classList.remove('is-pinned');
        gsap.set(track, { clearProps: 'transform' });
      }
      if (window.innerWidth < 1024) { return; }
      var header = document.querySelector('.site-header');
      var top = (header ? header.offsetHeight : 0) + 12;
      var distance = function () { return Math.max(0, track.scrollWidth - track.clientWidth); };
      if (pin.offsetHeight > window.innerHeight - top - 12 || distance() < 40) { return; }
      track.classList.add('is-pinned');
      pinTween = gsap.to(track, {
        x: function () { return -distance(); }, ease: 'none',
        scrollTrigger: { trigger: pin, start: 'top ' + top + 'px', end: function () { return '+=' + distance(); }, scrub: 0.8, pin: true, invalidateOnRefresh: true }
      });
    }
    if (pin && track) {
      setupPin();
      var resizeTimer = null;
      var lastSize = window.innerWidth + 'x' + window.innerHeight;
      window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
          var size = window.innerWidth + 'x' + window.innerHeight;
          if (size === lastSize) { return; }
          lastSize = size;
          setupPin();
          ST.refresh();
        }, 250);
      });
    }

    // Misure da ricalcolare quando arrivano font e immagini
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(function () { ST.refresh(); }); }
    window.addEventListener('load', function () { ST.refresh(); });

    // Rete di sicurezza: dopo 5 secondi niente di visibile nello schermo può restare nascosto.
    setTimeout(function () {
      document.querySelectorAll('.gfa-anim .hero *, .gfa-animated').forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (r.bottom > 0 && r.top < window.innerHeight && window.getComputedStyle(el).opacity === '0') {
          gsap.to(el, { autoAlpha: 1, y: 0, duration: 0.3 });
        }
      });
    }, 5000);
  }
})();
