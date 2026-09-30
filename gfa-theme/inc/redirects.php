<?php
/**
 * Redirect 301 dai vecchi indirizzi del sito GFA alle nuove pagine.
 * Scatta solo se il vecchio indirizzo non esiste più (404), così non copre pagine vere.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$map  = array(
			'servizi/distribuzione-sul-territorio-nazionale' => '/volantinaggio/',
			'servizi/servizi-stampa'                         => '/stampa-e-grafica/',
			'servizi/studio-grafico-ideazione-logo'          => '/stampa-e-grafica/',
			'servizi/pubblicita-mobile-wow'                  => '/volantinaggio/',
			'servizi/pubblicita-veicolare'                   => '/volantinaggio/',
			'servizi/noleggio-strumentazioni'                => '/promozione-eventi/',
			'servizi/prodotti-web'                           => '/chi-siamo/',
			'servizi/web-marketing'                          => '/chi-siamo/',
			'servizi/consulenze-e-strategie-di-marketing'    => '/chi-siamo/',
			'servizi'                                        => '/',
			'promozione'                                     => '/volantinaggio/#prezzi',
			'portfoglio'                                     => '/lavori/',
			'2019/05/17/ciao-mondo'                          => '/',
		);
		$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' );
		$path = preg_replace( '#^gfamarketing/#', '', $path );
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( home_url( $map[ $path ] ), 301 );
			exit;
		}
	}
);
