<?php
/**
 * Zone servite (/zone/): elenco delle sedi, come lavoriamo in ogni zona, preventivo.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero">
	<div class="wrap stack">
		<p class="eyebrow"><?php esc_html_e( 'Zone servite', 'gfa' ); ?></p>
		<h1><?php esc_html_e( 'Volantinaggio nelle nostre zone', 'gfa' ); ?></h1>
		<p class="lead">
			<?php
			// Il numero segue le sedi di Dati GFA invece di restare "Otto" scritto a mano.
			$gfa_count = count( gfa_sedi() );
			$gfa_words = array( 1 => 'Una', 'Due', 'Tre', 'Quattro', 'Cinque', 'Sei', 'Sette', 'Otto', 'Nove', 'Dieci', 'Undici', 'Dodici' );
			printf(
				/* translators: %s: numero delle sedi, in lettere fino a dodici. */
				esc_html( _n( '%s sede nel Nord Italia, con una squadra che conosce le proprie vie. Scegli la tua zona per vedere comuni coperti, modalità e referente.', '%s sedi nel Nord Italia, ognuna con squadre che conoscono le proprie vie. Scegli la tua zona per vedere comuni coperti, modalità e referente.', $gfa_count, 'gfa' ) ),
				esc_html( isset( $gfa_words[ $gfa_count ] ) ? $gfa_words[ $gfa_count ] : (string) $gfa_count )
			);
			?>
		</p>
		<div class="wp-block-buttons"><div class="wp-block-button is-style-gfa-primary"><a class="wp-block-button__link wp-element-button" href="#preventivo"><?php esc_html_e( 'Chiedi un preventivo per la tua zona', 'gfa' ); ?></a></div></div>
	</div>
</section>
<section class="section" id="zone">
	<div class="wrap zones">
		<div class="zones__text">
			<div class="section__head">
				<p class="eyebrow"><?php esc_html_e( 'Le sedi', 'gfa' ); ?></p>
				<h2><?php esc_html_e( 'Dal Ponente alla pianura.', 'gfa' ); ?></h2>
			</div>
			<?php echo do_blocks( '<!-- wp:gfa/zone-elenco /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php echo do_blocks( '<!-- wp:gfa/zone-mappa /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
</section>
<?php
echo do_blocks( gfa_pattern( 'metodo' ) . gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
get_footer();
