import test from 'node:test';
import assert from 'node:assert/strict';
import worker from '../src/index.mjs';
test('All CRM endpoints fail closed before any DB access without Access', async()=>{
  const DB={prepare(){throw Error('Must not read DB')}};
  for(const path of ['/','/admin','/api/leads','/api/health','/api/export.csv']){
    const r=await worker.fetch(new Request('https://crm.example.com'+path,{headers:{'Cf-Access-Authenticated-User-Email':'owner@example.com'}}),{DB},{});
    assert.equal(r.status,401);assert.match(r.headers.get('Cache-Control'),/no-store/);
  }
});
test('HTML denial discloses no lead data and cannot be framed or indexed',async()=>{
  const r=await worker.fetch(new Request('https://crm.example.com/',{headers:{Accept:'text/html'}}),{},{});
  assert.equal(r.status,401);assert.match(await r.text(),/Accesso riservato/);
  assert.equal(r.headers.get('X-Frame-Options'),'DENY');assert.match(r.headers.get('X-Robots-Tag'),/noindex/);
});
test('Authenticated UI is rendered with fresh CSP nonces and no external assets',async()=>{
  const ctx={access:{getIdentity:async()=>({email:'owner@example.com'})}};
  const r=await worker.fetch(new Request('https://crm.example.com/'),{},ctx);
  assert.equal(r.status,200);const html=await r.text();
  const nonce=/script nonce="([a-f0-9]+)"/.exec(html)?.[1];assert.ok(nonce);
  assert.match(r.headers.get('Content-Security-Policy'),new RegExp(`nonce-${nonce}`));
  assert.ok(html.includes(`<style nonce="${nonce}">`));
  assert.doesNotMatch(html,/<script\s+src=/);assert.doesNotMatch(html,/<link[^>]+stylesheet/);
  const second=await worker.fetch(new Request('https://crm.example.com/'),{},ctx);
  assert.notEqual(second.headers.get('Content-Security-Policy'),r.headers.get('Content-Security-Policy'));
});
