import { ApiError, errorResponse, insertLead, json, readJson } from './api.mjs';

async function secretMatches(provided, expected) {
  const encode = value => new TextEncoder().encode(value);
  const [a,b] = await Promise.all([crypto.subtle.digest('SHA-256', encode(provided)), crypto.subtle.digest('SHA-256', encode(expected))]);
  const left = new Uint8Array(a), right = new Uint8Array(b);
  let difference = 0;
  for (let i = 0; i < left.length; i++) difference |= left[i] ^ right[i];
  return difference === 0;
}

function mapWordpress(body) {
  const data = { ...body };
  for (const key of ['servizio','zona','quantita','periodo','dettagli','nome','azienda','email','telefono']) {
    if (Object.hasOwn(body, `gfa_${key}`)) data[key] = body[`gfa_${key}`];
  }
  if (Object.hasOwn(body, 'gfa_privacy')) data.privacy_accepted = body.gfa_privacy === '1';
  if (Object.hasOwn(body, 'gfa_google_ads_consent')) data.google_ads_consent = body.gfa_google_ads_consent === '1';
  if (Object.hasOwn(body, 'gfa_meta_ads_consent')) data.meta_ads_consent = body.gfa_meta_ads_consent === '1';
  return data;
}

export default {
  async fetch(request, env) {
    try {
      const url = new URL(request.url);
      if (url.pathname === '/health' && request.method === 'GET') return json({ service: 'gfa-leads-ingest', status: 'online' });
      if (url.pathname !== '/v1/leads') return json({ error: 'Risorsa non trovata.', code: 'not_found' }, 404);
      if (request.method !== 'POST') return json({ error: 'Metodo non consentito.', code: 'method_not_allowed' }, 405, { Allow: 'POST' });
      if (typeof env.INGEST_SECRET !== 'string' || env.INGEST_SECRET.length < 32) throw new ApiError(503, 'capture_not_configured', 'Raccolta lead non ancora attiva.');
      const provided = request.headers.get('X-GFA-Key') || '';
      if (provided.length > 1024 || !await secretMatches(provided, env.INGEST_SECRET)) throw new ApiError(401, 'unauthorized', 'Richiesta non autorizzata.');
      // Server-to-server only; never enable browser CORS or put this key in HTML.
      const body = await readJson(request);
      if (body.gfa_website || body.website) throw new ApiError(400, 'invalid_request', 'Richiesta non valida.');
      const result = await insertLead(env, mapWordpress(body), 'wordpress', 'wordpress');
      return json({ id: result.id, received: true, duplicate: result.duplicate }, result.duplicate ? 200 : 201);
    } catch (error) { return errorResponse(error); }
  },
};
