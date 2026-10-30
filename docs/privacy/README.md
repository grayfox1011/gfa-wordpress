# Informative privacy e cookie

Aggiornamento: 30 ottobre 2026. Le versioni senza segnaposti `privacy-policy.html` e `cookie-policy.html` corrispondono alle pagine WordPress esistenti Privacy (ID 3) e Cookie (ID 14). Il tema 0.5.17 introduce un banner e un pannello di preferenze per le sole memorie facoltative del tema; GA4, Google Ads e Meta Pixel restano inattivi. Le precedenti versioni `.bozza.html` restano documenti di lavoro separati, con i relativi avvisi. La pubblicazione delle informative e il banner non costituiscono certificazione di conformità né completano le verifiche tecniche e organizzative elencate sotto.

## File e uso

- `privacy-policy.html`: versione pubblicata dell'informativa generale.
- `cookie-policy.html`: versione pubblicata sui dati del browser e sui servizi futuri, con schede testuali per le memorie del tema.
- `privacy-policy.bozza.html`: precedente bozza di lavoro dell'informativa generale, da non pubblicare.
- `cookie-policy.bozza.html`: precedente bozza di lavoro con inventario preliminare e verifiche aperte, da non pubblicare.

I due file pubblicati contengono un blocco Gutenberg HTML con titolo e sezioni, senza script o modulo. Usano il template **Pagina a sezioni GFA**, che evita il modulo preventivo automatico. Le bozze storiche restano separate con avviso e segnaposti; non sovrascrivere con esse le pagine pubbliche. Prima della produzione verificare anche l'assegnazione della privacy nelle impostazioni WordPress e i collegamenti sul dominio definitivo.

## Evidenze e limiti

Audit iniziale del tema 0.5.16, commit `14e275b4d68461bca2a45d742a6d46d937c8346c`: nessun Google Analytics/Ads/GTM, Meta Pixel, CMP, `document.cookie` o `localStorage` nel codice esaminato. Con la versione 0.5.17 il tema aggiunge il controllo locale descritto sotto e la chiave `localStorage` `gfaConsent`; non aggiunge tag di tracking. Font, GSAP e Lenis sono locali. La lettura dei sorgenti **non è uno scan runtime** e non esclude codice inserito da plugin, hosting o tag manager esterni al tema.

Il controllo DOM iniziale, precedente alla pubblicazione, aveva mostrato titoli e modulo, ma nessun testo di policy, iframe o banner; le risorse elencate erano locali. Non è stata effettuata una scansione delle richieste di rete. L'accesso amministrativo è stato ripristinato: la schermata Plugin mostra soltanto **All-in-One WP Migration and Backup 7.113** attivo e nessuna CMP nell'elenco. Questo controllo non sostituisce la verifica di eventuali integrazioni esterne o delle impostazioni di produzione.

## Importazione iniziale delle bozze - 30 ottobre 2026

| Pagina creata | ID | Stato | Percorso amministrativo sul sito WordPress |
| --- | --- | --- | --- |
| Privacy Policy — bozza da completare | 194 | Bozza salvata, non pubblicata | [Apri bozza](https://wordpress1791795513908.app.runonflux.io/wp-admin/post.php?post=194&action=edit) |
| Cookie Policy — bozza da completare | 196 | Bozza salvata, non pubblicata | [Apri bozza](https://wordpress1791795513908.app.runonflux.io/wp-admin/post.php?post=196&action=edit) |

Il salvataggio di entrambe è stato confermato dall'interfaccia. In questa fase le pagine pubbliche originali Privacy (ID 3) e Cookie (ID 14) non erano state modificate o sostituite. Nessuna CMP, tag GA4/Google Ads/Meta Pixel o nuova configurazione del consenso è stata attivata. Le bozze sono rimaste documenti di lavoro non pubblicati. Evidenza storica: [schermata delle bozze WordPress](wordpress-bozze.jpg).

## Pubblicazione sulle pagine esistenti - 30 ottobre 2026

| Pagina | ID | Stato e percorso pubblico |
| --- | --- | --- |
| Privacy Policy | 3 | Pubblicata su `/privacy-policy/` |
| Cookie Policy | 14 | Pubblicata su `/cookie-policy/` |

WordPress ha confermato **Pagina aggiornata** per entrambe, con stato **Pubblicato**, template **Pagina a sezioni GFA** e slug originali invariati. Il controllo pubblico riferito dal revisore ha verificato testi e dati del titolare, assenza di avvisi BOZZA, segnaposti e riferimenti estranei; nessun modulo preventivo presente. I collegamenti Privacy e Cookie nel footer sono stati verificati. Le bozze separate 194 e 196 restano non pubblicate. Questa prima pubblicazione non aveva modificato tema o tag; nessuna nuova schermata è stata caricata nel repository.

I testi descrivono i servizi di tracking come futuri. Restano da verificare fornitori di hosting e posta, destinatari e trasferimenti effettivi, inventario runtime completo, applicazione dei criteri di conservazione e flusso del consenso prima dell'attivazione di GA4, Google Ads o Meta Pixel. La verifica dei contenuti pubblici non è una scansione di rete e non attesta la conformità complessiva del sito.

| Elemento realmente presente nel tema | Evidenza |
| --- | --- |
| `gfaConsent`, `localStorage` | `assets/js/cookie-consent-state.js`: scelta `functional`, versione del sistema, versione delle preferenze, `updatedAt` ed `expiresAt`. Accettazione e rifiuto valgono sei mesi di calendario dall'ultimo salvataggio, con gestione della fine del mese. La scadenza è verificata dal codice, non dal browser. |
| `gfaSeen`, `sessionStorage` | `assets/js/preload-head.js` e `main.js`: evita la ripetizione del preloader, soltanto con consenso Esperienza di navigazione; cancellato al clic sul logo o alla revoca. |
| `gfaTrans`, `sessionStorage` | Segnale di transizione, soltanto con consenso Esperienza di navigazione; letto e rimosso all'avvio della pagina successiva o cancellato alla revoca. |
| Durata delle due chiavi | Nessun TTL a giorni. Sessione della scheda e comportamento del browser; da verificare anche con ripristino sessione. |
| Modulo preventivo | `parts/preventivo.php` e `inc/quote-form.php`: invio tramite `wp_mail`, senza archivio richieste esplicito nel gestore del tema. |
| Recapito | `info@gfamarketing.com` è contatto pubblico e default di `email_preventivi`; il destinatario runtime non è stato verificato. |
| Policy | `inc/starter-content.php` contiene solo un segnaposto cookie. Il tema non crea il testo privacy. |

Le due memorie per effetti grafici sono trattate come facoltative e non vengono lette o scritte prima del consenso; il codice può rimuovere precedenti valori per rispettare assenza di scelta o rifiuto. La memoria tecnica `gfaConsent` conserva la scelta, non dati di navigazione. Non viene inviata a un archivio server e non equivale, da sola, a un registro centralizzato delle prove del consenso. La mancanza di scritture nel database del tema non esclude archivi email, log, plugin o backup.

## Banner del tema 0.5.17

- Banner non modale con **Accetta tutto**, **Rifiuta** e **Personalizza**; accettazione e rifiuto hanno lo stesso trattamento visivo. Il link permanente **Preferenze cookie** riapre un dialogo accessibile con gestione del focus, Tab ed Escape.
- Unica categoria facoltativa disponibile: **Esperienza di navigazione**, inizialmente disattivata. Accetta tutto riguarda soltanto questa categoria e non autorizza futuri servizi Google/Meta. Chiusura senza salvataggio, navigazione e scroll non esprimono un consenso.
- Stato iniziale eseguito prima del preloader; rifiuto e revoca rimuovono le due memorie del tema. Record scaduti, malformati, di versione obsoleta o con date incoerenti non abilitano memorie facoltative. Le modifiche provenienti da un'altra scheda sono recepite.
- Se il salvataggio nel browser fallisce, il controllo applica la scelta in memoria per la pagina corrente e segnala che non è persistente. Non conserva deliberatamente un precedente consenso permissivo quando la rimozione è possibile.
- Nessun Google tag, Meta Pixel, ping senza cookie o integrazione Consent Mode è incluso. Il banner locale dovrà essere esteso o sostituito con una gestione adatta ai servizi effettivi prima di aggiungerli.

Verifiche automatiche: `node --test tools/test-cookie-consent.cjs` (9 test su stato, gate del preloader, scadenza, rifiuto, revoca, storage bloccato e cambio da altra scheda); sintassi dei quattro JavaScript coinvolti e lint di tutti i 56 file PHP. I test di stato non sostituiscono la verifica del browser, della rete o di eventuali script esterni al tema.

Il tema 0.5.17 è stato installato sul sito di prova con conferma WordPress. Verificati nel browser: banner iniziale, categoria disattivata, chiusura con Escape e ritorno del focus, rifiuto persistente dopo ricarica, riapertura dal footer, accettazione persistente e revoca mediante disattivazione e salvataggio. A 390 × 844 px il dialogo è leggibile e scorre senza overflow orizzontale; i pulsanti hanno altezza di 46 px. Tab e Shift+Tab mantengono il focus nel dialogo. Il viewport è stato ripristinato dopo il controllo.

Le pagine pubblicate Cookie ID 14 e Privacy ID 3 sono state aggiornate anche con i testi relativi al banner, con conferma **Pagina aggiornata**. Le bozze separate e le personalizzazioni Gutenberg delle altre pagine restano preservate; nessun pattern è stato rigenerato. Nessuna schermata nuova è stata caricata nel repository.

## Configurazione futura dei tracciamenti, non ancora implementata

- **Consent Mode Basic**, con nessun hit ai servizi di tracking prima della scelta e con rifiuto totale; niente ping senza cookie. Bloccare realmente i tag, non limitarsi a impostare `denied` su tag già caricati. Consent Mode non è una CMP e non governa Meta.
- **GA4 → Statistiche; Google Ads e Meta Pixel → Marketing**, con preferenze per ciascun servizio. Non attivare Ads/Meta accettando soltanto GA4. Verificare le destinazioni del Google tag condiviso, i collegamenti GA4–Ads e l'assenza di attivazioni incrociate.
- Configurare coerentemente `analytics_storage`, `ad_storage`, `ad_user_data` e `ad_personalization`. Personalizzazione, remarketing, Google Signals, conversioni avanzate, matching avanzato e integrazioni server non sono implicitamente autorizzati: definire prima le funzioni effettive.
- Prevedere accettazione e rifiuto con pari evidenza e accessibilità, scelta granulare, riapertura permanente delle preferenze e revoca; memorizzare le scelte e la versione dell'informativa con una retention definita. Non riproporre il banner a ogni visita o in modo insistente: rispettare le condizioni di riproposizione delle linee guida del Garante. Non promettere cancellazioni di cookie terzi non controllabili.
- Il preventivo usa una base precontrattuale ai sensi dell'art. 6(1)(b) **ove applicabile**, non il consenso marketing. Valutare separatamente i referenti aziendali. Allineare la checkbox e la registrazione attuali («consenso privacy») alla sola presa visione e alla finalità effettiva. **[CONFERMARE EVENTUALI NEWSLETTER O CONTATTI PROMOZIONALI, NON PREVISTI DAL MODULO ATTUALE]**.
- Non inviare a GA4/Ads/Meta nome, email, telefono, testo libero o dati del preventivo dentro URL, eventi o parametri. Definire gli eventi ammessi prima di implementare le conversioni.

## Dati confermati dal titolare - 30 ottobre 2026

L'utente ha fornito la ragione sociale **GFA COMUNICAZIONE DI BATTISTONI EDOARDO**, la P.IVA **09182350968** e il VAT europeo **IT09182350968**. Alla domanda sulla sede legale ha confermato **Viale Monte Ceneri, 62, 20155 Milano MI**. I dati sono riportati in entrambe le bozze, con l'email aziendale già fornita **info@gfamarketing.com** per contatti e richieste privacy. Il codice fiscale non è stato fornito e non è stato dedotto dalla P.IVA; non è stata attribuita alcuna funzione di DPO.

Le bozze WordPress 194 e 196 sono state aggiornate con questi dati, con salvataggio confermato; restano non pubblicate. Gli stessi dati sono riportati anche nelle versioni successivamente pubblicate sulle pagine 3 e 14. L'aggiornamento dei dati del titolare non aveva modificato tema o tag; il banner locale è stato aggiunto in seguito nella versione 0.5.17.

## Verifiche e dati ancora necessari

1. Eventuale rappresentante e DPO, senza presumere che non esistano.
2. Dominio finale: nel tema compare `https://gfamarketing.com/`, ma non costituisce conferma delle impostazioni di produzione.
3. Hosting, email, eventuale CRM, plugin/log/backup: soggetti, ruoli, sedi di trattamento e tempi/criteri di conservazione effettivi.
4. ID GA4, Google Ads e Meta, eventuale contenitore GTM, eventi e conversioni, collegamenti fra servizi e impostazioni delle piattaforme. Gli ID servono alla configurazione; non occorre esporli automaticamente nelle informative.
5. Soggetti contrattuali di Google/Meta, informative, destinatari e trasferimenti: verificare paesi e meccanismi effettivi senza presumere adeguatezza, clausole o localizzazione UE.
6. Per i futuri servizi: gestione del consenso adatta alle integrazioni, inventario cookie/storage dopo configurazione, durata delle preferenze e delle prove del consenso, comportamento alla revoca. Il controllo attuale copre soltanto le memorie del tema.

## Verifica prima della produzione e dell'attivazione dei tracciamenti

1. Completare i dati e validare le basi/finalità, il testo delle pagine e il flusso del modulo. Risolvere la validazione server incompleta della zona e valutare protezioni antiabuso; provare ricezione email reale.
2. Prima dei nuovi tracciamenti, configurare la gestione del consenso e i servizi in staging, inizialmente bloccati; aggiornare categorie, versione delle preferenze e informative. Non riutilizzare il consenso alla sola Esperienza di navigazione per Google o Meta.
3. Controllare rete e storage con profilo pulito: nessuna scelta, rifiuto, GA4 solo, Ads solo, Meta solo, combinazioni e revoca; ripetere dopo ricarica, navigazione e cambio preferenze. Il test deve coprire anche eventuali invii server.
4. Inventariare nomi, domini, finalità e durate realmente osservati; distinguerli dalla retention dei dati nelle piattaforme. Verificare anche cookie/sessioni WordPress e plugin pertinenti ai visitatori.
5. Aggiornare le informative pubblicate con la configurazione effettiva, verificare nuovamente i collegamenti e il comportamento sul dominio finale. Le bozze storiche con avvisi e segnaposti restano documenti di lavoro. Conservare evidenze di configurazione e test.

## Fonti ufficiali consultate

- [Garante — Doveri: come trattare correttamente i dati, doc. 8981258](https://www.garanteprivacy.it/web/guest/home/docweb/-/docweb-display/docweb/8981258): principi, basi giuridiche e informazioni da fornire.
- [Garante — Linee guida cookie e altri strumenti di tracciamento, doc. 9677876](https://www.garanteprivacy.it/home/docweb/-/docweb-display/docweb/9677876): scelte preventive e informative sui tracciamenti.
- [Google — Consent Mode](https://developers.google.com/tag-platform/security/concepts/consent-mode): distinzione Basic/Advanced e segnali di consenso.
- [Google — EU User Consent Policy](https://www.google.com/about/company/user-consent-policy/): informazione sui destinatari, documentazione e revoca del consenso.
- [GDPR, testo ufficiale EUR-Lex](https://eur-lex.europa.eu/legal-content/IT/ALL/?uri=CELEX%3A32016R0679): in particolare artt. 6, 13 e 15–22.

Consultate il 30 ottobre 2026. Le fonti definiscono il quadro e i comportamenti tecnici; non dimostrano la configurazione del sito né completano i dati mancanti.
