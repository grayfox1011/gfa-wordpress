#!/usr/bin/env python3
"""Crea il prototipo statico dalle pagine di un WordPress con il tema GFA attivo.

Uso: python3 tools/build-prototipo.py http://localhost:8080
Scarica ogni pagina, inserisce nel file CSS, JS, font e immagini del tema e
riscrive i link interni verso i file .html del prototipo.
"""
import base64, os, re, sys, urllib.request

BASE = (sys.argv[1] if len(sys.argv) > 1 else 'http://localhost:8080').rstrip('/')
ROOT = os.path.dirname(os.path.abspath(__file__))
THEME = os.path.join(ROOT, '..', 'gfa-theme')
OUT = os.path.join(ROOT, '..', 'prototipo')
PAGES = {'': 'home', 'volantinaggio/': 'volantinaggio', 'stampa-e-grafica/': 'stampa-e-grafica',
         'promozione-eventi/': 'promozione-eventi', 'chi-siamo/': 'chi-siamo', 'franchising/': 'franchising',
         'contatti/': 'contatti'}
BAR = '<div class="proto-bar" id="top">Prototipo del nuovo sito GFA · i riquadri arancioni sono dati e foto che GFA deve fornire o confermare</div>'

opener = urllib.request.build_opener(urllib.request.ProxyHandler({}))
def get(url):
    return opener.open(url).read().decode('utf-8')

def data_uri(path, mime):
    return 'data:%s;base64,%s' % (mime, base64.b64encode(open(path, 'rb').read()).decode())

fonts = open(os.path.join(THEME, 'assets/fonts/fonts.css')).read()
fonts = re.sub(r'url\(([^)]+\.woff2)\)', lambda m: 'url(%s)' % data_uri(os.path.join(THEME, 'assets/fonts', m.group(1)), 'font/woff2'), fonts)
css = open(os.path.join(THEME, 'assets/css/main.css')).read()
js = open(os.path.join(THEME, 'assets/js/main.js')).read()
head_js = open(os.path.join(THEME, 'assets/js/preload-head.js')).read().strip()
vendor = [open(os.path.join(THEME, 'assets/js/vendor', f)).read() for f in ('gsap.min.js', 'ScrollTrigger.min.js', 'SplitText.min.js', 'lenis.min.js')]
placeholder = data_uri(os.path.join(THEME, 'assets/img/foto-da-fornire.svg'), 'image/svg+xml')

def link(m):
    href = m.group(1)
    path, _, frag = href.replace(BASE + '/', '').partition('#')
    if path in PAGES:
        return 'href="%s.html%s"' % (PAGES[path], '#' + frag if frag else '')
    if path in ('lavori/', 'zone/') or path.startswith('volantinaggio/'):
        return 'href="home.html#%s"' % ('lavori' if path == 'lavori/' else 'zone')
    return 'href="#top"'

os.makedirs(os.path.join(OUT, 'pagine'), exist_ok=True)
for path, slug in PAGES.items():
    html = get(BASE + '/' + path)
    title = re.search(r'<title>(.*?)</title>', html, re.S).group(1).strip()
    head = html[:html.index('</head>')]
    core_links = re.findall(r"<link rel='stylesheet'[^>]*href='([^']*/wp-includes/[^']*)'", head)
    block_css = '\n'.join(get(u.replace('&#038;', '&')) for u in core_links)
    block_css += '\n'.join(re.findall(r'<style[^>]*>(.*?)</style>', html, re.S))
    body = html[html.index('<a class="skip"'):html.index('</footer>') + len('</footer>')]
    body = body.replace(BASE + '/wp-content/themes/gfa-theme/assets/img/foto-da-fornire.svg', placeholder)
    body = re.sub(r'href="(%s[^"]*)"' % re.escape(BASE), link, body)
    body = body.replace('action="%s/wp-admin/admin-post.php"' % BASE, 'action="#preventivo"').replace('data-quote-form ', 'data-quote-form data-prototype ')
    inner = '<title>%s</title>\n<style>\n%s\n%s\n%s\n</style>\n%s\n%s\n' % (title, fonts, block_css, css, BAR, body)
    inner = '<script>%s</script>\n' % head_js + inner
    inner += ''.join('<script>\n%s\n</script>\n' % v for v in vendor) + '<script>\n%s\n</script>\n' % js
    full = '<!doctype html>\n<html lang="it">\n<head>\n<meta charset="utf-8">\n<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n</head>\n<body>\n%s</body>\n</html>\n' % inner
    open(os.path.join(OUT, 'pagine', slug + '.html'), 'w').write(full)
    if slug == 'home':
        open(os.path.join(OUT, 'gfa-prototipo.html'), 'w').write(inner.replace(title, 'GFA Percorso', 1))
    print(slug, len(full))
