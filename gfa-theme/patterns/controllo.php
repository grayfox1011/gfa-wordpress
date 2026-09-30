<?php
/**
 * Title: GFA · Controllo e report
 * Slug: gfa/controllo
 * Categories: gfa
 * Inserter: yes
 *
 * @package gfa
 */
?>
<!-- wp:group {"tagName":"section","anchor":"controllo","className":"section section--blue","layout":{"type":"default"}} -->
<section id="controllo" class="wp-block-group section section--blue"><!-- wp:group {"className":"wrap control","layout":{"type":"default"}} -->
<div class="wp-block-group wrap control"><!-- wp:group {"className":"control__text","layout":{"type":"default"}} -->
<div class="wp-block-group control__text"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Controllo</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Sai dove sono finiti i tuoi volantini.</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"lead"} -->
<p class="lead">Ogni giro lascia una traccia: percorso, orari e foto. Te la mandiamo senza che tu debba chiederla.</p>
<!-- /wp:paragraph -->
<!-- wp:list {"className":"checks"} -->
<ul class="wp-block-list checks"><!-- wp:list-item -->
<li><strong>Percorso registrato</strong>: il tragitto del distributore su mappa. <mark class="todo">GPS: da confermare con GFA</mark></li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li><strong>Foto a campione</strong>: cassette e punti di consegna, senza nomi dei residenti.</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li><strong>Copie per zona</strong>: quante ne abbiamo consegnate, via per via.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->
<!-- wp:html -->
<figure class="report">
<div class="report__bar"><span>REPORT · GIRO 0417</span><span>Rapallo · zona Centro</span><span>esempio</span></div>
<svg class="report__map" viewBox="0 0 560 300" role="img" aria-label="Mappa di esempio con il percorso del distributore e quattro tappe">
<rect class="block" x="40" y="30" width="150" height="90" rx="4"/><rect class="block" x="230" y="30" width="130" height="90" rx="4"/><rect class="block" x="400" y="30" width="120" height="90" rx="4"/>
<rect class="block" x="40" y="170" width="150" height="100" rx="4"/><rect class="block" x="230" y="170" width="130" height="100" rx="4"/><rect class="block" x="400" y="170" width="120" height="100" rx="4"/>
<path class="street" d="M20 145 H540 M210 15 V285 M380 15 V285"/>
<path class="route route-draw" d="M30 145 H200 V40 H372 V145 H200 V262 H390 V180 H530"/>
<circle class="stop" cx="30" cy="145" r="8"/><circle class="stop" cx="200" cy="40" r="8"/><circle class="stop" cx="372" cy="145" r="8"/><circle class="stop" cx="530" cy="180" r="8"/>
<text class="label" x="44" y="136">08:02 partenza</text><text class="label" x="214" y="58">Via Mameli</text><text class="label" x="386" y="136">09:40</text><text class="label" x="436" y="200">12:12 fine</text>
</svg>
<ol class="report__rows">
<li><b>08:02</b><span>Corso Italia</span><b>312 cassette</b></li>
<li><b>09:40</b><span>Via Mameli, via Venezia</span><b>418 cassette</b></li>
<li><b>11:05</b><span>Negozi del centro</span><b>38 punti</b></li>
</ol>
<figcaption class="report__note">Esempio illustrativo: sostituire con un report reale anonimizzato.</figcaption>
</figure>
<!-- /wp:html --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
