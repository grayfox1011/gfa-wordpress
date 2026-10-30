/* GFA consent state. Runs before preload-head.js; no optional storage before consent. */
(function () {
  'use strict';
  var KEY = 'gfaConsent';
  var VERSION = 1;
  var POLICY = '2026-10-30.1';
  var memory = null;
  var persisted = false;

  function expiry(timestamp) {
    var date = new Date(timestamp);
    var day = date.getUTCDate();
    date.setUTCDate(1);
    date.setUTCMonth(date.getUTCMonth() + 6);
    var lastDay = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + 1, 0)).getUTCDate();
    date.setUTCDate(Math.min(day, lastDay));
    return date.getTime();
  }

  function validate(raw) {
    if (!raw || typeof raw !== 'string') { return null; }
    try {
      var item = JSON.parse(raw);
      var now = Date.now();
      if (!item || item.version !== VERSION || item.policyVersion !== POLICY ||
          typeof item.functional !== 'boolean' || !Number.isSafeInteger(item.updatedAt) ||
          !Number.isSafeInteger(item.expiresAt) || item.updatedAt <= 0 || item.updatedAt > now ||
          item.expiresAt !== expiry(item.updatedAt) || item.expiresAt <= now) { return null; }
      return { version: VERSION, policyVersion: POLICY, updatedAt: item.updatedAt, expiresAt: item.expiresAt, functional: item.functional };
    } catch (error) { return null; }
  }

  function clearOptional() {
    try {
      window.sessionStorage.removeItem('gfaSeen');
      window.sessionStorage.removeItem('gfaTrans');
    } catch (error) { /* Optional storage may be unavailable. */ }
  }

  function current() {
    if (memory && memory.expiresAt <= Date.now()) {
      memory = null;
      persisted = false;
      try { window.localStorage.removeItem(KEY); } catch (error) { /* An expired choice is ignored even if removal is blocked. */ }
      clearOptional();
    }
    return memory;
  }

  function copy() {
    var item = current();
    return item ? { version: item.version, policyVersion: item.policyVersion, updatedAt: item.updatedAt, expiresAt: item.expiresAt, functional: item.functional } : null;
  }

  function notify() {
    window.dispatchEvent(new CustomEvent('gfa:consentchange', { detail: { choice: copy(), persisted: persisted } }));
  }

  try {
    var stored = window.localStorage.getItem(KEY);
    memory = validate(stored);
    persisted = !!memory;
    if (stored && !memory) {
      window.localStorage.removeItem(KEY);
    }
  } catch (error) { /* Choices remain in memory when browser storage is blocked. */ }
  // Clean up legacy theme memories as well; cleanup never reads their contents.
  if (!memory || !memory.functional) { clearOptional(); }

  window.GfaCookieConsent = {
    getChoice: copy,
    hasChoice: function () { return !!current(); },
    allowsFunctional: function () { var choice = current(); return !!(choice && choice.functional); },
    isPersisted: function () { current(); return persisted; },
    save: function (functional) {
      if (typeof functional !== 'boolean') { throw new TypeError('Consent must be boolean.'); }
      var now = Date.now();
      memory = { version: VERSION, policyVersion: POLICY, updatedAt: now, expiresAt: expiry(now), functional: functional };
      persisted = false;
      if (!functional) { clearOptional(); }
      try {
        var serialized = JSON.stringify(memory);
        window.localStorage.setItem(KEY, serialized);
        persisted = window.localStorage.getItem(KEY) === serialized;
      } catch (error) { /* The UI reports that this choice could not be persisted. */ }
      // A failed write must not leave an older permissive choice behind where removal is possible.
      if (!persisted) {
        try { window.localStorage.removeItem(KEY); } catch (error) { /* Storage is entirely blocked. */ }
      }
      notify();
      return persisted;
    }
  };

  window.addEventListener('storage', function (event) {
    if (event.key !== KEY && event.key !== null) { return; }
    memory = validate(event.newValue);
    persisted = !!memory;
    if (!memory || !memory.functional) { clearOptional(); }
    notify();
  });
})();
