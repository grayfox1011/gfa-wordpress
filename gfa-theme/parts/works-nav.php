<?php
/**
 * Contatore, barra e frecce dello slider dei lavori.
 *
 * Nascosti finché la fascia non scorre di lato: li mostra main.js (telefono, tablet, schermi
 * bassi); con la fascia fissata da GSAP o la griglia del computer restano nascosti.
 *
 * $args['track'] = id della fascia che le frecce fanno scorrere.
 *
 * @package gfa
 */

$gfa_track = isset( $args['track'] ) ? (string) $args['track'] : '';
?>
<div class="works-nav" data-works-nav hidden>
	<p class="works-nav__count" aria-live="polite"><span class="screen-reader-text"><?php esc_html_e( 'Lavoro', 'gfa' ); ?> </span><span data-works-count></span></p>
	<span class="works-nav__bar" aria-hidden="true"><span data-works-bar></span></span>
	<button class="works-nav__btn" type="button" data-works-prev aria-controls="<?php echo esc_attr( $gfa_track ); ?>" aria-label="<?php esc_attr_e( 'Lavoro precedente', 'gfa' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7"/></svg></button>
	<button class="works-nav__btn" type="button" data-works-next aria-controls="<?php echo esc_attr( $gfa_track ); ?>" aria-label="<?php esc_attr_e( 'Lavoro successivo', 'gfa' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7"/></svg></button>
</div>
