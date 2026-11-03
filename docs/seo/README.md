# SEO GFA: configurazione e passaggio in produzione

La configurazione del tema 0.5.20 prepara titoli, descrizioni, anteprime social e dati strutturati per GFA Comunicazione e per le sedi di Milano e Rapallo. Non garantisce posizionamenti, traffico o la visualizzazione di risultati avanzati su Google.

## Stato del sito di prova

Il sito Flux deve mantenere disattivata l'indicizzazione in **Impostazioni → Lettura → Scoraggia i motori di ricerca**. Il modulo SEO rispetta questa impostazione e non la modifica. La sitemap WordPress non viene pubblicata quando l'indicizzazione del sito è disattivata: è il comportamento previsto sul sito di prova.

`noindex` non protegge informazioni riservate: il sito di prova resta visitabile da chi ne conosce l'indirizzo. Se serve limitarne l'accesso, occorre una protezione distinta a livello di hosting.

## Funzioni del tema

- Titoli e descrizioni specifici per homepage, servizi, contatti, Chi siamo, referenze, sedi e pagine locali Milano/Rapallo.
- Campi **SEO GFA** nell'editor per personalizzare titolo e descrizione senza cambiare il titolo visibile; opzione per escludere una singola pagina dai motori e dalla sitemap.
- Canonical generati usando gli indirizzi di WordPress; nessun dominio Flux incorporato nei metadati. Per i singolari si mantiene il canonical del core.
- Metadati Open Graph e Twitter Card. L'immagine in evidenza della pagina ha priorità; in sua assenza viene usato il logo del sito.
- Dati strutturati Organization, WebSite, WebPage e, dove pertinenti, LocalBusiness e Service. Recapiti e sedi devono sempre corrispondere ai dati visibili nella pagina.
- Sitemap standard WordPress con le sole URL pubbliche ammesse e un provider aggiuntivo per `/lavori/` e `/zone/`. Autori e tassonomie non vengono proposti nella sitemap aziendale.
- Il modulo cede la gestione dei metadati ai plugin SEO riconosciuti, per evitare emissioni duplicate. L'installazione successiva di un plugin richiede la verifica e il trasferimento delle personalizzazioni GFA: non è una migrazione automatica dei campi.

La pagina Franchising resta una bozza e non deve essere ripubblicata. I modelli del tema non vanno rigenerati sulle pagine già curate: **Configura sito GFA → ripristino dei modelli** può sovrascrivere i testi modificati in WordPress.

I file `home.html`, `chi-siamo.html`, `milano.html` e `rapallo.html` in questa cartella sono copie dei blocchi aggiornati nel database WordPress. Non sono importati automaticamente dal tema e non sostituiscono un backup del database. Anche gli estratti delle due pagine locali sono stati aggiornati nell'editor. La pagina di esempio (ID 2) e l'articolo di esempio (ID 1) sono stati riportati in bozza.

## Prima del lancio sul dominio definitivo

- [ ] Confermare il dominio definitivo e la variante unica da usare (`www` oppure senza `www`). Il dominio finale non è stato fornito durante questa preparazione.
- [ ] Preparare un backup verificabile del sito e del database prima della migrazione.
- [ ] Configurare DNS, HTTPS e redirect verso la variante scelta; aggiornare Indirizzo WordPress e Indirizzo sito. Migrare le URL salvate nel database con uno strumento che gestisca correttamente i dati serializzati.
- [ ] Verificare che pagine, immagini, collegamenti interni, canonical, `og:url`, schema e sitemap non contengano più l'hostname di prova.
- [ ] Raccogliere le vecchie URL dal sito precedente, dalle sitemap e da Search Console. Confrontarle con `gfa-theme/inc/redirects.php` e associare ogni URL importante alla destinazione equivalente. La mappa attuale è un punto di partenza, non un inventario completo verificato. Non reindirizzare indiscriminatamente tutte le pagine rimosse alla home.
- [ ] Verificare che Milano e Rapallo riportino gli stessi indirizzi e recapiti nei testi, nei campi della sede, nei Dati GFA e nei dati strutturati. Non aggiungere altre sedi, orari, recensioni, statistiche o coperture non confermate.
- [ ] Rileggere i contenuti pubblicati ed eliminare segnaposto, pagine dimostrative e promesse di GPS, controllo, foto o report non offerte dall'azienda. Le pagine locali devono contenere informazioni utili e specifiche della sede.
- [ ] Verificare modulo preventivo e consegna effettiva delle richieste, collegamento WhatsApp, privacy e cookie. L'identità e la configurazione dei provider di hosting, posta e futuro tracciamento restano verifiche operative separate: la preparazione SEO non le certifica.
- [ ] Controllare le pagine principali da telefono e desktop, anche con JavaScript disabilitato. Misurare prestazioni e stabilità del layout sul dominio definitivo prima di intervenire su cache o script.
- [ ] Disattivare **Scoraggia i motori di ricerca** solo sul dominio di produzione, quando contenuti e configurazione sono pronti. Mantenere il sito di prova escluso dall'indicizzazione o protetto.
- [ ] Controllare sul dominio definitivo: risposta HTTP 200 delle pagine pubbliche, assenza di `noindex` e `X-Robots-Tag: noindex`, `robots.txt` compatibile con la scansione, un canonical per pagina, sitemap raggiungibile in `/wp-sitemap.xml` e nessuna bozza o pagina esclusa al suo interno.
- [ ] Validare i dati strutturati e controllare un campione di pagine nel Test dei risultati avanzati e in Controllo URL di Search Console. Alcuni tipi schema descrivono il contenuto ma non producono necessariamente risultati avanzati.

## Attività esterne ancora da eseguire

- [ ] Verificare la proprietà del dominio in Google Search Console mediante un account autorizzato. Credenziali, accesso DNS e proprietà Search Console non sono stati configurati da questo intervento.
- [ ] Inviare la sitemap del dominio definitivo e controllare indicizzazione, canonical scelti da Google ed eventuali errori. La pubblicazione e l'invio della sitemap non garantiscono l'indicizzazione immediata.
- [ ] Verificare le schede Google Business Profile esistenti per le due sedi, evitando duplicati e mantenendo nome, indirizzo, telefono e sito coerenti. Accesso e verifica delle schede non risultano completati in questo intervento.
- [ ] Rivedere dopo il lancio le vecchie URL che restituiscono 404, i redirect e le pagine escluse. Correggere problemi concreti emersi dai dati, senza promettere una posizione nei risultati.

Google Analytics, Google Ads e Meta Pixel non sono prerequisiti per l'indicizzazione. La loro eventuale attivazione richiede ID, gestione del consenso e verifica delle informative; non è implicita nella configurazione SEO.

## Riferimenti ufficiali

- [Google: Search Essentials](https://developers.google.com/search/docs/essentials)
- [Google: guida introduttiva alla SEO](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
- [Google: descrizioni e snippet](https://developers.google.com/search/docs/appearance/snippet)
- [Google: canonical e URL duplicate](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)
- [Google: esclusione dall'indicizzazione con noindex](https://developers.google.com/search/docs/crawling-indexing/block-indexing)
- [Google: creazione e invio delle sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
- [Google: dati strutturati LocalBusiness](https://developers.google.com/search/docs/appearance/structured-data/local-business)
- [Google: dati strutturati Organization](https://developers.google.com/search/docs/appearance/structured-data/organization)
- [WordPress: sitemap e siti non pubblici](https://developer.wordpress.org/reference/hooks/wp_sitemaps_enabled/)
- [Open Graph Protocol](https://ogp.me/)

## Verifiche del 2 novembre 2026

- Tema 0.5.20 installato correttamente; superati 18 controlli PHP del modulo SEO e il lint di 8 file PHP.
- Controllate 12 pagine live: homepage, tre servizi, Chi siamo, Contatti, due archivi, Milano, Rapallo e le due informative. Ciascuna presenta titolo e descrizione distinti, un H1, un canonical e JSON-LD decodificabile; il `noindex` del sito di prova resta presente.
- Verificata una URL con parametri UTM: canonical e `og:url` non includono i parametri di campagna.
- Nessuna eccedenza orizzontale rilevata nelle pagine Milano e Rapallo a 390 × 844 pixel.
- La sitemap XML mostra una pagina non trovata, coerentemente con l'indicizzazione disattivata sul sito di prova.
- Per la pagina Tasche Piene (ID 91), l'opzione SEO di esclusione dall'indicizzazione è stata selezionata, salvata e verificata dopo il ricaricamento. Il JSON-LD è assente nella pagina pubblica.

Questi controlli non includono una verifica HTTP da visitatore anonimo, Test dei risultati avanzati, Lighthouse o Search Console. Le verifiche sul dominio definitivo elencate sopra restano da eseguire al lancio.

