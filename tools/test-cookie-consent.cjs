/* Run: node --test tools/test-cookie-consent.cjs */
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const stateSource = fs.readFileSync(path.join(__dirname, '../gfa-theme/assets/js/cookie-consent-state.js'), 'utf8');
const preloadSource = fs.readFileSync(path.join(__dirname, '../gfa-theme/assets/js/preload-head.js'), 'utf8');
const choices = (functional = false, googleAds = false, metaAds = false) => ({ functional, googleAds, metaAds });

function browser(options = {}) {
  let now = options.now || Date.parse('2026-11-02T12:00:00.000Z');
  const local = new Map(options.record ? [['gfaConsent', options.record]] : []);
  const session = new Map([['gfaSeen', '1'], ['gfaTrans', '1']]);
  const calls = [];
  const listeners = {};
  const documentListeners = {};
  const events = [];
  const timers = new Map();
  let timerId = 0;
  const classes = new Set();
  const storage = (name, values) => ({
    getItem(key) { calls.push([name, 'get', key]); if (options.blocked) throw new Error('blocked'); return values.get(key) || null; },
    setItem(key, value) { calls.push([name, 'set', key]); if (options.blocked || options.writeFails) throw new Error('quota'); values.set(key, value); },
    removeItem(key) { calls.push([name, 'remove', key]); if (options.blocked) throw new Error('blocked'); values.delete(key); }
  });
  class FakeDate extends Date { static now() { return now; } }
  class Event { constructor(type, init = {}) { this.type = type; this.detail = init.detail; } }
  const window = {
    GfaAdvertisingConfig: options.config || { revision: 'providers-v1', google: { enabled: true }, meta: { enabled: true } },
    localStorage: storage('local', local), sessionStorage: storage('session', session),
    matchMedia: () => ({ matches: !!options.reduced }),
    addEventListener(type, listener) { (listeners[type] ||= []).push(listener); },
    dispatchEvent(event) { events.push(event); (listeners[event.type] || []).forEach(listener => listener(event)); }
  };
  if (options.getterThrows) {
    Object.defineProperty(window, 'localStorage', { get() { throw new Error('SecurityError'); } });
  }
  const document = {
    visibilityState: 'visible',
    documentElement: { classList: { add: value => classes.add(value), remove: (...values) => values.forEach(value => classes.delete(value)) } },
    addEventListener(type, listener) { (documentListeners[type] ||= []).push(listener); }
  };
  const context = vm.createContext({
    window, document, Date: FakeDate, CustomEvent: Event, history: {},
    setTimeout(callback, delay) { const id = ++timerId; timers.set(id, { callback, delay }); return id; },
    clearTimeout(id) { timers.delete(id); }
  });
  vm.runInContext(stateSource, context);
  return {
    consent: window.GfaCookieConsent, local, session, calls, classes, options, events, timers, window,
    preload: () => vm.runInContext(preloadSource, context),
    time: value => { now = value; },
    runTimer: () => { const entry = timers.entries().next().value; if (entry) { timers.delete(entry[0]); entry[1].callback(); } },
    emit: type => (listeners[type] || []).forEach(listener => listener()),
    visibility: (value = 'visible') => { document.visibilityState = value; (documentListeners.visibilitychange || []).forEach(listener => listener()); },
    storageEvent: (newValue, key = 'gfaConsent', storageArea = window.localStorage) => (listeners.storage || []).forEach(listener => listener({ key, newValue, storageArea }))
  };
}

test('Restoring a cached document observes same-tab refusal or deletion before retaining old grants', () => {
  for (const restore of ['pageshow', 'visible', 'recheck']) {
    const b = browser(); b.consent.save(choices(true, true, true));
    const refusal = { ...b.consent.getChoice(), functional: false, googleAds: false, metaAds: false };
    b.local.set('gfaConsent', JSON.stringify(refusal));
    const before = b.events.length;
    if (restore === 'pageshow') b.emit('pageshow');
    else if (restore === 'visible') b.visibility();
    else b.consent.recheck();
    assert.equal(b.consent.allowsGoogleAds(), false);
    assert.equal(b.consent.allowsMetaAds(), false);
    assert.equal(b.events.length, before + 1);
    b.emit('pageshow');
    assert.equal(b.events.length, before + 1, 'unchanged persistence does not emit another consent change');
  }
  const b = browser(); b.consent.save(choices(true, true, true));
  b.local.delete('gfaConsent'); b.emit('pageshow');
  assert.equal(b.consent.hasChoice(), false);
  assert.equal(b.consent.allowsGoogleAds(), false);
});

test('Restoration preserves page-only decisions but denies previously persisted choices that became unreadable', () => {
  const pageOnly = browser({ blocked: true }); pageOnly.consent.save(choices(true, true, true));
  pageOnly.emit('pageshow'); pageOnly.visibility();
  assert.equal(pageOnly.consent.allowsGoogleAds(), true);
  assert.equal(pageOnly.consent.isPersisted(), false);
  const persisted = browser(); persisted.consent.save(choices(true, true, true));
  persisted.options.blocked = true; persisted.emit('pageshow');
  assert.equal(persisted.consent.hasChoice(), false);
  assert.equal(persisted.consent.allowsGoogleAds(), false);
});

test('Restoration reads a renewed choice before checking the expired memory and does not delete it', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  const old = b.consent.getChoice();
  const otherPage = browser({ now: old.updatedAt + 86400000 }); otherPage.consent.save(choices(false, false, true));
  const renewed = otherPage.local.get('gfaConsent');
  b.local.set('gfaConsent', renewed); b.time(old.expiresAt); b.emit('pageshow');
  assert.equal(b.consent.allowsMetaAds(), true);
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.local.get('gfaConsent'), renewed);
});

test('No choice: all optional purposes denied, cleanup only, no optional reads or writes', () => {
  const b = browser();
  assert.equal(b.consent.hasChoice(), false);
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.consent.allowsMetaAds(), false);
  b.preload();
  assert.deepEqual(b.calls.filter(c => c[0] === 'session'), [['session', 'remove', 'gfaSeen'], ['session', 'remove', 'gfaTrans']]);
  assert.equal(b.local.size, 0);
  assert.equal(b.classes.has('gfa-preload'), true);
});

test('Explicit accept persists a versioned choice for available providers and functional memories', () => {
  const b = browser();
  assert.equal(b.consent.save(choices(true, true, true)), true);
  const record = b.consent.getChoice();
  assert.equal(record.functional, true);
  assert.equal(record.googleAds, true);
  assert.equal(record.metaAds, true);
  assert.equal(record.version, 2);
  assert.equal(record.policyVersion, '2026-11-02.ads-v1:providers-v1');
  const next = browser({ record: b.local.get('gfaConsent') });
  next.preload();
  assert.equal(next.consent.allowsFunctional(), true);
  assert.equal(next.consent.allowsGoogleAds(), true);
  assert.equal(next.consent.allowsMetaAds(), true);
  assert.ok(next.calls.some(c => c[0] === 'session' && c[1] === 'get' && c[2] === 'gfaSeen'));
  assert.equal(next.classes.has('gfa-trans-in'), true);
});

test('Functional permission never grants either advertising provider', () => {
  const b = browser(); b.consent.save(choices(true));
  assert.equal(b.consent.allowsFunctional(), true);
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.consent.allowsMetaAds(), false);
});

test('Google and Meta preferences are independent, including after reload', () => {
  for (const value of [choices(false, true, false), choices(false, false, true)]) {
    const b = browser(); b.consent.save(value);
    const next = browser({ record: b.local.get('gfaConsent') });
    assert.equal(next.consent.allowsFunctional(), false);
    assert.equal(next.consent.allowsGoogleAds(), value.googleAds);
    assert.equal(next.consent.allowsMetaAds(), value.metaAds);
  }
});

test('Unavailable providers cannot receive consent, even through accept-all values', () => {
  const config = { revision: 'off-v1', google: { enabled: false }, meta: { enabled: false } };
  const b = browser({ config }); b.consent.save(choices(true, true, true));
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.consent.allowsMetaAds(), false);
  const raw = JSON.parse(b.local.get('gfaConsent'));
  assert.equal(raw.googleAds, false);
  assert.equal(raw.metaAds, false);
  const activated = browser({ record: b.local.get('gfaConsent'), config: { revision: 'enabled-v2', google: { enabled: true }, meta: { enabled: true } } });
  assert.equal(activated.consent.hasChoice(), false);
  assert.equal(activated.consent.allowsGoogleAds(), false);
  assert.equal(activated.consent.allowsMetaAds(), false);
});

test('Old animation-only and changed-provider records require a new choice', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  const original = JSON.parse(b.local.get('gfaConsent'));
  for (const raw of [
    JSON.stringify({ ...original, version: 1, policyVersion: '2026-10-30.1' }),
    JSON.stringify({ ...original, policyVersion: '2026-11-02.ads-v1:other-account' })
  ]) {
    const next = browser({ record: raw });
    assert.equal(next.consent.hasChoice(), false);
    assert.equal(next.consent.allowsGoogleAds(), false);
    assert.equal(next.local.has('gfaConsent'), false);
  }
});

test('Refusal survives reload and revocation removes both animation memories', () => {
  const b = browser();
  b.consent.save(choices(true, true, true));
  b.session.set('gfaSeen', '1'); b.session.set('gfaTrans', '1');
  b.consent.save(choices());
  assert.equal(b.session.size, 0);
  const next = browser({ record: b.local.get('gfaConsent') });
  next.preload();
  assert.equal(next.consent.hasChoice(), true);
  assert.equal(next.consent.allowsFunctional(), false);
  assert.equal(next.consent.allowsGoogleAds(), false);
  assert.equal(next.consent.allowsMetaAds(), false);
  assert.equal(next.calls.some(c => c[0] === 'session' && c[1] !== 'remove'), false);
});

test('Six calendar months clamp month ends and leap years', () => {
  for (const [start, end] of [
    ['2026-03-01T12:00:00Z', '2026-09-01T12:00:00Z'],
    ['2026-08-31T12:00:00Z', '2027-02-28T12:00:00Z'],
    ['2027-08-31T12:00:00Z', '2028-02-29T12:00:00Z']
  ]) {
    const b = browser({ now: Date.parse(start) }); b.consent.save(choices());
    assert.equal(b.consent.getChoice().expiresAt, Date.parse(end));
  }
});

test('Malformed, incomplete, future and expired choices cannot grant permission', () => {
  const seed = browser(); seed.consent.save(choices(true, true, true));
  const record = JSON.parse(seed.local.get('gfaConsent'));
  const variants = ['not JSON', '{}', JSON.stringify({ ...record, version: 0 }), JSON.stringify({ ...record, functional: 'true' }), JSON.stringify({ ...record, googleAds: 'true' }), JSON.stringify({ ...record, metaAds: undefined }), JSON.stringify({ ...record, expiresAt: record.expiresAt + 1 }), JSON.stringify({ ...record, updatedAt: record.updatedAt + 86400000 })];
  for (const raw of variants) {
    const b = browser({ record: raw }); b.preload();
    assert.equal(b.consent.allowsFunctional(), false);
    assert.equal(b.consent.allowsGoogleAds(), false);
    assert.equal(b.consent.allowsMetaAds(), false);
    assert.equal(b.local.has('gfaConsent'), false);
    assert.equal(b.calls.some(c => c[0] === 'session' && c[1] !== 'remove'), false);
  }
  const expired = browser({ record: JSON.stringify(record), now: record.expiresAt });
  assert.equal(expired.consent.hasChoice(), false);
});

test('Storage blocking keeps a choice in memory only and remains revocable', () => {
  const b = browser({ blocked: true });
  assert.equal(b.consent.save(choices(true, true, true)), false);
  assert.equal(b.consent.allowsFunctional(), true);
  assert.equal(b.consent.allowsGoogleAds(), true);
  assert.equal(b.consent.isPersisted(), false);
  assert.equal(b.consent.save(choices()), false);
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.consent.allowsMetaAds(), false);
});

test('Failed revocation write removes an older permissive record when removal works', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  b.options.writeFails = true;
  assert.equal(b.consent.save(choices()), false);
  assert.equal(b.consent.allowsFunctional(), false);
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.local.has('gfaConsent'), false);
});

test('A throwing localStorage getter preserves page-only choice but unverifiable storage events revoke safely', () => {
  const b = browser({ getterThrows: true });
  assert.equal(b.consent.hasChoice(), false);
  assert.equal(b.consent.save(choices(true, true, true)), false);
  assert.equal(b.consent.allowsGoogleAds(), true);
  assert.doesNotThrow(() => b.storageEvent(null, 'gfaConsent', {}));
  assert.equal(b.consent.hasChoice(), false);
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.events.at(-1).detail.choice, null);
});

test('Other-tab revocation/deletion immediately dispatches a denied state', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  const denied = { ...b.consent.getChoice(), ...choices() };
  b.storageEvent(JSON.stringify(denied));
  assert.equal(b.consent.allowsGoogleAds(), false);
  assert.equal(b.consent.allowsMetaAds(), false);
  assert.equal(b.session.size, 0);
  assert.equal(b.events.at(-1).detail.choice.googleAds, false);
  b.consent.save(choices(true, true, true)); b.storageEvent(null, null);
  assert.equal(b.consent.hasChoice(), false);
  assert.equal(b.events.at(-1).detail.choice, null);
});

test('Unrelated storage events do not replace the chosen preferences', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  b.storageEvent(null, 'unrelated');
  b.storageEvent(null, 'gfaConsent', b.window.sessionStorage);
  assert.equal(b.consent.allowsGoogleAds(), true);
  assert.equal(b.consent.allowsMetaAds(), true);
});

test('Expiry emits one revocation event without recursion when listeners read the state', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  const expiration = b.consent.getChoice().expiresAt;
  let revocations = 0;
  b.window.addEventListener('gfa:consentchange', () => {
    if (!b.consent.hasChoice()) { revocations++; assert.equal(b.consent.allowsGoogleAds(), false); }
  });
  b.time(expiration);
  assert.equal(b.consent.allowsFunctional(), false);
  assert.equal(b.session.size, 0);
  assert.equal(b.local.has('gfaConsent'), false);
  assert.equal(revocations, 1);
  b.consent.getChoice(); b.consent.recheck();
  assert.equal(revocations, 1);
});

test('Expiry timer is bounded and visibility/pageshow can revoke a suspended page', () => {
  for (const trigger of ['timer', 'visibility', 'pageshow']) {
    const b = browser(); b.consent.save(choices(false, true, true));
    assert.ok([...b.timers.values()].every(t => t.delay <= 2147483647));
    b.time(b.consent.getChoice().expiresAt);
    if (trigger === 'timer') b.runTimer();
    else if (trigger === 'visibility') b.visibility();
    else b.emit('pageshow');
    assert.equal(b.events.at(-1).detail.choice, null);
    assert.equal(b.consent.allowsMetaAds(), false);
  }
});

test('Callers cannot alter returned records or accidentally consent with a legacy boolean', () => {
  const b = browser(); b.consent.save(choices(true, true, true));
  const copy = b.consent.getChoice(); copy.googleAds = false; copy.expiresAt += 86400000;
  assert.equal(b.consent.allowsGoogleAds(), true);
  assert.notEqual(b.consent.getChoice().expiresAt, copy.expiresAt);
  for (const value of [true, false, 'yes', null, {}, { functional: true }, choices(true, 'yes', false)]) {
    assert.throws(() => b.consent.save(value), /booleans/);
  }
});
