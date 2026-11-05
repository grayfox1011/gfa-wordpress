# GFA Leads

Prima versione separata dal sito WordPress: database D1, gestione contatti e storico delle attività. Il sito e la posta Keliweb non vengono modificati da questo progetto.

## Stato al 2 novembre 2026

- Database `gfa-leads-db` creato nell'account Cloudflare dedicato, con giurisdizione `eu`.
- Tabelle `leads` e `lead_activity` create e verificate vuote sulla console D1.
- Pannello completo e collaudato in locale: inserimento manuale, ricerca, filtri, stato, note e CSV filtrato (massimo 1.000 righe).
- 23 test automatici superati, bundle ESM compilati.
- **Worker non ancora pubblicato.** Occorre completare l'attivazione Cloudflare Zero Trust Free / Access. Il tentativo di pubblicare il template provvisorio è stato bloccato dal controllo automatico prima della creazione del Worker; non è stato ripetuto.
- Il modulo WordPress e le notifiche email non sono ancora collegati. Non sono stati inviati messaggi, creati iscritti a newsletter o trasferiti contatti reali.

## Componenti

`src/index.mjs` è il pannello privato. Cloudflare Access deve proteggere **All traffic**, con la policy **Cloudflare account** dell'account dedicato. Il codice richiede `ctx.access` anche se la protezione all'esterno viene disattivata: nessun dato è accessibile soltanto inviando un header email. `ACCESS_AUD` e `ADMIN_EMAILS` consentono restrizioni aggiuntive. L'interfaccia è servita dal Worker stesso, senza Static Assets binding, per mantenere il contesto Access.

`src/capture.mjs` prepara un secondo Worker per l'invio server-to-server da WordPress. Espone soltanto `POST /v1/leads`, richiede il secret `INGEST_SECRET` di almeno 32 caratteri tramite `X-GFA-Key` e rifiuta le richieste quando il secret manca. Non espone letture dei contatti, non abilita CORS e non va chiamato direttamente dal browser del visitatore. Non è ancora pubblicato o collegato a WordPress.

Entrambi condividono il binding D1 `DB`. I dati vengono inseriti prima di dichiarare una richiesta ricevuta. `submission_id` evita duplicati; un ID riusato con contenuto diverso viene rifiutato. Le modifiche richiedono la versione del record per evitare sovrascritture concorrenti. I consensi Google/Meta e l'eventuale consenso newsletter sono distinti; il pannello manuale non iscrive nessuno a campagne marketing. Le notifiche risultano esplicitamente `not_configured`.

## Sviluppo e verifica

Richiede Node.js con `node:sqlite` (collaudo effettuato con Node 26), npm e Wrangler.

```sh
npm install
npm test
npm run build
```

I bundle generati in `dist/` sono ignorati da Git. Il pacchetto amministrativo è `dist/worker.mjs`; quello di ingestione è `dist/capture.mjs`.

Per un'anteprima isolata senza credenziali:

```sh
node scripts/preview.mjs
```

La preview usa soltanto `127.0.0.1:8787`, un database temporaneo in memoria e un'identità fittizia. **Non distribuire questo server Node.** In produzione l'entry point è `src/index.mjs` e l'identità proviene da Cloudflare Access.

In alternativa, `wrangler.local.jsonc` configura lo sviluppo locale Workers con `access.dev`. Le migrazioni locali si applicano con:

```sh
npx wrangler d1 migrations apply gfa-leads-local --local --config wrangler.local.jsonc
npm run dev
```

## Completamento del deploy

1. Nell'account dedicato completare l'attivazione **Zero Trust Free**. Al momento della preparazione la procedura ha richiesto la schermata di pagamento; nessun dato è stato inserito e nessuna sottoscrizione è stata confermata dall'agente.
2. Autenticare Wrangler con l'account dedicato oppure usare il dashboard. Non usare le credenziali dell'account personale precedente. Non salvare token nei sorgenti.
3. Pubblicare `gfa-leads-admin` protetto con Access su **All traffic**, policy **Cloudflare account**. L'indirizzo proposto dal dashboard è `gfa-leads-admin.gfaledas.workers.dev`, ma non è ancora attivo.
4. Collegare `DB` al database indicato in `wrangler.jsonc`. Lo schema è già stato applicato alla nuova istanza D1. La migrazione usa `IF NOT EXISTS`, ma non rieseguire modifiche a schema senza verifica.
5. Verificare accesso anonimo negato, login autorizzato, un contatto fittizio identificato come test e gli aggiornamenti sul D1 reale. Verificare anche l'identità effettiva con `ctx.access` prima di aggiungere contatti reali.
6. In una fase successiva configurare il secret di ingestione lato server, integrare il modulo WordPress e collegare il servizio email. Le conversioni Ads devono essere emesse dopo la conferma del salvataggio, mantenendo i controlli di consenso esistenti.

La verifica locale non sostituisce il collaudo Cloudflare Access + D1 remoto, ancora da completare. La verifica browser desktop è stata eseguita; quella mobile è rimasta incompleta per l'interruzione della connessione al browser.

## Riferimenti

- [Cloudflare Access per Workers e ctx.access](https://developers.cloudflare.com/workers/configuration/cloudflare-access/)
- [Workers.dev](https://developers.cloudflare.com/workers/configuration/routing/workers-dev/)
- [Configurazione di Zero Trust](https://developers.cloudflare.com/cloudflare-one/setup/)
- [Database D1](https://developers.cloudflare.com/d1/get-started/)
