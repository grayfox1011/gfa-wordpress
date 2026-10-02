/* GFA — interazioni e animazioni.
   Regola: nessun contenuto deve restare invisibile. Le animazioni partono da uno stato nascosto
   solo se tutto è pronto, e ogni errore riporta la pagina allo stato visibile. */
(function () {
  'use strict';

  var root = document.documentElement;
  var lenis = null; // scroll morbido, creato con le animazioni
  var HEADER_OFFSET = 90;

  function reduced() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  // Esegue un pezzo di inizializzazione: se fallisce, gli altri partono lo stesso.
  function safely(fn) {
    try { fn(); } catch (err) { if (window.console) { window.console.error('GFA:', err); } }
  }

  /* ---------- Posizione di partenza ----------
     Pagine nuove e ricariche partono dall'alto; un link a una sezione (#preventivo) porta alla
     sezione; con "indietro" e "avanti" si torna dove si era. Il browser non ripristina da solo
     (history.scrollRestoration = 'manual' in preload-head.js), perché preload e dissolvenza
     ricalcolano la pagina: la posizione si salva quando si lascia la pagina, legata alla voce
     della cronologia (la stessa pagina visitata due volte ha due posizioni). */
  var SCROLL_KEY = (function () {
    try {
      var state = window.history.state;
      if (!state || typeof state !== 'object' || !state.gfaEntry) {
        var next = {};
        if (state && typeof state === 'object') {
          for (var k in state) { if (Object.prototype.hasOwnProperty.call(state, k)) { next[k] = state[k]; } }
        }
        next.gfaEntry = Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
        window.history.replaceState(next, '');
        state = next;
      }
      return 'gfaScroll:' + state.gfaEntry;
    } catch (e) {
      return 'gfaScroll:' + window.location.pathname + window.location.search;
    }
  })();

  function navigationType() {
    try {
      var nav = window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0];
      if (nav && nav.type) { return nav.type; }
      if (window.performance && window.performance.navigation) {
        return ['navigate', 'reload', 'back_forward'][window.performance.navigation.type] || 'navigate';
      }
    } catch (e) { /* API non disponibile */ }
    return 'navigate';
  }

  function savedScroll() {
    try {
      var value = parseInt(window.sessionStorage.getItem(SCROLL_KEY), 10);
      return isNaN(value) ? null : Math.max(0, value);
    } catch (e) { return null; }
  }

  function saveScroll() {
    try { window.sessionStorage.setItem(SCROLL_KEY, String(Math.round(window.scrollY))); } catch (e) { /* storage non disponibile */ }
  }

  // Numero di pixel da cui partire, oppure null quando decide il link alla sezione.
  var startY = (function () {
    if (navigationType() === 'back_forward') {
      var saved = savedScroll();
      if (saved !== null) { return saved; }
    }
    return window.location.hash.length > 1 ? null : 0;
  })();

  function scrollToY(y) {
    if (lenis) { lenis.scrollTo(y, { immediate: true, force: true }); } else { window.scrollTo(0, y); }
  }

  // Applica una posizione subito e di nuovo quando arrivano font e immagini (l'altezza della
  // pagina cambia), finché chi visita non scorre da sé.
  function settle(go) {
    var moved = false;
    var stop = function () { moved = true; };
    ['wheel', 'touchstart', 'keydown', 'mousedown'].forEach(function (type) {
      window.addEventListener(type, stop, { passive: true, once: true });
    });
    var run = function () { if (!moved) { go(); } };
    run();
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(run); }
    window.addEventListener('load', run);
    setTimeout(run, 700);
  }

  // Scorre fino a una sezione lasciando spazio all'header fisso.
  function scrollToTarget(target, immediate) {
    if (lenis) {
      lenis.scrollTo(target, { offset: -HEADER_OFFSET, immediate: !!immediate, force: true });
      return;
    }
    var y = target.getBoundingClientRect().top + window.scrollY - HEADER_OFFSET;
    window.scrollTo({ top: Math.max(0, y), behavior: immediate || reduced() ? 'auto' : 'smooth' });
  }

  // Mostra tutto: usato quando le animazioni non possono partire o qualcosa va storto.
  function revealAll() {
    root.classList.remove('gfa-anim', 'gfa-preload', 'gfa-trans-in');
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
    // Etichette tradotte da header.php.
    var labelClosed = toggle.getAttribute('data-label-closed') || toggle.textContent;
    var labelOpen = toggle.getAttribute('data-label-open') || 'Chiudi';
    function setOpen(open, focusToggle) {
      if (open) {
        var bottom = toggle.closest('.site-header').getBoundingClientRect().bottom;
        nav.style.setProperty('--nav-max', Math.max(200, window.innerHeight - bottom) + 'px');
      }
      nav.classList.toggle('is-open', open);
      // Menu aperto: la rotellina scorre il menu, non la pagina sotto (Lenis lo lascia stare).
      if (open) { nav.setAttribute('data-lenis-prevent', ''); } else { nav.removeAttribute('data-lenis-prevent'); }
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.textContent = open ? labelOpen : labelClosed;
      if (!open && focusToggle) { toggle.focus(); }
    }
    toggle.addEventListener('click', function () { setOpen(!nav.classList.contains('is-open')); });
    nav.addEventListener('click', function (e) { if (e.target.closest('a')) { setOpen(false); } });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) { setOpen(false, true); }
    });
    var wide = window.matchMedia('(min-width: 1181px)');
    var onWide = function (mq) { if (mq.matches) { setOpen(false); } };
    if (wide.addEventListener) { wide.addEventListener('change', onWide); } else if (wide.addListener) { wide.addListener(onWide); } // Safari prima della 14
  }

  /* ---------- Aree che scorrono di lato: raggiungibili da tastiera ----------
     La fascia dei lavori e le tabelle larghe ricevono tabindex ed etichetta solo mentre
     scorrono davvero; quando entrano nello spazio gli attributi aggiunti qui vengono tolti. */
  function initScrollRegions() {
    var regions = document.querySelectorAll('.works, .table-scroll, .wp-block-table, .prose table');
    if (!regions.length) { return; }
    function label(el) {
      if (el.classList.contains('works')) { return 'Lavori svolti'; }
      var caption = el.querySelector('figcaption, caption');
      return caption && caption.textContent.trim() ? caption.textContent.trim() : 'Tabella';
    }
    function update() {
      regions.forEach(function (el) {
        var overflow = window.getComputedStyle(el).overflowX;
        var scrolls = (overflow === 'auto' || overflow === 'scroll') && el.scrollWidth > el.clientWidth + 1;
        if (scrolls && !el.hasAttribute('tabindex')) {
          el.setAttribute('tabindex', '0');
          el.setAttribute('data-gfa-region', '');
          if (!el.hasAttribute('role')) { el.setAttribute('role', 'region'); el.setAttribute('data-gfa-role', ''); }
          if (!el.hasAttribute('aria-label') && !el.hasAttribute('aria-labelledby')) { el.setAttribute('aria-label', label(el)); el.setAttribute('data-gfa-label', ''); }
        } else if (!scrolls && el.hasAttribute('data-gfa-region')) {
          el.removeAttribute('tabindex');
          el.removeAttribute('data-gfa-region');
          if (el.hasAttribute('data-gfa-role')) { el.removeAttribute('role'); el.removeAttribute('data-gfa-role'); }
          if (el.hasAttribute('data-gfa-label')) { el.removeAttribute('aria-label'); el.removeAttribute('data-gfa-label'); }
        }
      });
    }
    var timer = null;
    update();
    window.addEventListener('load', update);
    window.addEventListener('resize', function () { clearTimeout(timer); timer = setTimeout(update, 200); });
  }

  /* ---------- Slider dei lavori ----------
     Dove la fascia scorre di lato (telefono, tablet, schermi bassi) contatore, barra e frecce
     dicono quanti lavori ci sono e dove si è; con la fascia fissata da GSAP restano nascosti. */
  function initWorksSliders() {
    document.querySelectorAll('[data-works-slider]').forEach(function (box) {
      var track = box.querySelector('.works');
      var nav = box.querySelector('[data-works-nav]');
      var cards = track ? track.querySelectorAll(':scope > .work') : [];
      if (!nav || cards.length < 2) { return; }
      var prev = nav.querySelector('[data-works-prev]');
      var next = nav.querySelector('[data-works-next]');
      var count = nav.querySelector('[data-works-count]');
      var bar = nav.querySelector('[data-works-bar]');
      var frame = null;
      var offset = function (card) { return card.offsetLeft - cards[0].offsetLeft; };
      function current() {
        var x = track.scrollLeft;
        if (x >= track.scrollWidth - track.clientWidth - 2) { return cards.length - 1; }
        var best = 0;
        for (var i = 1; i < cards.length; i++) {
          if (Math.abs(offset(cards[i]) - x) < Math.abs(offset(cards[best]) - x)) { best = i; }
        }
        return best;
      }
      function update() {
        frame = null;
        var max = track.scrollWidth - track.clientWidth;
        var active = max > 1 && !track.classList.contains('is-pinned') && /auto|scroll/.test(window.getComputedStyle(track).overflowX);
        nav.hidden = !active;
        box.classList.toggle('has-nav', active);
        if (!active) { return; }
        var text = (current() + 1) + ' / ' + cards.length;
        if (count.textContent !== text) { count.textContent = text; }
        prev.disabled = track.scrollLeft <= 2;
        next.disabled = track.scrollLeft >= max - 2;
        bar.style.width = (100 * track.clientWidth / track.scrollWidth) + '%';
        bar.style.transform = 'translateX(' + (100 * track.scrollLeft / track.clientWidth) + '%)';
      }
      function schedule() { if (!frame) { frame = window.requestAnimationFrame(update); } }
      function go(step) {
        var i = Math.max(0, Math.min(cards.length - 1, current() + step));
        track.scrollTo({ left: offset(cards[i]), behavior: reduced() ? 'auto' : 'smooth' });
      }
      prev.addEventListener('click', function () { go(-1); });
      next.addEventListener('click', function () { go(1); });
      track.addEventListener('scroll', schedule, { passive: true });
      window.addEventListener('resize', schedule);
      window.addEventListener('load', schedule);
      // La fascia fissata o liberata da GSAP cambia classe: il contatore compare o sparisce.
      if (window.MutationObserver) { new MutationObserver(schedule).observe(track, { attributes: true, attributeFilter: ['class'] }); }
      update();
    });
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
      var sendLabel = send.textContent;
      var msgCheck = form.getAttribute('data-msg-check') || 'Controlla %s per continuare.';
      var msgSending = form.getAttribute('data-msg-sending') || 'Invio in corso…';

      // Pulsante di invio pronto: all'apertura e tornando indietro dopo un invio, quando il
      // browser ripresenta la pagina con il pulsante ancora disattivato.
      function ready() {
        send.disabled = false;
        send.textContent = sendLabel;
      }

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
            error.textContent = msgCheck.replace('%s', label);
            fields[k].focus();
            return false;
          }
        }
        return true;
      }

      next.addEventListener('click', function () { if (valid(current)) { show(current + 1, true); } });
      back.addEventListener('click', function () { show(current - 1, true); });

      form.addEventListener('submit', function (e) {
        // Invio da tastiera prima dell'ultimo passo: si va al passo successivo, senza spedire
        // una richiesta priva di nome, email e consenso.
        if (current < steps.length - 1) {
          e.preventDefault();
          if (valid(current)) { show(current + 1, true); }
          return;
        }
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
        send.textContent = msgSending;
      });

      window.addEventListener('pageshow', function (e) { if (e.persisted) { ready(); } });
      ready();
      show(0, false);
    });
  }

  /* ---------- Posizione di partenza e passaggio tra pagine ---------- */
  var curtain = document.querySelector('.page-curtain');
  function initPageFlow() {
    if (startY !== null) { window.scrollTo(0, startY); }
    window.addEventListener('pagehide', saveScroll);

    // "Chiedi un preventivo" dell'header punta alla homepage: se il modulo è già in questa
    // pagina si scorre fin lì invece di cambiare pagina.
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[data-gfa-local]');
      if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
      var target = document.getElementById(a.getAttribute('data-gfa-local'));
      if (!target) { return; }
      e.preventDefault();
      scrollToTarget(target);
    });

    if (reduced() || !curtain) { return; }

    // Uscita: un velo copre tutta la pagina, poi si cambia pagina.
    var leaving = false;
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href]');
      if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
      if (a.target && a.target !== '_self') { return; }
      if (a.hasAttribute('download') || a.closest('#wpadminbar')) { return; }
      var url;
      try { url = new URL(a.href, window.location.href); } catch (err) { return; }
      if (url.origin !== window.location.origin || /\/wp-(admin|login)/i.test(url.pathname)) { return; }
      // Un file (PDF, documento, foglio, immagine, video…) non sostituisce la pagina: niente velo.
      if (/\.[a-z0-9]{2,5}$/i.test(url.pathname) && !/\.(php|html?)$/i.test(url.pathname)) { return; }
      if (url.pathname === window.location.pathname && url.search === window.location.search) { return; } // stessa pagina: ci pensa lo scroll
      if (!window.gsap) { return; }
      e.preventDefault();
      if (leaving) { return; }
      leaving = true;
      saveScroll();
      try { window.sessionStorage.setItem('gfaTrans', '1'); } catch (err) { /* storage non disponibile */ }
      var gone = false;
      var go = function () { if (!gone) { gone = true; window.location.href = url.href; } };
      if (a.classList.contains('brand')) { go(); return; } // il logo apre con il preload
      curtain.classList.add('is-active');
      window.gsap.fromTo(curtain, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.35, ease: 'power1.inOut', onComplete: go });
      setTimeout(go, 900); // se l'animazione si interrompe, si cambia pagina comunque
      // Rete di sicurezza: se dopo qualche secondo la pagina è ancora questa (risposta vuota,
      // navigazione annullata, server che non risponde) il velo si toglie.
      setTimeout(function () {
        leaving = false;
        curtain.classList.remove('is-active');
        window.gsap.set(curtain, { clearProps: 'all' });
        try { window.sessionStorage.removeItem('gfaTrans'); } catch (err) { /* storage non disponibile */ }
      }, 4000);
    });

    // Tornando indietro dalla cache del browser il velo non deve restare chiuso.
    window.addEventListener('pageshow', function (e) {
      if (!e.persisted) { return; }
      leaving = false;
      root.classList.remove('gfa-trans-in');
      curtain.classList.remove('is-active');
      if (window.gsap) { window.gsap.set(curtain, { clearProps: 'all' }); }
    });
  }

  safely(initPageFlow);
  safely(initMenu);
  safely(initHeader);
  safely(initBrand);
  safely(initRouteLines);
  safely(initForms);
  safely(initScrollRegions);
  safely(initWorksSliders);

  /* ---------- Animazioni ---------- */
  if (!window.gsap || !window.ScrollTrigger || reduced()) {
    revealAll();
    window.gfaReady = true;
    if (startY > 0) { settle(function () { scrollToY(startY); }); }
    return;
  }

  try {
    initAnimations();
    window.gfaReady = true;
  } catch (err) {
    window.gfaReady = true;
    revealAll();
    if (startY > 0) { settle(function () { scrollToY(startY); }); }
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
    if (window.Lenis) {
      lenis = new window.Lenis({ lerp: 0.1, anchors: { offset: -HEADER_OFFSET } });
      if (startY !== null) { lenis.scrollTo(startY, { immediate: true, force: true }); }
      lenis.on('scroll', ST.update);
      gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
      gsap.ticker.lagSmoothing(0);
    }

    // Righe di testo che salgono: divise solo durante l'animazione, poi il testo torna normale.
    // Così un ridimensionamento della finestra non può lasciare parole fuori dalla maschera.
    // Si dividono con il font del titolo già caricato: con quello di riserva, di larghezza diversa,
    // le righe cambierebbero a metà animazione. Il titolo resta nascosto al massimo 0,8 secondi.
    function riseText(el, opts) {
      opts = opts || {};
      var started = false;
      var start = function () {
        if (started) { return; }
        started = true;
        el.classList.add('gfa-animated');
        gsap.set(el, { visibility: 'visible' });
        if (!hasSplit) {
          gsap.from(el, { y: 30, autoAlpha: 0, duration: 0.9, ease: 'power3.out', delay: opts.delay || 0 });
          return;
        }
        var split = window.SplitText.create(el, { type: 'lines,words', mask: 'lines', linesClass: 'split-line', aria: 'none' });
        gsap.from(split.words, {
          yPercent: 115, duration: opts.duration || 1.1, stagger: opts.stagger || 0.05, ease: 'expo.out', delay: opts.delay || 0,
          onComplete: function () { split.revert(); }
        });
      };
      var ready = true;
      try {
        var style = window.getComputedStyle(el);
        var font = style.fontWeight + ' 1em ' + style.fontFamily;
        ready = !document.fonts || document.fonts.check(font);
        if (!ready) { document.fonts.load(font).then(start, start); }
      } catch (e) {
        ready = true;
      }
      if (ready) { start(); } else { setTimeout(start, 800); }
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
      // Link a una sezione da un'altra pagina, o ritorno con "indietro": si arriva al punto giusto
      // quando le misure sono definitive (font, immagini, sezioni fissate).
      var place = null;
      if (startY === null) {
        var target = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
        if (target) { place = function () { scrollToTarget(target, true); }; }
      } else if (startY > 0) {
        place = function () { scrollToY(startY); };
      }
      if (place) {
        window.addEventListener('load', function () { ST.refresh(); });
        settle(place);
      }
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
    } else if (curtain && root.classList.contains('gfa-trans-in')) {
      // Ingresso da un'altra pagina: la tendina si apre quando la pagina è pronta e in posizione.
      root.classList.remove('gfa-preload');
      if (preloader) { preloader.remove(); }
      if (lenis) { lenis.stop(); }
      gsap.set(curtain, { autoAlpha: 1 });
      var opened = false;
      var open = function () {
        if (opened) { return; }
        opened = true;
        ST.refresh();
        if (startY !== null) { scrollToY(startY); }
        startSite();
        gsap.to(curtain, {
          autoAlpha: 0, duration: 0.5, ease: 'power1.out',
          onComplete: function () { root.classList.remove('gfa-trans-in'); gsap.set(curtain, { clearProps: 'all' }); }
        });
      };
      if (document.fonts && document.fonts.ready) { document.fonts.ready.then(open); }
      setTimeout(open, 600);
    } else {
      root.classList.remove('gfa-preload', 'gfa-trans-in');
      if (preloader) { preloader.remove(); }
      startSite();
    }

    // 3. Titoli di sezione
    gsap.utils.toArray('.section__head h2, .page-hero h1, .control__text h2, .quote-box__text h2').forEach(function (h) {
      if (h.closest('.hero')) { return; }
      var head = h.parentElement;
      // Parte quando entra il blocco (etichetta, titolo, testo): l'etichetta sopra il titolo,
      // nascosta in partenza, non resta mai invisibile in fondo allo schermo.
      ST.create({
        trigger: head, start: 'top 96%', once: true,
        onEnter: function () {
          riseText(h);
          rise(head.querySelectorAll(':scope > .eyebrow, :scope > .lead, :scope > .wp-block-buttons'), { y: 20, delay: 0.2, duration: 0.8 });
        }
      });
    });

    // 4. Card e blocchi: a gruppi
    var cards = '.path, .mode, .quote, .person, .zone-list li, .work, .route-step, .checks li, .faq details, .facts-list li';
    ST.batch(cards, { start: 'top 97%', once: true, onEnter: function (els) { rise(els, { y: 50, stagger: 0.12 }); } });
    // Righe delle tabelle: compaiono senza spostarsi. Una riga spostata usciva dal riquadro della
    // tabella, che scorre di lato, e faceva comparire per un attimo la sua barra di scorrimento.
    ST.batch('.price-table tr', { start: 'top 97%', once: true, onEnter: function (els) { rise(els, { y: 0, stagger: 0.08 }); } });

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
    var track = pin ? pin.querySelector('.works') : null;
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
      // Contatore e frecce dello slider servono solo se la fascia non è fissata: non contano nella misura.
      var nav = pin.querySelector('[data-works-nav]');
      var navHeight = nav && !nav.hidden ? nav.offsetHeight + parseFloat(window.getComputedStyle(nav).marginTop) : 0;
      if (pin.offsetHeight - navHeight > window.innerHeight - top - 12 || distance() < 40) { return; }
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

    // 10. Slider dei lavori: la prima volta che compare la fascia si sposta un poco di lato e torna,
    // così si vede che si può trascinare. Si ferma appena la si tocca.
    document.querySelectorAll('[data-works-slider]').forEach(function (box) {
      var strip = box.querySelector('.works');
      if (!strip) { return; }
      ST.create({
        trigger: strip, start: 'top 75%', once: true,
        onEnter: function () {
          if (!box.classList.contains('has-nav') || strip.scrollLeft > 0) { return; }
          var restore = function () { strip.style.scrollSnapType = ''; };
          strip.style.scrollSnapType = 'none';
          var nudge = gsap.to(strip, {
            scrollLeft: Math.min(72, strip.scrollWidth - strip.clientWidth), duration: 0.5, delay: 0.5, ease: 'power2.out',
            yoyo: true, repeat: 1, repeatDelay: 0.25, onComplete: restore
          });
          ['pointerdown', 'touchstart', 'wheel', 'keydown'].forEach(function (type) {
            strip.addEventListener(type, function () { nudge.kill(); restore(); }, { passive: true, once: true });
          });
        }
      });
    });

    // Misure da ricalcolare quando arrivano font e immagini
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(function () { ST.refresh(); }); }
    window.addEventListener('load', function () { ST.refresh(); });

    // Rete di sicurezza: dopo 5 secondi niente di visibile nello schermo può restare nascosto.
    setTimeout(function () {
      document.querySelectorAll('.gfa-anim .hero *, .gfa-animated, .gfa-anim .eyebrow, .gfa-anim .lead, .gfa-anim .wp-block-buttons').forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (r.bottom > 0 && r.top < window.innerHeight && window.getComputedStyle(el).opacity === '0') {
          gsap.to(el, { autoAlpha: 1, y: 0, duration: 0.3 });
        }
      });
    }, 5000);
  }
})();
