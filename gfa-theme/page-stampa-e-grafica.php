<?php
/**
 * Pagina Stampa e grafica (slug: stampa-e-grafica).
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow"><?php esc_html_e( 'Prima della distribuzione', 'gfa' ); ?></p>
		<h1>Stampa e grafica</h1>
		<p class="lead">Progettiamo e stampiamo il materiale che poi distribuiamo: un solo referente dal file alla cassetta, senza passaggi tra fornitori diversi.</p>
		<p><a class="btn btn--primary" href="#preventivo">Chiedi un preventivo</a></p>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Cosa stampiamo</p><h2>Dal biglietto da visita al fondale da fiera</h2></div>
		<div class="modes">
			<div class="mode"><h3>Volantini e pieghevoli</h3><p>A6, A5, A4, pieghevoli a due e tre ante, fronte e retro.</p><p class="mode__when"><b>Per</b> volantinaggio in cassetta e nei negozi.</p></div>
			<div class="mode"><h3>Locandine e manifesti</h3><p>Da A3 a 70×100, per vetrine, bacheche e affissione.</p><p class="mode__when"><b>Per</b> eventi, spettacoli, inaugurazioni.</p></div>
			<div class="mode"><h3>Allestimenti</h3><p>Banner, striscioni, roll-up, fondali, rivestimenti per pavimenti.</p><p class="mode__when"><b>Per</b> fiere, stand e manifestazioni.</p></div>
			<div class="mode"><h3>Immagine coordinata e gadget</h3><p>Logo, biglietti da visita, carta intestata, shopper, abbigliamento.</p><p class="mode__when"><b>Per</b> attività che partono o si rinnovano.</p></div>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Studio grafico</p><h2>Come nasce un volantino</h2></div>
		<div class="route-line">
			<span class="route-progress" aria-hidden="true"></span>
			<ol class="route-steps">
				<li><h3>Brief</h3><p>Cosa promuovi, a chi, con quale offerta e scadenza.</p></li>
				<li><h3>Bozza</h3><p>Una prima proposta grafica. <span class="todo">numero di revisioni incluse</span></p></li>
				<li><h3>File di stampa</h3><p>Controllo di formati, margini e colori prima di stampare.</p></li>
				<li><h3>Stampa e consegna</h3><p>Il materiale va direttamente alla squadra di distribuzione.</p></li>
			</ol>
		</div>
	</div>
</section>

<?php get_template_part( 'parts/works' ); ?>

<section class="section section--alt">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Domande frequenti</p><h2>Prima di mandarci un file</h2></div>
		<div class="faq prose">
			<details><summary>Posso mandarvi un file già pronto?</summary><p>Sì. Lo controlliamo prima della stampa e ti avvisiamo se qualcosa non va. <span class="todo">formati accettati</span></p></details>
			<details><summary>Quanto costa la grafica?</summary><p>Si aggiunge al prezzo di stampa e distribuzione solo se ti serve. <span class="todo">prezzo di partenza da confermare</span></p></details>
			<details><summary>In quanti giorni avete il materiale stampato?</summary><p><span class="todo">tempi medi di stampa</span></p></details>
		</div>
	</div>
</section>
<?php
get_template_part( 'parts/quote' );
get_footer();
