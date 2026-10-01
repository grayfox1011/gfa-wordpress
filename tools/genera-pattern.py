#!/usr/bin/env python3
"""Genera i modelli a blocchi (gfa-theme/patterns/*.php) con markup Gutenberg valido.

Modifica i testi qui e rilancia: python3 tools/genera-pattern.py
"""
import json, os

OUT = os.path.join(os.path.dirname(__file__), '..', 'gfa-theme', 'patterns')
IMG = "<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/foto-da-fornire.svg"

def url(path):
    return "<?php echo esc_url( home_url( '%s' ) ); ?>" % path

def todo(t):
    return '<mark class="todo">%s</mark>' % t

def attrs(d):
    return (' ' + json.dumps(d, ensure_ascii=False, separators=(',', ':'))) if d else ''

def grp(cls, *inner, tag='div', anchor=None):
    a = {}
    if tag != 'div': a['tagName'] = tag
    if anchor: a['anchor'] = anchor
    a['className'] = cls
    a['layout'] = {'type': 'default'}
    idattr = ' id="%s"' % anchor if anchor else ''
    return '<!-- wp:group%s -->\n<%s%s class="wp-block-group %s">%s</%s>\n<!-- /wp:group -->' % (attrs(a), tag, idattr, cls, '\n'.join(inner), tag)

def h(text, level=2, cls=None):
    a = {}
    if level != 2: a['level'] = level
    if cls: a['className'] = cls
    c = 'wp-block-heading' + (' ' + cls if cls else '')
    return '<!-- wp:heading%s -->\n<h%d class="%s">%s</h%d>\n<!-- /wp:heading -->' % (attrs(a), level, c, text, level)

def p(text, cls=None):
    a = {'className': cls} if cls else {}
    c = ' class="%s"' % cls if cls else ''
    return '<!-- wp:paragraph%s -->\n<p%s>%s</p>\n<!-- /wp:paragraph -->' % (attrs(a), c, text)

def ul(items, cls=None, ordered=False):
    a = {}
    if ordered: a['ordered'] = True
    if cls: a['className'] = cls
    tag = 'ol' if ordered else 'ul'
    c = 'wp-block-list' + (' ' + cls if cls else '')
    lis = ''.join('<!-- wp:list-item -->\n<li>%s</li>\n<!-- /wp:list-item -->' % i for i in items)
    return '<!-- wp:list%s -->\n<%s class="%s">%s</%s>\n<!-- /wp:list -->' % (attrs(a), tag, c, lis, tag)

def buttons(*btns, cls=None):
    a = {'className': cls} if cls else {}
    inner = ''
    for text, href, style in btns:
        sc = 'is-style-' + style
        inner += '<!-- wp:button {"className":"%s"} -->\n<div class="wp-block-button %s"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div>\n<!-- /wp:button -->' % (sc, sc, href, text)
    c = 'wp-block-buttons' + (' ' + cls if cls else '')
    return '<!-- wp:buttons%s -->\n<div class="%s">%s</div>\n<!-- /wp:buttons -->' % (attrs(a), c, inner)

def table(head, rows, cls='price-table'):
    th = ''.join('<th>%s</th>' % x for x in head)
    tb = ''.join('<tr>' + ''.join('<td>%s</td>' % x for x in r) + '</tr>' for r in rows)
    return '<!-- wp:table {"hasFixedLayout":false,"className":"%s"} -->\n<figure class="wp-block-table %s"><table><thead><tr>%s</tr></thead><tbody>%s</tbody></table></figure>\n<!-- /wp:table -->' % (cls, cls, th, tb)

def details(summary, *paras):
    return '<!-- wp:details -->\n<details class="wp-block-details"><summary>%s</summary>%s</details>\n<!-- /wp:details -->' % (summary, '\n'.join(p(x) for x in paras))

def image(alt, caption, ratio='4/3'):
    a = {'aspectRatio': ratio, 'scale': 'cover', 'sizeSlug': 'full', 'linkDestination': 'none', 'className': 'photo-todo'}
    return '<!-- wp:image%s -->\n<figure class="wp-block-image size-full photo-todo"><img src="%s" alt="%s" style="aspect-ratio:%s;object-fit:cover"/><figcaption class="wp-element-caption">%s</figcaption></figure>\n<!-- /wp:image -->' % (attrs(a), IMG, alt, ratio, caption)

def html(raw):
    return '<!-- wp:html -->\n%s\n<!-- /wp:html -->' % raw

def dyn(name):
    return '<!-- wp:gfa/%s /-->' % name

def head(eyebrow, title, lead=None):
    parts = [p(eyebrow, 'eyebrow'), h(title)]
    if lead: parts.append(p(lead, 'lead'))
    return grp('section__head', *parts)

def section(cls, *inner, anchor=None, wrap='wrap'):
    return grp(cls, grp(wrap, *inner), tag='section', anchor=anchor)

def page_intro(eyebrow, title, lead, cta=True):
    items = [p(eyebrow, 'eyebrow'), h(title, 1), p(lead, 'lead')]
    if cta: items.append(buttons(('Chiedi un preventivo', '#preventivo', 'gfa-primary')))
    return section('page-hero', *items, wrap='wrap stack')

def steps(*s):
    return grp('route-line', grp('route-steps', *[grp('route-step', h(t, 3), p(d)) for t, d in s]))

def modes(*m):
    return grp('modes', *[grp('mode', h(t, 3), *[x if x.startswith('<!--') else p(x) for x in body]) for t, *body in m])

P = {}

P['hero'] = ('Hero homepage', section('hero',
    grp('hero__text',
        p('Volantinaggio · Stampa · Eventi', 'eyebrow'),
        h('Volantinaggio<br>che si può<br><mark class="hl">verificare.</mark>', 1),
        p('Distribuiamo volantini in cassetta, nei negozi e agli eventi. Pianifichiamo zone e quantità con te e ti mostriamo dove è passato ogni distributore.', 'lead'),
        buttons(('Chiedi un preventivo', '#preventivo', 'gfa-primary'), ('Come lavoriamo', '#metodo', 'gfa-ghost'), cls='hero__cta'),
        ul(['Zone e quantità pianificate con te', 'Report a fine giro', 'Stampa inclusa, se serve'], 'hero__proof')),
    grp('hero__visual',
        image('Da fornire: la squadra GFA al lavoro', 'Da fornire: la squadra GFA al lavoro, persone reali in divisa, furgone recente, liberatoria firmata.', '4/5'),
        grp('ticket',
            p('<strong>Giro di esempio · Rapallo centro</strong>', 'ticket__title'),
            p('Cassette <strong>1.240</strong>', 'ticket__row'),
            p('Negozi <strong>38</strong>', 'ticket__row'),
            p("Durata <strong>4 h 10'</strong>", 'ticket__row'),
            p('Report <strong>mappa + foto</strong>', 'ticket__row'))),
    wrap='wrap hero__grid'))

P['numeri'] = ('Numeri', grp('facts', grp('wrap', dyn('numeri')), tag='section'))

def path(tag, title, items, link, label, main=False):
    return grp('path' + (' path--main' if main else ''), p(tag, 'path__tag'), h(title, 3), ul(items), p('<a href="%s">%s →</a>' % (link, label), 'path__go'))

P['percorsi'] = ('Tre percorsi', section('section',
    head('Cosa ti serve', 'Parti da quello che devi fare.'),
    grp('paths',
        path('Servizio principale', 'Distribuire volantini', ['In cassetta, porta a porta', 'Nei negozi e nei punti di passaggio', 'A fiere, piazze ed eventi'], url('/volantinaggio/'), 'Volantinaggio', True),
        path('Prima della distribuzione', 'Preparare e stampare', ['Grafica di volantini e locandine', 'Stampa di ogni formato', 'Banner, striscioni, allestimenti'], url('/stampa-e-grafica/'), 'Stampa e grafica'),
        path('Per organizzatori', 'Promuovere un evento', ["Locandine e volantini dell'evento", 'Distribuzione nelle zone giuste', 'QR verso prenotazioni e biglietti'], url('/promozione-eventi/'), 'Promozione eventi')),
    anchor='servizi'))

P['metodo'] = ('Come lavoriamo', section('section section--alt',
    head('Come lavoriamo', 'Quattro tappe, un solo referente.', 'Non devi sapere quante copie ti servono: lo decidiamo insieme partendo da cosa vuoi promuovere e da chi vuoi raggiungere.'),
    steps(('Brief', 'Ci racconti cosa promuovi, dove e quando. Ti ricontattiamo entro ' + todo('tempo di risposta da confermare') + '.'),
          ('Piano zone', 'Scegliamo vie, comuni e modalità, e ti diciamo quante copie servono e perché.'),
          ('Distribuzione', 'Personale riconoscibile, in divisa, con un capo squadra che segue il giro.'),
          ('Report', 'A fine lavoro ricevi mappa del percorso, foto a campione e copie consegnate per zona.')),
    anchor='metodo'))

REPORT = '''<figure class="report">
<div class="report__bar"><span>REPORT · GIRO 0417</span><span>Rapallo · zona Centro</span><span>esempio</span></div>
<svg class="report__map" viewBox="0 0 560 300" role="img" aria-label="Mappa di esempio con il percorso del distributore e quattro tappe">
<rect class="block" x="40" y="30" width="150" height="90" rx="4"/><rect class="block" x="230" y="30" width="130" height="90" rx="4"/><rect class="block" x="400" y="30" width="120" height="90" rx="4"/>
<rect class="block" x="40" y="170" width="150" height="100" rx="4"/><rect class="block" x="230" y="170" width="130" height="100" rx="4"/><rect class="block" x="400" y="170" width="120" height="100" rx="4"/>
<path class="street" d="M20 145 H540 M210 15 V285 M380 15 V285"/>
<path class="route route-draw" d="M30 145 H200 V40 H372 V145 H200 V262 H390 V180 H530"/>
<circle class="stop" cx="30" cy="145" r="8"/><circle class="stop" cx="200" cy="40" r="8"/><circle class="stop" cx="372" cy="145" r="8"/><circle class="stop" cx="530" cy="180" r="8"/>
<text class="label" x="44" y="136">08:02 partenza</text><text class="label" x="214" y="58">Via Mameli</text><text class="label" x="386" y="136">09:40</text><text class="label" x="436" y="200">12:12 fine</text>
</svg>
<ol class="report__rows">
<li><b>08:02</b><span>Corso Italia</span><b>312 cassette</b></li>
<li><b>09:40</b><span>Via Mameli, via Venezia</span><b>418 cassette</b></li>
<li><b>11:05</b><span>Negozi del centro</span><b>38 punti</b></li>
</ol>
<figcaption class="report__note">Esempio illustrativo: sostituire con un report reale anonimizzato.</figcaption>
</figure>'''

P['controllo'] = ('Controllo e report', section('section section--blue',
    grp('control__text',
        p('Controllo', 'eyebrow'),
        h('Sai dove sono finiti i tuoi volantini.'),
        p('Ogni giro lascia una traccia: percorso, orari e foto. Te la mandiamo senza che tu debba chiederla.', 'lead'),
        ul(['<strong>Percorso registrato</strong>: il tragitto del distributore su mappa. ' + todo('GPS: da confermare con GFA'),
            '<strong>Foto a campione</strong>: cassette e punti di consegna, senza nomi dei residenti.',
            '<strong>Copie per zona</strong>: quante ne abbiamo consegnate, via per via.'], 'checks')),
    html(REPORT), anchor='controllo', wrap='wrap control'))

P['lavori'] = ('Lavori svolti', grp('section',
    grp('works-pin', grp('wrap',
        grp('works-head', head('Lavori svolti', 'Prima di chiederci un preventivo, guarda cosa abbiamo fatto.'), buttons(('Tutti i lavori', url('/lavori/'), 'gfa-ghost'))),
        dyn('lavori'))), tag='section', anchor='lavori'))

P['zone'] = ('Zone servite', section('section section--alt',
    grp('zones__text', head('Zone servite', 'Otto sedi, dal Ponente alla pianura.', 'Ogni sede conosce le sue vie. Scegli la tua zona per vedere comuni coperti, modalità e referente.'), dyn('zone-elenco')),
    dyn('zone-mappa'), anchor='zone', wrap='wrap zones'))

P['recensioni'] = ('Recensioni', section('section',
    head('Cosa dicono i clienti', 'Parole di chi ci ha già affidato un giro.'),
    grp('trust', *[grp('quote', p(todo('Recensione Google reale n. %d, testo integrale' % i), 'quote__text'), p(todo('Nome, attività, città'), 'quote__cite')) for i in (1, 2, 3)]),
    anchor='recensioni'))

P['preventivo'] = ('Preventivo', section('section section--blue',
    grp('quote-box__text', p('Preventivo', 'eyebrow'), h('Tre passaggi, poi ti ricontattiamo.'), p('Se non sai quante copie ti servono, va bene: lo scegliamo insieme.', 'lead'), dyn('contatti')),
    dyn('preventivo'), anchor='preventivo', wrap='wrap quote-box'))

# Volantinaggio
P['volantinaggio-intro'] = ('Volantinaggio: apertura', page_intro('Servizio principale', 'Volantinaggio e distribuzione volantini',
    'In cassetta, nei negozi, nelle piazze e agli eventi, nelle zone di Milano, Como, Rapallo, Genova, Sanremo, Bassano del Grappa, Modena e Reggio Emilia. Ti diciamo quale modalità conviene, quante copie servono e ti mostriamo il lavoro fatto.'))
P['volantinaggio-modalita'] = ('Volantinaggio: modalità', section('section',
    head('Modalità', 'Quale distribuzione ti serve?'),
    modes(('In cassetta, porta a porta', 'Un volantino in ogni cassetta delle vie scelte.', p('<strong>Conviene per</strong> offerte di negozi e servizi di quartiere, aperture, agenzie immobiliari.', 'mode__when')),
          ('Negozi e attività commerciali', 'Pile di volantini presso bar, edicole e negozi che accettano di esporli.', p('<strong>Conviene per</strong> eventi, corsi, iniziative culturali.', 'mode__when')),
          ('Postazioni fisse e centri commerciali', 'Distribuzione a mano in punti di passaggio, con personale in divisa.', p('<strong>Conviene per</strong> lanci di prodotto e promozioni a tempo.', 'mode__when')),
          ('Fiere, piazze ed eventi', 'Personale sul posto, anche con i pannelli indossabili WOW.', p("<strong>Conviene per</strong> farsi notare dove c'è già il pubblico giusto.", 'mode__when')))))
P['volantinaggio-prezzi'] = ('Volantinaggio: prezzi', section('section',
    head('Prezzi', 'Pacchetti con stampa inclusa', 'Prezzi IVA esclusa. La grafica si aggiunge solo se ti serve. ' + todo('Listino da confermare per sede')),
    table(['Copie', 'Formato', 'Include', 'Prezzo'], [
        ['5.000', 'A5 fronte/retro ' + todo('confermare'), 'Stampa + distribuzione in cassetta + report', '490 €'],
        ['10.000', 'A5 fronte/retro ' + todo('confermare'), 'Stampa + distribuzione in cassetta + report', '550 € ' + todo('verificare')],
        ['20.000', 'A5 fronte/retro ' + todo('confermare'), 'Stampa + distribuzione in cassetta + report', '920 €']]),
    anchor='prezzi'))
P['volantinaggio-faq'] = ('Volantinaggio: domande', section('section section--alt',
    head('Domande frequenti', 'Prima di chiedere un preventivo'),
    grp('faq',
        details('Quante copie mi servono?', 'Dipende da zona e obiettivo. Partiamo dal numero di cassette delle vie che ti interessano e ti proponiamo una quantità, con il perché.'),
        details('Come faccio a sapere che i volantini sono stati consegnati?', 'A fine giro ricevi il report con percorso, orari, foto a campione e copie per zona. ' + todo('Descrivere i controlli reali di GFA')),
        details('Serve un permesso del Comune?', 'Per la distribuzione in cassetta di norma no; per la distribuzione in strada alcuni Comuni hanno regole proprie. Le verifichiamo noi. ' + todo('Confermare con GFA')),
        details('In quanto tempo partite?', todo('Tempi medi da confermare')))))

# Stampa
P['stampa-intro'] = ('Stampa: apertura', page_intro('Prima della distribuzione', 'Stampa e grafica',
    'Progettiamo e stampiamo il materiale che poi distribuiamo: un solo referente dal file alla cassetta, senza passaggi tra fornitori diversi.'))
P['stampa-prodotti'] = ('Stampa: prodotti', section('section',
    head('Cosa stampiamo', 'Dal biglietto da visita al fondale da fiera'),
    modes(('Volantini e pieghevoli', 'A6, A5, A4, pieghevoli a due e tre ante, fronte e retro.', p('<strong>Per</strong> volantinaggio in cassetta e nei negozi.', 'mode__when')),
          ('Locandine e manifesti', 'Da A3 a 70×100, per vetrine, bacheche e affissione.', p('<strong>Per</strong> eventi, spettacoli, inaugurazioni.', 'mode__when')),
          ('Allestimenti', 'Banner, striscioni, roll-up, fondali, rivestimenti per pavimenti.', p('<strong>Per</strong> fiere, stand e manifestazioni.', 'mode__when')),
          ('Immagine coordinata e gadget', 'Logo, biglietti da visita, carta intestata, shopper, abbigliamento.', p('<strong>Per</strong> attività che partono o si rinnovano.', 'mode__when')))))
P['stampa-processo'] = ('Stampa: come nasce un volantino', section('section section--alt',
    head('Studio grafico', 'Come nasce un volantino'),
    steps(('Brief', 'Cosa promuovi, a chi, con quale offerta e scadenza.'),
          ('Bozza', 'Una prima proposta grafica. ' + todo('numero di revisioni incluse')),
          ('File di stampa', 'Controllo di formati, margini e colori prima di stampare.'),
          ('Stampa e consegna', 'Il materiale va direttamente alla squadra di distribuzione.'))))
P['stampa-faq'] = ('Stampa: domande', section('section section--alt',
    head('Domande frequenti', 'Prima di mandarci un file'),
    grp('faq',
        details('Posso mandarvi un file già pronto?', 'Sì. Lo controlliamo prima della stampa e ti avvisiamo se qualcosa non va. ' + todo('formati accettati')),
        details('Quanto costa la grafica?', 'Si aggiunge al prezzo di stampa e distribuzione solo se ti serve. ' + todo('prezzo di partenza da confermare')),
        details('In quanti giorni avete il materiale stampato?', todo('tempi medi di stampa')))))

# Eventi
P['eventi-intro'] = ('Eventi: apertura', page_intro('Per organizzatori, associazioni e Comuni', 'Promuovi il tuo evento sul territorio',
    "Dalla locandina alla distribuzione nelle zone giuste, con un QR che porta a informazioni e prenotazioni. L'evento lo organizzi tu: noi facciamo in modo che si sappia."))
P['eventi-percorso'] = ('Eventi: dal volantino alla prenotazione', section('section',
    head('Il percorso', 'Dal volantino alla prenotazione'),
    steps(('Locandina e volantino', 'Grafica coordinata per affissione e distribuzione.'),
          ('Stampa', 'Formati da A5 a 70×100, banner e striscioni.'),
          ('Distribuzione', 'Negozi, bacheche, cassette e punti di passaggio delle zone scelte.'),
          ('QR dedicato', "Porta alla pagina dell'evento: contiamo le scansioni, non le presenze."))))

# Chi siamo
P['chi-intro'] = ('Chi siamo: apertura', page_intro('Chi siamo', 'Nel volantinaggio dal ' + todo('anno'),
    'GFA distribuisce volantini e materiale pubblicitario nel Nord Italia, con otto sedi operative e squadre che conoscono le proprie zone.', cta=False))
P['chi-storia'] = ('Chi siamo: storia', section('section',
    grp('control__text', p('La nostra storia', 'eyebrow'), h('Da una sede a Milano a una rete di sedi'),
        p(todo("Storia da raccontare con GFA: anno e luogo di inizio, chi ha fondato l'azienda, come è cresciuta la rete, cosa è cambiato negli anni")),
        p(todo('Continuità tra GFA Comunicazione e GFA Marketing: come descriverla correttamente'))),
    image('Da fornire: foto storica o della prima sede', 'Da fornire: foto storica o della prima sede, anche vecchia, purché autentica e con autorizzazione.'),
    wrap='wrap control'))
P['chi-persone'] = ('Chi siamo: persone', section('section section--alt',
    head('Le persone', 'Chi segue il tuo lavoro', 'Un referente per ogni cliente, dalla prima telefonata al report finale.'),
    grp('trust', *[grp('person', image('Da fornire: ' + who, 'Da fornire: ' + spec, '1/1'), p('<strong>' + todo('Nome e cognome') + '</strong>'), p(todo('ruolo e sede'), 'quote__cite'))
        for who, spec in (('referente commerciale', 'ritratto su sfondo neutro, luce naturale, liberatoria.'), ('capo squadra distribuzione', 'in divisa, sul campo.'), ('studio grafico', 'al lavoro su un progetto reale.'))])))
P['chi-impegni'] = ('Chi siamo: impegni', section('section',
    head('Come lavoriamo', 'Tre impegni che puoi verificare'),
    grp('paths',
        grp('path', p('Personale', 'path__tag'), h('Riconoscibile', 3), p('Divise e mezzi con il marchio GFA. ' + todo('dipendenti diretti o collaboratori?'))),
        grp('path', p('Controllo', 'path__tag'), h('Documentato', 3), p('Report di ogni giro con percorso, foto a campione e copie per zona.')),
        grp('path', p('Privacy', 'path__tag'), h('Rispettata', 3), p('Nessun dato personale nelle foto dei report, dati dei clienti trattati secondo il GDPR.')))))
P['chi-dati'] = ('Chi siamo: dati societari', section('section', head('Dati societari', 'Chi eroga il servizio'), dyn('dati-societari')))

# Franchising
P['franchising-intro'] = ('Franchising: apertura', section('page-hero',
    p('Rete GFA', 'eyebrow'), h('Apri una sede GFA nella tua zona', 1),
    p('Metodo di lavoro, marchio, strumenti di controllo e clienti della rete. Tu porti la conoscenza del territorio. ' + todo('Condizioni del franchising da confermare con GFA'), 'lead'),
    buttons(('Candidati', '#candidatura', 'gfa-primary')), wrap='wrap stack'))
P['franchising-offerta'] = ('Franchising: cosa ricevi', section('section',
    head('Cosa ricevi', 'Cosa mette la rete, cosa metti tu'),
    modes(('Dalla rete', ul(['Marchio e immagine coordinata', 'Metodo di pianificazione e report', 'Formazione iniziale ' + todo('durata'), 'Pagina della tua zona su questo sito'])),
          ('Da te', ul(['Conoscenza della zona e dei commercianti', 'Squadra di distribuzione', 'Un magazzino o spazio per il materiale', todo('investimento iniziale e royalty')])))))
P['franchising-percorso'] = ('Franchising: come funziona', section('section section--alt',
    head('Come funziona', 'Dalla candidatura alla prima distribuzione'),
    steps(('Candidatura', 'Ci dici dove vuoi operare e con quale esperienza.'), ('Colloquio', 'Verifichiamo insieme la zona e il potenziale.'),
          ('Formazione', 'Metodo, strumenti, primi clienti affiancati.'), ('Avvio', 'La tua sede compare nella rete e sul sito.'))))
P['franchising-candidatura'] = ('Franchising: candidatura', section('section section--blue',
    grp('quote-box__text', p('Candidatura', 'eyebrow'), h('Parliamone.'), p('Scrivici la zona che ti interessa: ti ricontattiamo per un primo colloquio senza impegno.', 'lead')),
    grp('form', p('<strong>Scrivi a</strong> <?php echo esc_html( gfa_opt( \'email\' ) ); ?> con oggetto "Franchising", indicando zona, esperienza e un recapito.'),
        p(todo('In alternativa: modulo dedicato, se GFA vuole ricevere le candidature separate dai preventivi'), 'hint')),
    anchor='candidatura', wrap='wrap quote-box'))

P['contatti-intro'] = ('Contatti: apertura', page_intro('Contatti', 'Parla con la sede più vicina', 'Per un preventivo usa il modulo qui sotto: ti ricontatta il referente della tua zona.', cta=False))

os.makedirs(OUT, exist_ok=True)
for slug, (title, content) in P.items():
    with open(os.path.join(OUT, slug + '.php'), 'w') as f:
        f.write('<?php\n/**\n * Title: GFA · %s\n * Slug: gfa/%s\n * Categories: gfa\n * Inserter: yes\n *\n * @package gfa\n */\n?>\n%s\n' % (title, slug, content))
print(len(P), 'modelli scritti')
