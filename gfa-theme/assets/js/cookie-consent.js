/* Accessible first-party controls. This file never loads an advertising tag. */
(function () {
  'use strict';
  var consent = window.GfaCookieConsent;
  var config = window.GfaAdvertisingConfig || {};
  var banner = document.getElementById('gfa-cookie-banner');
  var dialog = document.getElementById('gfa-cookie-dialog');
  var functional = document.getElementById('gfa-cookie-functional');
  var google = document.getElementById('gfa-cookie-google');
  var meta = document.getElementById('gfa-cookie-meta');
  var status = document.getElementById('gfa-cookie-status');
  if (!consent || !banner || !dialog || !functional || !google || !meta || !status) { return; }
  var returnFocus = null;
  var statusTimer;
  google.disabled = !(config.google && config.google.enabled === true);
  meta.disabled = !(config.meta && config.meta.enabled === true);

  function updateBanner() { banner.hidden = consent.hasChoice() || dialog.open; }
  function updateControls() {
    functional.checked = consent.allowsFunctional();
    google.checked = !google.disabled && consent.allowsGoogleAds();
    meta.checked = !meta.disabled && consent.allowsMetaAds();
  }

  function closePreferences() {
    if (dialog.open) { dialog.close(); }
    updateBanner();
    var focusTarget = returnFocus && document.contains(returnFocus) && returnFocus.getClientRects().length ? returnFocus : document.querySelector('[data-gfa-cookie-open]');
    if (focusTarget) { focusTarget.focus({ preventScroll: true }); }
  }

  function openPreferences(trigger) {
    returnFocus = trigger || document.activeElement;
    updateControls();
    status.hidden = true;
    banner.hidden = true;
    dialog.showModal();
    dialog.querySelector('[data-gfa-cookie-action="close"]').focus({ preventScroll: true });
  }

  function save(values) {
    var source = document.activeElement;
    var saved = consent.save(values);
    closePreferences();
    updateBanner();
    if (banner.contains(source)) {
      var reopen = document.querySelector('[data-gfa-cookie-open]');
      if (reopen) { reopen.focus({ preventScroll: true }); }
    }
    clearTimeout(statusTimer);
    status.textContent = saved ? 'Preferenze salvate. Puoi modificarle o revocare il consenso dal link "Preferenze cookie" nel footer.' : 'Scelta applicata a questa pagina. Il browser non consente di salvarla: alla prossima visita potremmo richiederla.';
    status.hidden = false;
    statusTimer = setTimeout(function () { status.hidden = true; }, saved ? 6000 : 12000);
  }

  document.querySelectorAll('[data-gfa-cookie-open]').forEach(function (button) {
    button.hidden = false;
    button.addEventListener('click', function () { openPreferences(button); });
  });

  [banner, dialog].forEach(function (container) {
    container.addEventListener('click', function (event) {
      var button = event.target.closest('[data-gfa-cookie-action]');
      if (!button || !container.contains(button)) { return; }
      var action = button.getAttribute('data-gfa-cookie-action');
      if (action === 'accept') { save({ functional: true, googleAds: !google.disabled, metaAds: !meta.disabled }); }
      else if (action === 'reject') { save({ functional: false, googleAds: false, metaAds: false }); }
      else if (action === 'save') { save({ functional: functional.checked, googleAds: !google.disabled && google.checked, metaAds: !meta.disabled && meta.checked }); }
      else if (action === 'preferences') { openPreferences(button); }
      else if (action === 'close') { closePreferences(); }
    });
  });

  dialog.addEventListener('cancel', function (event) { event.preventDefault(); closePreferences(); });
  dialog.addEventListener('close', updateBanner);
  dialog.addEventListener('keydown', function (event) {
    if (event.key !== 'Tab') { return; }
    var controls = Array.prototype.slice.call(dialog.querySelectorAll('button:not([disabled]), a[href], input:not([disabled]), [tabindex="0"]'));
    var first = controls[0];
    var last = controls[controls.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });
  window.addEventListener('gfa:consentchange', function () { updateControls(); updateBanner(); });
  window.addEventListener('pageshow', function () { updateControls(); updateBanner(); });
  updateControls();
  updateBanner();
})();