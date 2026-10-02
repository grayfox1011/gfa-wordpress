# GFA — nuovo sito

- `gfa-theme/` — tema WordPress "GFA Percorso", pagine modificabili con Gutenberg (istruzioni in `gfa-theme/README.md`).
- `prototipo/` — prototipo statico da mostrare prima dell'installazione (`gfa-prototipo.html` + `pagine/`).
- `tools/genera-pattern.py` — rigenera i modelli a blocchi dopo aver cambiato i testi di partenza.
- `tools/build-prototipo.py` — rigenera il prototipo da un WordPress con il tema attivo:
  `python3 tools/build-prototipo.py http://localhost:8080`

Verificato su WordPress 7.1.2 (versione 0.4.0), seguendo i criteri del report di verifica desktop/mobile:
- titoli e hero sempre visibili dopo caricamento, 5 ridimensionamenti desktop/mobile, scroll completo,
  librerie bloccate e movimento ridotto; nessuna riga lasciata divisa da SplitText;
- portfolio fissato solo se il blocco intero entra nello schermo (1366×768 e 1024×600: scorrimento normale);
- menu mobile: voci separate (min 48 px), Escape e CTA chiudono, scorrimento interno a 640×360;
- nessuno scorrimento laterale a 320, 390, 421 e 768 px su tutte le pagine; prezzi a schede su telefono;
- modulo: errore del server visibile, funziona anche senza JavaScript, un solo invio, email ricevuta.

Versione 0.6.0 (frontend, dal report del 1 ottobre 2026) provata su WordPress 7.1.2 in locale:
40 controlli nel browser su computer e telefono (modulo, scroll con "indietro", dissolvenza, logo,
zone, menu, focus, modalità scura), nessuna violazione axe-core su 16 pagine da telefono e da
computer, nessun errore JavaScript.

Versione 0.6.1 (scritte e Gutenberg, 2 ottobre 2026), stesse prove più:
- editor a blocchi: le 9 pagine si aprono senza blocchi non validi, i 36 modelli inseriti come
  dall'inseritore sono validi, una modifica scritta nell'editor e salvata compare sul sito;
- mappa del report conservata salvando con `DISALLOW_UNFILTERED_HTML` (prima restavano solo le scritte);
- 12 pagine a 360, 390, 768, 1024, 1280 e 1440 px: nessuna parola spezzata, nessun grassetto o
  corsivo finto, nessun testo fuori dal suo riquadro; marchio e menu nella larghezza del contenuto
  da 1181 px in su.

Versione 0.6.2 (telefono e pagina Lavori svolti, 2 ottobre 2026), stesse prove più:
- slider dei lavori con gesti touch veri su telefono (contatore, frecce, spinta iniziale, fine
  corsa), con movimento ridotto, e fascia fissata da GSAP solo dove entra nello schermo;
- pagina Lavori svolti con 13 lavori di prova: griglia, numeri di pagina, scheda con "Altri
  lavori"; nessuna violazione axe-core; nell'editor 9 pagine e 40 modelli validi e un clic sui
  blocchi automatici seleziona il blocco senza aprire il sito.

Versione 0.6.3 (2 ottobre 2026): slider anche nella pagina Lavori svolti sotto i 1024 px; sedi
aggiunte in autonomia (zona in bozza creata salvando Personalizza, mappa con 54 città del Nord,
nomi confrontati senza maiuscole e accenti), provato aggiungendo e togliendo una sede da
Personalizza; stesse prove della 0.6.2.

Versione 0.6.4 (2 ottobre 2026): le righe delle tabelle (prezzi, dati societari) compaiono con
una dissolvenza invece di salire, e fascia dei lavori e tabelle scorrono solo di lato: durante
l'animazione non compare più per un attimo una barra di scorrimento. Stesse prove della 0.6.3.

Versione 0.6.5 (2 ottobre 2026): tabelle dei prezzi e dei dati societari senza riquadro
scorrevole (entrano a tutte le larghezze, da 320 px in su), quindi nessuna barra di scorrimento
in nessun browser anche con file vecchi in cache. Confronto 0.6.3/0.6.4/0.6.5 con le barre di
scorrimento visibili come su Windows e campioni ogni 50 ms durante l'animazione.

Il prototipo statico non è stato rigenerato: mostra ancora la 0.5.

Tutti i dati aziendali non confermati compaiono come segnaposto arancioni "da fornire".
