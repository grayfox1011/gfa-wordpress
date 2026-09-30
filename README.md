# GFA — nuovo sito

- `gfa-theme/` — tema WordPress "GFA Percorso", pagine modificabili con Gutenberg (istruzioni in `gfa-theme/README.md`).
- `prototipo/` — prototipo statico da mostrare prima dell'installazione (`gfa-prototipo.html` + `pagine/`).
- `tools/genera-pattern.py` — rigenera i modelli a blocchi dopo aver cambiato i testi di partenza.
- `tools/build-prototipo.py` — rigenera il prototipo da un WordPress con il tema attivo:
  `python3 tools/build-prototipo.py http://localhost:8080`

Verificato su WordPress 7.1.2: attivazione, creazione di pagine e menu, validità dei blocchi
nell'editor, modulo preventivo (invio e messaggio di conferma), redirect, pagine senza
scorrimento orizzontale a 1366 e 400 px.

Tutti i dati aziendali non confermati compaiono come segnaposto arancioni "da fornire".
