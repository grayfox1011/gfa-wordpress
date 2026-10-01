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
computer, nessun errore JavaScript. Il prototipo statico non è stato rigenerato: mostra ancora la 0.5.

Tutti i dati aziendali non confermati compaiono come segnaposto arancioni "da fornire".
