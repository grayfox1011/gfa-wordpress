<?php
// Rende front-page.php del tema come HTML statico, con funzioni WordPress simulate.
define( 'ABSPATH', __DIR__ );
$T = dirname( __DIR__ ) . '/gfa-theme';
function __( $s ) { return $s; }
function esc_html__( $s ) { return htmlspecialchars( $s ); }
function esc_html_e( $s ) { echo htmlspecialchars( $s ); }
function esc_attr_e( $s ) { echo htmlspecialchars( $s, ENT_QUOTES ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function wp_kses_post( $s ) { return $s; }
function get_theme_mod( $k, $d = '' ) { return $d; }
function get_template_directory() { global $T; return $T; }
function home_url( $p = '/' ) {
	$map = array( '/volantinaggio/' => '#servizi', '/stampa-e-grafica/' => '#servizi', '/promozione-eventi/' => '#servizi', '/lavori/' => '#lavori', '/zone/' => '#zone', '/chi-siamo/' => '#metodo', '/#preventivo' => '#preventivo', '/' => '#top', '/franchising/' => '#zone', '/cookie-policy/' => '#top', '/privacy-policy/' => '#top' );
	return isset( $map[ $p ] ) ? $map[ $p ] : '#zone';
}
function sanitize_title( $s ) { return strtolower( preg_replace( '/\W+/', '-', $s ) ); }
function sanitize_key( $s ) { return $s; }
function wp_unslash( $s ) { return $s; }
function admin_url( $s ) { return '#preventivo'; }
function wp_nonce_field() {}
function get_privacy_policy_url() { return ''; }
function has_custom_logo() { return false; }
function wp_nav_menu( $a ) { call_user_func( $a['fallback_cb'] ); }
function language_attributes() {}
function bloginfo() {}
function body_class() {}
function wp_head() {}
function wp_body_open() {}
function wp_footer() {}
function get_posts() { return array(); }
function get_permalink() { return '#'; }
class WP_Query { function __construct( $a ) {} function have_posts() { return false; } }
function add_action() {} function add_filter() {} function remove_action() {}
function get_header() { global $T; ob_start(); include $T . '/header.php'; $h = ob_get_clean(); echo substr( $h, strpos( $h, '<a class="skip"' ) ); }
function get_footer() { global $T; ob_start(); include $T . '/footer.php'; $h = ob_get_clean(); echo substr( $h, 0, strpos( $h, '</body>' ) ); }
function get_template_part( $slug, $name = null, $args = array( 'prototype' => true ) ) { global $T; include $T . '/' . $slug . '.php'; }
require $T . '/inc/helpers.php';
function gfa_fallback_menu() {
	echo '<ul><li><a href="#servizi">Volantinaggio</a></li><li><a href="#servizi">Stampa e grafica</a></li><li><a href="#servizi">Promozione eventi</a></li><li><a href="#lavori">Lavori svolti</a></li><li><a href="#zone">Zone servite</a></li><li><a href="#metodo">Chi siamo</a></li></ul>';
}
include $T . '/front-page.php';
