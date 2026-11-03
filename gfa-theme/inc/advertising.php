<?php
/** Google Ads e Meta: configurazione esplicita e ricevute di conversione senza dati del modulo. @package gfa */
defined( 'ABSPATH' ) || exit;

function gfa_advertising_defaults() {
	return array( 'google_enabled' => false, 'google_id' => '', 'google_label' => '', 'meta_enabled' => false, 'meta_id' => '' );
}

function gfa_advertising_valid_google_id( $value ) { return is_string( $value ) && 1 === preg_match( '/^AW-[0-9]{6,15}$/D', $value ); }
function gfa_advertising_valid_google_label( $value ) { return is_string( $value ) && 1 === preg_match( '/^[A-Za-z0-9_-]{1,100}$/D', $value ); }
function gfa_advertising_valid_meta_id( $value ) { return is_string( $value ) && 1 === preg_match( '/^[0-9]{8,20}$/D', $value ); }
function gfa_advertising_enabled_value( $value ) { return true === $value || 1 === $value || '1' === $value; }

function gfa_advertising_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out = gfa_advertising_defaults();
	foreach ( array( 'google_id', 'google_label', 'meta_id' ) as $key ) {
		$value = isset( $input[ $key ] ) && is_string( $input[ $key ] ) ? trim( $input[ $key ] ) : '';
		$validator = array( 'google_id' => 'gfa_advertising_valid_google_id', 'google_label' => 'gfa_advertising_valid_google_label', 'meta_id' => 'gfa_advertising_valid_meta_id' );
		if ( '' !== $value && ! call_user_func( $validator[ $key ], $value ) ) {
			add_settings_error( 'gfa_advertising', $key, 'Formato non valido: ' . $key . '. Il valore non è stato salvato.' );
			continue;
		}
		$out[ $key ] = $value;
	}
	$google_requested = isset( $input['google_enabled'] ) && gfa_advertising_enabled_value( $input['google_enabled'] );
	$meta_requested = isset( $input['meta_enabled'] ) && gfa_advertising_enabled_value( $input['meta_enabled'] );
	$out['google_enabled'] = $google_requested && '' !== $out['google_id'] && '' !== $out['google_label'];
	$out['meta_enabled'] = $meta_requested && '' !== $out['meta_id'];
	if ( $google_requested && ! $out['google_enabled'] ) { add_settings_error( 'gfa_advertising', 'google_incomplete', 'Google Ads resta spento: inserisci ID AW e label di conversione validi.' ); }
	if ( $meta_requested && ! $out['meta_enabled'] ) { add_settings_error( 'gfa_advertising', 'meta_incomplete', 'Meta Pixel resta spento: inserisci un ID Pixel valido.' ); }
	return $out;
}

/** Verifica anche valori salvati da importazioni, senza fidarsi della sola schermata impostazioni. */
function gfa_advertising_settings() {
	$saved = get_option( 'gfa_advertising', array() );
	$saved = is_array( $saved ) ? array_merge( gfa_advertising_defaults(), $saved ) : gfa_advertising_defaults();
	$saved['google_id'] = gfa_advertising_valid_google_id( $saved['google_id'] ) ? $saved['google_id'] : '';
	$saved['google_label'] = gfa_advertising_valid_google_label( $saved['google_label'] ) ? $saved['google_label'] : '';
	$saved['meta_id'] = gfa_advertising_valid_meta_id( $saved['meta_id'] ) ? $saved['meta_id'] : '';
	$saved['google_enabled'] = gfa_advertising_enabled_value( $saved['google_enabled'] ) && '' !== $saved['google_id'] && '' !== $saved['google_label'];
	$saved['meta_enabled'] = gfa_advertising_enabled_value( $saved['meta_enabled'] ) && '' !== $saved['meta_id'];
	return $saved;
}

add_action( 'admin_init', function () {
	register_setting( 'gfa_advertising', 'gfa_advertising', array( 'type' => 'array', 'sanitize_callback' => 'gfa_advertising_sanitize_settings', 'default' => gfa_advertising_defaults() ) );
} );
add_action( 'admin_menu', function () { add_options_page( 'Pubblicità GFA', 'Pubblicità GFA', 'manage_options', 'gfa-advertising', 'gfa_advertising_settings_page' ); } );

function gfa_advertising_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$s = gfa_advertising_settings();
	?>
	<div class="wrap"><h1>Pubblicità GFA</h1>
	<p>I tag restano spenti finché non inserisci gli identificativi e abiliti il rispettivo servizio. Anche dopo l’attivazione vengono caricati solo con il consenso del visitatore.</p>
	<p>Configura una conversione Google Ads per l’invio della richiesta di preventivo. Non inserire codici JavaScript, token di accesso o credenziali: servono soltanto gli ID pubblici e la label.</p>
	<?php settings_errors( 'gfa_advertising' ); ?>
	<form action="options.php" method="post">
	<?php settings_fields( 'gfa_advertising' ); ?>
	<h2>Google Ads</h2>
	<p><label><input type="checkbox" name="gfa_advertising[google_enabled]" value="1" <?php checked( $s['google_enabled'] ); ?>> Abilita Google Ads dopo il consenso</label></p>
	<p><label for="gfa-google-id">ID Google Ads (AW-…)</label><br><input class="regular-text" id="gfa-google-id" name="gfa_advertising[google_id]" value="<?php echo esc_attr( $s['google_id'] ); ?>" autocomplete="off"></p>
	<p><label for="gfa-google-label">Label conversione preventivo</label><br><input class="regular-text" id="gfa-google-label" name="gfa_advertising[google_label]" value="<?php echo esc_attr( $s['google_label'] ); ?>" autocomplete="off"></p>
	<p>Consent Mode v2 in modalità base: nessun tag o ping Google prima dell’accettazione. Google Analytics e conversioni avanzate non sono inclusi.</p>
	<h2>Meta Pixel</h2>
	<p><label><input type="checkbox" name="gfa_advertising[meta_enabled]" value="1" <?php checked( $s['meta_enabled'] ); ?>> Abilita Meta Pixel dopo il consenso</label></p>
	<p><label for="gfa-meta-id">ID Meta Pixel</label><br><input class="regular-text" id="gfa-meta-id" name="gfa_advertising[meta_id]" value="<?php echo esc_attr( $s['meta_id'] ); ?>" autocomplete="off" inputmode="numeric"></p>
	<p>Eventi PageView e Lead. Corrispondenza avanzata, raccolta automatica e Conversions API non sono abilitati da questa integrazione.</p>
	<h2>Prima dell’attivazione</h2>
	<p>Verifica account e informative, evita tag duplicati in altri plugin o Tag Manager e collauda consenso, revoca e conversioni. Ogni cambio di configurazione richiede una nuova scelta al visitatore.</p>
	<p>La conversione indica che WordPress ha accettato la richiesta per l’invio email; non certifica la consegna nella casella del destinatario. Non vengono trasmessi nome, email, telefono o testo del modulo ai tag.</p>
	<?php submit_button(); ?>
	</form></div>
	<?php
}

/** Una ricevuta è creata solo dopo wp_mail=true e per i servizi già consentiti all’invio. */
function gfa_quote_receipt_create( $eligibility ) {
	if ( ! is_array( $eligibility ) ) { return ''; }
	$s = gfa_advertising_settings();
	$google = $s['google_enabled'] && isset( $eligibility['googleAds'] ) && true === $eligibility['googleAds'];
	$meta = $s['meta_enabled'] && isset( $eligibility['metaAds'] ) && true === $eligibility['metaAds'];
	if ( ! $google && ! $meta ) { return ''; }
	try { $token = bin2hex( random_bytes( 32 ) ); } catch ( Exception $error ) { return ''; }
	$receipt = array( 'eventId' => wp_generate_uuid4(), 'googleAds' => $google, 'metaAds' => $meta, 'expires' => time() + 600 );
	return set_transient( 'gfa_lead_' . hash( 'sha256', $token ), $receipt, 600 ) ? $token : '';
}

function gfa_quote_receipt_consume( $token ) {
	if ( ! is_string( $token ) || ! preg_match( '/^[a-f0-9]{64}$/D', $token ) ) { return null; }
	$key = 'gfa_lead_' . hash( 'sha256', $token );
	$item = get_transient( $key );
	// Il risultato di delete impedisce di assegnare la stessa ricevuta a due richieste concorrenti.
	if ( ! is_array( $item ) || ! delete_transient( $key ) || ! isset( $item['expires'], $item['eventId'], $item['googleAds'], $item['metaAds'] )
		|| ! is_int( $item['expires'] ) || $item['expires'] <= time() || ! is_string( $item['eventId'] )
		|| ! preg_match( '/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/D', $item['eventId'] )
		|| ! is_bool( $item['googleAds'] ) || ! is_bool( $item['metaAds'] ) || ( ! $item['googleAds'] && ! $item['metaAds'] ) ) { return null; }
	return array( 'eventId' => $item['eventId'], 'googleAds' => $item['googleAds'], 'metaAds' => $item['metaAds'] );
}

if ( isset( $_GET['gfa_receipt'] ) && ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
add_action( 'template_redirect', function () {
	if ( ! isset( $_GET['gfa_receipt'] ) ) { return; }
	nocache_headers();
	// Nessun dato del modulo viene conservato nella ricevuta né esportato nel codice della pagina.
	$token = is_string( $_GET['gfa_receipt'] ) ? wp_unslash( $_GET['gfa_receipt'] ) : '';
	$GLOBALS['gfa_advertising_receipt'] = gfa_quote_receipt_consume( $token );
}, 0 );

function gfa_advertising_tracking_allowed() {
	if ( is_admin() || is_feed() || is_preview() || is_search() || is_404() || is_attachment() ) { return false; }
	if ( is_singular() ) {
		$id = get_queried_object_id();
		return 'publish' === get_post_status( $id ) && '' === (string) get_post_field( 'post_password', $id ) && '1' !== (string) get_post_meta( $id, '_gfa_seo_noindex', true );
	}
	return is_front_page() || is_post_type_archive( array( 'lavoro', 'zona' ) );
}

/** Getter senza effetti: può essere letto dal banner senza consumare altre ricevute. */
function gfa_advertising_config() {
	$s = gfa_advertising_settings();
	$google = array( 'enabled' => $s['google_enabled'], 'id' => $s['google_id'], 'conversionLabel' => $s['google_label'] );
	$meta = array( 'enabled' => $s['meta_enabled'], 'id' => $s['meta_id'] );
	$allowed = gfa_advertising_tracking_allowed();
	$page_url = is_singular() ? get_permalink( get_queried_object_id() ) : ( is_post_type_archive( 'lavoro' ) ? get_post_type_archive_link( 'lavoro' ) : ( is_post_type_archive( 'zona' ) ? get_post_type_archive_link( 'zona' ) : home_url( '/' ) ) );
	return array(
		'revision' => substr( hash( 'sha256', wp_json_encode( array( $google['enabled'] ? $google : array( 'enabled' => false ), $meta['enabled'] ? $meta : array( 'enabled' => false ) ) ) ), 0, 16 ),
		'google' => $google, 'meta' => $meta, 'pageUrl' => $page_url,
		'trackingAllowed' => $allowed,
		'receipt' => $allowed && isset( $GLOBALS['gfa_advertising_receipt'] ) ? $GLOBALS['gfa_advertising_receipt'] : null,
	);
}

add_action( 'wp_head', function () {
	echo '<script id="gfa-advertising-config">window.GfaAdvertisingConfig=' . wp_json_encode( gfa_advertising_config(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES ) . ';</script>' . "\n";
}, -1 );
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'gfa-advertising', get_template_directory_uri() . '/assets/js/advertising.js', array( 'gfa-cookie-consent' ), GFA_VERSION, true );
} );
