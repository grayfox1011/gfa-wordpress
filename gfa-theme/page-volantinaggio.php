<?php
/**
 * Pagina Volantinaggio (slug: volantinaggio) — la pagina commerciale principale.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow"><?php esc_html_e( 'Servizio principale', 'gfa' ); ?></p>
		<h1>Volantinaggio e distribuzione volantini</h1>
		<p class="lead">In cassetta, nei negozi, nelle piazze e agli eventi, nelle zone di <?php echo esc_html( implode( ', ', gfa_sedi() ) ); ?>. Ti diciamo quale modalità conviene, quante copie servono e ti mostriamo il lavoro fatto.</p>
		<p><a class="btn btn--primary" href="#preventivo">Chiedi un preventivo</a></p>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Modalità</p><h2>Quale distribuzione ti serve?</h2></div>
		<div class="modes">
			<div class="mode"><h3>In cassetta, porta a porta</h3><p>Un volantino in ogni cassetta delle vie scelte.</p><p class="mode__when"><b>Conviene per</b> offerte di negozi e servizi di quartiere, aperture, agenzie immobiliari.</p></div>
			<div class="mode"><h3>Negozi e attività commerciali</h3><p>Pile di volantini presso bar, edicole e negozi che accettano di esporli.</p><p class="mode__when"><b>Conviene per</b> eventi, corsi, iniziative culturali.</p></div>
			<div class="mode"><h3>Postazioni fisse e centri commerciali</h3><p>Distribuzione a mano in punti di passaggio, con personale in divisa.</p><p class="mode__when"><b>Conviene per</b> lanci di prodotto e promozioni a tempo.</p></div>
			<div class="mode"><h3>Fiere, piazze ed eventi</h3><p>Personale sul posto, anche con i pannelli indossabili WOW.</p><p class="mode__when"><b>Conviene per</b> farsi notare dove c'è già il pubblico giusto.</p></div>
		</div>
	</div>
</section>

<?php get_template_part( 'parts/control' ); ?>

<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Prezzi</p><h2>Pacchetti con stampa inclusa</h2><p class="lead">Prezzi IVA esclusa. La grafica si aggiunge solo se ti serve. <span class="todo">Listino da confermare per sede</span></p></div>
		<div class="table-scroll">
			<table class="price-table">
				<thead><tr><th scope="col">Copie</th><th scope="col">Formato</th><th scope="col">Include</th><th scope="col">Prezzo</th></tr></thead>
				<tbody>
					<tr><td>5.000</td><td>A5 fronte/retro <span class="todo">confermare</span></td><td>Stampa + distribuzione in cassetta + report</td><td>490 €</td></tr>
					<tr><td>10.000</td><td>A5 fronte/retro <span class="todo">confermare</span></td><td>Stampa + distribuzione in cassetta + report</td><td>550 € <span class="todo">verificare</span></td></tr>
					<tr><td>20.000</td><td>A5 fronte/retro <span class="todo">confermare</span></td><td>Stampa + distribuzione in cassetta + report</td><td>920 €</td></tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Domande frequenti</p><h2>Prima di chiedere un preventivo</h2></div>
		<div class="faq prose">
			<details><summary>Quante copie mi servono?</summary><p>Dipende da zona e obiettivo. Partiamo dal numero di cassette delle vie che ti interessano e ti proponiamo una quantità, con il perché.</p></details>
			<details><summary>Come faccio a sapere che i volantini sono stati consegnati?</summary><p>A fine giro ricevi il report con percorso, orari, foto a campione e copie per zona. <span class="todo">Descrivere i controlli reali di GFA</span></p></details>
			<details><summary>Serve un permesso del Comune?</summary><p>Per la distribuzione in cassetta di norma no; per la distribuzione in strada alcuni Comuni hanno regole proprie. Le verifichiamo noi. <span class="todo">Confermare con GFA</span></p></details>
			<details><summary>In quanto tempo partite?</summary><p><span class="todo">Tempi medi da confermare</span></p></details>
		</div>
	</div>
</section>
<?php
get_template_part( 'parts/quote' );
get_footer();
