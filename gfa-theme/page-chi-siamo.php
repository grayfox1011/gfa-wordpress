<?php
/**
 * Pagina Chi siamo (slug: chi-siamo).
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow"><?php esc_html_e( 'Chi siamo', 'gfa' ); ?></p>
		<h1><?php echo gfa_opt( 'anno' ) ? esc_html( 'Nel volantinaggio dal ' . gfa_opt( 'anno' ) ) : 'Nel volantinaggio dal <span class="todo">anno</span>'; ?></h1>
		<p class="lead">GFA distribuisce volantini e materiale pubblicitario nel Nord Italia, con <?php echo esc_html( (string) count( gfa_sedi() ) ); ?> sedi operative e squadre che conoscono le proprie zone.</p>
	</div>
</section>

<section class="section">
	<div class="wrap control">
		<div class="stack">
			<p class="eyebrow">La nostra storia</p>
			<h2>Da una sede a Milano a una rete di sedi</h2>
			<p><span class="todo">Storia da raccontare con GFA: anno e luogo di inizio, chi ha fondato l'azienda, come è cresciuta la rete, cosa è cambiato negli anni</span></p>
			<p><span class="todo">Continuità tra GFA Comunicazione e GFA Marketing: come descriverla correttamente</span></p>
		</div>
		<?php gfa_photo_slot( 'Foto storica o della prima sede', 'Anche vecchia, purché autentica e con autorizzazione' ); ?>
	</div>
</section>

<section class="section section--alt">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Le persone</p><h2>Chi segue il tuo lavoro</h2><p class="lead">Un referente per ogni cliente, dalla prima telefonata al report finale.</p></div>
		<div class="trust">
			<figure class="quote" style="margin:0"><?php gfa_photo_slot( 'Ritratto del referente commerciale', 'Sfondo neutro, luce naturale, liberatoria' ); ?><figcaption><strong><span class="todo">Nome e cognome</span></strong><br><span class="todo">ruolo e sede</span></figcaption></figure>
			<figure class="quote" style="margin:0"><?php gfa_photo_slot( 'Capo squadra distribuzione', 'In divisa, sul campo' ); ?><figcaption><strong><span class="todo">Nome e cognome</span></strong><br><span class="todo">ruolo e sede</span></figcaption></figure>
			<figure class="quote" style="margin:0"><?php gfa_photo_slot( 'Studio grafico', 'Al lavoro su un progetto reale' ); ?><figcaption><strong><span class="todo">Nome e cognome</span></strong><br><span class="todo">ruolo e sede</span></figcaption></figure>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Come lavoriamo</p><h2>Tre impegni che puoi verificare</h2></div>
		<div class="paths">
			<div class="path"><span class="path__tag">Personale</span><h3>Riconoscibile</h3><p>Divise e mezzi con il marchio GFA. <span class="todo">dipendenti diretti o collaboratori?</span></p></div>
			<div class="path"><span class="path__tag">Controllo</span><h3>Documentato</h3><p>Report di ogni giro con percorso, foto a campione e copie per zona.</p></div>
			<div class="path"><span class="path__tag">Privacy</span><h3>Rispettata</h3><p>Nessun dato personale nelle foto dei report, dati dei clienti trattati secondo il GDPR.</p></div>
		</div>
	</div>
</section>

<?php get_template_part( 'parts/zones' ); ?>

<section class="section">
	<div class="wrap">
		<div class="section__head"><p class="eyebrow">Dati societari</p><h2>Chi eroga il servizio</h2></div>
		<div class="table-scroll">
			<table class="price-table">
				<tbody>
					<tr><th scope="row">Ragione sociale</th><td><?php gfa_value( 'ragione', 'ragione sociale' ); ?></td></tr>
					<tr><th scope="row">Partita IVA</th><td><?php gfa_value( 'piva', 'P.IVA da confermare' ); ?></td></tr>
					<tr><th scope="row">REA</th><td><?php gfa_value( 'rea', 'numero REA' ); ?></td></tr>
					<tr><th scope="row">Sede legale</th><td><?php gfa_value( 'sede_legale', 'indirizzo completo' ); ?></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php
get_template_part( 'parts/quote' );
get_footer();
