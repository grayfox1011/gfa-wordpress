/* Google Ads / Meta: tags are fetched only after separate consent. No form data enters either SDK. */
(function () {
  'use strict';
  var config = window.GfaAdvertisingConfig || {};
  var consent = window.GfaCookieConsent;
  var google = config.google || {};
  var meta = config.meta || {};
  var activePage = config.trackingAllowed === true;
  var validGoogle = google.enabled === true && /^AW-[0-9]{6,15}$/.test(google.id || '') && /^[A-Za-z0-9_-]{1,100}$/.test(google.conversionLabel || '');
  var validMeta = meta.enabled === true && /^[0-9]{8,20}$/.test(meta.id || '');
  var state = { google: { requested: false, ready: false, sent: false }, meta: { requested: false, ready: false, sent: false } };
  var reloadScheduled = false;
  var receipt = config.receipt && /^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/.test(config.receipt.eventId || '') ? config.receipt : null;
  var eligible = { google: false, meta: false };
  var prior = { google: false, meta: false };

  // A receipt is a first-party proof, never an advertising URL parameter.
  try {
    var clean = new URL(window.location.href);
    if (clean.searchParams.has('gfa_receipt')) {
      clean.searchParams.delete('gfa_receipt');
      window.history.replaceState(window.history.state, '', clean.pathname + clean.search + clean.hash);
    }
  } catch (error) { activePage = false; /* Do not risk exposing an unremoved receipt to a third-party SDK. */ }
  if (!consent) { return; }

  function allows(provider) {
    if (!activePage) { return false; }
    return provider === 'google' ? validGoogle && consent.allowsGoogleAds() : validMeta && consent.allowsMetaAds();
  }

  // Queued locally; this creates no request and no cookie.
  window.dataLayer = window.dataLayer || [];
  window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
  function googleConsent(granted) {
    return { ad_storage: granted ? 'granted' : 'denied', ad_user_data: granted ? 'granted' : 'denied', ad_personalization: granted ? 'granted' : 'denied', analytics_storage: 'denied' };
  }
  window.gtag('consent', 'default', googleConsent(false));
  window.gtag('set', 'ads_data_redaction', true);
  window.gtag('set', 'url_passthrough', false);

  // No retrospective conversion: both submission and initial landing-page consent are required.
  eligible.google = !!(receipt && receipt.googleAds === true && allows('google'));
  eligible.meta = !!(receipt && receipt.metaAds === true && allows('meta'));

  function storageKey(provider) { return 'gfaAdsSent:' + provider + ':' + receipt.eventId; }
  function alreadySent(provider) {
    if (state[provider].sent) { return true; }
    // Optional deduplication storage is read only for the provider currently consented.
    if (!allows(provider) || !receipt) { return true; }
    try { return window.sessionStorage.getItem(storageKey(provider)) === '1'; } catch (error) { return false; }
  }
  function markSent(provider) {
    state[provider].sent = true;
    if (allows(provider)) { try { window.sessionStorage.setItem(storageKey(provider), '1'); } catch (error) { /* In-memory + server receipt + destination ID still deduplicate. */ } }
  }
  function convert(provider) {
    if (!eligible[provider] || !receipt || !state[provider].ready || !allows(provider) || alreadySent(provider)) { return; }
    // Mark first to prevent synchronous callbacks or another consent event from duplicating the event.
    markSent(provider);
    eligible[provider] = false;
    if (provider === 'google') {
      window.gtag('event', 'conversion', { send_to: google.id + '/' + google.conversionLabel, transaction_id: receipt.eventId });
    } else {
      window.fbq('trackSingle', meta.id, 'Lead', {}, { eventID: receipt.eventId });
    }
  }
  function appendScript(src, onload) {
    var script = document.createElement('script');
    script.async = true;
    script.src = src;
    script.onload = onload;
    document.head.appendChild(script);
  }
  function loadGoogle() {
    if (!allows('google')) { return; }
    if (state.google.requested) { convert('google'); return; }
    state.google.requested = true;
    window.gtag('consent', 'update', googleConsent(true));
    appendScript('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(google.id), function () {
      if (!allows('google') || reloadScheduled) { return; }
      state.google.ready = true;
      window.gtag('js', new Date());
      window.gtag('config', google.id, { allow_enhanced_conversions: false, page_location: config.pageUrl, send_page_view: false });
      convert('google');
    });
  }
  function metaStub() {
    if (window.fbq) { return; }
    var fbq = function () { if (fbq.callMethod) { fbq.callMethod.apply(fbq, arguments); } else { fbq.queue.push(arguments); } };
    window.fbq = fbq;
    if (!window._fbq) { window._fbq = fbq; }
    fbq.push = fbq;
    fbq.loaded = true;
    fbq.version = '2.0';
    fbq.queue = [];
  }
  function loadMeta() {
    if (!allows('meta')) { return; }
    if (state.meta.requested) { convert('meta'); return; }
    state.meta.requested = true;
    metaStub();
    appendScript('https://connect.facebook.net/en_US/fbevents.js', function () {
      if (!allows('meta') || reloadScheduled) { return; }
      state.meta.ready = true;
      window.fbq('consent', 'grant');
      window.fbq('set', 'autoConfig', false, meta.id);
      window.fbq('init', meta.id);
      window.fbq('trackSingle', meta.id, 'PageView');
      convert('meta');
    });
  }
  function clearProviderStorage(provider) {
    if (provider === 'google') {
      try { window.localStorage.removeItem('_gcl_ls'); } catch (error) { /* Google's first-party linker storage can also be blocked. */ }
    }
    try {
      var prefix = 'gfaAdsSent:' + provider + ':';
      for (var i = window.sessionStorage.length - 1; i >= 0; i--) {
        var key = window.sessionStorage.key(i);
        if (key && key.indexOf(prefix) === 0) { window.sessionStorage.removeItem(key); }
      }
    } catch (error) { /* Deletion is attempted even when optional storage is blocked. */ }
    // Only cookies owned by these integrations; HttpOnly and third-party cookies are inaccessible.
    var pattern = provider === 'google' ? /^_(?:gcl_|gac_)/ : /^_fb[pc]$/;
    var names;
    try {
      names = document.cookie.split(';').map(function (part) { return part.split('=')[0].trim(); }).filter(function (name) { return pattern.test(name); });
    } catch (error) { return; /* Cookie access must never prevent SDK revocation or the required reload. */ }
    var host = window.location.hostname;
    var domains = [''];
    var labels = host.split('.');
    for (var d = 0; d < labels.length - 1; d++) { domains.push(labels.slice(d).join('.')); domains.push('.' + labels.slice(d).join('.')); }
    var paths = ['/'];
    var segments = window.location.pathname.split('/').filter(Boolean);
    for (var p = 1; p <= segments.length; p++) { paths.push('/' + segments.slice(0, p).join('/')); paths.push('/' + segments.slice(0, p).join('/') + '/'); }
    names.forEach(function (name) { domains.forEach(function (domain) { paths.forEach(function (path) {
      try {
        document.cookie = name + '=; Max-Age=0; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=' + path + (domain ? '; domain=' + domain : '') + '; SameSite=Lax';
      } catch (error) { /* A blocked domain/path must not stop other deletions or SDK revocation. */ }
    }); }); });
  }
  function revoke(provider) {
    eligible[provider] = false;
    if (provider === 'google' && state.google.requested) { window.gtag('consent', 'update', googleConsent(false)); }
    if (provider === 'meta' && window.fbq && state.meta.requested) { window.fbq('consent', 'revoke'); }
    clearProviderStorage(provider);
    if (state[provider].requested && !reloadScheduled) {
      reloadScheduled = true;
      // A loaded SDK cannot be unloaded reliably; the next document starts without it.
      window.setTimeout(function () { window.location.reload(); }, 0);
    }
  }
  function sync() {
    var now = { google: allows('google'), meta: allows('meta') };
    ['google', 'meta'].forEach(function (provider) {
      if (!now[provider]) { eligible[provider] = false; }
      if (prior[provider] && !now[provider]) { revoke(provider); }
    });
    prior = now;
    if (!reloadScheduled) { loadGoogle(); loadMeta(); }
  }
  // Consent is captured at the real submit event, after any changes made while filling the form.
  document.querySelectorAll('form[data-quote-form]').forEach(function (form) {
    form.addEventListener('submit', function () {
      [['google', 'gfa_google_ads_consent'], ['meta', 'gfa_meta_ads_consent']].forEach(function (item) {
        var input = form.querySelector('input[name="' + item[1] + '"]');
        if (!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = item[1]; form.appendChild(input); }
        input.value = allows(item[0]) ? '1' : '0';
      });
    }, true);
  });
  window.addEventListener('gfa:consentchange', sync);
  window.addEventListener('pageshow', sync);
  if (!allows('google')) { clearProviderStorage('google'); }
  if (!allows('meta')) { clearProviderStorage('meta'); }
  sync();
})();
