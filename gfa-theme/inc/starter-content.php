<?php
/**
 * Alla prima attivazione crea pagine, menu e zone (bozze), senza toccare ciò che esiste già.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pagine del sito: slug => titolo. Il layout arriva dal file page-<slug>.php.
 *
 * @return array<string,string>
 */
function gfa_starter_pages() {
	return array(
		'home'              => 'Home',
		'volantinaggio'     => 'Volantinaggio',
		'stampa-e-grafica'  => 'Stampa e grafica',
		'promozione-eventi' => 'Promozione eventi',
		'chi-siamo'         => 'Chi siamo',
		'franchising'       => 'Franchising',
		'contatti'          => 'Contatti',
		'cookie-policy'     => 'Cookie policy',
	);
}

add_action(
	'after_switch_theme',
	function () {
		if ( get_option( 'gfa_starter_done' ) ) {
			return;
		}

		$ids = array();
		foreach ( gfa_starter_pages() as $slug => $title ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$ids[ $slug ] = $page->ID;
				continue;
			}
			$ids[ $slug ] = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => 'cookie-policy' === $slug ? "<!-- wp:paragraph -->\n<p>Testo della cookie policy da redigere con il consulente privacy.</p>\n<!-- /wp:paragraph -->" : gfa_page_content( $slug ),
					'page_template' => 'cookie-policy' === $slug ? '' : 'template-sezioni.php',
				)
			);
		}

		// Homepage statica.
		if ( ! empty( $ids['home'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $ids['home'] );
		}

		// Menu principale, solo se non ne esiste già uno assegnato.
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $locations['primary'] ) ) {
			$menu_id = wp_create_nav_menu( 'Menu principale GFA' );
			if ( ! is_wp_error( $menu_id ) ) {
				foreach ( array( 'volantinaggio', 'stampa-e-grafica', 'promozione-eventi' ) as $slug ) {
					wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-object-id' => (int) $ids[ $slug ], 'menu-item-status' => 'publish' ) );
				}
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Lavori svolti', 'menu-item-url' => home_url( '/lavori/' ), 'menu-item-status' => 'publish' ) );
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Zone servite', 'menu-item-url' => home_url( '/zone/' ), 'menu-item-status' => 'publish' ) );
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-object-id' => (int) $ids['chi-siamo'], 'menu-item-status' => 'publish' ) );
				$locations['primary'] = $menu_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}

		// Una zona in bozza per ogni sede: si pubblica quando i dati sono confermati.
		foreach ( gfa_sedi() as $sede ) {
			if ( get_page_by_path( sanitize_title( $sede ), OBJECT, 'zona' ) ) {
				continue;
			}
			wp_insert_post(
				array(
					'post_type'    => 'zona',
					'post_status'  => 'draft',
					'post_title'   => $sede,
					'post_name'    => sanitize_title( $sede ),
					'post_excerpt' => 'Volantinaggio in cassetta, nei negozi e agli eventi a ' . $sede . ' e dintorni.',
				)
			);
		}

		update_option( 'gfa_starter_done', 1 );
		flush_rewrite_rules();
	},
	20
);
