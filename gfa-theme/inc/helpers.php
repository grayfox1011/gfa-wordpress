<?php
/**
 * Dati aziendali e segnaposto.
 *
 * Ogni dato non ancora confermato da GFA resta vuoto: il tema mostra un
 * segnaposto "da fornire" invece di inventare numeri, indirizzi o P.IVA.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valori predefiniti dei dati aziendali (modificabili da Aspetto → Personalizza → Dati GFA).
 *
 * @return array<string,string>
 */
function gfa_defaults() {
	return array(
		'brand'         => 'GFA',
		'ragione'       => '',
		'piva'          => '',
		'rea'           => '',
		'sede_legale'   => '',
		'telefono'      => '',
		'email'         => 'info@gfamarketing.com',
		'whatsapp'      => '',
		'anno'          => '',
		'sedi'          => 'Milano, Como, Rapallo, Genova, Sanremo, Bassano del Grappa, Modena, Reggio Emilia',
		'copie_anno'    => '',
		'email_preventivi' => '',
	);
}

/**
 * Legge un dato aziendale.
 *
 * @param string $key Chiave.
 * @return string
 */
function gfa_opt( $key ) {
	$defaults = gfa_defaults();
	$fallback = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return (string) get_theme_mod( 'gfa_' . $key, $fallback );
}

/**
 * Stampa un dato o, se manca, un segnaposto visibile.
 *
 * @param string $key   Chiave.
 * @param string $label Cosa deve fornire il cliente.
 */
function gfa_value( $key, $label ) {
	$value = gfa_opt( $key );
	if ( '' === $value ) {
		gfa_todo( $label );
		return;
	}
	echo esc_html( $value );
}

/**
 * Segnaposto "da fornire".
 *
 * @param string $label Testo del segnaposto.
 */
function gfa_todo( $label ) {
	echo '<span class="todo">' . esc_html( $label ) . '</span>';
}

/**
 * Riquadro foto da fornire.
 *
 * @param string $title Cosa ritrae la foto.
 * @param string $spec  Indicazioni pratiche.
 */
function gfa_photo_slot( $title, $spec ) {
	echo '<div class="photo-slot" role="img" aria-label="' . esc_attr( 'Foto da fornire: ' . $title ) . '">';
	echo '<span>FOTO DA FORNIRE</span><strong>' . esc_html( $title ) . '</strong><span>' . esc_html( $spec ) . '</span>';
	echo '</div>';
}

/**
 * Elenco sedi come array.
 *
 * @return string[]
 */
function gfa_sedi() {
	return array_filter( array_map( 'trim', explode( ',', gfa_opt( 'sedi' ) ) ) );
}

/**
 * Il modulo preventivo va aggiunto in fondo alla pagina?
 *
 * No nelle informative (privacy e cookie policy: niente raccolta di dati in fondo a un testo
 * legale) e quando il contenuto contiene già il modulo, che comparirebbe due volte.
 *
 * @return bool
 */
function gfa_show_quote_form() {
	if ( is_privacy_policy() || is_page( 'cookie-policy' ) ) {
		return false;
	}
	return ! has_block( 'gfa/preventivo' );
}

/**
 * Logo caricato da Personalizza → Identità del sito, alto quanto il marchio GFA (38 px).
 *
 * La misura "medium" non ritaglia il logo; sizes dice al browser la larghezza reale, così su uno
 * schermo denso arriva il file adatto e non l'originale.
 *
 * @return string
 */
function gfa_brand_logo() {
	$id    = (int) get_theme_mod( 'custom_logo' );
	$ratio = gfa_logo_ratio();
	$attr  = array(
		'class'    => 'brand__logo',
		'alt'      => '',
		'loading'  => 'eager',
		'decoding' => 'async',
	);
	if ( $ratio > 0 ) {
		$attr['sizes'] = (int) ceil( 38 * $ratio ) . 'px';
	}
	return wp_get_attachment_image( $id, 'medium', false, $attr );
}

/**
 * Larghezza diviso altezza del logo caricato, 0 se non si conosce.
 *
 * @return float
 */
function gfa_logo_ratio() {
	$meta = wp_get_attachment_metadata( (int) get_theme_mod( 'custom_logo' ) );
	if ( ! is_array( $meta ) || empty( $meta['width'] ) || empty( $meta['height'] ) ) {
		return 0;
	}
	return (float) $meta['width'] / (float) $meta['height'];
}
