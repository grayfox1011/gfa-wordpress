(function () {
  var d = document.documentElement;
  try {
    if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }
    var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var allowed = window.GfaCookieConsent && window.GfaCookieConsent.allowsFunctional();
    if (!reduced) {
      d.classList.add('gfa-anim');
      // Without consent the animation works without reading or writing session storage.
      if (!allowed) { d.classList.add('gfa-preload'); }
      else {
        var storage = window.sessionStorage;
        if (!storage.getItem('gfaSeen')) { d.classList.add('gfa-preload'); }
        else if (storage.getItem('gfaTrans') === '1') { d.classList.add('gfa-trans-in'); }
      }
    }
    if (allowed) { window.sessionStorage.removeItem('gfaTrans'); }
  } catch (error) { /* Storage is optional; the fail-safe still reveals the page. */ }
  setTimeout(function () {
    if (!window.gfaReady) { d.classList.remove('gfa-anim', 'gfa-preload', 'gfa-trans-in'); }
  }, 4000);
})();

