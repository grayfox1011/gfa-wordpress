/* Run: node --test tools/test-cookie-consent.cjs */
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const stateSource = fs.readFileSync(path.join(__dirname, '../gfa-theme/assets/js/cookie-consent-state.js'), 'utf8');
const preloadSource = fs.readFileSync(path.join(__dirname, '../gfa-theme/assets/js/preload-head.js'), 'utf8');

function browser(options = {}) {
  let now = options.now || Date.parse('2026-10-30T12:00:00.000Z');
  const local = new Map(options.record ? [['gfaConsent', options.record]] : []);
  const session = new Map([['gfaSeen', '1'], ['gfaTrans', '1']]);
  const calls = [];
  const listeners = {};
  const classes = new Set();
  const storage = (name, values) => ({
    getItem(key) { calls.push([name, 'get', key]); if (options.blocked) throw new Error('blocked'); return values.get(key) || null; },
    setItem(key, value) { calls.push([name, 'set', key]); if (options.blocked || options.writeFails) throw new Error('quota'); values.set(key, value); },
    removeItem(key) { calls.push([name, 'remove', key]); if (options.blocked) throw new Error('blocked'); values.delete(key); }
  });
  class FakeDate extends Date { static now() { return now; } }
  class Event { constructor(type, init) { this.type = type; this.detail = init.detail; } }
  const window = {
    localStorage: storage('local', local), sessionStorage: storage('session', session),
    matchMedia: () => ({ matches: !!options.reduced }),
    addEventListener(type, listener) { (listeners[type] ||= []).push(listener); },
    dispatchEvent(event) { (listeners[event.type] || []).forEach(listener => listener(event)); }
  };
  const context = vm.createContext({ window, Date: FakeDate, CustomEvent: Event, history: {}, setTimeout() {}, document: { documentElement: { classList: { add: value => classes.add(value), remove: (...values) => values.forEach(value => classes.delete(value)) } } } });
  vm.runInContext(stateSource, context);
  return { consent: window.GfaCookieConsent, local, session, calls, classes, options,
    preload: () => vm.runInContext(preloadSource, context),
    time: value => { now = value; },
    storageEvent: (newValue, key = 'gfaConsent') => (listeners.storage || []).forEach(listener => listener({ key, newValue })) };
}

test('No choice: cleanup only; no optional reads or writes before consent', () => {
  const b = browser();
  assert.equal(b.consent.hasChoice(), false);
  b.preload();
  assert.deepEqual(b.calls.filter(c => c[0] === 'session'), [['session', 'remove', 'gfaSeen'], ['session', 'remove', 'gfaTrans']]);
  assert.equal(b.local.size, 0);
  assert.equal(b.classes.has('gfa-preload'), true);
});

test('Accept persists a versioned choice and enables optional memories on the next page', () => {
  const b = browser();
  assert.equal(b.consent.save(true), true);
  const record = b.consent.getChoice();
  assert.equal(record.functional, true);
  assert.equal(record.version, 1);
  assert.equal(record.policyVersion, '2026-10-30.1');
  const next = browser({ record: b.local.get('gfaConsent') });
  next.preload();
  assert.equal(next.consent.allowsFunctional(), true);
  assert.ok(next.calls.some(c => c[0] === 'session' && c[1] === 'get' && c[2] === 'gfaSeen'));
  assert.equal(next.classes.has('gfa-trans-in'), true);
});

test('Refusal survives reload; revocation removes both optional memories', () => {
  const b = browser();
  b.consent.save(true);
  b.session.set('gfaSeen', '1'); b.session.set('gfaTrans', '1');
  b.consent.save(false);
  assert.equal(b.session.size, 0);
  const next = browser({ record: b.local.get('gfaConsent') });
  next.preload();
  assert.equal(next.consent.hasChoice(), true);
  assert.equal(next.consent.allowsFunctional(), false);
  assert.equal(next.calls.some(c => c[0] === 'session' && c[1] !== 'remove'), false);
});

test('Six calendar months include month-end clamping and leap-year dates', () => {
  for (const [start, end] of [
    ['2026-03-01T12:00:00Z', '2026-09-01T12:00:00Z'],
    ['2026-08-31T12:00:00Z', '2027-02-28T12:00:00Z'],
    ['2027-08-31T12:00:00Z', '2028-02-29T12:00:00Z']
  ]) {
    const b = browser({ now: Date.parse(start) }); b.consent.save(false);
    assert.equal(b.consent.getChoice().expiresAt, Date.parse(end));
  }
});

test('Expired, future-dated, malformed and obsolete choices cannot grant consent', () => {
  const seed = browser(); seed.consent.save(true);
  const record = JSON.parse(seed.local.get('gfaConsent'));
  const variants = ['not JSON', '{}', JSON.stringify({ ...record, version: 0 }), JSON.stringify({ ...record, policyVersion: 'old' }), JSON.stringify({ ...record, functional: 'true' }), JSON.stringify({ ...record, expiresAt: record.expiresAt + 1 }), JSON.stringify({ ...record, updatedAt: record.updatedAt + 86400000 })];
  for (const raw of variants) {
    const b = browser({ record: raw }); b.preload();
    assert.equal(b.consent.allowsFunctional(), false);
    assert.equal(b.local.has('gfaConsent'), false);
    assert.equal(b.calls.some(c => c[0] === 'session' && c[1] !== 'remove'), false);
  }
  const expired = browser({ record: JSON.stringify(record), now: record.expiresAt });
  assert.equal(expired.consent.hasChoice(), false);
});

test('Storage blocking keeps the current choice in memory and reports no persistence', () => {
  const b = browser({ blocked: true });
  assert.equal(b.consent.save(true), false);
  assert.equal(b.consent.allowsFunctional(), true);
  assert.equal(b.consent.isPersisted(), false);
  assert.equal(b.consent.save(false), false);
  assert.equal(b.consent.allowsFunctional(), false);
});

test('Failed revocation write removes a previous permissive record when removal works', () => {
  const b = browser(); b.consent.save(true);
  b.options.writeFails = true;
  assert.equal(b.consent.save(false), false);
  assert.equal(b.consent.allowsFunctional(), false);
  assert.equal(b.local.has('gfaConsent'), false);
});

test('Other-tab revocation or storage deletion immediately disables optional storage', () => {
  const b = browser(); b.consent.save(true);
  const denied = { ...b.consent.getChoice(), functional: false };
  b.storageEvent(JSON.stringify(denied));
  assert.equal(b.consent.allowsFunctional(), false);
  assert.equal(b.session.size, 0);
  b.consent.save(true); b.storageEvent(null, null);
  assert.equal(b.consent.hasChoice(), false);
});

test('Choice expires in a page kept open and cannot be extended by a caller', () => {
  const b = browser(); b.consent.save(true);
  const copy = b.consent.getChoice(); copy.functional = false;
  assert.equal(b.consent.allowsFunctional(), true);
  b.time(copy.expiresAt);
  assert.equal(b.consent.allowsFunctional(), false);
  assert.equal(b.session.size, 0);
  assert.throws(() => b.consent.save('yes'), /boolean/);
});
