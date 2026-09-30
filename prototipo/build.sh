#!/usr/bin/env bash
# Rigenera il prototipo statico dal tema (PHP CLI + Python 3).
# Uscita: gfa-prototipo.html (pagina principale dell'artifact) e pagine/*.html.
set -euo pipefail
cd "$(dirname "$0")"
mkdir -p pagine
for tpl in front-page page-volantinaggio page-stampa-e-grafica page-promozione-eventi page-chi-siamo page-franchising page-contatti; do
  php render.php "$tpl" > "pagine/$tpl.body"
done
python3 - <<'PY'
import base64, re, os
T = '../gfa-theme'
fonts = open(T + '/assets/fonts/fonts.css').read()
fonts = re.sub(r'url\(([^)]+\.woff2)\)', lambda m: 'url(data:font/woff2;base64,' + base64.b64encode(open(T + '/assets/fonts/' + m.group(1), 'rb').read()).decode() + ')', fonts)
css = open(T + '/assets/css/main.css').read()
js = open(T + '/assets/js/main.js').read()
names = {'front-page': ('home', 'Home'), 'page-volantinaggio': ('volantinaggio', 'Volantinaggio'), 'page-stampa-e-grafica': ('stampa-e-grafica', 'Stampa e grafica'), 'page-promozione-eventi': ('promozione-eventi', 'Promozione eventi'), 'page-chi-siamo': ('chi-siamo', 'Chi siamo'), 'page-franchising': ('franchising', 'Franchising'), 'page-contatti': ('contatti', 'Contatti')}
def page(body, title):
    return f'''<title>{title}</title>
<style>
{fonts}
{css}
</style>
<div class="proto-bar" id="top">Prototipo del nuovo sito GFA · i riquadri arancioni sono dati e foto che GFA deve fornire o confermare</div>
{body}
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
{js}
</script>
'''
full = lambda inner: '<!doctype html>\n<html lang="it">\n<head>\n<meta charset="utf-8">\n<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n</head>\n<body>\n' + inner + '</body>\n</html>\n'
for tpl, (slug, label) in names.items():
    body = open(f'pagine/{tpl}.body').read()
    os.remove(f'pagine/{tpl}.body')
    title = 'GFA Percorso' if slug == 'home' else f'{label} · GFA'
    open(f'pagine/{slug}.html', 'w').write(full(page(body, title)))
    if slug == 'home':
        open('gfa-prototipo.html', 'w').write(page(body, 'GFA Percorso'))
PY
