/* GFA — interazioni e animazioni (GSAP + ScrollTrigger, se presenti) */
(function () {
  'use strict';
  window.gfaReady = true;

  // Menu mobile
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Header compatto allo scroll
  var header = document.querySelector('.site-header');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-compact', window.scrollY > 40); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Linea del percorso: aggiunta da script, così nei blocchi resta solo il contenuto
  document.querySelectorAll('.route-line').forEach(function (line) {
    if (!line.querySelector('.route-progress')) {
      var bar = document.createElement('span');
      bar.className = 'route-progress';
      bar.setAttribute('aria-hidden', 'true');
      line.insertBefore(bar, line.firstChild);
    }
  });

  // Modulo preventivo a passi
  document.querySelectorAll('[data-quote-form]').forEach(function (form) {
    var steps = Array.prototype.slice.call(form.querySelectorAll('fieldset[data-step]'));
    var marks = Array.prototype.slice.call(form.querySelectorAll('.form__steps li'));
    var back = form.querySelector('[data-back]');
    var next = form.querySelector('[data-next]');
    var send = form.querySelector('[data-send]');
    var error = form.querySelector('.form__error');
    var current = 0;

    function show(i) {
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
      if (window.gsap && !reduced()) {
        window.gsap.fromTo(steps[i], { x: 16 }, { x: 0, duration: 0.3, ease: 'power2.out' });
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

    next.addEventListener('click', function () { if (valid(current)) { show(current + 1); } });
    back.addEventListener('click', function () { show(current - 1); });

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
      }
    });

    show(0);
  });

  function reduced() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  var root = document.documentElement;
  var preloader = document.querySelector('.preloader');
  function endPreload() {
    root.classList.remove('gfa-preload');
    if (preloader) { preloader.remove(); }
  }

  // Senza GSAP o con "riduci animazioni": nessun movimento, tutto subito visibile.
  if (!window.gsap || reduced()) { endPreload(); root.classList.remove('gfa-anim'); return; }
  var gsap = window.gsap;
  var ST = window.ScrollTrigger;
  if (ST) { gsap.registerPlugin(ST); }
  if (window.SplitText) { gsap.registerPlugin(window.SplitText); }

  // Scroll morbido (Lenis) collegato a ScrollTrigger
  var lenis = null;
  if (window.Lenis) {
    lenis = new window.Lenis({ lerp: 0.1, wheelMultiplier: 1, anchors: { offset: -90 } });
    if (ST) { lenis.on('scroll', ST.update); }
    gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
    gsap.ticker.lagSmoothing(0);
    lenis.stop();
  }

  // Testo diviso in righe con maschera: le righe salgono dal basso
  function splitLines(el, opts) {
    if (!window.SplitText || !el) { return null; }
    return window.SplitText.create(el, { type: 'lines,words', mask: 'lines', linesClass: 'split-line', autoSplit: true, onSplit: opts && opts.onSplit });
  }

  // 1. Hero, dopo il preload
  function heroIntro() {
    var hero = document.querySelector('.hero');
    if (!hero) { return; }
    var tl = gsap.timeline({ defaults: { ease: 'expo.out' } });
    var h1 = hero.querySelector('h1');
    var hl = hero.querySelectorAll('mark.hl');
    gsap.set(hl, { backgroundSize: '0% 100%' });
    if (h1) {
      gsap.set(h1, { visibility: 'visible' });
      if (window.SplitText) {
        splitLines(h1, { onSplit: function (self) {
          return tl.from(self.words, { yPercent: 115, duration: 1.3, stagger: 0.07 }, 0);
        } });
      } else {
        tl.from(h1, { y: 40, autoAlpha: 0, duration: 1 }, 0);
      }
    }
    tl.to(hl, { backgroundSize: '100% 100%', duration: 0.9, ease: 'power2.inOut' }, 0.8)
      .fromTo(hero.querySelectorAll('.eyebrow, .lead, .hero__cta .wp-block-button, .hero__proof li'), { y: 24, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 1, stagger: 0.08 }, 0.35)
      .fromTo(hero.querySelectorAll('.hero__visual img'), { clipPath: 'inset(100% 0% 0% 0%)', scale: 1.15 }, { clipPath: 'inset(0% 0% 0% 0%)', scale: 1, duration: 1.6, ease: 'expo.inOut' }, 0.1)
      .fromTo(hero.querySelectorAll('.ticket'), { y: 60, rotate: -4, autoAlpha: 0 }, { y: 0, rotate: 0, autoAlpha: 1, duration: 1.1 }, 0.9);
  }

  // 2. Preload: logo, percorso che si disegna, poi la tenda sale
  function startSite() {
    if (lenis) { lenis.start(); }
    heroIntro();
    if (ST) { ST.refresh(); }
  }
  if (preloader && root.classList.contains('gfa-preload')) {
    try { sessionStorage.setItem('gfaSeen', '1'); } catch (e) { /* storage non disponibile */ }
    var path = preloader.querySelector('.preloader__route path');
    var len = path ? path.getTotalLength() : 0;
    if (path) { gsap.set(path, { strokeDasharray: '10 10', strokeDashoffset: 0 }); }
    gsap.timeline({ onComplete: function () { endPreload(); } })
      .from('.preloader__box', { scale: 0.7, rotate: -6, autoAlpha: 0, duration: 0.5, ease: 'back.out(1.6)' })
      .from('.preloader__letter', { yPercent: 110, duration: 0.55, stagger: 0.07, ease: 'expo.out' }, 0.15)
      .fromTo('.preloader__route', { clipPath: 'inset(0% 100% 0% 0%)' }, { clipPath: 'inset(0% 0% 0% 0%)', duration: 0.7, ease: 'power2.inOut' }, 0.45)
      .from('.preloader__claim', { y: 12, autoAlpha: 0, duration: 0.45 }, 0.7)
      .to('.preloader__inner', { y: -24, autoAlpha: 0, duration: 0.4, ease: 'power2.in' }, '+=0.3')
      .to(preloader, { yPercent: -100, duration: 0.85, ease: 'expo.inOut', onStart: startSite }, '-=0.1');
    void len;
  } else {
    endPreload();
    startSite();
  }

  // 3. Percorso GPS nel report: il tracciato si disegna come un giro reale
  var route = document.querySelector('.report__map .route-draw');
  if (route && route.getTotalLength && ST) {
    var rlen = route.getTotalLength();
    gsap.set(route, { strokeDasharray: rlen, strokeDashoffset: rlen });
    var rtl = gsap.timeline({ scrollTrigger: { trigger: '.report', start: 'top 70%', once: true } });
    rtl.to(route, { strokeDashoffset: 0, duration: 2.6, ease: 'power1.inOut' })
      .from('.report__map .stop', { scale: 0, transformOrigin: 'center', duration: 0.45, stagger: 0.55, ease: 'back.out(2.5)' }, 0)
      .from('.report__rows li', { x: -20, autoAlpha: 0, duration: 0.5, stagger: 0.25 }, 0.6)
      .set(route, { strokeDasharray: '9 7', strokeDashoffset: 0 });
  }

  if (!ST) { return; }

  // 4. Titoli di sezione: righe che salgono quando la sezione entra
  document.querySelectorAll('.section__head h2, .page-hero h1, .control__text h2, .quote-box__text h2').forEach(function (h) {
    if (h.closest('.hero')) { return; }
    var head = h.parentElement;
    gsap.set(h, { visibility: 'visible' });
    var tl = gsap.timeline({ scrollTrigger: { trigger: h, start: 'top 85%', once: true } });
    splitLines(h, { onSplit: function (self) {
      return tl.from(self.words, { yPercent: 115, duration: 1.1, stagger: 0.05, ease: 'expo.out' }, 0);
    } });
    if (!window.SplitText) { tl.from(h, { y: 30, autoAlpha: 0, duration: 0.9 }, 0); }
    tl.from(head.querySelectorAll(':scope > .eyebrow, :scope > .lead, :scope > .wp-block-buttons'), { y: 20, autoAlpha: 0, duration: 0.8, stagger: 0.1, ease: 'power3.out' }, 0.2);
  });

  // 5. Card e blocchi: entrano a gruppi, uno dopo l'altro
  var cards = '.path, .mode, .quote, .person, .zone-list li, .work, .route-step, .checks li, .faq details, .facts-list li, .price-table tr';
  ST.batch(cards, {
    start: 'top 88%',
    once: true,
    onEnter: function (els) {
      gsap.fromTo(els, { y: 50, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.9, stagger: 0.12, ease: 'power3.out', overwrite: true });
    }
  });

  // 6. Metodo: la linea blu avanza con lo scroll
  document.querySelectorAll('.route-progress').forEach(function (bar) {
    gsap.fromTo(bar, { scaleX: 0 }, {
      scaleX: 1, ease: 'none',
      scrollTrigger: { trigger: bar.parentElement, start: 'top 75%', end: 'bottom 50%', scrub: 0.6 }
    });
  });

  // 7. Mappa sedi: i punti compaiono uno alla volta
  if (document.querySelector('.north-map')) {
    gsap.timeline({ scrollTrigger: { trigger: '.north-map', start: 'top 80%', once: true } })
      .from('.north-map .land', { autoAlpha: 0, scale: 0.96, transformOrigin: 'center', duration: 0.8 })
      .from('.north-map .dot', { scale: 0, transformOrigin: 'center', duration: 0.4, stagger: 0.12, ease: 'back.out(2.5)' }, 0.3)
      .from('.north-map text', { autoAlpha: 0, duration: 0.4, stagger: 0.12 }, 0.45);
  }

  // 8. Foto: si scoprono dal basso
  gsap.utils.toArray('.photo-todo img, .wp-block-image img').forEach(function (img) {
    if (img.closest('.hero')) { return; }
    gsap.from(img, { clipPath: 'inset(100% 0% 0% 0%)', duration: 1.3, ease: 'expo.inOut', scrollTrigger: { trigger: img, start: 'top 85%', once: true } });
  });

  // 9. Lavori: scorrimento orizzontale fissato, solo su schermi larghi
  var mm = gsap.matchMedia();
  mm.add('(min-width: 1024px)', function () {
    var track = document.querySelector('.works');
    if (!track) { return; }
    var distance = function () { return Math.max(0, track.scrollWidth - track.clientWidth); };
    if (distance() < 40) { return; }
    track.style.overflowX = 'visible';
    var tween = gsap.to(track, {
      x: function () { return -distance(); }, ease: 'none',
      scrollTrigger: { trigger: '.works-pin', start: 'top 12%', end: function () { return '+=' + distance(); }, scrub: 0.8, pin: true, invalidateOnRefresh: true }
    });
    return function () { tween.scrollTrigger && tween.scrollTrigger.kill(); tween.kill(); track.style.overflowX = ''; gsap.set(track, { x: 0 }); };
  });

  // Le misure cambiano quando arrivano font e immagini
  window.addEventListener('load', function () { ST.refresh(); });
})();
