<?php
/**
 * Controllo: esempio di report con percorso.
 *
 * @package gfa
 */
?>
<section class="section section--blue" id="controllo">
	<div class="wrap control">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Controllo', 'gfa' ); ?></p>
			<h2>Sai dove sono finiti i tuoi volantini.</h2>
			<p class="lead" style="margin-top:1rem">Ogni giro lascia una traccia: percorso, orari e foto. Te la mandiamo senza che tu debba chiederla.</p>
			<ul class="checks">
				<li><span><b>Percorso registrato</b> — il tragitto del distributore su mappa. <span class="todo">GPS: da confermare con GFA</span></span></li>
				<li><span><b>Foto a campione</b> — cassette e punti di consegna, senza nomi dei residenti.</span></li>
				<li><span><b>Copie per zona</b> — quante ne abbiamo consegnate, via per via.</span></li>
			</ul>
		</div>
		<figure class="report" style="margin:0">
			<div class="report__bar"><span>REPORT · GIRO 0417</span><span>Rapallo · zona Centro</span><span>esempio</span></div>
			<svg class="report__map" viewBox="0 0 560 300" role="img" aria-label="Mappa di esempio con il percorso del distributore e quattro tappe">
				<rect class="block" x="40" y="30" width="150" height="90" rx="4"/>
				<rect class="block" x="230" y="30" width="130" height="90" rx="4"/>
				<rect class="block" x="400" y="30" width="120" height="90" rx="4"/>
				<rect class="block" x="40" y="170" width="150" height="100" rx="4"/>
				<rect class="block" x="230" y="170" width="130" height="100" rx="4"/>
				<rect class="block" x="400" y="170" width="120" height="100" rx="4"/>
				<path class="street" d="M20 145 H540 M210 15 V285 M380 15 V285"/>
				<path class="route route-draw" d="M30 145 H200 V40 H372 V145 H200 V262 H390 V180 H530"/>
				<circle class="stop" cx="30" cy="145" r="8"/>
				<circle class="stop" cx="200" cy="40" r="8"/>
				<circle class="stop" cx="372" cy="145" r="8"/>
				<circle class="stop" cx="530" cy="180" r="8"/>
				<text class="label" x="44" y="136">08:02 partenza</text>
				<text class="label" x="214" y="58">Via Mameli</text>
				<text class="label" x="386" y="136">09:40</text>
				<text class="label" x="436" y="200">12:12 fine</text>
			</svg>
			<ol class="report__rows">
				<li><b>08:02</b><span>Corso Italia</span><b>312 cassette</b></li>
				<li><b>09:40</b><span>Via Mameli, via Venezia</span><b>418 cassette</b></li>
				<li><b>11:05</b><span>Negozi del centro</span><b>38 punti</b></li>
			</ol>
			<figcaption class="report__note">Esempio illustrativo: sostituire con un report reale anonimizzato.</figcaption>
		</figure>
	</div>
</section>
