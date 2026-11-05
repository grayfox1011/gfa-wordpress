import test from 'node:test';
import assert from 'node:assert/strict';
import { renderDashboard } from '../src/ui.mjs';

const html = renderDashboard();

function optionsOf(id) {
  const select = html.match(new RegExp('<select\\b[^>]*\\bid="' + id + '"[^>]*>([\\s\\S]*?)<\\/select>'));
  assert.ok(select, `Select ${id} must exist`);
  const options = [...select[1].matchAll(/<option\b([^>]*)>([^<]*)<\/option\s*>/g)];
  // A browser recovers malformed option markup by dropping choices. Reject
  // any text/tags outside the explicit pairs instead of accepting that repair.
  assert.equal(select[1].replace(/<option\b[^>]*>[^<]*<\/option\s*>/g, '').trim(), '', `Malformed option in ${id}`);
  return options.map(([, attributes, label]) => {
    const value = attributes.match(/\bvalue="([^"]*)"/);
    return value ? value[1] : label;
  });
}

test('HTML closing tags cannot carry attributes', () => {
  // Regression: </option value="new"> silently removed the New lead filter.
  assert.doesNotMatch(html, /<\/[A-Za-z][A-Za-z0-9:-]*\s+[^\s>][^>]*>/);
});

test('every status is selectable from filtering and editing', () => {
  assert.deepEqual(optionsOf('status-filter'), ['', 'new', 'contacted', 'quoted', 'won', 'lost']);
  assert.deepEqual(optionsOf('update-status'), ['new', 'contacted', 'quoted', 'won', 'lost']);
});

test('manual creation retains every service, quantity and source option', () => {
  assert.deepEqual(optionsOf('new-service'), ['', 'Volantinaggio', 'Grafica, stampa e distribuzione', 'Promozione evento', 'Stampa e grafica']);
  assert.deepEqual(optionsOf('new-quantity'), ['', 'Non lo so, consigliatemi', 'Fino a 5.000', '5.000-10.000', '10.000-20.000', 'Oltre 20.000']);
  assert.deepEqual(optionsOf('new-source'), ['manual', 'test']);
  const selectIds = [...html.matchAll(/<select\b[^>]*\bid="([^"]+)"/g)].map(match => match[1]);
  assert.equal(selectIds.length, 5, 'Update select regression coverage if another select is added');
  selectIds.forEach(optionsOf);
});

test('embedded application script parses and static element references resolve', () => {
  const script = html.match(/<script>([\s\S]*?)<\/script>/)?.[1];
  assert.ok(script);
  assert.doesNotThrow(() => new Function(script));
  const ids = [...html.matchAll(/\bid="([^"]+)"/g)].map(match => match[1]);
  assert.equal(new Set(ids).size, ids.length, 'Duplicate element IDs');
  const references = [...script.matchAll(/\$\('([^']+)'\)/g)].map(match => match[1]);
  references.forEach(id => assert.ok(ids.includes(id), `Missing element ${id}`));
});
