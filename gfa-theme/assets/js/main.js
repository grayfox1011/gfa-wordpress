/* GFA — interazioni e animazioni (GSAP + ScrollTrigger, se presenti) */
(function () {
  'use strict';

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

  // Animazioni: tutto è già visibile a riposo, GSAP aggiunge solo movimento.
  if (!window.gsap || reduced()) { return; }
  var gsap = window.gsap;
  if (window.ScrollTrigger) { gsap.registerPlugin(window.ScrollTrigger); }

  // Hero: le righe del titolo salgono in sequenza
  gsap.from('.hero h1 .line', { yPercent: 35, duration: 0.8, ease: 'power3.out', stagger: 0.12 });
  gsap.from('.ticket', { y: 30, rotate: -3, duration: 0.9, ease: 'power3.out', delay: 0.35 });

  // Percorso GPS nel report: il tracciato si disegna come un giro reale
  var route = document.querySelector('.report__map .route-draw');
  if (route && route.getTotalLength) {
    var len = route.getTotalLength();
    route.style.strokeDasharray = len;
    route.style.strokeDashoffset = 0;
    gsap.from(route, {
      strokeDashoffset: len, duration: 2.4, ease: 'none',
      scrollTrigger: window.ScrollTrigger ? { trigger: '.report', start: 'top 75%' } : undefined
    });
    gsap.from('.report__map .stop', {
      scale: 0.4, transformOrigin: 'center', duration: 0.4, stagger: 0.35, ease: 'back.out(2)',
      scrollTrigger: window.ScrollTrigger ? { trigger: '.report', start: 'top 75%' } : undefined
    });
  }

  if (!window.ScrollTrigger) { return; }

  // Metodo: la linea blu avanza con lo scroll
  var progress = document.querySelector('.route-progress');
  if (progress) {
    gsap.fromTo(progress, { scaleX: 0.08 }, {
      scaleX: 1, ease: 'none',
      scrollTrigger: { trigger: '.route-line', start: 'top 80%', end: 'bottom 55%', scrub: true }
    });
  }

  // Card e blocchi: salgono di poco quando entrano (niente dissolvenze da zero)
  gsap.utils.toArray('.path, .mode, .quote, .zone-list li').forEach(function (el, i) {
    gsap.from(el, {
      y: 24, duration: 0.6, ease: 'power2.out', delay: (i % 3) * 0.08,
      scrollTrigger: { trigger: el, start: 'top 90%' }
    });
  });

  // Mappa sedi: i punti compaiono uno alla volta
  gsap.from('.north-map .dot', {
    scale: 0, transformOrigin: 'center', duration: 0.35, stagger: 0.1, ease: 'back.out(2)',
    scrollTrigger: { trigger: '.north-map', start: 'top 80%' }
  });

  // Lavori: scorrimento orizzontale fissato, solo su schermi larghi
  var mm = gsap.matchMedia();
  mm.add('(min-width: 1024px)', function () {
    var track = document.querySelector('.works');
    if (!track) { return; }
    var distance = function () { return Math.max(0, track.scrollWidth - track.clientWidth); };
    if (distance() < 40) { return; }
    track.style.overflowX = 'visible';
    var tween = gsap.to(track, {
      x: function () { return -distance(); }, ease: 'none',
      scrollTrigger: { trigger: '.works-pin', start: 'top 12%', end: function () { return '+=' + distance(); }, scrub: true, pin: true, invalidateOnRefresh: true }
    });
    return function () { tween.scrollTrigger && tween.scrollTrigger.kill(); tween.kill(); track.style.overflowX = ''; gsap.set(track, { x: 0 }); };
  });
})();
