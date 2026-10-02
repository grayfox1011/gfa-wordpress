# GFA Percorso — tema WordPress su misura

Tema classico (PHP) per il nuovo sito GFA. Volantinaggio al centro, controllo dimostrabile,
promozione eventi, lavori svolti, zone servite e preventivo guidato. Nessuna dipendenza esterna:
font e GSAP sono inclusi nel tema (niente invio di dati a Google Fonts o CDN).

## Installazione
1. Comprimi la cartella `gfa-theme` (o usa lo zip) e caricala da Aspetto → Temi → Aggiungi → Carica.
2. Attiva il tema. Vai in Impostazioni → Permalink e salva una volta.
3. Aspetto → Personalizza → **Dati GFA**: compila solo i dati confermati. I campi vuoti
   compaiono sul sito come segnaposto arancioni ("da fornire").

## Pagine modificabili con Gutenberg
Ogni pagina è fatta di **modelli a blocchi** (Sezioni GFA): testi, titoli, pulsanti, elenchi, tabelle
prezzi, domande frequenti e foto si modificano direttamente nell'editor, come in qualsiasi pagina.

- **Aggiungere una sezione:** "+" → Modelli → categoria *Sezioni GFA* (32 sezioni).
- **Ricominciare una pagina da capo:** "+" → Modelli → *Pagine GFA*.
- **Sostituire una foto segnaposto:** clic sull'immagine → *Sostituisci*; poi cancella la didascalia "Da fornire".
- **Segnare un dato da confermare:** seleziona il testo → menu formato → *Da fornire* (evidenziazione arancione).
- **Evidenziatore giallo** (come "verificare." in homepage): menu formato → *Evidenziatore giallo*.
- **Pulsanti:** stili *Giallo GFA*, *Blu GFA*, *Bordo* nel pannello del pulsante.
- **A capo nei titoli** (Maiusc+Invio): in homepage ogni riga del titolo entra con la sua animazione.
- **Pagina Lavori svolti** (`/lavori/`): è la pagina *Lavori svolti* in Pagine, fatta di sezioni
  (apertura, tutti i lavori, come leggere una scheda, controllo, preventivo) e modificabile come le
  altre; il blocco *GFA · Tutti i lavori* mette al suo posto le schede, 12 per pagina con i numeri
  di pagina: griglia sul computer, slider con le frecce sotto i 1024 px, così con molti lavori la
  pagina non diventa lunghissima sul telefono. Le schede si aggiungono da Lavori svolti → Aggiungi lavoro.
- **Pagina nuova:** Pagine → Aggiungi propone i modelli *Pagine GFA*. Una pagina che comincia con una
  sezione GFA si vede a tutta larghezza anche con il template predefinito; il titolo scritto
  nell'editor serve al menu e alla scheda del browser e nelle pagine a sezioni non compare sul sito.
- **Mappa d'esempio del report** (sezione *Controllo*): è un blocco HTML e si modifica solo come
  codice; per il report vero sostituiscila con un blocco Immagine. Il tema conserva i disegni SVG
  semplici anche dove WordPress filtra l'HTML (multisito, `DISALLOW_UNFILTERED_HTML`), e rimette
  la mappa nelle pagine salvate quando il filtro la toglieva lasciando solo le scritte.

Blocchi automatici (categoria *GFA*), che leggono i dati invece di scriverli a mano:
Numeri, Lavori svolti, Tutti i lavori, Elenco sedi, Mappa sedi, Modulo preventivo, Recapiti,
Dati societari. Nell'editor l'anteprima di questi blocchi non è cliccabile: un clic seleziona il
blocco invece di aprire i link delle schede o scrivere nel modulo.
Header e footer leggono i dati da Aspetto → Personalizza → Dati GFA.
- **Logo:** Personalizza → Identità del sito. Nell'header è alto 38 px al posto del riquadro giallo
  "GFA"; un logo largo (almeno il doppio dell'altezza) contiene già il nome, quindi il testo
  "GFA · Volantinaggio e distribuzione" accanto non compare.
- **Menu del footer:** la colonna "Servizi" usa il menu assegnato alla posizione *Menu footer*
  (Aspetto → Menu); senza menu mostra l'elenco predefinito.

### Cosa non si modifica dall'editor a blocchi
Queste parti stanno nel tema o leggono dati inseriti altrove:
- **Header:** sottotitolo "Volantinaggio e distribuzione" e pulsante "Chiedi un preventivo" (tema);
  voci dal menu principale, logo da Identità del sito, nome da Dati GFA.
- **Footer:** frase "Volantinaggio, stampa e promozione sul territorio.", titoli delle colonne e link
  "Apri una sede GFA" (tema); sedi, recapiti e dati societari da Dati GFA.
- **Modulo preventivo:** domande, scelte e messaggi (tema); destinatario da Dati GFA. In fondo a
  pagine standard, articoli, lavori e zone il tema aggiunge la sezione *Preventivo* com'è nel tema:
  per cambiarne i testi in una pagina, inserisci lì la sezione *Preventivo* e modificala.
- **Blocchi automatici:** si spostano e si tolgono come gli altri blocchi, i testi vengono dai dati.
  *Numeri*: anno, sedi e copie da Dati GFA, "3 servizi" fisso; *Elenco sedi* e *Mappa sedi*: Dati
  GFA → Sedi, posizioni sulla mappa nel tema; *Lavori svolti* e *Tutti i lavori*: i lavori
  pubblicati, e finché non ce n'è nessuno quattro esempi scritti nel tema; *Recapiti* e *Dati
  societari*: Dati GFA.
  La nota "Confermare per ogni sede" sotto l'elenco sparisce quando ogni sede ha la sua zona pubblicata.
- **Pagine create dal tema:** archivio Zone servite (`/zone/`), pagina 404 e risultati della ricerca.
- **Zone e lavori:** testo, riassunto e immagine in evidenza dall'editor; indirizzo, telefono,
  comuni e campi della scheda nei riquadri sotto l'editor; etichette ("Sede", "Comuni serviti",
  "Esigenza"…) nel tema.

### Configurazione del sito (Aspetto → Configura sito GFA)
La configurazione (pagine mancanti, pagine vuote riempite, homepage statica sulla pagina "Home",
menu, zone in bozza, permalink) parte da sola:
- a ogni attivazione del tema;
- alla prima apertura della bacheca dopo aver caricato una nuova versione dello zip, anche sopra
  il tema già attivo.

Non sovrascrive pagine già scritte. Il pulsante in Aspetto → Configura sito GFA la rilancia a mano;
con la casella "Riporta anche le pagine GFA già scritte ai modelli più recenti" aggiorna i testi delle
pagine dopo un aggiornamento del tema (il contenuto precedente resta nelle revisioni);
finché la homepage non è impostata, la bacheca mostra un avviso con il link.

### Creazione automatica all'attivazione
Il tema crea le pagine mancanti già riempite con i modelli, con il template *Pagina a sezioni GFA*,
imposta la homepage statica, crea il menu principale e una zona in bozza per ogni sede.
Non tocca pagine o menu che esistono già: se una pagina con lo stesso slug c'è già (es. `contatti`),
aprila e inserisci il modello *Pagina Contatti* (si vede a tutta larghezza anche senza cambiare template).

| Pagina | Slug |
| --- | --- |
| Home (homepage statica) | `home` |
| Volantinaggio | `volantinaggio` |
| Stampa e grafica | `stampa-e-grafica` |
| Promozione eventi | `promozione-eventi` |
| Chi siamo | `chi-siamo` |
| Franchising | `franchising` |
| Contatti | `contatti` |
| Lavori svolti (mostrata in `/lavori/`) | `lavori` |
| Cookie policy | `cookie-policy` |

Contenuti dedicati nel menu di amministrazione:
- **Lavori svolti** → `/lavori/`: campi servizio, esigenza, zona e periodo, attività, prova.
  La scheda di un lavoro mostra solo i campi compilati, il link a tutti i lavori e gli altri lavori.
- **Zone servite** → `/zone/` e `/volantinaggio/<città>/`: campi indirizzo, telefono, comuni.
  Il titolo della zona è il nome della sede (es. "Rapallo"; maiuscole e accenti non contano).
  Nell'elenco sedi una sede compare con il link solo quando la sua zona è pubblicata: le zone in
  bozza darebbero "pagina non trovata".

### Aggiungere una sede
1. Personalizza → Dati GFA → *Sedi*: aggiungi il nome in fondo all'elenco, separato da una virgola,
   e pubblica. Subito la sede compare nel footer, nell'elenco sedi (senza link), nel numero delle
   sedi (blocco Numeri e pagina Zone servite) e sulla mappa; in Zone servite nasce la sua zona in
   bozza.
2. Zone servite → apri la nuova zona: testo, riassunto, indirizzo, telefono e comuni, poi
   *Pubblica*. Da quel momento l'elenco sedi porta alla sua pagina.
3. A mano, perché sono testi scritti: il titolo "Otto sedi, dal Ponente alla pianura." nelle pagine
   con la sezione Zone servite (Home, Chi siamo, Franchising, Contatti) e l'elenco delle città
   nell'apertura della pagina Volantinaggio.

La mappa conosce i capoluoghi di provincia del Nord e i centri liguri vicini alle sedi (54 città,
in `inc/map.php`): una sede in un'altra città resta nell'elenco ma non compare sulla mappa finché
non si aggiunge una riga con le sue coordinate. Le etichette si spostano da sole per non
sovrapporsi.

### Redirect dai vecchi indirizzi
Già inclusi nel tema (301, solo se il vecchio indirizzo non esiste più): tutte le pagine
`/servizi/...`, `/promozione/`, `/portfoglio/` e l'articolo "Ciao mondo!".

## Modulo preventivo
- Invio via `admin-post.php`, con nonce, campo esca anti-spam e consenso privacy obbligatorio.
- Compare da solo in fondo alle pagine standard, agli articoli, ai lavori e alle zone, ma non
  nella privacy policy, nella cookie policy e nelle pagine che lo contengono già.
- Finché l'informativa privacy non è pubblicata (Impostazioni → Privacy), al posto del link il
  consenso mostra il segnaposto arancione "informativa privacy da pubblicare".
- Premendo Invio nei primi due passi si va al passo successivo: la richiesta parte solo
  dall'ultimo, con nome, email e consenso.
- Arriva all'email "richieste di preventivo" (Dati GFA), altrimenti all'email dell'amministratore.
- Installare un plugin SMTP (es. WP Mail SMTP) e fare una prova reale di invio e ricezione.

## Da fare prima della pubblicazione
- Dati societari corretti (P.IVA e titolare del trattamento: oggi il sito mostra la P.IVA di un'altra attività).
- Informativa privacy GDPR e banner cookie conforme (con Consent Mode v2 se si usa Google Ads).
- Foto reali senza dati personali leggibili (nomi sulle cassette oscurati) e senza logo sovrapposto.
- Eliminare l'articolo "Ciao mondo!".

## Struttura
```
style.css, functions.php
inc/      helpers, setup, customizer, post-types, quote-form, blocks (blocchi dinamici), map (mappa sedi), redirects, starter-content
patterns/ 32 sezioni a blocchi (generate da tools/genera-pattern.py)
parts/    markup dei blocchi dinamici (lavori, tutti i lavori, sedi, modulo, recapiti)
theme.json palette GFA e impostazioni editor
assets/   css/main.css, css/editor.css, js/main.js, js/editor.js, img/foto-da-fornire.svg,
          js/vendor (GSAP 3.13 con ScrollTrigger e SplitText, Lenis 1.3.11), fonts (OFL; Public Sans
          è variabile: un solo file per normale e semigrassetto)
```
Animazioni (GSAP 3.13 + ScrollTrigger + SplitText, Lenis 1.3 per lo scroll morbido, tutto incluso nel tema):
- schermata di caricamento con il logo GFA, solo alla prima pagina visitata in una sessione;
  se JavaScript non parte sparisce comunque dopo 6 secondi;
- titolo della homepage che sale riga per riga e evidenziatore giallo che si disegna;
- titoli di sezione divisi in righe, card che entrano a gruppi, percorso GPS che si disegna,
  linea dei passi legata allo scroll, punti delle sedi, foto che si scoprono;
- lavori svolti: sul computer la fascia si fissa e scorre di lato con la pagina; dove non si fissa
  (telefono, tablet, schermi bassi) è uno slider da trascinare col dito, con contatore, barra e
  frecce, e la prima volta che compare si sposta un poco di lato per far vedere che scorre; lo
  stesso slider sostituisce la griglia della pagina Lavori svolti sotto i 1024 px;
- passaggio tra le pagine con GSAP: una dissolvenza a schermo intero (header compreso) copre la pagina,
  si cambia pagina e il velo si toglie quando la nuova pagina è pronta; i link a file (PDF, documenti,
  immagini…) aprono il file senza dissolvenza e, se una pagina non arriva, dopo 4 secondi il velo si
  toglie comunque;
- le pagine nuove e le ricariche partono dall'alto; con "indietro" e "avanti" si torna al punto in cui
  si era; i link a una sezione (#preventivo) arrivano alla sezione giusta e "Chiedi un preventivo"
  dell'header scorre al modulo quando la pagina lo contiene già; il logo apre la home con il preload;
- gli elementi animati partono già nascosti, così non compaiono e spariscono prima di animarsi;
- tutto si spegne con "riduci animazioni" del sistema operativo: la pagina resta completa e ferma.

Il sito è sempre chiaro, come il prototipo approvato: la modalità scura del sistema non cambia i colori.
