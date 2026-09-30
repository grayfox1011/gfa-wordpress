<?php
/**
 * Pagina Promozione eventi (slug: promozione-eventi).
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow">Per organizzatori, associazioni e Comuni</p>
		<h1>Promuovi il tuo evento sul territorio</h1>
		<p class="lead">Dalla locandina alla distribuzione nelle zone giuste, con un QR che porta a informazioni e prenotazioni. L'evento lo organizzi tu: noi facciamo in modo che si sappia.</p>
		<p><a class="btn btn--primary" href="#preventivo">Chiedi un preventivo</a></p>
	</div>
</section>
<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Il percorso</p><h2>Dal volantino alla prenotazione</h2></div>
		<div class="route-line">
			<span class="route-progress" aria-hidden="true"></span>
			<ol class="route-steps">
				<li><h3>Locandina e volantino</h3><p>Grafica coordinata per affissione e distribuzione.</p></li>
				<li><h3>Stampa</h3><p>Formati da A5 a 70×100, banner e striscioni.</p></li>
				<li><h3>Distribuzione</h3><p>Negozi, bacheche, cassette e punti di passaggio delle zone scelte.</p></li>
				<li><h3>QR dedicato</h3><p>Porta alla pagina dell'evento: contiamo le scansioni, non le presenze.</p></li>
			</ol>
		</div>
	</div>
</section>
<?php get_template_part( 'parts/works' ); ?>
<?php
get_template_part( 'parts/quote' );
get_footer();
