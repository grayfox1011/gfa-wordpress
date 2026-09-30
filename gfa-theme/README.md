# GFA Percorso — tema WordPress su misura

Tema classico (PHP) per il nuovo sito GFA. Volantinaggio al centro, controllo dimostrabile,
promozione eventi, lavori svolti, zone servite e preventivo guidato. Nessuna dipendenza esterna:
font e GSAP sono inclusi nel tema (niente invio di dati a Google Fonts o CDN).

## Installazione
1. Comprimi la cartella `gfa-theme` (o usa lo zip) e caricala da Aspetto → Temi → Aggiungi → Carica.
2. Attiva il tema. Vai in Impostazioni → Permalink e salva una volta.
3. Aspetto → Personalizza → **Dati GFA**: compila solo i dati confermati. I campi vuoti
   compaiono sul sito come segnaposto arancioni ("da fornire").

## Pagine (create in automatico alla prima attivazione)
All'attivazione il tema crea le pagine mancanti, imposta la homepage statica, crea il menu
principale e una zona in bozza per ogni sede. Non sovrascrive pagine o menu che esistono già.

| Pagina | Slug | Layout |
| --- | --- | --- |
| Home | `home` (homepage statica) | `front-page.php` |
| Volantinaggio | `volantinaggio` | `page-volantinaggio.php` |
| Stampa e grafica | `stampa-e-grafica` | `page-stampa-e-grafica.php` |
| Promozione eventi | `promozione-eventi` | `page-promozione-eventi.php` |
| Chi siamo | `chi-siamo` | `page-chi-siamo.php` |
| Franchising | `franchising` | `page-franchising.php` |
| Contatti | `contatti` (stesso indirizzo del vecchio sito) | `page-contatti.php` |
| Cookie policy, Privacy | `cookie-policy`, pagina privacy di WordPress | `page.php` |

Contenuti dedicati nel menu di amministrazione:
- **Lavori svolti** → `/lavori/` — campi: servizio, esigenza, zona e periodo, attività, prova.
- **Zone servite** → `/zone/` e `/volantinaggio/<città>/` — campi: indirizzo, telefono, comuni.
  Il titolo della zona deve coincidere con il nome della sede (es. "Rapallo").

Menu: Aspetto → Menu, posizione "Menu principale". Finché è vuoto viene mostrato un menu di riserva.

## Modulo preventivo
- Invio via `admin-post.php`, con nonce, campo esca anti-spam e consenso privacy obbligatorio.
- Arriva all'email "richieste di preventivo" (Dati GFA), altrimenti all'email dell'amministratore.
- Installare un plugin SMTP (es. WP Mail SMTP) e fare una prova reale di invio e ricezione.

## Da fare prima della pubblicazione
- Dati societari corretti (P.IVA e titolare del trattamento: oggi il sito mostra la P.IVA di un'altra attività).
- Informativa privacy GDPR e banner cookie conforme (con Consent Mode v2 se si usa Google Ads).
- Foto reali senza dati personali leggibili (nomi sulle cassette oscurati) e senza logo sovrapposto.
- Redirect 301 dai vecchi indirizzi (plugin Redirection):
  - `/servizi/distribuzione-sul-territorio-nazionale/` → `/volantinaggio/`
  - `/servizi/servizi-stampa/`, `/servizi/studio-grafico-ideazione-logo/` → `/stampa-e-grafica/`
  - `/servizi/pubblicita-mobile-wow/`, `/servizi/pubblicita-veicolare/`, `/servizi/noleggio-strumentazioni/` → `/volantinaggio/` (o pagine dedicate se restano)
  - `/servizi/prodotti-web/`, `/servizi/web-marketing/`, `/servizi/consulenze-e-strategie-di-marketing/` → da decidere
  - `/promozione/` → `/volantinaggio/`, `/portfoglio/` → `/lavori/` (`/contatti/` resta valido)
- Eliminare l'articolo "Ciao mondo!".

## Struttura
```
style.css, functions.php
inc/      helpers (dati e segnaposto), setup (script, menu, schema), customizer, post-types, quote-form
parts/    hero, facts, paths, method, control, works, work-card, zones, trust, quote
assets/   css/main.css, js/main.js, js/vendor (GSAP 3.12.5 + ScrollTrigger), fonts (OFL)
```
Animazioni: tutto è visibile senza JavaScript; GSAP aggiunge solo movimento e si spegne con
"riduci animazioni" del sistema operativo.
