<?php
/**
 * Pagina Franchising (slug: franchising).
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow"><?php esc_html_e( 'Rete GFA', 'gfa' ); ?></p>
		<h1>Apri una sede GFA nella tua zona</h1>
		<p class="lead">Metodo di lavoro, marchio, strumenti di controllo e clienti della rete. Tu porti la conoscenza del territorio. <span class="todo">Condizioni del franchising da confermare con GFA</span></p>
		<p><a class="btn btn--primary" href="#candidatura">Candidati</a></p>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Cosa ricevi</p><h2>Cosa mette la rete, cosa metti tu</h2></div>
		<div class="modes">
			<div class="mode"><h3>Dalla rete</h3><ul><li>Marchio e immagine coordinata</li><li>Metodo di pianificazione e report</li><li>Formazione iniziale <span class="todo">durata</span></li><li>Pagina della tua zona su questo sito</li></ul></div>
			<div class="mode"><h3>Da te</h3><ul><li>Conoscenza della zona e dei commercianti</li><li>Squadra di distribuzione</li><li>Un magazzino o spazio per il materiale</li><li><span class="todo">investimento iniziale e royalty</span></li></ul></div>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Come funziona</p><h2>Dalla candidatura alla prima distribuzione</h2></div>
		<div class="route-line">
			<span class="route-progress" aria-hidden="true"></span>
			<ol class="route-steps">
				<li><h3>Candidatura</h3><p>Ci dici dove vuoi operare e con quale esperienza.</p></li>
				<li><h3>Colloquio</h3><p>Verifichiamo insieme la zona e il potenziale.</p></li>
				<li><h3>Formazione</h3><p>Metodo, strumenti, primi clienti affiancati.</p></li>
				<li><h3>Avvio</h3><p>La tua sede compare nella rete e sul sito.</p></li>
			</ol>
		</div>
	</div>
</section>

<?php get_template_part( 'parts/zones' ); ?>

<section class="section section--blue" id="candidatura">
	<div class="wrap quote-box">
		<div>
			<p class="eyebrow">Candidatura</p>
			<h2>Parliamone.</h2>
			<p class="lead" style="margin-top:1rem">Scrivici la zona che ti interessa: ti richiamiamo per un primo colloquio senza impegno.</p>
		</div>
		<div class="form">
			<p><strong>Scrivi a</strong> <?php echo esc_html( gfa_opt( 'email' ) ); ?> con oggetto "Franchising", indicando zona, esperienza e un recapito.</p>
			<p class="hint"><span class="todo">In alternativa: modulo dedicato, se GFA vuole ricevere le candidature separate dai preventivi</span></p>
		</div>
	</div>
</section>
<?php
get_footer();
