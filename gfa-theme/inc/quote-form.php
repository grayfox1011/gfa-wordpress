<?php
/**
 * Richiesta di preventivo: invio via admin-post, nonce, honeypot, consenso privacy.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gestisce l'invio del modulo preventivo.
 */
function gfa_handle_quote() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( 'preventivo', $back );

	if ( ! isset( $_POST['gfa_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gfa_quote_nonce'] ), 'gfa_quote' ) ) {
		wp_safe_redirect( add_query_arg( 'preventivo', 'errore', $back ) . '#preventivo' );
		exit;
	}

	// Honeypot: un campo nascosto che solo i bot compilano.
	if ( ! empty( $_POST['gfa_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'preventivo', 'inviato', $back ) . '#preventivo' );
		exit;
	}

	$fields = array(
		'servizio' => 'Servizio',
		'zona'     => 'Comune o zona',
		'quantita' => 'Quantità',
		'periodo'  => 'Periodo',
		'dettagli' => 'Dettagli',
		'nome'     => 'Nome',
		'azienda'  => 'Azienda o ente',
		'email'    => 'Email',
		'telefono' => 'Telefono',
	);
	$data = array();
	foreach ( $fields as $key => $label ) {
		$raw          = isset( $_POST[ 'gfa_' . $key ] ) ? wp_unslash( $_POST[ 'gfa_' . $key ] ) : '';
		$data[ $key ] = 'dettagli' === $key ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	$data['email'] = sanitize_email( $data['email'] );

	$consent = ! empty( $_POST['gfa_privacy'] );
	if ( '' === $data['nome'] || ! is_email( $data['email'] ) || '' === $data['servizio'] || ! $consent ) {
		wp_safe_redirect( add_query_arg( 'preventivo', 'errore', $back ) . '#preventivo' );
		exit;
	}

	$to = gfa_opt( 'email_preventivi' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$body = '';
	foreach ( $fields as $key => $label ) {
		$body .= $label . ': ' . ( '' !== $data[ $key ] ? $data[ $key ] : '—' ) . "\n";
	}
	$body .= "\nConsenso privacy: sì (" . current_time( 'mysql' ) . ")\n";

	$sent = wp_mail(
		$to,
		sprintf( 'Richiesta preventivo: %s — %s', $data['servizio'], $data['zona'] ),
		$body,
		array( 'Reply-To: ' . $data['nome'] . ' <' . $data['email'] . '>' )
	);

	wp_safe_redirect( add_query_arg( 'preventivo', $sent ? 'inviato' : 'errore', $back ) . '#preventivo' );
	exit;
}
add_action( 'admin_post_nopriv_gfa_preventivo', 'gfa_handle_quote' );
add_action( 'admin_post_gfa_preventivo', 'gfa_handle_quote' );
