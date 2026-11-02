/* Offline integration checks: no third-party SDK or advertising endpoint is contacted. */
/* Run: node --test tools/test-advertising.cjs */
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const engineSource = () => fs.readFileSync(path.join(__dirname, '../gfa-theme/assets/js/advertising.js'), 'utf8');
const EVENT_ID = '9b29c72e-2fc0-45ef-9292-c995ab9cf741';

function browser(options = {}) {
  const initialUrl = options.url || 'https://gfa.example.test/contatti/?preventivo=inviato&gfa_receipt=opaque-proof';
  let url = new URL(initialUrl);
  let choice = { functional: false, googleAds: false, metaAds: false, ...options.choice };
  const listeners = {}, timers = [], scripts = [], storageCalls = [], cookieWrites = [], historyCalls = [], fieldReads = [], formListeners = [];
  const formInputs = { gfa_google_ads_consent: { value: '0' }, gfa_meta_ads_consent: { value: '0' } };
  const form = {
    addEventListener(type, listener) { if (type === 'submit') formListeners.push(listener); },
    querySelector(selector) { const name = selector.match(/name="([^"]+)"/)?.[1]; fieldReads.push(name); return formInputs[name] || null; },
    appendChild(input) { formInputs[input.name] = input; return input; }
  };
  const session = options.session || new Map();
  const local = options.local || new Map([['_gcl_ls', 'google-linker'], ['unrelated', 'keep']]);
  const cookies = new Map(Object.entries(options.cookies || { _gcl_aw: 'google', _fbp: 'meta', unrelated: 'keep' }));
  let reloads = 0;
  const location = {
    get href() { return url.href; }, get hostname() { return url.hostname; }, get pathname() { return url.pathname; },
    get search() { return url.search; }, get hash() { return url.hash; }, get origin() { return url.origin; },
    get protocol() { return url.protocol; }, reload() { reloads++; }
  };
  const storage = {
    getItem(key) { storageCalls.push(['get', key]); if (options.storageBlocked) throw new Error('storage blocked'); return session.get(key) ?? null; },
    setItem(key, value) { storageCalls.push(['set', key, value]); if (options.storageBlocked) throw new Error('storage blocked'); session.set(key, String(value)); },
    removeItem(key) { storageCalls.push(['remove', key]); if (options.storageBlocked) throw new Error('storage blocked'); session.delete(key); },
    key(index) { return [...session.keys()][index] ?? null; }, get length() { return session.size; }
  };
  function addEventListener(type, fn) { (listeners[type] ||= []).push(fn); }
  function emit(type, detail) { (listeners[type] || []).forEach(fn => fn({ type, detail })); }
  function append(script) {
    script.parentNode = parent;
    scripts.push({ node: script, url: script.src, location: location.href });
    return script;
  }
  const parent = { appendChild: append, insertBefore: append, removeChild(node) { node.parentNode = null; } };
  const document = {
    head: parent, body: parent, documentElement: parent, title: 'GFA', readyState: 'complete',
    referrer: '', addEventListener,
    get cookie() { if (options.cookieGetThrows) throw new Error('cookie reads blocked'); return [...cookies].map(([key, value]) => `${key}=${value}`).join('; '); },
    set cookie(value) {
      if (options.cookieSetThrows) throw new Error('cookie writes blocked');
      cookieWrites.push(value);
      const [pair] = value.split(';');
      const split = pair.indexOf('=');
      const key = pair.slice(0, split).trim(), content = pair.slice(split + 1);
      if (/max-age\s*=\s*0|expires\s*=\s*(?:thu, 01 jan 1970|thu, 1 jan 1970)/i.test(value) || !content) cookies.delete(key);
      else cookies.set(key, content);
    },
    createElement(tag) {
      const events = {};
      return { tagName: tag.toUpperCase(), attributes: {}, addEventListener(type, fn) { (events[type] ||= []).push(fn); },
        setAttribute(name, value) { this.attributes[name] = String(value); this[name] = value; },
        getAttribute(name) { return this.attributes[name] ?? this[name] ?? null; },
        remove() { this.parentNode = null; }, fire(type) { if (typeof this['on' + type] === 'function') this['on' + type](); (events[type] || []).forEach(fn => fn()); }
      };
    },
    getElementById(id) { return scripts.map(s => s.node).find(s => s.id === id) || null; },
    getElementsByTagName(tag) { return tag === 'script' ? [{ parentNode: parent }] : []; },
    querySelector() { return null; }, querySelectorAll(selector) { return options.form && selector === 'form[data-quote-form]' ? [form] : []; }
  };
  const history = { state: null, replaceState(state, title, next) { historyCalls.push(String(next)); if (options.historyBlocked) throw new Error('history blocked'); url = new URL(String(next), url); } };
  const consent = {
    getChoice() { return { ...choice }; }, allowsGoogleAds() { return choice.googleAds; }, allowsMetaAds() { return choice.metaAds; },
    allowsFunctional() { return choice.functional; }
  };
  const config = {
    revision: 'offline-test', google: { enabled: true, id: 'AW-123456789', conversionLabel: 'test_Label-01' },
    meta: { enabled: true, id: '123456789012345' }, receipt: options.receipt === false ? null : { eventId: EVENT_ID, googleAds: true, metaAds: true },
    pageUrl: 'https://gfa.example.test/contatti/', trackingAllowed: true, ...options.config
  };
  const setTimeout = fn => { timers.push(fn); return timers.length; };
  const window = { GfaAdvertisingConfig: config, GfaCookieConsent: consent, location, history, sessionStorage: storage,
    localStorage: { removeItem(key) { local.delete(key); }, getItem(key) { return local.get(key) ?? null; }, setItem(key, value) { local.set(key, String(value)); } }, addEventListener,
    removeEventListener(type, fn) { listeners[type] = (listeners[type] || []).filter(item => item !== fn); },
    dispatchEvent(event) { emit(event.type, event.detail); }, setTimeout, clearTimeout() {}, document };
  class CustomEvent { constructor(type, init = {}) { this.type = type; this.detail = init.detail; } }
  const context = vm.createContext({ window, document, location, history, URL, URLSearchParams, Date, CustomEvent,
    setTimeout, clearTimeout() {}, console, navigator: { language: 'it-IT' } });
  vm.runInContext(engineSource(), context);
  const commands = value => (value || []).map(item => Array.from(item));
  return {
    window, config, scripts, session, local, cookies, storageCalls, cookieWrites, historyCalls, location, formInputs, fieldReads,
    google: () => commands(window.dataLayer), meta: () => commands(window.fbq?.queue),
    setChoice(next) { choice = { ...choice, ...next }; emit('gfa:consentchange', { choice: { ...choice }, persisted: !options.storageBlocked }); },
    fireLoads() { scripts.filter(s => !s.loaded).forEach(s => { s.loaded = true; s.node.fire('load'); }); },
    flushTimers() { let count = 0; while (timers.length && count++ < 20) timers.shift()(); },
    reloadCount: () => reloads, submitForm: () => formListeners.forEach(listener => listener({ type: 'submit' })),
    emit
  };
}

function conversions(b) { return b.google().filter(c => c[0] === 'event' && c[1] === 'conversion'); }
function metaEvents(b, name) { return b.meta().filter(c => c[0] === 'trackSingle' && c[2] === name); }

test('No opt-in: no external scripts, events or optional storage access; receipt URL is cleaned', () => {
  const b = browser();
  assert.equal(b.scripts.length, 0);
  assert.equal(conversions(b).length, 0);
  assert.equal(metaEvents(b, 'Lead').length, 0);
  assert.equal(b.storageCalls.some(c => c[0] !== 'remove'), false);
  assert.equal(new URL(b.location.href).searchParams.has('gfa_receipt'), false);
});

test('Google consent alone loads only Google, after denied defaults, and sends a PII-free receipt conversion once', () => {
  const b = browser({ choice: { googleAds: true } }); b.fireLoads();
  assert.equal(b.scripts.filter(s => /googletagmanager\.com/.test(s.url)).length, 1);
  assert.equal(b.scripts.some(s => /facebook/.test(s.url)), false);
  const commands = b.google();
  const denied = commands.findIndex(c => c[0] === 'consent' && c[1] === 'default');
  const config = commands.findIndex(c => c[0] === 'config');
  assert.ok(denied >= 0 && config > denied);
  for (const key of ['ad_storage', 'ad_user_data', 'ad_personalization', 'analytics_storage']) assert.equal(commands[denied][2][key], 'denied');
  const grant = commands.find(c => c[0] === 'consent' && c[1] === 'update' && c[2].ad_storage === 'granted');
  assert.ok(grant); assert.equal(grant[2].analytics_storage, 'denied');
  assert.equal(conversions(b).length, 1);
  assert.equal(conversions(b)[0][2].send_to, 'AW-123456789/test_Label-01');
  assert.equal(conversions(b)[0][2].transaction_id, EVENT_ID);
  assert.equal(metaEvents(b, 'Lead').length, 0);
  b.setChoice({ googleAds: true }); b.fireLoads();
  assert.equal(conversions(b).length, 1); assert.equal(b.scripts.length, 1);
  assert.ok(b.scripts.every(s => !s.location.includes('gfa_receipt')));
});

test('Meta consent alone disables automatic configuration and sends only PageView and a deduplicated Lead', () => {
  const b = browser({ choice: { metaAds: true } }); b.fireLoads();
  assert.equal(b.scripts.length, 1); assert.match(b.scripts[0].url, /^https:\/\/connect\.facebook\.net\/en_US\/fbevents\.js$/);
  assert.equal(conversions(b).length, 0);
  assert.ok(b.meta().some(c => c[0] === 'set' && c[1] === 'autoConfig' && c[2] === false));
  const init = b.meta().find(c => c[0] === 'init'); assert.ok(init); assert.equal(init[1], '123456789012345'); assert.equal(init.length, 2);
  assert.equal(metaEvents(b, 'PageView').length, 1); assert.equal(metaEvents(b, 'Lead').length, 1);
  assert.equal(metaEvents(b, 'Lead')[0][4].eventID, EVENT_ID);
  assert.deepEqual(Object.keys(metaEvents(b, 'Lead')[0][3]), []);
  b.setChoice({ metaAds: true }); b.fireLoads();
  assert.equal(metaEvents(b, 'PageView').length, 1); assert.equal(metaEvents(b, 'Lead').length, 1);
});

test('A forged success query never creates a conversion without a server receipt', () => {
  const b = browser({ receipt: false, choice: { googleAds: true, metaAds: true } }); b.fireLoads();
  assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
  assert.equal(metaEvents(b, 'PageView').length, 1);
});

test('A late opt-in cannot retroactively count a request made without landing-page consent', () => {
  const b = browser(); b.setChoice({ googleAds: true, metaAds: true }); b.fireLoads();
  assert.equal(b.scripts.length, 2);
  assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
  assert.equal(metaEvents(b, 'PageView').length, 1);
});

test('Receipt eligibility is provider-specific and cannot be upgraded by landing consent', () => {
  const b = browser({ choice: { googleAds: true, metaAds: true }, config: { receipt: { eventId: EVENT_ID, googleAds: false, metaAds: true } } }); b.fireLoads();
  assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 1);
});

test('Provider configuration and private context fail closed even when consent is true', () => {
  for (const config of [
    { google: { enabled: false, id: 'AW-123456789', conversionLabel: 'label' }, meta: { enabled: false, id: '123456789012345' } },
    { google: { enabled: true, id: '', conversionLabel: '' }, meta: { enabled: true, id: '' } },
    { trackingAllowed: false }
  ]) {
    const b = browser({ choice: { googleAds: true, metaAds: true }, config }); b.fireLoads();
    assert.equal(b.scripts.length, 0); assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
  }
});

test('A receipt URL that cannot be cleaned never loads advertising SDKs despite consent', () => {
  const b = browser({ historyBlocked: true, choice: { googleAds: true, metaAds: true } });
  b.setChoice({ googleAds: true, metaAds: true }); b.fireLoads();
  assert.equal(b.historyCalls.length, 1); assert.equal(b.scripts.length, 0);
  assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'PageView').length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
});

test('Revocation gates subsequent events, clears owned cookies and reloads only once to discard loaded SDKs', () => {
  const b = browser({ choice: { googleAds: true, metaAds: true }, receipt: false }); b.fireLoads();
  b.setChoice({ googleAds: false, metaAds: false }); b.setChoice({ googleAds: false, metaAds: false }); b.flushTimers();
  assert.equal(b.reloadCount(), 1);
  assert.ok(b.google().some(c => c[0] === 'consent' && c[1] === 'update' && c[2].ad_storage === 'denied'));
  assert.ok(b.meta().some(c => c[0] === 'consent' && c[1] === 'revoke'));
  assert.equal(b.cookies.has('_gcl_aw'), false); assert.equal(b.cookies.has('_fbp'), false); assert.equal(b.cookies.get('unrelated'), 'keep');
  b.fireLoads(); assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
});

test('A grant revoked while SDK loading cannot send a delayed Lead', () => {
  const b = browser({ choice: { googleAds: true, metaAds: true } });
  assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
  b.setChoice({ googleAds: false, metaAds: false }); b.fireLoads(); b.flushTimers();
  assert.equal(conversions(b).length, 0); assert.equal(metaEvents(b, 'Lead').length, 0);
  assert.equal(b.reloadCount(), 1);
});

test('Session duplicate prevention survives a second rendering of the same receipt', () => {
  const session = new Map();
  const first = browser({ session, choice: { googleAds: true, metaAds: true } }); first.fireLoads();
  assert.equal(conversions(first).length, 1); assert.equal(metaEvents(first, 'Lead').length, 1);
  const second = browser({ session, choice: { googleAds: true, metaAds: true } }); second.fireLoads();
  assert.equal(conversions(second).length, 0); assert.equal(metaEvents(second, 'Lead').length, 0);
});

test('Blocked optional storage keeps current-page deduplication and does not break consent', () => {
  const b = browser({ storageBlocked: true, choice: { googleAds: true, metaAds: true } }); b.fireLoads();
  b.setChoice({ googleAds: true, metaAds: true }); b.fireLoads();
  assert.equal(conversions(b).length, 1); assert.equal(metaEvents(b, 'Lead').length, 1);
});

test('The actual submit snapshots current provider consent, not earlier UI state or personal fields', () => {
  const b = browser({ form: true, receipt: false });
  b.submitForm(); assert.equal(b.formInputs.gfa_google_ads_consent.value, '0'); assert.equal(b.formInputs.gfa_meta_ads_consent.value, '0');
  b.setChoice({ googleAds: true }); b.submitForm();
  assert.equal(b.formInputs.gfa_google_ads_consent.value, '1'); assert.equal(b.formInputs.gfa_meta_ads_consent.value, '0');
  b.setChoice({ googleAds: false, metaAds: true }); b.submitForm();
  assert.equal(b.formInputs.gfa_google_ads_consent.value, '0'); assert.equal(b.formInputs.gfa_meta_ads_consent.value, '1');
  assert.ok(b.fieldReads.every(name => ['gfa_google_ads_consent', 'gfa_meta_ads_consent'].includes(name)));
});

test('Revoking one provider removes only its owned storage and keeps the other choice intact', () => {
  const b = browser({ receipt: false, choice: { googleAds: true, metaAds: true } }); b.fireLoads();
  b.setChoice({ googleAds: false }); b.flushTimers();
  assert.equal(b.cookies.has('_gcl_aw'), false); assert.equal(b.cookies.get('_fbp'), 'meta');
  assert.equal(b.window.GfaCookieConsent.allowsMetaAds(), true);
  assert.equal(b.meta().some(c => c[0] === 'consent' && c[1] === 'revoke'), false);
  assert.equal(b.reloadCount(), 1);
});

test('Blocked cookie reads or writes cannot prevent both provider revocations and the reload', () => {
  for (const blocked of [{ cookieGetThrows: true }, { cookieSetThrows: true }]) {
    const b = browser({ ...blocked, receipt: false, choice: { googleAds: true, metaAds: true } }); b.fireLoads();
    b.setChoice({ googleAds: false, metaAds: false }); b.flushTimers();
    assert.equal(b.reloadCount(), 1);
    assert.ok(b.google().some(c => c[0] === 'consent' && c[1] === 'update' && c[2].ad_storage === 'denied'));
    assert.ok(b.meta().some(c => c[0] === 'consent' && c[1] === 'revoke'));
  }
});

test('Initially blocked cookie access still permits consent listener setup and later opt-in', () => {
  for (const blocked of [{ cookieGetThrows: true }, { cookieSetThrows: true }]) {
    const b = browser({ ...blocked, receipt: false });
    assert.equal(b.scripts.length, 0); assert.equal(b.reloadCount(), 0);
    b.setChoice({ googleAds: true, metaAds: true }); b.fireLoads();
    assert.equal(b.scripts.length, 2); assert.equal(metaEvents(b, 'PageView').length, 1);
  }
});

test('Google revocation removes its linker localStorage key while Meta revocation preserves it', () => {
  for (const provider of ['googleAds', 'metaAds']) {
    const b = browser({ receipt: false, choice: { googleAds: true, metaAds: true } }); b.fireLoads();
    b.setChoice({ [provider]: false }); b.flushTimers();
    assert.equal(b.local.has('_gcl_ls'), provider === 'metaAds');
    assert.equal(b.local.get('unrelated'), 'keep'); assert.equal(b.reloadCount(), 1);
  }
});
