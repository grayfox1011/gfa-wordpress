<?php
/**
 * Pagina Contatti (slug: contatti) — mantiene il vecchio indirizzo /contatti/.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow"><?php esc_html_e( 'Contatti', 'gfa' ); ?></p>
		<h1>Parla con la sede più vicina</h1>
		<p class="lead">Per un preventivo usa il modulo qui sotto: ti richiama il referente della tua zona.</p>
	</div>
</section>
<?php
get_template_part( 'parts/quote' );
get_template_part( 'parts/zones' );
get_footer();
