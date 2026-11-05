import { authenticate } from './auth.mjs';
import { handleApi } from './api.mjs';
import { renderDashboard } from './ui.mjs';

function secure(response, nonce) {
  const headers = new Headers(response.headers);
  headers.set('Cache-Control', 'no-store, private');
  headers.set('X-Robots-Tag', 'noindex, nofollow, noarchive');
  headers.set('X-Content-Type-Options', 'nosniff');
  headers.set('Referrer-Policy', 'no-referrer');
  headers.set('X-Frame-Options', 'DENY');
  headers.set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
  headers.set('Content-Security-Policy', `default-src 'none'; script-src 'nonce-${nonce}'; style-src 'nonce-${nonce}'; img-src 'self' data:; connect-src 'self'; font-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'`);
  return new Response(response.body, { status: response.status, headers });
}

function lockedPage(nonce) {
  return `<!doctype html><html lang="it"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Accesso riservato · GFA Leads</title><style nonce="${nonce}">*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:#fbfaf6;color:#141a2e;font:17px/1.6 system-ui,sans-serif;padding:24px}main{max-width:560px;border-top:6px solid #f5c518;padding:40px;background:white;box-shadow:0 12px 48px #141a2e0d}.brand{color:#1f3fae;font-size:24px;font-weight:800;letter-spacing:-1px}h1{font-size:34px;line-height:1.15;margin:28px 0 18px}p{color:#4b5268}small{display:block;border-top:1px solid #e5e5e5;padding-top:22px;margin-top:30px;color:#4b5268}</style><main><div class="brand">GFA / LEADS</div><h1>Accesso riservato.</h1><p>Il pannello richiede l’autenticazione tramite Cloudflare Access.</p><p>Se la configurazione è ancora in corso, torna qui quando l’accesso sarà stato attivato.</p><small>I dati dei contatti sono disponibili soltanto agli utenti autorizzati.</small></main></html>`;
}

export default {
  async fetch(request, env, ctx) {
    const nonce = crypto.randomUUID().replaceAll('-', '');
    try {
      if (new URL(request.url).pathname === '/robots.txt') {
        return secure(new Response('User-agent: *\nDisallow: /\n', {headers:{'Content-Type':'text/plain; charset=utf-8'}}), nonce);
      }
      const identity = await authenticate(request, env, ctx);
      if (identity instanceof Response) {
        if ((request.headers.get('Accept') || '').includes('text/html')) {
          return secure(new Response(lockedPage(nonce), {status:identity.status,headers:{'Content-Type':'text/html; charset=utf-8'}}), nonce);
        }
        return secure(identity, nonce);
      }
      const path = new URL(request.url).pathname;
      if (path.startsWith('/api/')) return secure(await handleApi(request, env, identity), nonce);
      if ((path === '/' || path === '/admin') && request.method === 'GET') {
        const html = renderDashboard().replaceAll('<script>', `<script nonce="${nonce}">`).replaceAll('<style>', `<style nonce="${nonce}">`);
        return secure(new Response(html, {headers:{'Content-Type':'text/html; charset=utf-8'}}), nonce);
      }
      return secure(new Response('Pagina non trovata', {status:404}), nonce);
    } catch {
      return secure(Response.json({error:'Servizio temporaneamente non disponibile.'}, {status:503}), nonce);
    }
  }
};
