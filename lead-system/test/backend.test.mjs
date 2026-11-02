import test from 'node:test';
import assert from 'node:assert/strict';
import { DatabaseSync } from 'node:sqlite';
import { readFileSync } from 'node:fs';
import { authenticate } from '../src/auth.mjs';
import { handleApi, insertLead } from '../src/api.mjs';
import capture from '../src/capture.mjs';

const schema = readFileSync(new URL('../migrations/0001.sql', import.meta.url), 'utf8');
const origin = 'https://gfa-leads-admin.example.workers.dev';
const identity = { email: 'owner@example.com' };

function database() {
  const sqlite = new DatabaseSync(':memory:');
  sqlite.exec(schema);
  function prepare(query, bindings = []) {
    return {
      bind(...values) { return prepare(query, values); },
      async first() { return sqlite.prepare(query).get(...bindings) || null; },
      async all() { return { results: sqlite.prepare(query).all(...bindings), success: true }; },
      async run() { const result = sqlite.prepare(query).run(...bindings); return { success: true, meta: { changes: Number(result.changes) } }; },
    };
  }
  return {
    prepare,
    async batch(statements) {
      sqlite.exec('BEGIN');
      try { const results = []; for (const statement of statements) results.push(await statement.run()); sqlite.exec('COMMIT'); return results; }
      catch (error) { sqlite.exec('ROLLBACK'); throw error; }
    },
    close() { sqlite.close(); },
  };
}

function lead(extra = {}) {
  return { servizio: 'Volantinaggio', zona: 'Milano', nome: 'Contatto dimostrativo', email: 'lead@example.com', privacy_accepted: true, submission_id: crypto.randomUUID(), source: 'test', ...extra };
}
function request(path, method = 'GET', body, headers = {}) {
  return new Request(origin + path, { method, headers: { ...(body === undefined ? {} : { 'Content-Type': 'application/json', Origin: origin }), ...headers }, ...(body === undefined ? {} : { body: JSON.stringify(body) }) });
}
async function result(env, path, method, body, headers) {
  const response = await handleApi(request(path, method, body, headers), env, identity);
  return { status: response.status, body: await response.json(), response };
}

test('Access missing rejects spoofed identity headers before using D1', async () => {
  const response = await authenticate(request('/', 'GET', undefined, { 'Cf-Access-Authenticated-User-Email': identity.email }), {}, {});
  assert.equal(response.status, 401);
});

test('Access identity, audience and exact email allowlist are enforced', async () => {
  const ctx = { access: { aud: 'app-id', getIdentity: async () => ({ email: 'OWNER@example.com' }) } };
  assert.deepEqual(await authenticate(request('/'), { ACCESS_AUD: 'app-id', ADMIN_EMAILS: identity.email }, ctx), identity);
  assert.equal((await authenticate(request('/'), { ACCESS_AUD: 'another-app' }, ctx)).status, 403);
  assert.equal((await authenticate(request('/'), { ADMIN_EMAILS: 'other@example.com' }, ctx)).status, 403);
  assert.equal((await authenticate(request('/'), {}, { access: { getIdentity: async () => { throw Error('upstream'); } } })).status, 503);
});

test('Manual creation persists fields, never marks a newsletter subscription and records an audit event', async t => {
  const DB = database(); t.after(() => DB.close());
  const env = { DB };
  const created = await result(env, '/api/leads', 'POST', lead({ marketing_consent: true, google_ads_consent: true }));
  assert.equal(created.status, 201);
  assert.equal(created.body.received, true);
  assert.equal(created.body.lead.marketing_consent, false);
  assert.equal(created.body.lead.google_ads_consent, false);
  assert.equal(created.body.lead.notification_status, 'not_configured');
  const detail = await result(env, '/api/leads/' + created.body.id);
  assert.equal(detail.body.activity.length, 1);
  assert.equal(detail.body.activity[0].action, 'created');
  assert.equal(detail.body.activity[0].actor, identity.email);
  assert.equal(detail.body.lead.submission_hash, undefined);
});

test('Same submission is idempotent, while a reused ID with different content is rejected', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  const data = lead();
  const first = await result(env, '/api/leads', 'POST', data);
  const second = await result(env, '/api/leads', 'POST', data);
  assert.equal(second.status, 200);
  assert.equal(second.body.id, first.body.id);
  assert.equal(second.body.duplicate, true);
  assert.equal((await result(env, '/api/leads/' + first.body.id)).body.activity.length, 1);
  assert.equal((await result(env, '/api/leads', 'POST', { ...data, nome: 'Different' })).status, 409);
  assert.equal((await result(env, '/api/leads')).body.total, 1);
});

test('Invalid fields, privacy and email injection cannot create records', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  for (const patch of [{ servizio: 'Unknown' }, { privacy_accepted: false }, { nome: '' }, { email: 'a@example.com\r\nBcc: test@example.com' }, { zona: 'x'.repeat(201) }, { submission_id: 'not-an-id' }]) {
    assert.equal((await result(env, '/api/leads', 'POST', lead(patch))).status, 400);
  }
  assert.equal((await result(env, '/api/leads')).body.total, 0);
});

test('Cross-origin and missing-origin mutations are refused', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  assert.equal((await result(env, '/api/leads', 'POST', lead(), { Origin: 'https://evil.example' })).status, 403);
  const missing = request('/api/leads', 'POST', lead()); missing.headers.delete('Origin');
  assert.equal((await handleApi(missing, env, identity)).status, 403);
  assert.equal((await result(env, '/api/leads', 'POST', lead(), { 'Sec-Fetch-Site': 'cross-site' })).status, 403);
});

test('Search treats SQL metacharacters and LIKE wildcards literally', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  await insertLead(env, lead({ nome: '100% reale', zona: 'Rapallo' }), identity.email);
  await insertLead(env, lead({ nome: 'Un altro contatto' }), identity.email);
  assert.equal((await result(env, '/api/leads?q=' + encodeURIComponent('%'))).body.total, 1);
  assert.equal((await result(env, '/api/leads?q=' + encodeURIComponent("' OR 1=1 --"))).body.total, 0);
  assert.equal((await result(env, '/api/leads?q=Rapallo')).body.total, 1);
});

test('Status changes and notes are audited; outdated versions cannot overwrite newer work', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  const created = await insertLead(env, lead(), identity.email);
  const updated = await result(env, '/api/leads/' + created.id, 'PATCH', { status: 'contacted', note: 'Richiamare domani', version: 1 });
  assert.equal(updated.status, 200);
  assert.equal(updated.body.lead.status, 'contacted');
  assert.equal(updated.body.lead.version, 2);
  assert.equal((await result(env, '/api/leads/' + created.id, 'PATCH', { status: 'won', version: 1 })).status, 409);
  for (const version of [undefined, null, '2', 0, -1, 1.5]) {
    const rejected = await result(env, '/api/leads/' + created.id, 'PATCH', { note: 'A stale note', version });
    assert.equal(rejected.status, 409);
    assert.match(rejected.body.error, /[Rr]icarica/);
  }
  const detail = await result(env, '/api/leads/' + created.id);
  assert.equal(detail.body.activity.length, 3);
  assert.equal(detail.body.activity.find(a => a.action === 'note_added').note, 'Richiamare domani');
  assert.equal((await result(env, '/api/leads?status=contacted')).body.counts.contacted, 1);
  assert.equal((await result(env, '/api/leads?status=unknown')).status, 400);
});

test('Pagination returns bounded results and counts', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  for (let i = 0; i < 26; i++) await insertLead(env, lead({ nome: `Contatto ${i}` }), identity.email);
  const page1 = (await result(env, '/api/leads')).body;
  const page2 = (await result(env, '/api/leads?page=2')).body;
  assert.equal(page1.leads.length, 25); assert.equal(page2.leads.length, 1);
  assert.equal(page1.total_pages, 2); assert.equal(page1.counts.all, 26);
  assert.equal((await result(env, '/api/leads?page=-1')).status, 400);
});

test('CSV values cannot execute as spreadsheet formulas', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  await insertLead(env, lead({ nome: '=HYPERLINK("https://evil.example")', telefono: '+39000000000', dettagli: '\n@SUM(1;1)' }), identity.email);
  const response = await handleApi(request('/api/export.csv'), env, identity);
  const csv = await response.text();
  assert.equal(response.status, 200);
  assert.ok(csv.includes('"\'=HYPERLINK(""https://evil.example"")"'));
  assert.ok(csv.includes('"\'+39000000000"'));
  assert.ok(csv.includes('"\'@SUM(1;1)"'));
  assert.equal(response.headers.get('Cache-Control'), 'no-store');
});

test('Oversized and malformed requests are rejected before writes', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  assert.equal((await result(env, '/api/leads', 'POST', lead({ dettagli: 'a'.repeat(25000) }))).status, 413);
  const bad = new Request(origin + '/api/leads', { method: 'POST', body: '{', headers: { Origin: origin, 'Content-Type': 'application/json' } });
  assert.equal((await handleApi(bad, env, identity)).status, 400);
  const badType = new Request(origin + '/api/leads', { method: 'POST', body: '{}', headers: { Origin: origin, 'Content-Type': 'text/plain' } });
  assert.equal((await handleApi(badType, env, identity)).status, 415);
});

test('CSV uses the same selected filters as the lead list', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB };
  await insertLead(env, lead({ nome: 'Rapallo contact', zona: 'Rapallo' }), identity.email);
  await insertLead(env, lead({ nome: 'Milano contact', zona: 'Milano' }), identity.email);
  const response = await handleApi(request('/api/export.csv?q=Rapallo&status=new&is_test=1'), env, identity);
  const csv = await response.text();
  assert.equal(response.status, 200);
  assert.ok(csv.includes('Rapallo contact'));
  assert.ok(!csv.includes('Milano contact'));
});

test('Database faults never report a lead as received or expose an internal error', async () => {
  const response = await result({ DB: { prepare() { throw Error('secret database detail'); } } }, '/api/leads');
  assert.equal(response.status, 503);
  assert.equal(JSON.stringify(response.body).includes('secret database detail'), false);
  assert.equal((await result({}, '/api/leads', 'POST', lead())).status, 503);
});

test('Ingest fails closed without configured secret and does not accept GET lead reads', async () => {
  assert.equal((await capture.fetch(request('/v1/leads', 'POST', lead()), {})).status, 503);
  assert.equal((await capture.fetch(request('/v1/leads'), { INGEST_SECRET: 'x'.repeat(40) })).status, 405);
  assert.equal((await capture.fetch(request('/api/leads'), {})).status, 404);
});

test('Ingest authenticates server request, maps WP fields, preserves consents and returns no PII', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB, INGEST_SECRET: 'x'.repeat(40) };
  const body = { submission_id: crypto.randomUUID(), gfa_servizio: 'Volantinaggio', gfa_zona: 'Rapallo', gfa_nome: 'Richiesta WordPress', gfa_email: 'wordpress@example.com', gfa_privacy: '1', gfa_google_ads_consent: '1', gfa_meta_ads_consent: '0', privacy_policy_version: 'gfa-site-v1', marketing_consent: true };
  assert.equal((await capture.fetch(request('/v1/leads', 'POST', body, { 'X-GFA-Key': 'wrong' }), env)).status, 401);
  const response = await capture.fetch(request('/v1/leads', 'POST', body, { 'X-GFA-Key': env.INGEST_SECRET }), env);
  assert.equal(response.status, 201);
  const receipt = await response.json();
  assert.deepEqual(Object.keys(receipt).sort(), ['duplicate', 'id', 'received']);
  const stored = await DB.prepare('SELECT * FROM leads WHERE id=?').bind(receipt.id).first();
  assert.equal(stored.source, 'wordpress'); assert.equal(stored.google_ads_consent, 1); assert.equal(stored.meta_ads_consent, 0); assert.equal(stored.marketing_consent, 0);
  assert.equal(response.headers.get('Access-Control-Allow-Origin'), null);
});

test('Honeypot rejects requests without recording success', async t => {
  const DB = database(); t.after(() => DB.close()); const env = { DB, INGEST_SECRET: 'x'.repeat(40) };
  const response = await capture.fetch(request('/v1/leads', 'POST', lead({ gfa_website: 'bot.example' }), { 'X-GFA-Key': env.INGEST_SECRET }), env);
  assert.equal(response.status, 400);
  assert.equal((await DB.prepare('SELECT COUNT(*) AS total FROM leads').first()).total, 0);
});
