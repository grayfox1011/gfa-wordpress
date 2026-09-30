<?php
/**
 * Hero.
 *
 * @package gfa
 */
$gfa_title = gfa_opt( 'hero_titolo' );
?>
<section class="hero">
	<div class="wrap hero__grid">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Volantinaggio · Stampa · Eventi', 'gfa' ); ?></p>
			<h1><span class="line">Volantinaggio</span><span class="line">che si può</span><span class="line"><span class="hl">verificare.</span></span></h1>
			<p class="lead"><?php echo esc_html( gfa_opt( 'hero_testo' ) ); ?></p>
			<div class="hero__cta">
				<a class="btn btn--primary" href="#preventivo"><?php esc_html_e( 'Chiedi un preventivo', 'gfa' ); ?></a>
				<a class="btn btn--ghost" href="#metodo"><?php esc_html_e( 'Come lavoriamo', 'gfa' ); ?></a>
			</div>
			<p class="hero__proof"><span>Zone e quantità pianificate con te</span><span>Report a fine giro</span><span>Stampa inclusa, se serve</span></p>
		</div>
		<div class="hero__visual">
			<?php gfa_photo_slot( 'La squadra GFA al lavoro', 'Orizzontale o verticale, persone reali in divisa, furgone recente, liberatoria firmata' ); ?>
			<div class="ticket" aria-label="<?php esc_attr_e( 'Esempio di riepilogo giro', 'gfa' ); ?>">
				<strong>Giro di esempio · Rapallo centro</strong>
				<div class="ticket__row"><span>Cassette</span><b>1.240</b></div>
				<div class="ticket__row"><span>Negozi</span><b>38</b></div>
				<div class="ticket__row"><span>Durata</span><b>4 h 10'</b></div>
				<div class="ticket__row"><span>Report</span><b>mappa + foto</b></div>
			</div>
		</div>
	</div>
</section>
