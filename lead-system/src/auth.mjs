function deny(status, code, error) {
  return Response.json({ error, code }, { status, headers: { 'Cache-Control': 'no-store' } });
}

// Only Cloudflare's authenticated execution context is accepted. Request headers
// and cookies are not identity evidence and are deliberately not read here.
export async function authenticate(_request, env, ctx) {
  if (!ctx?.access) return deny(401, 'access_required', 'Accedi con Cloudflare per aprire il gestionale.');
  if (env.ACCESS_AUD && ctx.access.aud !== env.ACCESS_AUD) {
    return deny(403, 'access_denied', 'Accesso non autorizzato.');
  }
  try {
    const identity = await ctx.access.getIdentity();
    const email = typeof identity?.email === 'string' ? identity.email.trim().toLowerCase() : '';
    if (!email || !email.includes('@')) return deny(403, 'access_denied', 'Accesso non autorizzato.');
    const allowed = String(env.ADMIN_EMAILS || '').split(',').map(value => value.trim().toLowerCase()).filter(Boolean);
    if (allowed.length && !allowed.includes(email)) return deny(403, 'access_denied', 'Accesso non autorizzato.');
    return { email };
  } catch {
    return deny(503, 'identity_unavailable', 'Verifica dell’accesso temporaneamente non disponibile.');
  }
}
