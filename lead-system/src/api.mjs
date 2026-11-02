export const STATUSES = ['new', 'contacted', 'quoted', 'won', 'lost'];
export const SERVICES = ['Volantinaggio', 'Grafica, stampa e distribuzione', 'Promozione evento', 'Stampa e grafica'];
const UUID = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
const MAX_BODY = 24 * 1024;
const PAGE_SIZE = 25;

export class ApiError extends Error {
  constructor(status, code, message) { super(message); this.status = status; this.code = code; }
}

export function json(data, status = 200, extra = {}) {
  return Response.json(data, { status, headers: { 'Cache-Control': 'no-store', 'X-Content-Type-Options': 'nosniff', 'X-Robots-Tag': 'noindex, nofollow', ...extra } });
}

export function errorResponse(error) {
  return error instanceof ApiError
    ? json({ error: error.message, code: error.code }, error.status)
    : json({ error: 'Servizio temporaneamente non disponibile. Riprova tra poco.', code: 'service_unavailable' }, 503);
}

export async function readJson(request) {
  if (!(request.headers.get('Content-Type') || '').split(';')[0].trim().toLowerCase().endsWith('/json')) {
    throw new ApiError(415, 'json_required', 'Invia i dati in formato JSON.');
  }
  const declared = Number(request.headers.get('Content-Length'));
  if (declared > MAX_BODY) throw new ApiError(413, 'body_too_large', 'La richiesta contiene troppi dati.');
  if (!request.body) throw new ApiError(400, 'invalid_json', 'Dati della richiesta mancanti.');
  const reader = request.body.getReader();
  let length = 0;
  const chunks = [];
  while (true) {
    const { value, done } = await reader.read();
    if (done) break;
    length += value.byteLength;
    if (length > MAX_BODY) { await reader.cancel(); throw new ApiError(413, 'body_too_large', 'La richiesta contiene troppi dati.'); }
    chunks.push(value);
  }
  const bytes = new Uint8Array(length);
  let offset = 0;
  for (const chunk of chunks) { bytes.set(chunk, offset); offset += chunk.byteLength; }
  try {
    const body = JSON.parse(new TextDecoder('utf-8', { fatal: true }).decode(bytes));
    if (!body || typeof body !== 'object' || Array.isArray(body)) throw new Error();
    return body;
  } catch { throw new ApiError(400, 'invalid_json', 'I dati della richiesta non sono validi.'); }
}

function text(value, label, max, required = false, multiline = false) {
  if (value == null) value = '';
  if (typeof value !== 'string') throw new ApiError(400, 'invalid_field', `${label}: valore non valido.`);
  const clean = value.trim().normalize('NFC');
  if ((required && !clean) || clean.length > max || /[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F]/.test(clean) || (!multiline && /[\r\n\t]/.test(clean))) {
    throw new ApiError(400, 'invalid_field', `${label}: controlla il valore inserito.`);
  }
  return clean;
}

function normalizeLead(body, source) {
  const lead = {
    servizio: text(body.servizio, 'Servizio', 100, true),
    zona: text(body.zona, 'Zona', 200, true),
    quantita: text(body.quantita, 'Quantità', 100),
    periodo: text(body.periodo, 'Periodo', 200),
    dettagli: text(body.dettagli, 'Dettagli', 6000, false, true),
    nome: text(body.nome, 'Nome', 150, true),
    azienda: text(body.azienda, 'Azienda', 200),
    email: text(body.email, 'Email', 254, true).toLowerCase(),
    telefono: text(body.telefono, 'Telefono', 50),
  };
  if (!SERVICES.includes(lead.servizio)) throw new ApiError(400, 'invalid_service', 'Seleziona un servizio valido.');
  if (!/^[^\s@<>]+@[^\s@<>]+\.[^\s@<>]+$/.test(lead.email)) throw new ApiError(400, 'invalid_email', 'Inserisci un indirizzo email valido.');
  if (body.privacy_accepted !== true) throw new ApiError(400, 'privacy_required', 'Conferma di aver fornito l’informativa per gestire questa richiesta.');
  const submissionId = text(body.submission_id, 'Identificativo richiesta', 36, true);
  if (!UUID.test(submissionId)) throw new ApiError(400, 'invalid_submission_id', 'Identificativo della richiesta non valido.');
  lead.source = source === 'wordpress' ? 'wordpress' : body.is_test === true || body.source === 'test' ? 'test' : 'manual';
  lead.is_test = lead.source === 'test' ? 1 : 0;
  lead.marketing_consent = 0; // Enquiry permission never implies newsletter subscription.
  lead.google_ads_consent = source === 'wordpress' && body.google_ads_consent === true ? 1 : 0;
  lead.meta_ads_consent = source === 'wordpress' && body.meta_ads_consent === true ? 1 : 0;
  lead.privacy_policy_version = text(body.privacy_policy_version || (source === 'wordpress' ? '' : 'gfa-leads-manual-v1'), 'Versione informativa', 120, true);
  return { lead, submissionId };
}

function expose(row) {
  if (!row) return null;
  const { submission_hash, change_id, ...lead } = row;
  for (const field of ['marketing_consent', 'google_ads_consent', 'meta_ads_consent', 'is_test']) lead[field] = Boolean(lead[field]);
  return lead;
}

async function digest(value) {
  return Array.from(new Uint8Array(await crypto.subtle.digest('SHA-256', new TextEncoder().encode(value))), byte => byte.toString(16).padStart(2, '0')).join('');
}

export async function insertLead(env, body, actor, source = 'manual') {
  if (!env.DB) throw new ApiError(503, 'database_not_configured', 'Archivio lead non ancora collegato.');
  const { lead, submissionId } = normalizeLead(body, source);
  const submissionHash = await digest(JSON.stringify(lead));
  const id = crypto.randomUUID();
  const now = new Date().toISOString();
  const changeId = crypto.randomUUID();
  const columns = ['id', 'submission_id', 'submission_hash', 'created_at', 'updated_at', 'change_id', ...Object.keys(lead), 'privacy_accepted_at'];
  const values = [id, submissionId, submissionHash, now, now, changeId, ...Object.values(lead), now];
  await env.DB.batch([
    env.DB.prepare(`INSERT INTO leads (${columns.join(',')}) VALUES (${columns.map(() => '?').join(',')}) ON CONFLICT(submission_id) DO NOTHING`).bind(...values),
    env.DB.prepare("INSERT INTO lead_activity (id,lead_id,created_at,actor,action,new_status) SELECT ?,id,?,?,'created','new' FROM leads WHERE id=?").bind(crypto.randomUUID(), now, actor, id),
  ]);
  const stored = await env.DB.prepare('SELECT * FROM leads WHERE submission_id=?').bind(submissionId).first();
  if (!stored) throw new Error('Save did not persist');
  if (stored.submission_hash !== submissionHash) throw new ApiError(409, 'submission_conflict', 'Questa richiesta è già stata usata per dati diversi. Ricarica il modulo.');
  return { id: stored.id, lead: expose(stored), received: true, duplicate: stored.id !== id };
}

function assertOrigin(request) {
  if (request.headers.get('Origin') !== new URL(request.url).origin) throw new ApiError(403, 'origin_denied', 'Origine della richiesta non autorizzata.');
  const site = request.headers.get('Sec-Fetch-Site');
  if (site && site !== 'same-origin' && site !== 'none') throw new ApiError(403, 'origin_denied', 'Origine della richiesta non autorizzata.');
}

function leadFilter(url) {
  const where = [];
  const values = [];
  const q = text(url.searchParams.get('q'), 'Ricerca', 200);
  const status = url.searchParams.get('status') || '';
  if (status && status !== 'all') {
    if (!STATUSES.includes(status)) throw new ApiError(400, 'invalid_status', 'Stato non valido.');
    where.push('status=?'); values.push(status);
  }
  const isTest = url.searchParams.get('is_test');
  if (isTest != null && isTest !== '') {
    if (!['0','1'].includes(isTest)) throw new ApiError(400, 'invalid_filter', 'Filtro prove non valido.');
    where.push('is_test=?'); values.push(Number(isTest));
  }
  if (q) {
    where.push("(nome LIKE ? ESCAPE '\\' OR email LIKE ? ESCAPE '\\' OR azienda LIKE ? ESCAPE '\\' OR zona LIKE ? ESCAPE '\\')");
    const term = `%${q.replace(/[\\%_]/g, '\\$&')}%`;
    values.push(term, term, term, term);
  }
  return { clause: where.length ? ` WHERE ${where.join(' AND ')}` : '', values };
}

async function listLeads(env, url) {
  const { clause, values } = leadFilter(url);
  const requestedPage = Number(url.searchParams.get('page') || 1);
  if (!Number.isInteger(requestedPage) || requestedPage < 1 || requestedPage > 10000) throw new ApiError(400, 'invalid_page', 'Pagina non valida.');
  const total = Number((await env.DB.prepare(`SELECT COUNT(*) AS total FROM leads${clause}`).bind(...values).first()).total);
  const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
  const page = Math.min(requestedPage, totalPages);
  const rows = await env.DB.prepare(`SELECT * FROM leads${clause} ORDER BY created_at DESC,id DESC LIMIT ? OFFSET ?`).bind(...values, PAGE_SIZE, (page - 1) * PAGE_SIZE).all();
  const summary = await env.DB.prepare('SELECT status,COUNT(*) AS total FROM leads GROUP BY status').all();
  const counts = Object.fromEntries(['all', ...STATUSES].map(status => [status, 0]));
  for (const row of summary.results) { counts[row.status] = Number(row.total); counts.all += Number(row.total); }
  return json({ leads: rows.results.map(expose), counts, total, page, page_size: PAGE_SIZE, total_pages: totalPages });
}

async function getLead(env, id) {
  const lead = await env.DB.prepare('SELECT * FROM leads WHERE id=?').bind(id).first();
  if (!lead) throw new ApiError(404, 'lead_not_found', 'Contatto non trovato.');
  return lead;
}

async function updateLead(request, env, identity, id) {
  const body = await readJson(request);
  if (!Number.isSafeInteger(body.version) || body.version < 1) throw new ApiError(409, 'stale_version', 'Ricarica il contatto prima di salvare: manca una versione valida.');
  const existing = await getLead(env, id);
  if (body.version !== existing.version) throw new ApiError(409, 'stale_version', 'Il contatto è stato aggiornato. Ricaricalo prima di salvare.');
  const status = body.status ?? existing.status;
  if (!STATUSES.includes(status)) throw new ApiError(400, 'invalid_status', 'Seleziona uno stato valido.');
  const note = text(body.note, 'Nota', 4000, false, true);
  if (status === existing.status && !note) return json({ lead: expose(existing) });
  const now = new Date().toISOString();
  const changeId = crypto.randomUUID();
  const queries = [env.DB.prepare('UPDATE leads SET status=?,updated_at=?,version=version+1,change_id=? WHERE id=? AND version=?').bind(status, now, changeId, id, existing.version)];
  if (status !== existing.status) queries.push(env.DB.prepare("INSERT INTO lead_activity (id,lead_id,created_at,actor,action,previous_status,new_status) SELECT ?,id,?,?,'status_changed',?,? FROM leads WHERE id=? AND change_id=?").bind(crypto.randomUUID(), now, identity.email, existing.status, status, id, changeId));
  if (note) queries.push(env.DB.prepare("INSERT INTO lead_activity (id,lead_id,created_at,actor,action,note) SELECT ?,id,?,?,'note_added',? FROM leads WHERE id=? AND change_id=?").bind(crypto.randomUUID(), now, identity.email, note, id, changeId));
  const results = await env.DB.batch(queries);
  if (!results[0].meta.changes) throw new ApiError(409, 'stale_version', 'Il contatto è stato aggiornato. Ricaricalo prima di salvare.');
  return json({ lead: expose(await getLead(env, id)) });
}

export function csvCell(value) {
  let string = String(value ?? '');
  // Neutralise spreadsheet formula prefixes, including prefixes after whitespace.
  if (/^[\s]*[=+@-]/u.test(string) || /^[\t\r\n]/.test(string)) string = `'${string}`;
  return `"${string.replaceAll('"', '""')}"`;
}

async function exportCsv(env, url) {
  const { clause, values } = leadFilter(url);
  const total = Number((await env.DB.prepare(`SELECT COUNT(*) AS total FROM leads${clause}`).bind(...values).first()).total);
  // Keep the complete result and its encoded CSV safely below Worker memory and
  // D1 result limits even when every long-text field contains multibyte text.
  if (total > 1000) throw new ApiError(413, 'export_limit', 'L’esportazione supera 1.000 contatti. Restringi i filtri e riprova.');
  const rows = (await env.DB.prepare(`SELECT * FROM leads${clause} ORDER BY created_at DESC,id DESC LIMIT 1000`).bind(...values).all()).results;
  const columns = ['id','created_at','status','nome','azienda','email','telefono','servizio','zona','quantita','periodo','dettagli','source','is_test','privacy_accepted_at','privacy_policy_version','marketing_consent','notification_status'];
  const csv = '\uFEFF' + [columns.map(csvCell).join(';'), ...rows.map(row => columns.map(column => csvCell(row[column])).join(';'))].join('\r\n');
  return new Response(csv, { headers: { 'Content-Type': 'text/csv; charset=utf-8', 'Content-Disposition': 'attachment; filename="gfa-leads.csv"', 'Cache-Control': 'no-store', 'X-Content-Type-Options': 'nosniff', 'X-Robots-Tag': 'noindex, nofollow' } });
}

export async function handleApi(request, env, identity) {
  try {
    if (!identity?.email) throw new ApiError(401, 'access_required', 'Accesso richiesto.');
    const url = new URL(request.url);
    if (request.method === 'GET' && url.pathname === '/api/health') {
      if (!env.DB) return json({ ok: false, database: 'not_configured', notifications: 'not_configured', user: identity }, 503);
      await env.DB.prepare('SELECT id FROM leads LIMIT 1').first();
      return json({ ok: true, database: 'ready', notifications: 'not_configured', user: identity });
    }
    if (!env.DB) throw new ApiError(503, 'database_not_configured', 'Archivio lead non ancora collegato.');
    if (!['GET','HEAD'].includes(request.method)) assertOrigin(request);
    if (url.pathname === '/api/leads') {
      if (request.method === 'GET') return await listLeads(env, url);
      if (request.method === 'POST') {
        const result = await insertLead(env, await readJson(request), identity.email);
        return json(result, result.duplicate ? 200 : 201);
      }
      return json({ error: 'Metodo non consentito.', code: 'method_not_allowed' }, 405, { Allow: 'GET, POST' });
    }
    if (url.pathname === '/api/export.csv' && request.method === 'GET') return await exportCsv(env, url);
    const match = url.pathname.match(/^\/api\/leads\/([^/]+)$/);
    if (match) {
      const id = match[1];
      if (!UUID.test(id)) throw new ApiError(404, 'lead_not_found', 'Contatto non trovato.');
      if (request.method === 'GET') {
        const lead = expose(await getLead(env, id));
        const activity = (await env.DB.prepare('SELECT * FROM lead_activity WHERE lead_id=? ORDER BY created_at DESC,id DESC LIMIT 200').bind(id).all()).results;
        return json({ lead, activity });
      }
      if (request.method === 'PATCH') return await updateLead(request, env, identity, id);
      return json({ error: 'Metodo non consentito.', code: 'method_not_allowed' }, 405, { Allow: 'GET, PATCH' });
    }
    return json({ error: 'Risorsa non trovata.', code: 'not_found' }, 404);
  } catch (error) { return errorResponse(error); }
}
