<?php
/**
 * Fiducia: recensioni reali (segnaposto finché non ci sono).
 *
 * @package gfa
 */
?>
<section class="section" id="recensioni">
	<div class="wrap">
		<div class="section__head">
			<p class="eyebrow"><?php esc_html_e( 'Cosa dicono i clienti', 'gfa' ); ?></p>
			<h2>Parole di chi ci ha già affidato un giro.</h2>
		</div>
		<div class="trust">
			<?php for ( $gfa_i = 1; $gfa_i <= 3; $gfa_i++ ) : ?>
				<figure class="quote" style="margin:0">
					<blockquote><span class="todo">Recensione Google reale n. <?php echo (int) $gfa_i; ?>, testo integrale</span></blockquote>
					<cite><span class="todo">Nome, attività, città</span></cite>
				</figure>
			<?php endfor; ?>
		</div>
	</div>
</section>
