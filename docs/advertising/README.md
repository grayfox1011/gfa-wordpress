# Predisposizione Google Ads e Meta

Aggiornamento: 2 novembre 2026. Google Ads e Meta Pixel sono predisposti nel tema, **disabilitati per impostazione iniziale e senza identificativi configurati**. GA4, Google Tag Manager, conversioni avanzate, corrispondenza avanzata e Conversions API non sono inclusi. La predisposizione non attiva campagne e non modifica gli account pubblicitari.

Il banner separa Esperienza di navigazione (`functional`), Google Ads (`googleAds`) e Meta Pixel (`metaAds`). Tutte le categorie facoltative partono disattivate. Una categoria pubblicitaria non configurata e abilitata è indisponibile: Accetta tutto non raccoglie consenso per un servizio futuro.

## Configurazione in WordPress

Aprire **Impostazioni → Pubblicità GFA** con un account che dispone di `manage_options`. L'opzione WordPress è `gfa_advertising`.

| Campo | Dato da inserire | Stato iniziale |
| --- | --- | --- |
| Abilita Google Ads dopo il consenso (`google_enabled`) | Spunta soltanto dopo le verifiche sotto | Disattivato |
| ID Google Ads (`google_id`) | ID pubblico nel formato `AW-…` dell'account corretto | Vuoto |
| Label conversione preventivo (`google_label`) | Label dell'azione di conversione per la richiesta di preventivo | Vuoto |
| Abilita Meta Pixel dopo il consenso (`meta_enabled`) | Spunta soltanto dopo le verifiche sotto | Disattivato |
| ID Meta Pixel (`meta_id`) | Identificativo numerico del pixel corretto | Vuoto |

Non inserire snippet JavaScript, password, token API o credenziali. Il salvataggio con ID non validi o incompleti non abilita il relativo servizio. Gli ID possono essere salvati lasciando entrambe le spunte disattivate. Un ID formalmente valido non dimostra che appartenga all'account corretto.

1. Confermare il dominio definitivo e verificare le impostazioni WordPress, canonical e pagine pubbliche. Non abilitare gli account di produzione sul dominio provvisorio.
2. In Google Ads creare o individuare l'azione per la richiesta di preventivo e recuperare ID `AW-…` e label. L'evento usa `send_to: AW-ID/LABEL` e `transaction_id` casuale; non attribuisce un valore monetario inventato. Configurare il conteggio coerentemente con un contatto, senza duplicare la stessa conversione tramite importazione da altre integrazioni.
3. In Meta verificare pixel e dominio dell'account corretto. Controllare in Gestione eventi che corrispondenza avanzata automatica, rilevazione automatica degli eventi e regole Event Setup Tool non aggiungano raccolte o conversioni. L'integrazione del tema disabilita `autoConfig`, non passa dati utente a `init` e non chiama `optinMetaEnabledCapi`.
4. Verificare che plugin, hosting, contenitori GTM, strumenti pubblicitari o altri snippet non carichino tag duplicati o prima del consenso. Controllare le destinazioni collegate al Google tag: non introdurre implicitamente GA4 o altre destinazioni.
5. Completare le informative con fornitori e configurazione effettivi, finalità, trasferimenti, cookie e durate realmente osservati; verificare i rapporti e gli accordi applicabili. La scelta è separata per fornitore e comprende misurazione e personalizzazione pubblicitaria: non riutilizzare consensi relativi alle sole animazioni o alla richiesta di preventivo.
6. Collaudare prima in ambiente di prova, con il consenso delle persone che eseguono il test. L'eventuale invio reale di un modulo genera un'email; i test automatici indicati sotto utilizzano simulazioni. Attivare in produzione soltanto dopo le verifiche su dominio, account, informative, rete e revoca.

## Consenso e caricamento

Google Ads usa **Consent Mode v2 Basic**: nessun caricamento di `gtag.js` né ping Google prima dell'opt-in a Google Ads. Le istruzioni iniziali sono accodate localmente con `ad_storage`, `ad_user_data`, `ad_personalization` e `analytics_storage` a `denied`. Il consenso a Google Ads porta i primi tre a `granted`; `analytics_storage` resta sempre `denied`. `url_passthrough` è disattivato, `ads_data_redaction` è abilitato e le conversioni avanzate sono disattivate. Non è presente un pixel alternativo in `<noscript>`.

Meta carica `fbevents.js` soltanto dopo l'opt-in a Meta Pixel, applica `consent: grant` e `autoConfig: false` prima dell'inizializzazione e invia `PageView`. La conversione esplicita è `Lead`, inviata con `trackSingle` al solo pixel configurato e con `eventID` casuale. Non vengono trasmessi nome, email, telefono o testo del modulo come parametri; questo **non significa assenza di dati personali nel traffico dei provider**: dopo il consenso gli SDK possono ricevere IP, browser, URL della pagina e identificativi dei cookie.

Il controllo si applica alle pagine pubbliche ammesse dal tema. Amministrazione, anteprime, ricerca, 404, allegati, pagine protette e pagine marcate noindex non abilitano i tag. Non vi sono conversioni automatiche sui clic WhatsApp, email o telefono.

`localStorage.gfaConsent` usa il record versione `2` con scelte separate, `updatedAt`, `expiresAt` e `policyVersion`. Quest'ultima contiene `2026-11-02.ads-v1:` e la revisione della configurazione dei fornitori. Scelte vecchie, scadute o incompatibili non autorizzano i tag; modificare flag, ID o label rende necessaria una nuova scelta. La validità prevista è di **sei mesi di calendario** dall'ultimo salvataggio, con gestione della fine del mese. Non è una dichiarazione che ogni consenso debba per legge scadere dopo sei mesi. Il record locale non è un registro centralizzato delle prove del consenso.

## Conversione del preventivo

Il modulo può produrre una conversione solo dopo validazione e `wp_mail()` con esito positivo. L'esito significa che il sistema di invio ha elaborato la richiesta senza errori: **non certifica la consegna dell'email, un cliente acquisito o una vendita**. Errori, honeypot e semplice apertura di `?preventivo=inviato` non devono generare conversioni.

1. Al submit vengono acquisiti i consensi allora presenti. Il server verifica inoltre che il fornitore sia effettivamente configurato e abilitato.
2. Dopo il successo viene creata una ricevuta per i soli fornitori autorizzati. Contiene un UUID, le due idoneità e la scadenza, senza dati del modulo. Il token casuale ha 32 byte; il transient WordPress `gfa_lead_` usa l'hash del token come chiave. Validità: **600 secondi**, con utilizzo singolo e controllo della scadenza. Un transient scaduto può rimanere fisicamente nel database fino alla pulizia prevista da WordPress, ma non è più valido.
3. Il ritorno può contenere `gfa_receipt`. Il riferimento viene eliminato dall'URL del browser prima del caricamento degli SDK; non diventa un parametro pubblicitario. La risposta di conferma non deve essere memorizzata da cache/CDN. Il tema imposta `DONOTCACHEPAGE` e intestazioni no-cache: verificare che infrastruttura e cache le rispettino.
4. L'evento parte soltanto se il fornitore era consentito sia al submit sia all'apertura iniziale della pagina di conferma ed è ancora consentito al momento dell'invio. Se il consenso arriva dopo, la conversione precedente non viene recuperata. Una ricevuta assente, scaduta o già consumata non produce eventi.
5. Google riceve l'UUID come `transaction_id`; Meta come `eventID`. Il tema applica inoltre controlli propri contro gli invii duplicati. Non attribuire a `eventID` da solo una garanzia generale di deduplicazione browser-only.

## Memorie e revoca

| Memoria | Quando viene usata | Durata e rimozione |
| --- | --- | --- |
| `localStorage.gfaConsent` | Ricorda consenso e rifiuto; memoria tecnica | Sei mesi di calendario; controllo di validità del tema |
| `sessionStorage.gfaSeen`, `gfaTrans` | Solo con Esperienza di navigazione | Secondo funzione/sessione; rimozione alla revoca |
| `sessionStorage.gfaAdsSent:google:<UUID>` | Solo con consenso Google Ads e conversione autorizzata | Sessione della scheda; rimozione alla revoca Google |
| `sessionStorage.gfaAdsSent:meta:<UUID>` | Solo con consenso Meta Pixel e conversione autorizzata | Sessione della scheda; rimozione alla revoca Meta |

Il tema non aggiunge un altro cookie proprietario per le ricevute o la deduplicazione. Il ripristino della sessione del browser può ripristinare anche il sessionStorage. Con gli identificativi vuoti e i servizi disattivati non vengono create memorie pubblicitarie.

La revoca blocca i successivi eventi del tema, invia a Google l'aggiornamento `denied` o a Meta `consent: revoke`, elimina le chiavi di sessione pertinenti e tenta di eliminare i cookie accessibili `_gcl_*`, `_gac_*`, `_fbp` e `_fbc` sui percorsi e domini applicabili. Alla revoca di Google tenta inoltre di rimuovere dal localStorage la chiave `_gcl_ls`, documentata da Google per la memorizzazione dei clic tramite Conversion Linker, se presente e tecnicamente accessibile. Questa lista descrive la pulizia prevista, **non un inventario di cookie o memorie attualmente installati né le loro durate**. Cookie HttpOnly o appartenenti a domini terzi non possono essere eliminati da questo codice. Se l'SDK era caricato, la pagina viene ricaricata per avviare un documento senza il fornitore revocato; eliminare il solo elemento script non scaricherebbe l'SDK. La revoca non annulla trasmissioni già avvenute.

## Verifica

Usare i test presenti in `tools/` e i controlli di sintassi appropriati al tema. Registrare separatamente gli esiti effettivamente eseguiti: questa guida non costituisce evidenza di un test live con account configurati.

- Nessuna scelta, rifiuto e sola Esperienza di navigazione: nessuna richiesta a Google Ads o Meta, inclusi ping senza cookie.
- Solo Google Ads, solo Meta e entrambi: caricare esclusivamente i fornitori consentiti. Con servizi disabilitati o ID incompleti nessun SDK deve partire, anche con vecchi valori permissivi nel browser.
- Scelta scaduta, cambio di configurazione, storage bloccato e modifica da altra scheda: comportamento coerente e nessun recupero implicito di un consenso precedente.
- Revoca dopo il caricamento, anche mentre uno script è in download: stop degli eventi, pulizia applicabile e ricarica senza riattivazione del fornitore.
- Conversioni: ricevuta valida, errore, honeypot, URL di successo inventato, scadenza, replay/ricarica e consenso concesso solo dopo il submit. Nessun dato del modulo negli eventi o nell'URL pubblicitario.
- Sul dominio finale controllare richieste di rete, cookie, storage, eventuali tag esterni e account con gli strumenti di verifica dei provider. Completare l'inventario effettivo prima dell'attivazione.

La verifica automatica può usare SDK simulati e `wp_mail` simulato, senza richieste ai provider né email reali. L'invio di eventi reali, l'analisi dell'attribuzione e la ricezione email restano controlli distinti, eseguibili soltanto con account, identificativi e destinatari di prova definiti.

## Verifica eseguita — 2 novembre 2026

- Tema 0.5.21 installato con conferma di WordPress; script del tema serviti con versione 0.5.21. Il sito di prova mantiene `noindex`.
- La vecchia scelta relativa alle sole animazioni non è riutilizzata: viene mostrato il nuovo banner. Nel pannello amministrativo Google Ads e Meta Pixel risultano disattivati, con identificativi e label vuoti.
- I tre selettori partono non selezionati; i due pubblicitari sono disabilitati finché i servizi non sono configurati. Nello stato attuale «Accetta tutto» abilita soltanto Esperienza di navigazione. Verificati rifiuto persistente dopo ricarica, riapertura dal footer e chiusura con Escape.
- Controlli desktop e mobile a 390 × 844 pixel: nessuna eccedenza orizzontale; le azioni in fondo al pannello sono raggiungibili da tastiera.
- Privacy Policy ID 3 e Cookie Policy ID 14 aggiornate e pubblicate con data 2 novembre 2026. Google Analytics 4 è escluso dalla configurazione.
- Una ricevuta inventata composta da 64 zeri non produce una ricevuta utilizzabile; il parametro è rimosso dall'URL. Nessun elemento script degli SDK pubblicitari rilevato nel DOM. Questo controllo non equivale a una scansione completa delle richieste di rete.
- Superati 20 test del consenso, 17 test JavaScript pubblicitari, 11 controlli PHP pubblicitari, 18 regressioni SEO e il lint di 6 file PHP modificati.

I test automatici usano fornitori e invio email simulati. Nessun identificativo è configurato e non sono stati inviati eventi reali agli account pubblicitari né verificati conversioni negli account, attribuzione o recapito effettivo delle email. Questi controlli restano da eseguire con la configurazione definitiva e destinatari di prova concordati.

## Informative e fonti

Le versioni aggiornate per le pagine pubbliche sono [Privacy Policy](../privacy/privacy-policy.html) e [Cookie Policy](../privacy/cookie-policy.html), entrambe in un solo blocco HTML Gutenberg. Usare il template **Pagina a sezioni GFA**; non pubblicare le versioni storiche `.bozza.html`. I documenti in questa cartella descrivono lo stato predisposto e disattivato, non certificano configurazioni future.

- [Google: Consent Mode Basic e Advanced](https://developers.google.com/tag-platform/security/concepts/consent-mode)
- [Google: impostazione e aggiornamento del consenso](https://developers.google.com/tag-platform/security/guides/consent)
- [Google: conversioni con Google tag](https://developers.google.com/tag-platform/devguides/conversions)
- [Google Ads: transaction ID e deduplicazione](https://support.google.com/google-ads/answer/6386790?hl=en)
- [Google: Conversion Linker e localStorage `_gcl_ls`](https://support.google.com/tagmanager/answer/7549390?hl=it)
- [Meta: template ufficiale con API consenso, autoConfig ed eventID](https://github.com/meta/GoogleTagManager-WebTemplate-For-FacebookPixel/blob/main/template.tpl)
- [WordPress: significato del risultato di wp_mail](https://developer.wordpress.org/reference/functions/wp_mail/)
- [Garante: linee guida cookie e altri strumenti di tracciamento](https://www.garanteprivacy.it/home/docweb/-/docweb-display/docweb/9677876)

Fonti consultate il 2 novembre 2026. Le pagine Meta Developers non erano disponibili al recupero; le API citate sono state verificate nel repository ufficiale Meta. Le linee guida del Garante consentono la riproposizione della scelta dopo almeno sei mesi dalla precedente presentazione del banner, oppure prima in caso di cambiamenti significativi o impossibilità di riconoscere la scelta; non impongono una scadenza universale del consenso a sei mesi.
