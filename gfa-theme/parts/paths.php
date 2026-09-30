<?php
/**
 * Tre percorsi.
 *
 * @package gfa
 */
?>
<section class="section" id="servizi">
	<div class="wrap">
		<div class="section__head">
			<p class="eyebrow"><?php esc_html_e( 'Cosa ti serve', 'gfa' ); ?></p>
			<h2>Parti da quello che devi fare.</h2>
		</div>
		<div class="paths">
			<a class="path path--main" href="<?php echo esc_url( home_url( '/volantinaggio/' ) ); ?>">
				<span class="path__tag">Servizio principale</span>
				<h3>Distribuire volantini</h3>
				<ul><li>In cassetta, porta a porta</li><li>Nei negozi e nei punti di passaggio</li><li>A fiere, piazze ed eventi</li></ul>
				<span class="path__go">Volantinaggio →</span>
			</a>
			<a class="path" href="<?php echo esc_url( home_url( '/stampa-e-grafica/' ) ); ?>">
				<span class="path__tag">Prima della distribuzione</span>
				<h3>Preparare e stampare</h3>
				<ul><li>Grafica di volantini e locandine</li><li>Stampa di ogni formato</li><li>Banner, striscioni, allestimenti</li></ul>
				<span class="path__go">Stampa e grafica →</span>
			</a>
			<a class="path" href="<?php echo esc_url( home_url( '/promozione-eventi/' ) ); ?>">
				<span class="path__tag">Per organizzatori</span>
				<h3>Promuovere un evento</h3>
				<ul><li>Locandine e volantini dell'evento</li><li>Distribuzione nelle zone giuste</li><li>QR verso prenotazioni e biglietti</li></ul>
				<span class="path__go">Promozione eventi →</span>
			</a>
		</div>
	</div>
</section>
