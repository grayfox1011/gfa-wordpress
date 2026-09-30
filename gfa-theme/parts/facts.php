<?php
/**
 * Numeri: solo dati confermati, altrimenti segnaposto.
 *
 * @package gfa
 */
?>
<section class="facts" aria-label="<?php esc_attr_e( 'GFA in numeri', 'gfa' ); ?>">
	<div class="wrap">
		<ul>
			<li><span class="num"><?php echo gfa_opt( 'anno' ) ? esc_html( 'Dal ' . gfa_opt( 'anno' ) ) : '<span class="todo">anno di inizio</span>'; ?></span><span class="lbl">nel volantinaggio</span></li>
			<li><span class="num"><?php echo esc_html( (string) count( gfa_sedi() ) ); ?></span><span class="lbl">sedi operative al Nord</span></li>
			<li><span class="num"><?php gfa_value( 'copie_anno', 'copie/anno' ); ?></span><span class="lbl">volantini distribuiti in un anno</span></li>
			<li><span class="num">3</span><span class="lbl">servizi in un solo referente: grafica, stampa, distribuzione</span></li>
		</ul>
	</div>
</section>
