<?php
/** Offline WordPress boundary tests. Run: php -n tools/test-advertising.php */
define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['ad_test'] = array();
$GLOBALS['ad_hooks'] = array();
function ad_reset( $extra = array() ) {
	$GLOBALS['ad_test'] = array_replace( array(
		'options' => array(), 'transients' => array(), 'errors' => array(), 'mail' => array(),
		'mail_result' => true, 'nonce_valid' => true, 'clock' => time(), 'delete_ok' => true,
		'referer' => 'https://gfa.example.test/contatti/', 'can_manage' => true,
		'public_context' => true, 'nonce' => 'valid', 'uuid_count' => 0,
	), $extra );
	$_POST = array(); $_GET = array(); $_SERVER['REQUEST_URI'] = '/contatti/';
}
function ad_state( $key ) { return $GLOBALS['ad_test'][ $key ] ?? null; }
function add_action( $hook, $callback, $priority = 10, $accepted = 1 ) { $GLOBALS['ad_hooks'][ $hook ][] = array( $callback, $priority, $accepted ); }
function add_filter( $hook, $callback, $priority = 10, $accepted = 1 ) { add_action( $hook, $callback, $priority, $accepted ); }
function apply_filters( $hook, $value, ...$args ) { return $value; }
function register_setting() {}
function add_options_page() {}
function add_settings_section() {}
function add_settings_field() {}
function settings_fields() {}
function do_settings_sections() {}
function submit_button() {}
function settings_errors() {}
function add_settings_error( $setting, $code, $message, $type = 'error' ) { $GLOBALS['ad_test']['errors'][] = compact( 'setting', 'code', 'message', 'type' ); }
function get_option( $key, $default = false ) { return ad_state( 'options' )[ $key ] ?? ( 'admin_email' === $key ? 'admin@example.test' : $default ); }
function current_user_can() { return ad_state( 'can_manage' ); }
function wp_die( $message = '' ) { throw new RuntimeException( 'wp_die: ' . $message ); }
function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( preg_replace( '/[\r\n\t]+/', ' ', strip_tags( (string) $value ) ) ) : ''; }
function sanitize_textarea_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; }
function sanitize_email( $value ) { return filter_var( $value, FILTER_SANITIZE_EMAIL ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function wp_unslash( $value ) { return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( (string) $value ); }
function absint( $value ) { return abs( (int) $value ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return esc_attr( $value ); }
function esc_url( $value ) { return (string) $value; }
function esc_url_raw( $value ) { return (string) $value; }
function checked( $checked, $current = true, $echo = true ) { $result = (string) $checked === (string) $current ? 'checked="checked"' : ''; if ( $echo ) { echo $result; } return $result; }
function __( $value ) { return $value; }
function esc_html__( $value ) { return esc_html( $value ); }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function home_url( $path = '/' ) { return 'https://gfa.example.test/' . ltrim( $path, '/' ); }
function admin_url( $path = '' ) { return home_url( 'wp-admin/' . ltrim( $path, '/' ) ); }
function get_template_directory_uri() { return home_url( '/wp-content/themes/gfa-theme' ); }
function wp_enqueue_script() {}
function wp_add_inline_script() {}
function wp_get_referer() { return ad_state( 'referer' ); }
function wp_verify_nonce( $nonce, $action ) { return ad_state( 'nonce_valid' ) && 'valid' === $nonce; }
function wp_generate_uuid4() { $GLOBALS['ad_test']['uuid_count']++; return sprintf( '9b29c72e-2fc0-45ef-9292-%012d', ad_state( 'uuid_count' ) ); }
function wp_rand( $min = 0, $max = 0 ) { return random_int( $min, $max ?: PHP_INT_MAX ); }
function set_transient( $key, $value, $ttl ) { $GLOBALS['ad_test']['transients'][ $key ] = array( 'value' => $value, 'until' => ad_state( 'clock' ) + $ttl, 'ttl' => $ttl ); return true; }
function get_transient( $key ) {
	$item = ad_state( 'transients' )[ $key ] ?? null;
	return $item && $item['until'] > ad_state( 'clock' ) ? $item['value'] : false;
}
function delete_transient( $key ) {
	if ( ! ad_state( 'delete_ok' ) || ! isset( $GLOBALS['ad_test']['transients'][ $key ] ) ) { return false; }
	unset( $GLOBALS['ad_test']['transients'][ $key ] ); return true;
}
function is_email( $value ) { return (bool) filter_var( $value, FILTER_VALIDATE_EMAIL ); }
function gfa_opt( $key ) { return in_array( $key, array( 'email', 'email_preventivi' ), true ) ? 'info@example.test' : ''; }
function current_time( $type ) { return '2026-11-02 12:00:00'; }
function wp_mail( ...$args ) { $GLOBALS['ad_test']['mail'][] = $args; return ad_state( 'mail_result' ); }
class AdRedirect extends RuntimeException { public $url; public function __construct( $url ) { $this->url = $url; parent::__construct( $url ); } }
function wp_safe_redirect( $url, $status = 302 ) { throw new AdRedirect( $url ); }
function ad_url( $url, $changes ) {
	$parts = parse_url( $url ?: home_url( '/contatti/' ) ); $query = array(); parse_str( $parts['query'] ?? '', $query );
	foreach ( $changes as $key => $value ) { if ( false === $value ) { unset( $query[ $key ] ); } else { $query[ $key ] = $value; } }
	return ( $parts['scheme'] ?? 'https' ) . '://' . ( $parts['host'] ?? 'gfa.example.test' ) . ( $parts['path'] ?? '/' ) . ( $query ? '?' . http_build_query( $query ) : '' ) . ( isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '' );
}
function remove_query_arg( $keys, $url = false ) { return ad_url( $url, array_fill_keys( (array) $keys, false ) ); }
function add_query_arg( $key, $value = null, $url = null ) { return is_array( $key ) ? ad_url( $value, $key ) : ad_url( $url, array( $key => $value ) ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_validate_redirect( $url, $fallback = '' ) { return 'gfa.example.test' === parse_url( $url, PHP_URL_HOST ) ? $url : $fallback; }
function is_admin() { return false; }
function is_feed() { return false; }
function is_preview() { return false; }
function is_404() { return false; }
function is_search() { return false; }
function is_attachment() { return false; }
function is_singular( $type = '' ) { return true; }
function is_front_page() { return false; }
function is_post_type_archive() { return false; }
function get_queried_object_id() { return 13; }
function get_post_status() { return ad_state( 'public_context' ) ? 'publish' : 'draft'; }
function get_post_field( $key ) { return ''; }
function get_post_meta() { return ''; }
function post_password_required() { return false; }
function get_permalink() { return home_url( '/contatti/' ); }
function wp_get_canonical_url() { return get_permalink(); }
function nocache_headers() { $GLOBALS['ad_test']['nocache'] = true; }

ad_reset();
require __DIR__ . '/../gfa-theme/inc/advertising.php';
require __DIR__ . '/../gfa-theme/inc/quote-form.php';

function ad_assert( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function ad_equal( $expected, $actual, $message ) { ad_assert( $expected === $actual, $message . ' — actual: ' . var_export( $actual, true ) ); }
function ad_enabled() {
	return array( 'google_enabled' => true, 'google_id' => 'AW-123456789', 'google_label' => 'test_Label-01', 'meta_enabled' => true, 'meta_id' => '123456789012345' );
}
function ad_configure() { $GLOBALS['ad_test']['options']['gfa_advertising'] = ad_enabled(); }
function ad_submission( $overrides = array() ) {
	$_POST = array_replace( array(
		'gfa_quote_nonce' => 'valid', 'gfa_website' => '', 'gfa_servizio' => 'Volantinaggio', 'gfa_zona' => 'Milano',
		'gfa_quantita' => 'Fino a 5.000', 'gfa_nome' => 'Example Person', 'gfa_email' => 'private@example.test',
		'gfa_telefono' => '3331234567', 'gfa_dettagli' => 'Private campaign details', 'gfa_privacy' => '1',
		'gfa_google_ads_consent' => '1', 'gfa_meta_ads_consent' => '1',
	), $overrides );
	try { gfa_handle_quote(); } catch ( AdRedirect $redirect ) { return $redirect->url; }
	throw new RuntimeException( 'Expected a redirect' );
}

$checks = array();
$checks['Configuration starts disabled and validates provider IDs rather than accepting script markup'] = function () {
	ad_reset(); $defaults = gfa_advertising_defaults();
	ad_assert( ! $defaults['google_enabled'] && ! $defaults['meta_enabled'], 'Default switches must be off' );
	$clean = gfa_advertising_sanitize_settings( ad_enabled() );
	ad_equal( 'AW-123456789', $clean['google_id'], 'Valid Google ID retained' ); ad_equal( '123456789012345', $clean['meta_id'], 'Valid Meta ID retained' );
	$bad = gfa_advertising_sanitize_settings( array( 'google_enabled' => '1', 'google_id' => 'AW-123456789<script>', 'google_label' => 'bad/label', 'meta_enabled' => '1', 'meta_id' => '<script>123456789</script>' ) );
	ad_assert( ! $bad['google_enabled'] && ! $bad['meta_enabled'], 'Invalid input must not activate a tag' );
	ad_assert( false === strpos( json_encode( $bad ), '<script>' ), 'No arbitrary markup is stored' );
};
$checks['Receipt is random, short-lived, PII-free and can be consumed only once'] = function () {
	ad_reset(); ad_configure();
	$token = gfa_quote_receipt_create( array( 'googleAds' => true, 'metaAds' => true ) );
	ad_assert( 1 === preg_match( '/^[a-f0-9]{64}$/D', $token ), 'Receipt token is a 256-bit opaque value' );
	$items = ad_state( 'transients' ); ad_equal( 1, count( $items ), 'One transient created' );
	$item = reset( $items ); ad_equal( 600, $item['ttl'], 'Receipt expires after ten minutes' );
	ad_assert( false === strpos( json_encode( $item ), 'private@example.test' ), 'No contact details in receipt' );
	$receipt = gfa_quote_receipt_consume( $token );
	ad_assert( is_array( $receipt ) && ! empty( $receipt['eventId'] ), 'Valid receipt accepted' );
	ad_equal( true, $receipt['googleAds'], 'Google eligibility retained' ); ad_equal( true, $receipt['metaAds'], 'Meta eligibility retained' );
	ad_equal( null, gfa_quote_receipt_consume( $token ), 'Receipt cannot be reused' );
};
$checks['Malformed persisted activation switches cannot enable a configured tag'] = function () {
	foreach ( array( 'false', 'true', 'yes', array( '1' ) ) as $switch ) {
		ad_reset(); ad_configure();
		$GLOBALS['ad_test']['options']['gfa_advertising']['google_enabled'] = $switch;
		$GLOBALS['ad_test']['options']['gfa_advertising']['meta_enabled'] = $switch;
		$config = gfa_advertising_config();
		ad_equal( false, $config['google']['enabled'], 'Google activation must be explicit' );
		ad_equal( false, $config['meta']['enabled'], 'Meta activation must be explicit' );
		ad_equal( '', gfa_quote_receipt_create( array( 'googleAds' => true, 'metaAds' => true ) ), 'Malformed switches cannot issue a proof' );
	}
};
$checks['Forged, expired or already-claimed proof never emits a receipt'] = function () {
	ad_reset(); ad_configure();
	foreach ( array( '', 'inviato', str_repeat( 'f', 64 ), array( 'attack' ) ) as $fake ) { ad_equal( null, gfa_quote_receipt_consume( $fake ), 'Invalid proof rejected' ); }
	$token = gfa_quote_receipt_create( array( 'googleAds' => true, 'metaAds' => false ) );
	$GLOBALS['ad_test']['clock'] += 601; ad_equal( null, gfa_quote_receipt_consume( $token ), 'Expired proof rejected' );
	ad_reset(); ad_configure(); $token = gfa_quote_receipt_create( array( 'googleAds' => true, 'metaAds' => false ) );
	$GLOBALS['ad_test']['delete_ok'] = false; ad_equal( null, gfa_quote_receipt_consume( $token ), 'Failed atomic claim cannot count' );
};
$checks['Expired or malformed cached receipt values fail closed even when cache TTL has not elapsed'] = function () {
	foreach ( array( array( 'expires' => time() - 1 ), array( 'eventId' => array( 'invalid' ) ), array( 'googleAds' => 'false' ), array( 'metaAds' => 'true' ) ) as $override ) {
		ad_reset(); ad_configure(); $token = gfa_quote_receipt_create( array( 'googleAds' => true, 'metaAds' => true ) );
		$key = 'gfa_lead_' . hash( 'sha256', $token );
		$GLOBALS['ad_test']['transients'][ $key ]['value'] = array_replace( $GLOBALS['ad_test']['transients'][ $key ]['value'], $override );
		ad_equal( null, gfa_quote_receipt_consume( $token ), 'Malformed cached value is ignored' );
	}
};
$checks['Configuration revision changes with provider activation or account changes but stays stable otherwise'] = function () {
	ad_reset(); $off = gfa_advertising_config();
	ad_equal( $off['revision'], gfa_advertising_config()['revision'], 'Unchanged settings have stable revision' );
	ad_configure(); $on = gfa_advertising_config(); ad_assert( $off['revision'] !== $on['revision'], 'Activation invalidates old choices' );
	$GLOBALS['ad_test']['options']['gfa_advertising']['meta_id'] = '123456789012346';
	ad_assert( $on['revision'] !== gfa_advertising_config()['revision'], 'Changing recipient account invalidates old choices' );
	$GLOBALS['ad_test']['public_context'] = false; $private = gfa_advertising_config();
	ad_equal( false, $private['trackingAllowed'], 'Unpublished content cannot activate tracking' );
};
$checks['Receipt needs configured providers and explicit provider-specific submission consent'] = function () {
	ad_reset(); ad_equal( '', gfa_quote_receipt_create( array( 'googleAds' => true, 'metaAds' => true ) ), 'Unconfigured tags cannot issue a receipt' );
	ad_configure();
	foreach ( array( array(), array( 'googleAds' => false, 'metaAds' => false ), array( 'googleAds' => 'true', 'metaAds' => '1' ) ) as $consent ) {
		ad_equal( '', gfa_quote_receipt_create( $consent ), 'Consent is strict, not truthy' );
	}
	$token = gfa_quote_receipt_create( array( 'googleAds' => false, 'metaAds' => true ) );
	$receipt = gfa_quote_receipt_consume( $token ); ad_equal( false, $receipt['googleAds'], 'No Google eligibility added' ); ad_equal( true, $receipt['metaAds'], 'Meta eligibility kept' );
};
$checks['Only a valid quote accepted by wp_mail receives a conversion proof'] = function () {
	ad_reset(); ad_configure(); $url = ad_submission();
	ad_equal( 1, count( ad_state( 'mail' ) ), 'One mail handoff attempted' );
	parse_str( parse_url( $url, PHP_URL_QUERY ), $query );
	ad_equal( 'inviato', $query['preventivo'], 'Success redirect retained' );
	ad_assert( ! empty( $query['gfa_receipt'] ), 'Successful request gets proof' );
	$stored = json_encode( ad_state( 'transients' ) );
	foreach ( array( 'Example Person', 'private@example.test', '3331234567', 'Private campaign details', 'Milano' ) as $pii ) { ad_assert( false === strpos( $stored, $pii ), 'Receipt must not store submitted personal data' ); }
};
$checks['Honeypot, nonce failure, invalid fields and mail failure never count as leads'] = function () {
	foreach ( array(
		array( array( 'gfa_website' => 'bot' ), array(), 0 ),
		array( array(), array( 'nonce_valid' => false ), 0 ),
		array( array( 'gfa_email' => 'invalid' ), array(), 0 ),
		array( array( 'gfa_nome' => '' ), array(), 0 ),
		array( array( 'gfa_privacy' => '' ), array(), 0 ),
		array( array(), array( 'mail_result' => false ), 1 ),
	) as $case ) {
		ad_reset( $case[1] ); ad_configure(); $url = ad_submission( $case[0] );
		ad_equal( $case[2], count( ad_state( 'mail' ) ), 'Mail calls match valid/invalid path' );
		ad_equal( 0, count( ad_state( 'transients' ) ), 'Failure or honeypot has no conversion proof' );
		ad_assert( false === strpos( $url, 'gfa_receipt=' ), 'No proof in failure redirect' );
	}
};
$checks['A successful non-consenting request still works but cannot be counted later'] = function () {
	ad_reset(); ad_configure(); $url = ad_submission( array( 'gfa_google_ads_consent' => '', 'gfa_meta_ads_consent' => '' ) );
	ad_equal( 1, count( ad_state( 'mail' ) ), 'Refusing advertising does not block the quote' );
	ad_assert( false !== strpos( $url, 'preventivo=inviato' ), 'Normal success shown' );
	ad_equal( 0, count( ad_state( 'transients' ) ), 'No retroactive receipt created' );
};
$checks['Malformed required fields, service or privacy confirmation reject mail and conversion proof'] = function () {
	foreach ( array(
		array( 'gfa_quote_nonce' => array( 'valid' ) ),
		array( 'gfa_zona' => '' ), array( 'gfa_servizio' => 'Unknown service' ),
		array( 'gfa_nome' => array( 'Example Person' ) ), array( 'gfa_email' => array( 'private@example.test' ) ),
		array( 'gfa_zona' => array( 'Milano' ) ), array( 'gfa_servizio' => array( 'Volantinaggio' ) ),
		array( 'gfa_privacy' => 'true' ), array( 'gfa_privacy' => 'yes' ), array( 'gfa_privacy' => array( '1' ) ),
	) as $fields ) {
		ad_reset(); ad_configure(); $url = ad_submission( $fields );
		ad_equal( 0, count( ad_state( 'mail' ) ), 'Malformed submission must not send mail' );
		ad_equal( 0, count( ad_state( 'transients' ) ), 'Malformed submission must not create a proof' );
		ad_assert( false === strpos( $url, 'gfa_receipt=' ), 'Malformed submission redirect cannot carry a proof' );
	}
};

$passed = 0;
foreach ( $checks as $name => $run ) {
	try { $run(); echo 'PASS ' . $name . PHP_EOL; $passed++; }
	catch ( Throwable $error ) { fwrite( STDERR, 'FAIL ' . $name . ': ' . $error->getMessage() . PHP_EOL ); exit( 1 ); }
}
echo 'Advertising PHP: ' . $passed . ' checks passed.' . PHP_EOL;
