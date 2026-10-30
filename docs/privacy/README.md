# Bozze privacy e cookie — non pubblicare

Preparazione: 30 ottobre 2026. Questi documenti richiedono i dati mancanti, verifica del trattamento reale e revisione del titolare/consulente prima della pubblicazione. Le informative sono state importate in WordPress come nuove bozze separate; nessuna policy è stata pubblicata e nessuna CMP o tag di tracking è stato attivato.

## File e uso

- `privacy-policy.bozza.html`: frammento Gutenberg per l'informativa generale.
- `cookie-policy.bozza.html`: frammento Gutenberg con inventario preliminare e servizi futuri.

Contengono blocchi standard, senza script o modulo. Importare prima in pagine **bozza** tramite editor del codice; il titolo della pagina resta gestito da WordPress. Mantenere l'avviso iniziale e i segnaposti fino alla revisione. Alla pubblicazione usare pagine senza modulo preventivo automatico e assegnare la privacy nelle impostazioni WordPress; verificare i collegamenti `/privacy-policy/` e `/cookie-policy/` sul dominio definitivo.

## Evidenze e limiti

Audit del tema 0.5.16, commit `14e275b4d68461bca2a45d742a6d46d937c8346c`. Nessun Google Analytics/Ads/GTM, Meta Pixel, CMP, `document.cookie` o `localStorage` rilevato nel codice esaminato. Font, GSAP e Lenis sono locali. Questo **non è uno scan runtime** e non esclude codice inserito da plugin, hosting o tag manager esterni al tema.

Il controllo DOM riferito dal revisore sulle pagine pubbliche ha mostrato titoli e modulo, ma nessun testo di policy, iframe o banner; le risorse elencate erano locali. Non è stata effettuata una scansione delle richieste di rete. L'accesso amministrativo è stato ripristinato: la schermata Plugin mostra soltanto **All-in-One WP Migration and Backup 7.113** attivo e nessuna CMP nell'elenco. Questo controllo non sostituisce la verifica di eventuali integrazioni esterne o delle impostazioni di produzione.

## Importazione WordPress — 30 ottobre 2026

| Pagina creata | ID | Stato | Percorso amministrativo sul sito WordPress |
| --- | --- | --- | --- |
| Privacy Policy — bozza da completare | 194 | Bozza salvata, non pubblicata | [Apri bozza](https://wordpress1791795513908.app.runonflux.io/wp-admin/post.php?post=194&action=edit) |
| Cookie Policy — bozza da completare | 196 | Bozza salvata, non pubblicata | [Apri bozza](https://wordpress1791795513908.app.runonflux.io/wp-admin/post.php?post=196&action=edit) |

Il salvataggio di entrambe è stato confermato dall'interfaccia. Le pagine pubbliche originali Privacy (ID 3) e Cookie (ID 14) non sono state modificate o sostituite. Nessuna CMP, tag GA4/Google Ads/Meta Pixel o nuova configurazione del consenso è stata attivata. Le bozze restano da completare e approvare. Evidenza: [schermata delle bozze WordPress](wordpress-bozze.jpg).

| Elemento realmente presente nel tema | Evidenza |
| --- | --- |
| `gfaSeen`, `sessionStorage` | `assets/js/preload-head.js` e `main.js`: evita la ripetizione del preloader; cancellato al clic sul logo. |
| `gfaTrans`, `sessionStorage` | Segnale di transizione, letto e rimosso dallo script iniziale della pagina successiva. |
| Durata delle due chiavi | Nessun TTL a giorni. Sessione della scheda e comportamento del browser; da verificare anche con ripristino sessione. |
| Modulo preventivo | `parts/preventivo.php` e `inc/quote-form.php`: invio tramite `wp_mail`, senza archivio richieste esplicito nel gestore del tema. |
| Recapito | `info@gfamarketing.com` è contatto pubblico e default di `email_preventivi`; il destinatario runtime non è stato verificato. |
| Policy | `inc/starter-content.php` contiene solo un segnaposto cookie. Il tema non crea il testo privacy. |

Non classificare automaticamente le memorie per effetti grafici come strettamente necessarie: valutarne l'uso o rimuovere lo storage superfluo. La mancanza di scritture nel database del tema non esclude archivi email, log, plugin o backup.

## Decisione tecnica proposta, non ancora implementata

- **Consent Mode Basic**, con nessun hit ai servizi di tracking prima della scelta e con rifiuto totale; niente ping senza cookie. Bloccare realmente i tag, non limitarsi a impostare `denied` su tag già caricati. Consent Mode non è una CMP e non governa Meta.
- **GA4 → Statistiche; Google Ads e Meta Pixel → Marketing**, con preferenze per ciascun servizio. Non attivare Ads/Meta accettando soltanto GA4. Verificare le destinazioni del Google tag condiviso, i collegamenti GA4–Ads e l'assenza di attivazioni incrociate.
- Configurare coerentemente `analytics_storage`, `ad_storage`, `ad_user_data` e `ad_personalization`. Personalizzazione, remarketing, Google Signals, conversioni avanzate, matching avanzato e integrazioni server non sono implicitamente autorizzati: definire prima le funzioni effettive.
- Prevedere accettazione e rifiuto con pari evidenza e accessibilità, scelta granulare, riapertura permanente delle preferenze e revoca; memorizzare le scelte e la versione dell'informativa con una retention definita. Non riproporre il banner a ogni visita o in modo insistente: rispettare le condizioni di riproposizione delle linee guida del Garante. Non promettere cancellazioni di cookie terzi non controllabili.
- Il preventivo usa una base precontrattuale ai sensi dell'art. 6(1)(b) **ove applicabile**, non il consenso marketing. Valutare separatamente i referenti aziendali. Allineare la checkbox e la registrazione attuali («consenso privacy») alla sola presa visione e alla finalità effettiva. **[CONFERMARE EVENTUALI NEWSLETTER O CONTATTI PROMOZIONALI, NON PREVISTI DAL MODULO ATTUALE]**.
- Non inviare a GA4/Ads/Meta nome, email, telefono, testo libero o dati del preventivo dentro URL, eventi o parametri. Definire gli eventi ammessi prima di implementare le conversioni.

## Dati ancora necessari

1. Identità legale e indirizzo/recapito del titolare; eventuale rappresentante e DPO, senza presumere che non esistano. Gli indirizzi Milano/Rapallo sono operativi. P.IVA nota: `09182350968`.
2. Dominio finale: nel tema compare `https://gfamarketing.com/`, ma non costituisce conferma delle impostazioni di produzione.
3. Hosting, email, eventuale CRM, plugin/log/backup: soggetti, ruoli, sedi di trattamento e tempi/criteri di conservazione effettivi.
4. ID GA4, Google Ads e Meta, eventuale contenitore GTM, eventi e conversioni, collegamenti fra servizi e impostazioni delle piattaforme. Gli ID servono alla configurazione; non occorre esporli automaticamente nelle informative.
5. Soggetti contrattuali di Google/Meta, informative, destinatari e trasferimenti: verificare paesi e meccanismi effettivi senza presumere adeguatezza, clausole o localizzazione UE.
6. CMP scelta, integrazioni, inventario cookie/storage dopo configurazione, durata delle preferenze e delle prove del consenso, comportamento alla revoca.

## Verifica prima del rilascio

1. Completare i dati e validare le basi/finalità, il testo delle pagine e il flusso del modulo. Risolvere la validazione server incompleta della zona e valutare protezioni antiabuso; provare ricezione email reale.
2. Configurare CMP e servizi in staging, inizialmente bloccati. Nessun tag di produzione soltanto per compilare queste bozze.
3. Controllare rete e storage con profilo pulito: nessuna scelta, rifiuto, GA4 solo, Ads solo, Meta solo, combinazioni e revoca; ripetere dopo ricarica, navigazione e cambio preferenze. Il test deve coprire anche eventuali invii server.
4. Inventariare nomi, domini, finalità e durate realmente osservati; distinguerli dalla retention dei dati nelle piattaforme. Verificare anche cookie/sessioni WordPress e plugin pertinenti ai visitatori.
5. Completare le informative, rimuovere avvisi e segnaposti soltanto dopo approvazione, pubblicare/collegare le pagine e ricontrollare il comportamento sul dominio finale. Conservare evidenze di configurazione e test.

## Fonti ufficiali consultate

- [Garante — Doveri: come trattare correttamente i dati, doc. 8981258](https://www.garanteprivacy.it/web/guest/home/docweb/-/docweb-display/docweb/8981258): principi, basi giuridiche e informazioni da fornire.
- [Garante — Linee guida cookie e altri strumenti di tracciamento, doc. 9677876](https://www.garanteprivacy.it/home/docweb/-/docweb-display/docweb/9677876): scelte preventive e informative sui tracciamenti.
- [Google — Consent Mode](https://developers.google.com/tag-platform/security/concepts/consent-mode): distinzione Basic/Advanced e segnali di consenso.
- [Google — EU User Consent Policy](https://www.google.com/about/company/user-consent-policy/): informazione sui destinatari, documentazione e revoca del consenso.
- [GDPR, testo ufficiale EUR-Lex](https://eur-lex.europa.eu/legal-content/IT/ALL/?uri=CELEX%3A32016R0679): in particolare artt. 6, 13 e 15–22.

Consultate il 30 ottobre 2026. Le fonti definiscono il quadro e i comportamenti tecnici; non dimostrano la configurazione del sito né completano i dati mancanti.
