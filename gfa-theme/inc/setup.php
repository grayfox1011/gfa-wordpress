<?php
/**
 * Supporti del tema, menu, script e stili.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'gfa', get_template_directory() . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 80, 'flex-width' => true ) );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'wp-block-styles' );
		remove_theme_support( 'core-block-patterns' );
		add_editor_style( array( 'assets/fonts/fonts.css', 'assets/css/main.css', 'assets/css/editor.css' ) );
		add_image_size( 'gfa-card', 720, 960, true );
		register_nav_menus(
			array(
				'primary' => __( 'Menu principale', 'gfa' ),
				'footer'  => __( 'Menu footer', 'gfa' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$uri = get_template_directory_uri();
		wp_enqueue_style( 'gfa-fonts', $uri . '/assets/fonts/fonts.css', array(), GFA_VERSION );
		wp_enqueue_style( 'gfa-main', $uri . '/assets/css/main.css', array( 'gfa-fonts' ), GFA_VERSION );

		// GSAP self-hosted: nessun dato inviato a CDN esterne.
		wp_enqueue_script( 'gsap', $uri . '/assets/js/vendor/gsap.min.js', array(), '3.13.0', true );
		wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/assets/js/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.13.0', true );
		wp_enqueue_script( 'gsap-splittext', $uri . '/assets/js/vendor/SplitText.min.js', array( 'gsap' ), '3.13.0', true );
		wp_enqueue_script( 'lenis', $uri . '/assets/js/vendor/lenis.min.js', array(), '1.3.11', true );
		wp_enqueue_script( 'gfa-main', $uri . '/assets/js/main.js', array( 'gsap', 'gsap-scrolltrigger', 'gsap-splittext', 'lenis' ), GFA_VERSION, true );
	}
);

// Preload: la classe va messa prima che la pagina compaia, quindi lo script è in linea nell'head.
add_action(
	'wp_head',
	function () {
		echo '<script>' . file_get_contents( get_template_directory() . '/assets/js/preload-head.js' ) . '</script>' . "\n"; // phpcs:ignore
		// Font principali precaricati: meno salti del testo tra una pagina e l'altra.
		foreach ( array( 'Archivo-500-800-latin.woff2', 'PublicSans-400-latin.woff2' ) as $font ) {
			echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/' . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		}
	},
	1
);

// Icona del sito di riserva finché GFA non carica la sua (Personalizza → Identità del sito).
add_action(
	'wp_head',
	function () {
		if ( ! has_site_icon() ) {
			echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( get_template_directory_uri() . '/assets/img/favicon.svg' ) . '">' . "\n";
		}
	}
);

// Via l'articolo di esempio dal feed e gli emoji di WordPress (peso inutile).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Menu di riserva finché il cliente non crea il menu in Aspetto → Menu.
 */
function gfa_fallback_menu() {
	$items = array(
		''                 => 'Home',
		'volantinaggio'    => 'Volantinaggio',
		'stampa-e-grafica' => 'Stampa e grafica',
		'promozione-eventi' => 'Promozione eventi',
		'lavori'           => 'Lavori svolti',
		'zone'             => 'Zone servite',
		'chi-siamo'        => 'Chi siamo',
	);
	echo '<ul>';
	foreach ( $items as $slug => $label ) {
		echo '<li><a href="' . esc_url( home_url( '' === $slug ? '/' : '/' . $slug . '/' ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Dati strutturati LocalBusiness (solo con dati confermati).
 */
add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() || '' === gfa_opt( 'ragione' ) ) {
			return;
		}
		$data = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'LocalBusiness',
			'name'      => gfa_opt( 'ragione' ),
			'url'       => home_url( '/' ),
			'telephone' => gfa_opt( 'telefono' ),
			'email'     => gfa_opt( 'email' ),
			'vatID'     => gfa_opt( 'piva' ),
			'address'   => gfa_opt( 'sede_legale' ),
			'areaServed' => array_values( gfa_sedi() ),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $data ) ) . '</script>' . "\n";
	}
);
