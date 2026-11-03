<?php
/**
 * Standalone regression checks for the theme's SEO integration.
 * Run from the repository: php -n tools/test-seo.php
 *
 * WordPress is represented by small stateful stubs: these tests check observable
 * metadata, exclusion and authorization behavior, not WordPress internals.
 */
define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['seo_test_hooks'] = array();
$GLOBALS['seo_test_state'] = array();

function seo_test_reset( $overrides = array() ) {
	$GLOBALS['seo_test_state'] = array_replace(
		array(
			'home' => 'https://staging.example.test',
			'front' => true,
			'singular' => true,
			'type' => 'page',
			'archive' => '',
			'search' => false,
			'author' => false,
			'date' => false,
			'tax' => false,
			'category' => false,
			'tag' => false,
			'404' => false,
			'attachment' => false,
			'preview' => false,
			'admin' => false,
			'feed' => false,
			'paged' => 1,
			'page' => 1,
			'id' => 7,
			'slug' => 'home',
			'title' => 'Home',
			'content' => '<p>Volantinaggio a Milano e Rapallo.</p>',
			'excerpt' => '',
			'status' => 'publish',
			'password' => '',
			'office_status' => 'publish',
			'public' => 0,
			'permalink_structure' => '/%postname%/',
			'query_string' => '',
			'meta' => array(),
			'can_edit' => true,
			'nonce_valid' => true,
			'autosave' => false,
			'revision' => false,
			'writes' => array(),
			'queries' => array(),
			'mods' => array( 'custom_logo' => 177 ),
			'company' => array(
				'brand' => 'GFA', 'ragione' => '', 'piva' => '09182350968',
				'telefono' => '3471174430', 'email' => 'info@gfamarketing.com',
				'indirizzo_milano' => 'Viale Monte Ceneri, 62, 20155 Milano MI',
				'indirizzo_rapallo' => 'Via Libertà 46', 'anno' => '1985',
			),
		),
		$overrides
	);
	$_POST = array();
}
function seo_test_state( $key ) { return isset( $GLOBALS['seo_test_state'][ $key ] ) ? $GLOBALS['seo_test_state'][ $key ] : null; }
function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['seo_test_hooks'][ $hook ][] = array( $callback, $priority, $accepted_args ); }
function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { add_filter( $hook, $callback, $priority, $accepted_args ); }
function remove_action( $hook, $callback, $priority = 10 ) {}
function apply_filters( $hook, $value, ...$args ) {
	$callbacks = isset( $GLOBALS['seo_test_hooks'][ $hook ] ) ? $GLOBALS['seo_test_hooks'][ $hook ] : array();
	usort( $callbacks, function ( $a, $b ) { return $a[1] <=> $b[1]; } );
	foreach ( $callbacks as $item ) { $value = call_user_func_array( $item[0], array_slice( array_merge( array( $value ), $args ), 0, $item[2] ) ); }
	return $value;
}
function do_action( $hook, ...$args ) {
	foreach ( isset( $GLOBALS['seo_test_hooks'][ $hook ] ) ? $GLOBALS['seo_test_hooks'][ $hook ] : array() as $item ) { call_user_func_array( $item[0], array_slice( $args, 0, $item[2] ) ); }
}
function is_admin() { return seo_test_state( 'admin' ); }
function is_front_page() { return seo_test_state( 'front' ); }
function is_home() { return false; }
function is_singular( $types = '' ) { return seo_test_state( 'singular' ) && ( ! $types || in_array( seo_test_state( 'type' ), (array) $types, true ) ); }
function is_page( $page = '' ) { return is_singular( 'page' ) && ( ! $page || in_array( seo_test_state( 'slug' ), (array) $page, true ) || in_array( seo_test_state( 'id' ), (array) $page, true ) ); }
function is_single() { return is_singular() && ! is_page(); }
function is_post_type_archive( $type = '' ) { return (bool) seo_test_state( 'archive' ) && ( ! $type || in_array( seo_test_state( 'archive' ), (array) $type, true ) ); }
function is_search() { return seo_test_state( 'search' ); }
function is_author() { return seo_test_state( 'author' ); }
function is_date() { return seo_test_state( 'date' ); }
function is_tax() { return seo_test_state( 'tax' ); }
function is_category() { return seo_test_state( 'category' ); }
function is_tag() { return seo_test_state( 'tag' ); }
function is_archive() { return is_post_type_archive() || is_author() || is_date() || is_tax() || is_category() || is_tag(); }
function is_404() { return seo_test_state( '404' ); }
function is_attachment() { return seo_test_state( 'attachment' ); }
function is_preview() { return seo_test_state( 'preview' ); }
function is_feed() { return seo_test_state( 'feed' ); }
function is_paged() { return seo_test_state( 'paged' ) > 1; }
function get_query_var( $key, $default = '' ) { $value = seo_test_state( $key ); return null === $value ? $default : $value; }
function get_queried_object_id() { return seo_test_state( 'id' ); }
function get_the_ID() { return seo_test_state( 'id' ); }
function get_post_type( $post = null ) { return is_object( $post ) && isset( $post->post_type ) ? $post->post_type : seo_test_state( 'type' ); }
function get_post_status( $post = null ) { return is_object( $post ) && isset( $post->post_status ) ? $post->post_status : seo_test_state( 'status' ); }
function get_post( $id = null ) {
	return (object) array( 'ID' => $id ?: seo_test_state( 'id' ), 'post_name' => seo_test_state( 'slug' ), 'post_type' => seo_test_state( 'type' ), 'post_status' => seo_test_state( 'status' ), 'post_title' => seo_test_state( 'title' ), 'post_content' => seo_test_state( 'content' ), 'post_excerpt' => seo_test_state( 'excerpt' ), 'post_password' => seo_test_state( 'password' ) );
}
function get_queried_object() { return is_post_type_archive() ? (object) array( 'name' => seo_test_state( 'archive' ), 'label' => seo_test_state( 'archive' ) ) : get_post(); }
function get_post_field( $field, $post = null ) { $post = is_object( $post ) ? $post : get_post( $post ); return isset( $post->$field ) ? $post->$field : ''; }
function get_the_title( $post = null ) { return get_post_field( 'post_title', $post ); }
function get_the_excerpt( $post = null ) { return get_post_field( 'post_excerpt', $post ); }
function get_post_meta( $post_id, $key = '', $single = false ) { $meta = seo_test_state( 'meta' ); return isset( $meta[ $post_id ][ $key ] ) ? $meta[ $post_id ][ $key ] : ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['seo_test_state']['meta'][ $id ][ $key ] = $value; $GLOBALS['seo_test_state']['writes'][] = array( 'update', $id, $key, $value ); }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['seo_test_state']['meta'][ $id ][ $key ] ); $GLOBALS['seo_test_state']['writes'][] = array( 'delete', $id, $key ); }
function metadata_exists( $type, $id, $key ) { return '' !== get_post_meta( $id, $key, true ); }
function get_option( $name, $default = false ) { return 'permalink_structure' === $name ? seo_test_state( 'permalink_structure' ) : ( 'blog_public' === $name ? seo_test_state( 'public' ) : ( 'page_on_front' === $name ? 7 : ( 'blogname' === $name ? 'GFA Comunicazione' : $default ) ) ); }
function get_bloginfo( $show = '' ) { return 'language' === $show ? 'it-IT' : ( 'description' === $show ? 'Volantinaggio, stampa e promozione eventi dal 1985.' : 'GFA Comunicazione' ); }
function get_locale() { return 'it_IT'; }
function home_url( $path = '/' ) { return rtrim( seo_test_state( 'home' ), '/' ) . '/' . ltrim( $path, '/' ); }
function get_permalink( $post = null ) {
	$id = is_object( $post ) ? $post->ID : $post;
	if ( 7 === (int) $id || ( ! $id && is_front_page() ) ) { return home_url( '/' ); }
	if ( 22 === (int) $id || 23 === (int) $id ) { return home_url( '/volantinaggio/' . ( 22 === (int) $id ? 'milano' : 'rapallo' ) . '/' ); }
	if ( is_object( $post ) && isset( $post->post_name ) && 'zona' === $post->post_type ) { return home_url( '/volantinaggio/' . $post->post_name . '/' ); }
	return home_url( ( 'zona' === seo_test_state( 'type' ) ? '/volantinaggio/' : '/' ) . seo_test_state( 'slug' ) . '/' );
}
function wp_get_canonical_url( $post = null ) { return get_permalink( $post ); }
function get_post_type_archive_link( $type ) { return home_url( 'zona' === $type ? '/zone/' : '/lavori/' ); }
function get_pagenum_link( $number, $escape = true ) { return get_post_type_archive_link( seo_test_state( 'archive' ) ) . ( $number > 1 ? 'page/' . $number . '/' : '' ) . seo_test_state( 'query_string' ); }
function add_query_arg( $key, $value = null, $url = null ) {
	$args = is_array( $key ) ? $key : array( $key => $value );
	$url = is_array( $key ) ? $value : $url;
	$parts = explode( '?', $url, 2 );
	$query = array();
	if ( isset( $parts[1] ) ) { parse_str( $parts[1], $query ); }
	return $parts[0] . '?' . http_build_query( array_merge( $query, $args ) );
}
function user_trailingslashit( $value, $type = '' ) { return rtrim( $value, '/' ) . '/'; }
function trailingslashit( $value ) { return rtrim( $value, '/' ) . '/'; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function get_page_by_path( $path, $output = OBJECT, $type = 'page' ) {
	if ( 'zona' !== $type || ! in_array( $path, array( 'milano', 'rapallo' ), true ) ) { return null; }
	return (object) array( 'ID' => 'milano' === $path ? 22 : 23, 'post_name' => $path, 'post_title' => ucfirst( $path ), 'post_type' => 'zona', 'post_status' => seo_test_state( 'office_status' ) );
}
function get_theme_mod( $key, $default = false ) { $mods = seo_test_state( 'mods' ); return isset( $mods[ $key ] ) ? $mods[ $key ] : $default; }
function gfa_opt( $key ) { $company = seo_test_state( 'company' ); return isset( $company[ $key ] ) ? $company[ $key ] : ''; }
function gfa_sedi() { return array( 'Milano', 'Rapallo' ); }
function get_post_thumbnail_id( $post = null ) { return 0; }
function has_post_thumbnail( $post = null ) { return false; }
function wp_get_attachment_image_src( $id, $size = 'thumbnail', $icon = false ) { return $id ? array( home_url( '/wp-content/uploads/logo.jpg' ), 1200, 505, false ) : false; }
function wp_get_attachment_url( $id ) { return $id ? home_url( '/wp-content/uploads/logo.jpg' ) : false; }
function wp_json_encode( $data, $options = 0, $depth = 512 ) { return json_encode( $data, $options, $depth ); }
function wp_strip_all_tags( $string, $remove_breaks = false ) { return strip_tags( $string ); }
function strip_shortcodes( $string ) { return preg_replace( '/\[[^\]]+\]/', '', $string ); }
function wp_trim_words( $text, $count = 55, $more = null ) { $words = preg_split( '/\s+/', trim( strip_tags( $text ) ) ); return implode( ' ', array_slice( $words, 0, $count ) ) . ( count( $words ) > $count ? ( null === $more ? '…' : $more ) : '' ); }
function sanitize_text_field( $value ) { return trim( preg_replace( '/\s+/', ' ', strip_tags( (string) $value ) ) ); }
function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_unslash( $value ) { return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( $value ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return esc_attr( $value ); }
function esc_url( $value ) { return esc_attr( $value ); }
function esc_textarea( $value ) { return esc_attr( $value ); }
function __( $value, $domain = null ) { return $value; }
function esc_html__( $value, $domain = null ) { return esc_html( $value ); }
function esc_attr__( $value, $domain = null ) { return esc_attr( $value ); }
function checked( $checked, $current = true, $echo = true ) { $value = (string) $checked === (string) $current ? ' checked="checked"' : ''; if ( $echo ) { echo $value; } return $value; }
function wp_nonce_field( $action, $name ) { echo '<input name="' . esc_attr( $name ) . '" value="valid">'; }
function wp_verify_nonce( $nonce, $action = -1 ) { return seo_test_state( 'nonce_valid' ); }
function current_user_can( $capability, ...$args ) { return seo_test_state( 'can_edit' ); }
function wp_is_post_autosave( $id ) { return seo_test_state( 'autosave' ); }
function wp_is_post_revision( $id ) { return seo_test_state( 'revision' ); }
function add_meta_box( ...$args ) {}
function register_post_meta( ...$args ) {}
function post_password_required( $post = null ) { return false; }
function post_type_exists( $name ) { return in_array( $name, array( 'page', 'post', 'zona', 'lavoro' ), true ); }
function get_post_type_object( $name ) { return (object) array( 'public' => true, 'publicly_queryable' => true, 'has_archive' => true, 'name' => $name ); }
function is_post_type_viewable( $type ) { return true; }
function wp_register_sitemap_provider( $name, $provider ) { $GLOBALS['seo_test_state']['provider'] = $provider; }
class WP_Sitemaps_Provider { public $name; public $object_type; }

function seo_test_assert( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function seo_test_run( $name, $callback ) { $callback(); echo 'PASS ' . $name . PHP_EOL; }
function seo_test_capture( $callback ) { ob_start(); $callback(); return ob_get_clean(); }
seo_test_reset();

$plugin_constants = array( 'yoast' => 'WPSEO_VERSION', 'rankmath' => 'RANK_MATH_VERSION', 'aioseo' => 'AIOSEO_VERSION', 'seopress' => 'SEOPRESS_VERSION' );
$plugin_probe = isset( $argv[1] ) && 0 === strpos( $argv[1], '--plugin=' ) ? substr( $argv[1], 9 ) : '';
if ( isset( $plugin_constants[ $plugin_probe ] ) ) { define( $plugin_constants[ $plugin_probe ], 'test-version' ); }

require __DIR__ . '/../gfa-theme/inc/seo.php';
if ( file_exists( __DIR__ . '/../gfa-theme/inc/seo-admin.php' ) ) { require_once __DIR__ . '/../gfa-theme/inc/seo-admin.php'; }

if ( $plugin_probe ) {
	seo_test_assert( ! gfa_seo_enabled(), 'External SEO plugin must own SEO output.' );
	seo_test_assert( '' === seo_test_capture( 'gfa_seo_head' ), 'External plugin must not get duplicate metadata.' );
	seo_test_assert( 'Plugin title' === gfa_seo_document_title( 'Plugin title' ), 'External plugin title must pass through.' );
	$robots = array( 'index' => true );
	seo_test_assert( $robots === gfa_seo_robots( $robots ), 'External plugin robots must pass through.' );
	$args = array( 'post_type' => 'page' );
	seo_test_assert( $args === gfa_seo_sitemap_query( $args, 'page' ), 'External sitemap query must pass through.' );
	seo_test_assert( 'provider' === gfa_seo_sitemap_providers( 'provider', 'users' ), 'External sitemap provider must pass through.' );
	gfa_seo_register_sitemap();
	seo_test_assert( null === seo_test_state( 'provider' ), 'Do not register a second sitemap with an SEO plugin.' );
	$_POST = array( 'gfa_seo_nonce' => 'valid', 'gfa_seo_title' => 'Ignored' );
	gfa_seo_save( 7 );
	seo_test_assert( array() === seo_test_state( 'writes' ), 'Inactive theme SEO must not write metadata.' );
	echo 'PASS plugin interoperability: ' . $plugin_probe . PHP_EOL;
	exit( 0 );
}

seo_test_run( 'staging remains noindex and does not expose an archive sitemap', function () {
	seo_test_reset();
	$robots = gfa_seo_robots( array( 'index' => true, 'nofollow' => true, 'max-image-preview' => 'large' ) );
	seo_test_assert( true === $robots['noindex'] && ! isset( $robots['index'] ), 'Staging must stay noindex without contradictory index.' );
	seo_test_assert( true === $robots['nofollow'] && ! isset( $robots['follow'] ), 'Existing nofollow must be preserved.' );
	seo_test_assert( 'large' === $robots['max-image-preview'], 'Unrelated robots directives must survive.' );
	seo_test_assert( array() === gfa_seo_archive_urls(), 'Staging archives must not be proposed for indexing.' );
	gfa_seo_register_sitemap();
	seo_test_assert( null === seo_test_state( 'provider' ), 'Staging must not register the custom sitemap.' );
	seo_test_assert( 0 === get_option( 'blog_public' ), 'SEO output must not mutate site visibility.' );
} );

seo_test_run( 'singular HTML has one description and delegates canonical/title tags to core', function () {
	seo_test_reset();
	$html = seo_test_capture( 'gfa_seo_head' );
	seo_test_assert( 1 === substr_count( $html, '<meta name="description"' ), 'Exactly one meta description required.' );
	seo_test_assert( false === strpos( $html, '<title' ), 'Theme must not duplicate the core title tag.' );
	seo_test_assert( false === strpos( $html, 'rel="canonical"' ), 'Theme must not duplicate singular canonical.' );
	seo_test_assert( 1 === substr_count( $html, 'property="og:url"' ), 'Exactly one OG URL required.' );
	seo_test_assert( 'Volantinaggio a Milano e Rapallo | GFA' === gfa_seo_document_title( 'test' ), 'Home title must replace staging label.' );
	seo_test_assert( 1 === substr_count( $html, 'type="application/ld+json"' ), 'Schema must be one coherent graph.' );
} );

seo_test_run( 'service and local pages have distinct relevant metadata', function () {
	$titles = array();
	foreach ( array( 'volantinaggio', 'stampa-e-grafica', 'promozione-eventi', 'chi-siamo', 'contatti', 'milano', 'rapallo' ) as $slug ) {
		seo_test_reset( array( 'front' => false, 'id' => 50, 'slug' => $slug, 'type' => in_array( $slug, array( 'milano', 'rapallo' ), true ) ? 'zona' : 'page' ) );
		$m = gfa_seo_metadata();
		seo_test_assert( '' !== $m['description'], 'Missing description for ' . $slug );
		seo_test_assert( false === stripos( $m['description'], 'GPS' ) && false === stripos( $m['description'], 'report' ), 'Unconfirmed promises in ' . $slug );
		$titles[] = $m['title'];
	}
	seo_test_assert( count( $titles ) === count( array_unique( $titles ) ), 'Primary titles must not be duplicated.' );
} );

seo_test_run( 'archive pagination has one canonical matching OG URL', function () {
	seo_test_reset( array( 'front' => false, 'singular' => false, 'archive' => 'zona', 'paged' => 2, 'query_string' => '?utm_source=campaign' ) );
	$html = seo_test_capture( 'gfa_seo_head' );
	seo_test_assert( 1 === substr_count( $html, 'rel="canonical"' ), 'Archive needs exactly one canonical.' );
	$url = home_url( '/zone/page/2/' );
	seo_test_assert( $url === gfa_seo_canonical(), 'Paginated archive must canonicalize to its own page.' );
	seo_test_assert( false !== strpos( $html, 'property="og:url" content="' . $url . '"' ), 'OG URL must equal canonical.' );
	seo_test_assert( false !== strpos( gfa_seo_metadata()['title'], 'Pagina 2' ), 'Paginated titles must distinguish the page.' );
} );

seo_test_run( 'private or non-content routes emit no metadata or schema', function () {
	$cases = array( array( 'preview' => true ), array( 'status' => 'draft' ), array( 'status' => 'private' ), array( 'password' => 'protected' ), array( '404' => true ), array( 'search' => true ), array( 'attachment' => true ), array( 'feed' => true ) );
	foreach ( $cases as $case ) {
		seo_test_reset( $case );
		seo_test_assert( '' === seo_test_capture( 'gfa_seo_head' ), 'Unsafe metadata in context ' . json_encode( $case ) );
		seo_test_assert( '' === gfa_seo_canonical(), 'Unsafe canonical in context ' . json_encode( $case ) );
		seo_test_assert( array() === gfa_seo_schema( array() ), 'Unsafe schema in context ' . json_encode( $case ) );
	}
} );

seo_test_run( 'production robots preserve indexable pages but exclude auxiliary routes', function () {
	seo_test_reset( array( 'public' => 1 ) );
	seo_test_assert( array( 'max-image-preview' => 'large' ) === gfa_seo_robots( array( 'max-image-preview' => 'large' ) ), 'Ordinary public page must remain indexable.' );
	foreach ( array( 'search', 'author', 'date', 'tax', 'category', 'tag', '404', 'attachment', 'preview' ) as $key ) {
		seo_test_reset( array( 'public' => 1, $key => true ) );
		$robots = gfa_seo_robots( array( 'index' => true ) );
		seo_test_assert( ! isset( $robots['index'] ) && true === $robots['noindex'], 'Missing noindex for ' . $key );
	}
	seo_test_reset( array( 'public' => 1, 'meta' => array( 7 => array( '_gfa_seo_noindex' => '1' ) ) ) );
	seo_test_assert( true === gfa_seo_robots( array() )['noindex'], 'Editor noindex must be honored.' );
	seo_test_assert( array() === gfa_seo_schema( gfa_seo_metadata() ), 'Noindex content must not get business schema.' );
	seo_test_reset( array( 'public' => 1, 'password' => 'already-unlocked-in-browser' ) );
	seo_test_assert( false === post_password_required(), 'Test simulates the visitor having unlocked the password.' );
	seo_test_assert( true === gfa_seo_robots( array() )['noindex'] && '' === seo_test_capture( 'gfa_seo_head' ), 'Unlocked protected pages still must not receive indexable metadata.' );
} );

seo_test_run( 'structured data contains only two confirmed offices and correct local service', function () {
	seo_test_reset();
	$graph = gfa_seo_schema( gfa_seo_metadata() )['@graph'];
	$offices = array_values( array_filter( $graph, function ( $node ) { return 'LocalBusiness' === $node['@type']; } ) );
	seo_test_assert( 2 === count( $offices ), 'Exactly Milano and Rapallo required.' );
	seo_test_assert( '20155' === $offices[0]['address']['postalCode'], 'Confirmed Milano postal code missing.' );
	seo_test_assert( ! isset( $offices[1]['address']['postalCode'] ), 'Do not invent Rapallo postal code.' );
	foreach ( $offices as $office ) {
		seo_test_assert( '+393471174430' === $office['telephone'], 'Telephone must use international form.' );
		seo_test_assert( 'PostalAddress' === $office['address']['@type'], 'Address must be structured.' );
		foreach ( array( 'aggregateRating', 'review', 'openingHours', 'geo' ) as $field ) { seo_test_assert( ! isset( $office[ $field ] ), 'Do not invent unsupported business data: ' . $field ); }
	}
	seo_test_reset( array( 'front' => false, 'type' => 'zona', 'id' => 22, 'slug' => 'milano' ) );
	$graph = gfa_seo_schema( gfa_seo_metadata() )['@graph'];
	$services = array_values( array_filter( $graph, function ( $node ) { return 'Service' === $node['@type']; } ) );
	seo_test_assert( 1 === count( $services ) && 'Milano' === $services[0]['areaServed'][0]['name'], 'Local service must target its own city.' );
	seo_test_assert( home_url( '/#sede-milano' ) === $services[0]['provider']['@id'], 'Local service must reference the corresponding office.' );
	seo_test_reset( array( 'office_status' => 'draft' ) );
	foreach ( gfa_seo_offices() as $office ) { seo_test_assert( '' === $office['url'], 'Draft office URLs must not appear in schema.' ); }
	seo_test_reset( array( 'meta' => array( 22 => array( '_gfa_seo_noindex' => '1' ) ) ) );
	seo_test_assert( '' === gfa_seo_offices()['milano']['url'], 'Noindex office URLs must not appear in schema.' );
	$GLOBALS['seo_test_state']['company']['indirizzo_milano'] = 'Via Nuova 10, Milano';
	seo_test_assert( ! isset( gfa_seo_offices()['milano']['postal'] ), 'Changed street must not inherit an obsolete postal code.' );
} );

seo_test_run( 'domain migration updates canonical, social image and schema URLs', function () {
	seo_test_reset( array( 'home' => 'https://www.gfa-production.example', 'front' => false, 'id' => 13, 'slug' => 'contatti' ) );
	$html = seo_test_capture( 'gfa_seo_head' );
	seo_test_assert( false === strpos( $html, 'staging.example.test' ), 'Staging hostname must not leak after migration.' );
	seo_test_assert( 'https://www.gfa-production.example/contatti/' === gfa_seo_canonical(), 'Canonical must follow WordPress home URL.' );
	seo_test_assert( false !== strpos( $html, 'https://www.gfa-production.example/wp-content/uploads/logo.jpg' ), 'Social image must use current attachment URL.' );
	seo_test_assert( false !== strpos( $html, 'https://www.gfa-production.example/#organization' ), 'Schema IDs must follow current origin.' );
} );

seo_test_run( 'office schema follows published location contact overrides and omits unavailable data', function () {
	seo_test_reset( array( 'front' => false, 'type' => 'zona', 'id' => 22, 'slug' => 'milano', 'meta' => array(
		22 => array( 'gfa_indirizzo' => 'Via Esempio 10, 20121 Milano MI', 'gfa_telefono' => '02 34567890' ),
		23 => array( 'gfa_indirizzo' => 'Via Prova 8, 16035 Rapallo GE', 'gfa_telefono' => '0185 123456' ),
	) ) );
	$offices = gfa_seo_offices();
	seo_test_assert( 'Via Esempio 10' === $offices['milano']['street'] && '20121' === $offices['milano']['postal'], 'Published location address must override company default and parse its explicit postal code.' );
	seo_test_assert( '+390234567890' === $offices['milano']['phone'], 'Italian landline must preserve its area-code zero and gain country code.' );
	seo_test_assert( '16035' === $offices['rapallo']['postal'] && 'GE' === $offices['rapallo']['region'] && '+390185123456' === $offices['rapallo']['phone'], 'Confirmed Rapallo location details must be parsed without retaining missing-default assumptions.' );
	$graph = gfa_seo_schema( gfa_seo_metadata() )['@graph'];
	$businesses = array_values( array_filter( $graph, function ( $node ) { return 'LocalBusiness' === $node['@type']; } ) );
	$services = array_values( array_filter( $graph, function ( $node ) { return 'Service' === $node['@type']; } ) );
	seo_test_assert( 1 === count( $businesses ) && 'Via Esempio 10' === $businesses[0]['address']['streetAddress'] && '+390234567890' === $businesses[0]['telephone'], 'Rendered local schema must use the same contact overrides as the location template.' );
	seo_test_assert( $businesses[0]['@id'] === $services[0]['provider']['@id'], 'Service must reference the emitted local business.' );
	seo_test_assert( '+393471174430' === gfa_seo_phone( '0039 3471174430' ) && '' === gfa_seo_phone( 'invalid' ), 'International numbers normalize and invalid values are omitted.' );
	$GLOBALS['seo_test_state']['meta'][22]['gfa_telefono'] = 'invalid';
	$graph = gfa_seo_schema( gfa_seo_metadata() )['@graph'];
	foreach ( $graph as $node ) { if ( 'LocalBusiness' === $node['@type'] ) { seo_test_assert( ! isset( $node['telephone'] ), 'An invalid explicit local phone must not emit a malformed telephone.' ); } }
	$GLOBALS['seo_test_state']['office_status'] = 'draft';
	seo_test_assert( 'Viale Monte Ceneri, 62' === gfa_seo_offices()['milano']['street'], 'Unpublished location contact overrides must not leak into public schema.' );
	$GLOBALS['seo_test_state']['company']['indirizzo_milano'] = '';
	$graph = gfa_seo_schema( gfa_seo_metadata() )['@graph'];
	foreach ( $graph as $node ) {
		seo_test_assert( 'LocalBusiness' !== $node['@type'], 'A local business without either address source must be omitted.' );
		if ( 'Service' === $node['@type'] ) { seo_test_assert( home_url( '/#organization' ) === $node['provider']['@id'], 'Missing local business must fall back to an existing organization provider.' ); }
	}
} );

seo_test_run( 'editor overrides are escaped in attributes and JSON scripts', function () {
	$payload = '&quot;><script>alert("x")</script>';
	seo_test_reset( array( 'meta' => array( 7 => array( '_gfa_seo_title' => $payload, '_gfa_seo_description' => $payload ) ) ) );
	$GLOBALS['seo_test_state']['company']['ragione'] = '</script><script>alert("schema")</script>';
	$html = seo_test_capture( 'gfa_seo_head' );
	seo_test_assert( 1 === substr_count( $html, '<script' ) && 1 === substr_count( $html, '</script>' ), 'Metadata must not inject an executable script.' );
	seo_test_assert( false === strpos( $html, '</script><script>' ), 'JSON must escape script-closing characters.' );
	preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $html, $match );
	$decoded = json_decode( $match[1], true );
	seo_test_assert( is_array( $decoded ) && JSON_ERROR_NONE === json_last_error(), 'Escaped JSON-LD must remain valid JSON.' );
	seo_test_assert( false !== strpos( $html, '&quot;' ), 'Quotes must be escaped in metadata attributes.' );
	seo_test_assert( false === strpos( gfa_seo_document_title( '' ), '<' ), 'Direct title filter must not allow markup.' );
	$GLOBALS['seo_test_state']['meta'][7]['_gfa_seo_title'] = '&lt;/title&gt;&lt;script&gt;alert(1)&lt;/script&gt; &amp; GFA';
	$title = gfa_seo_document_title( '' );
	seo_test_assert( false === strpos( $title, '<' ) && false !== strpos( $title, '&amp;' ), 'Encoded title markup must be stripped and ampersands escaped.' );
} );

function seo_test_matches_meta_query( $query, $meta ) {
	if ( isset( $query['key'] ) ) {
		$exists = array_key_exists( $query['key'], $meta );
		if ( 'NOT EXISTS' === $query['compare'] ) { return ! $exists; }
		if ( ! $exists ) { return false; }
		return '!=' === $query['compare'] ? (string) $meta[ $query['key'] ] !== (string) $query['value'] : (string) $meta[ $query['key'] ] === (string) $query['value'];
	}
	$results = array();
	foreach ( $query as $key => $clause ) { if ( 'relation' !== $key ) { $results[] = seo_test_matches_meta_query( $clause, $meta ); } }
	return isset( $query['relation'] ) && 'OR' === $query['relation'] ? in_array( true, $results, true ) : ! in_array( false, $results, true );
}

seo_test_run( 'sitemap excludes noindex/password content without dropping valid or prefiltered pages', function () {
	seo_test_reset( array( 'public' => 1 ) );
	$args = gfa_seo_sitemap_query( array( 'meta_query' => array( array( 'key' => 'language', 'value' => 'it', 'compare' => '=' ) ) ), 'page' );
	seo_test_assert( false === $args['has_password'], 'Password pages must be excluded.' );
	seo_test_assert( seo_test_matches_meta_query( $args['meta_query'], array( 'language' => 'it' ) ), 'Pages without SEO metadata must remain eligible.' );
	seo_test_assert( seo_test_matches_meta_query( $args['meta_query'], array( 'language' => 'it', '_gfa_seo_noindex' => '0' ) ), 'Explicit indexable pages must remain eligible.' );
	seo_test_assert( ! seo_test_matches_meta_query( $args['meta_query'], array( 'language' => 'it', '_gfa_seo_noindex' => '1' ) ), 'Noindex pages must be excluded.' );
	seo_test_assert( ! seo_test_matches_meta_query( $args['meta_query'], array( 'language' => 'en' ) ), 'Existing sitemap filters must remain effective.' );
	seo_test_assert( false === gfa_seo_sitemap_providers( 'users-provider', 'users' ) && false === gfa_seo_sitemap_providers( 'taxonomy-provider', 'taxonomies' ), 'Noindex archive providers must be excluded.' );
	seo_test_assert( 'posts-provider' === gfa_seo_sitemap_providers( 'posts-provider', 'posts' ), 'Content sitemap provider must survive.' );
	gfa_seo_register_sitemap();
	$provider = seo_test_state( 'provider' );
	seo_test_assert( 2 === count( $provider->get_url_list( 1 ) ) && array() === $provider->get_url_list( 2 ), 'Archive sitemap must contain only its two real URLs.' );
	seo_test_assert( 1 === $provider->get_max_num_pages(), 'Archive count must match its URL list.' );
} );

seo_test_run( 'archive exclusion keeps robots, structured data and sitemap consistent', function () {
	seo_test_reset( array( 'public' => 1, 'front' => false, 'singular' => false, 'archive' => 'zona' ) );
	add_filter( 'gfa_seo_archive_indexable', function ( $indexable, $type ) { return 'zona' !== $type && $indexable; }, 10, 2 );
	seo_test_assert( true === gfa_seo_robots( array() )['noindex'], 'Excluded archive must have noindex.' );
	seo_test_assert( array() === gfa_seo_schema( gfa_seo_metadata() ), 'Excluded archive must not have business schema.' );
	$entries = gfa_seo_archive_urls();
	seo_test_assert( 1 === count( $entries ) && home_url( '/lavori/' ) === $entries[0]['loc'], 'Excluded archive must not occur in sitemap.' );
	unset( $GLOBALS['seo_test_hooks']['gfa_seo_archive_indexable'] );
} );

seo_test_run( 'metadata saving requires a valid nonce, permissions and an ordinary supported post', function () {
	$invalid = array( array( 'nonce_valid' => false ), array( 'can_edit' => false ), array( 'revision' => true ), array( 'autosave' => true ), array( 'type' => 'attachment' ) );
	foreach ( $invalid as $case ) {
		seo_test_reset( $case );
		$_POST = array( 'gfa_seo_nonce' => 'valid', 'gfa_seo_title' => 'Changed', 'gfa_seo_description' => 'Changed', 'gfa_seo_noindex' => '1' );
		gfa_seo_save( 7 );
		seo_test_assert( array() === seo_test_state( 'writes' ), 'Unauthorized metadata write in ' . json_encode( $case ) );
	}
	seo_test_reset();
	$_POST = array( 'gfa_seo_title' => 'No nonce' );
	gfa_seo_save( 7 );
	seo_test_assert( array() === seo_test_state( 'writes' ), 'Missing nonce must prevent writes.' );
	$_POST['gfa_seo_nonce'] = array( 'invalid' );
	gfa_seo_save( 7 );
	seo_test_assert( array() === seo_test_state( 'writes' ), 'Malformed nonce must prevent writes without throwing.' );
} );

seo_test_run( 'authorized save sanitizes input and clearing overrides restores defaults', function () {
	seo_test_reset();
	$_POST = array( 'gfa_seo_nonce' => 'valid', 'gfa_seo_title' => '<b>Titolo</b>', 'gfa_seo_description' => " Testo\n locale ", 'gfa_seo_noindex' => '1' );
	gfa_seo_save( 7 );
	seo_test_assert( 'Titolo' === get_post_meta( 7, '_gfa_seo_title', true ), 'Title must be sanitized.' );
	seo_test_assert( 'Testo locale' === get_post_meta( 7, '_gfa_seo_description', true ), 'Description must be sanitized.' );
	seo_test_assert( '1' === get_post_meta( 7, '_gfa_seo_noindex', true ), 'Noindex must save.' );
	$_POST = array( 'gfa_seo_nonce' => 'valid', 'gfa_seo_title' => '', 'gfa_seo_description' => '' );
	gfa_seo_save( 7 );
	seo_test_assert( '' === get_post_meta( 7, '_gfa_seo_title', true ) && '' === get_post_meta( 7, '_gfa_seo_noindex', true ), 'Blank fields and unchecked flag must clear overrides.' );
	seo_test_assert( 'Volantinaggio a Milano e Rapallo | GFA' === gfa_seo_metadata()['title'], 'Cleared title must fall back to defaults.' );
} );

foreach ( array_keys( $plugin_constants ) as $plugin ) {
	$command = escapeshellarg( PHP_BINARY ) . ' -n ' . escapeshellarg( __FILE__ ) . ' ' . escapeshellarg( '--plugin=' . $plugin );
	exec( $command, $output, $status );
	seo_test_assert( 0 === $status, 'Plugin isolation probe failed: ' . $plugin . '\n' . implode( '\n', $output ) );
	echo implode( PHP_EOL, $output ) . PHP_EOL;
	$output = array();
}
echo 'All SEO regression checks passed.' . PHP_EOL;

