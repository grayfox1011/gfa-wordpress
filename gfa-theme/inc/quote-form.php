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
	$back = remove_query_arg( array( 'preventivo', 'gfa_receipt' ), $back );
	$back = strtok( $back, '#' );
	$back = wp_validate_redirect( $back, home_url( '/' ) );

	if ( ! isset( $_POST['gfa_quote_nonce'] ) || ! is_string( $_POST['gfa_quote_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gfa_quote_nonce'] ), 'gfa_quote' ) ) {
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
		$raw          = isset( $_POST[ 'gfa_' . $key ] ) && is_string( $_POST[ 'gfa_' . $key ] ) ? wp_unslash( $_POST[ 'gfa_' . $key ] ) : '';
		$data[ $key ] = 'dettagli' === $key ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	$data['email'] = sanitize_email( $data['email'] );

	$consent = isset( $_POST['gfa_privacy'] ) && '1' === $_POST['gfa_privacy'];
	$services = array( 'Volantinaggio', 'Grafica, stampa e distribuzione', 'Promozione evento', 'Stampa e grafica' );
	if ( '' === $data['nome'] || ! is_email( $data['email'] ) || '' === $data['zona'] || ! in_array( $data['servizio'], $services, true ) || ! $consent ) {
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

	$return = add_query_arg( 'preventivo', $sent ? 'inviato' : 'errore', $back );
	if ( $sent ) {
		$token = gfa_quote_receipt_create( array(
			'googleAds' => isset( $_POST['gfa_google_ads_consent'] ) && '1' === $_POST['gfa_google_ads_consent'],
			'metaAds' => isset( $_POST['gfa_meta_ads_consent'] ) && '1' === $_POST['gfa_meta_ads_consent'],
		) );
		if ( '' !== $token ) { $return = add_query_arg( 'gfa_receipt', $token, $return ); }
	}
	wp_safe_redirect( $return . '#preventivo' );
	exit;
}
add_action( 'admin_post_nopriv_gfa_preventivo', 'gfa_handle_quote' );
add_action( 'admin_post_gfa_preventivo', 'gfa_handle_quote' );

