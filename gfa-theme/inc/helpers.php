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
		'hero_titolo'   => 'Volantinaggio che si può verificare.',
		'hero_testo'    => 'Distribuiamo volantini in cassetta, nei negozi e agli eventi. Pianifichiamo zone e quantità con te e ti mostriamo dove è passato ogni distributore.',
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
