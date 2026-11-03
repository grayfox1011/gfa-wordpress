/* First-party choices only. Advertising tags are loaded by advertising.js after consent. */
(function () {
  'use strict';
  var KEY = 'gfaConsent';
  var VERSION = 2;
  var config = window.GfaAdvertisingConfig || {};
  var POLICY = '2026-11-02.ads-v1:' + (typeof config.revision === 'string' ? config.revision : 'off');
  var available = {
    googleAds: !!(config.google && config.google.enabled === true),
    metaAds: !!(config.meta && config.meta.enabled === true)
  };
  var memory = null;
  var persisted = false;
  var expiryTimer;

  function expiry(timestamp) {
    var date = new Date(timestamp);
    var day = date.getUTCDate();
    date.setUTCDate(1);
    date.setUTCMonth(date.getUTCMonth() + 6);
    var lastDay = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + 1, 0)).getUTCDate();
    date.setUTCDate(Math.min(day, lastDay));
    return date.getTime();
  }

  function snapshot(item) {
    return item ? {
      version: item.version, policyVersion: item.policyVersion,
      updatedAt: item.updatedAt, expiresAt: item.expiresAt,
      functional: item.functional, googleAds: item.googleAds, metaAds: item.metaAds
    } : null;
  }

  function validate(raw) {
    if (!raw || typeof raw !== 'string') { return null; }
    try {
      var item = JSON.parse(raw);
      var now = Date.now();
      if (!item || item.version !== VERSION || item.policyVersion !== POLICY ||
          typeof item.functional !== 'boolean' || typeof item.googleAds !== 'boolean' || typeof item.metaAds !== 'boolean' ||
          !Number.isSafeInteger(item.updatedAt) || !Number.isSafeInteger(item.expiresAt) ||
          item.updatedAt <= 0 || item.updatedAt > now || item.expiresAt !== expiry(item.updatedAt) || item.expiresAt <= now) { return null; }
      item.googleAds = item.googleAds && available.googleAds;
      item.metaAds = item.metaAds && available.metaAds;
      return snapshot(item);
    } catch (error) { return null; }
  }

  function clearOptional() {
    try {
      window.sessionStorage.removeItem('gfaSeen');
      window.sessionStorage.removeItem('gfaTrans');
    } catch (error) { /* Removing old optional memories is best effort. */ }
  }

  // Use a snapshot without calling current(): listeners may query the choice again.
  function notify() {
    window.dispatchEvent(new CustomEvent('gfa:consentchange', { detail: { choice: snapshot(memory), persisted: persisted } }));
  }

  function scheduleExpiry() {
    clearTimeout(expiryTimer);
    if (memory) {
      // Browser timers are limited to about 24.8 days; re-arm until the actual expiry.
      expiryTimer = setTimeout(function () { current(); scheduleExpiry(); }, Math.min(Math.max(1, memory.expiresAt - Date.now()), 2147483647));
    }
  }

  function current() {
    if (memory && memory.expiresAt <= Date.now()) {
      memory = null;
      persisted = false;
      clearTimeout(expiryTimer);
      try { window.localStorage.removeItem(KEY); } catch (error) { /* Expired choices remain denied even if removal is blocked. */ }
      clearOptional();
      notify();
    }
    return memory;
  }

  function copy() { return snapshot(current()); }

  function refreshPersisted() {
    // A restored document can have stale memory: same-tab changes do not emit storage events.
    // An explicit page-only choice remains usable when storage was unavailable at save time.
    if (!persisted) { return copy(); }
    var next = null;
    try { next = validate(window.localStorage.getItem(KEY)); } catch (error) { /* Unverifiable persisted choices fail closed. */ }
    var changed = JSON.stringify(snapshot(memory)) !== JSON.stringify(next);
    memory = next;
    persisted = !!next;
    if (!memory || !memory.functional) { clearOptional(); }
    scheduleExpiry();
    if (changed) { notify(); }
    return snapshot(memory);
  }

  try {
    var stored = window.localStorage.getItem(KEY);
    memory = validate(stored);
    persisted = !!memory;
    if (stored && !memory) { window.localStorage.removeItem(KEY); }
  } catch (error) { /* The current page still supports an in-memory choice. */ }
  if (!memory || !memory.functional) { clearOptional(); }

  window.GfaCookieConsent = {
    getChoice: copy,
    hasChoice: function () { return !!current(); },
    allowsFunctional: function () { var choice = current(); return !!(choice && choice.functional); },
    allowsGoogleAds: function () { var choice = current(); return !!(choice && choice.googleAds && available.googleAds); },
    allowsMetaAds: function () { var choice = current(); return !!(choice && choice.metaAds && available.metaAds); },
    isPersisted: function () { current(); return persisted; },
    recheck: refreshPersisted,
    save: function (values) {
      if (!values || typeof values !== 'object' || Array.isArray(values) ||
          typeof values.functional !== 'boolean' || typeof values.googleAds !== 'boolean' || typeof values.metaAds !== 'boolean') {
        throw new TypeError('Consent requires functional, googleAds and metaAds booleans.');
      }
      var now = Date.now();
      memory = {
        version: VERSION, policyVersion: POLICY, updatedAt: now, expiresAt: expiry(now),
        functional: values.functional,
        googleAds: values.googleAds && available.googleAds,
        metaAds: values.metaAds && available.metaAds
      };
      persisted = false;
      if (!memory.functional) { clearOptional(); }
      try {
        var serialized = JSON.stringify(memory);
        window.localStorage.setItem(KEY, serialized);
        persisted = window.localStorage.getItem(KEY) === serialized;
      } catch (error) { /* The UI explains when persistence failed. */ }
      if (!persisted) {
        try { window.localStorage.removeItem(KEY); } catch (error) { /* Storage is entirely blocked. */ }
      }
      scheduleExpiry();
      notify();
      return persisted;
    }
  };

  window.addEventListener('storage', function (event) {
    if (event.key !== KEY && event.key !== null) { return; }
    if (event.storageArea) {
      try {
        if (event.storageArea !== window.localStorage) { return; }
      } catch (error) {
        // A storage policy can change while the page is open. Revoke rather than trusting an unverifiable event.
        memory = null;
        persisted = false;
        clearOptional();
        scheduleExpiry();
        notify();
        return;
      }
    }
    memory = validate(event.newValue);
    persisted = !!memory;
    if (!memory || !memory.functional) { clearOptional(); }
    scheduleExpiry();
    notify();
  });
  window.addEventListener('pageshow', refreshPersisted);
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') { refreshPersisted(); } else { current(); }
  });
  scheduleExpiry();
})();
