#!/usr/bin/env bash
# Rigenera prototipo/gfa-prototipo.html dal tema (PHP CLI + Python 3).
set -euo pipefail
cd "$(dirname "$0")"
php render.php > body.html
python3 - <<'PY'
import base64, re
T = '../gfa-theme'
fonts = open(T + '/assets/fonts/fonts.css').read()
fonts = re.sub(r'url\(([^)]+\.woff2)\)', lambda m: 'url(data:font/woff2;base64,' + base64.b64encode(open(T + '/assets/fonts/' + m.group(1), 'rb').read()).decode() + ')', fonts)
css = open(T + '/assets/css/main.css').read()
js = open(T + '/assets/js/main.js').read()
body = open('body.html').read()
open('gfa-prototipo.html', 'w').write(f'''<title>GFA Percorso</title>
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
''')
PY
rm body.html
