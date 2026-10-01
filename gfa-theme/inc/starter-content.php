<?php
/**
 * Configurazione del sito GFA: pagine, homepage, menu e zone.
 *
 * Parte da sola alla prima attivazione del tema e si può rilanciare quando serve da
 * Aspetto → Configura sito GFA (per esempio dopo aver sostituito il tema caricando lo zip,
 * che non conta come nuova attivazione). Non cancella e non sovrascrive contenuti scritti.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pagine del sito: slug => titolo.
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

/**
 * Esegue la configurazione.
 *
 * @return array<string,string[]> Cosa è stato fatto, per il riepilogo.
 */
function gfa_run_setup( $reset = false ) {
	$done = array( 'create' => array(), 'riempite' => array(), 'ripristinate' => array(), 'saltate' => array() );
	$ids  = array();

	foreach ( gfa_starter_pages() as $slug => $title ) {
		$is_cookie = 'cookie-policy' === $slug;
		$content   = $is_cookie ? "<!-- wp:paragraph -->\n<p>Testo della cookie policy da redigere con il consulente privacy.</p>\n<!-- /wp:paragraph -->" : gfa_page_content( $slug );
		$page      = get_page_by_path( $slug );

		if ( $page && 'trash' !== $page->post_status ) {
			$ids[ $slug ] = $page->ID;
			if ( $reset && ! $is_cookie ) {
				// Ripristino richiesto: il contenuto precedente resta nelle revisioni della pagina.
				wp_update_post( array( 'ID' => $page->ID, 'post_content' => $content, 'post_status' => 'publish' ) );
				update_post_meta( $page->ID, '_wp_page_template', 'template-sezioni.php' );
				$done['ripristinate'][] = $title;
			} elseif ( '' === trim( $page->post_content ) ) {
				// Pagina vuota: la riempiamo con i modelli GFA.
				wp_update_post( array( 'ID' => $page->ID, 'post_content' => $content, 'post_status' => 'publish' ) );
				if ( ! $is_cookie ) {
					update_post_meta( $page->ID, '_wp_page_template', 'template-sezioni.php' );
				}
				$done['riempite'][] = $title;
			} else {
				$done['saltate'][] = $title;
			}
			continue;
		}

		$ids[ $slug ]     = wp_insert_post(
			array(
				'post_type'     => 'page',
				'post_status'   => 'publish',
				'post_title'    => $title,
				'post_name'     => $slug,
				'post_content'  => $content,
				'page_template' => $is_cookie ? '' : 'template-sezioni.php',
			)
		);
		$done['create'][] = $title;
	}

	// Homepage statica.
	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $ids['home'] );
	}

	// Menu principale, solo se non ne è già assegnato uno valido.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) || ! wp_get_nav_menu_object( $locations['primary'] ) ) {
		$menu    = wp_get_nav_menu_object( 'Menu principale GFA' );
		$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( 'Menu principale GFA' );
		if ( ! is_wp_error( $menu_id ) && ! $menu ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Home', 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-object-id' => (int) $ids['home'], 'menu-item-status' => 'publish' ) );
			foreach ( array( 'volantinaggio', 'stampa-e-grafica', 'promozione-eventi' ) as $slug ) {
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-object-id' => (int) $ids[ $slug ], 'menu-item-status' => 'publish' ) );
			}
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Lavori svolti', 'menu-item-url' => home_url( '/lavori/' ), 'menu-item-status' => 'publish' ) );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Zone servite', 'menu-item-url' => home_url( '/zone/' ), 'menu-item-status' => 'publish' ) );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-object-id' => (int) $ids['chi-siamo'], 'menu-item-status' => 'publish' ) );
		}
		if ( ! is_wp_error( $menu_id ) ) {
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	// Menu GFA creato da una versione precedente senza la voce Home: la aggiunge in testa.
	$gfa_menu = wp_get_nav_menu_object( 'Menu principale GFA' );
	if ( $gfa_menu && ! empty( $ids['home'] ) ) {
		$has_home = false;
		foreach ( (array) wp_get_nav_menu_items( $gfa_menu->term_id ) as $item ) {
			if ( (int) $item->object_id === (int) $ids['home'] || trailingslashit( $item->url ) === trailingslashit( home_url( '/' ) ) ) {
				$has_home = true;
			}
		}
		if ( ! $has_home ) {
			wp_update_nav_menu_item( $gfa_menu->term_id, 0, array( 'menu-item-title' => 'Home', 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-object-id' => (int) $ids['home'], 'menu-item-position' => -1, 'menu-item-status' => 'publish' ) );
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

	// Permalink "nome articolo": senza, lavori e pagine città non funzionano.
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	update_option( 'gfa_starter_done', 1 );
	flush_rewrite_rules();
	return $done;
}

// All'attivazione del tema: sempre (non sovrascrive nulla di già scritto).
add_action(
	'after_switch_theme',
	function () {
		gfa_run_setup();
		update_option( 'gfa_setup_version', GFA_VERSION );
	},
	20
);

// Installazione caricando lo zip sopra il tema già attivo (WordPress non la conta come
// attivazione) o aggiornamento: alla prima apertura della bacheca con la nuova versione.
add_action(
	'admin_init',
	function () {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}
		if ( get_option( 'gfa_setup_version' ) === GFA_VERSION ) {
			return;
		}
		update_option( 'gfa_setup_version', GFA_VERSION );
		gfa_run_setup();
	}
);

/**
 * La homepage statica è impostata su una pagina pubblicata?
 *
 * @return bool
 */
function gfa_home_ok() {
	$front = (int) get_option( 'page_on_front' );
	return 'page' === get_option( 'show_on_front' ) && $front && 'publish' === get_post_status( $front );
}

// Aspetto → Configura sito GFA.
add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Configura sito GFA', 'gfa' ), __( 'Configura sito GFA', 'gfa' ), 'manage_options', 'gfa-setup', 'gfa_setup_page' );
	}
);

/**
 * Schermata di configurazione.
 */
function gfa_setup_page() {
	$result = null;
	if ( isset( $_POST['gfa_setup_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['gfa_setup_nonce'] ), 'gfa_setup' ) && current_user_can( 'manage_options' ) ) {
		$result = gfa_run_setup( ! empty( $_POST['gfa_reset'] ) );
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Configura sito GFA', 'gfa' ) . '</h1>';
	if ( $result ) {
		echo '<div class="notice notice-success"><p><strong>Configurazione completata.</strong></p><ul style="list-style:disc;padding-left:1.5em">';
		foreach ( array( 'create' => 'Pagine create', 'riempite' => 'Pagine vuote riempite con i modelli', 'ripristinate' => 'Pagine riportate ai modelli aggiornati', 'saltate' => 'Pagine già scritte, lasciate come sono' ) as $key => $label ) {
			if ( $result[ $key ] ) {
				echo '<li>' . esc_html( $label . ': ' . implode( ', ', $result[ $key ] ) ) . '</li>';
			}
		}
		echo '<li>Homepage impostata sulla pagina "Home", menu principale e zone in bozza pronti.</li></ul></div>';
	}
	echo '<p>Crea le pagine mancanti con i modelli GFA, riempie quelle vuote, imposta la homepage statica, il menu principale, le zone in bozza e i permalink.</p>';
	echo '<p>Non modifica le pagine che hanno già un contenuto. Per rifarne una da capo: aprila, cancella il contenuto e inserisci il modello che ti serve dalla categoria <em>Pagine GFA</em>.</p>';
	echo '<p>Homepage: <strong>' . ( gfa_home_ok() ? esc_html__( 'impostata', 'gfa' ) : esc_html__( 'non impostata', 'gfa' ) ) . '</strong></p>';
	echo '<form method="post">';
	wp_nonce_field( 'gfa_setup', 'gfa_setup_nonce' );
	echo '<p><label><input type="checkbox" name="gfa_reset" value="1"> Riporta anche le pagine GFA già scritte ai modelli più recenti del tema (Home, Volantinaggio, Stampa e grafica, Promozione eventi, Chi siamo, Franchising, Contatti). I testi modificati a mano si perdono, ma restano recuperabili dalle revisioni di ogni pagina.</label></p>';
	submit_button( __( 'Configura il sito', 'gfa' ) );
	echo '</form></div>';
}

// Avviso in bacheca finché la homepage non è impostata.
add_action(
	'admin_notices',
	function () {
		if ( gfa_home_ok() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'appearance_page_gfa-setup' === $screen->id ) {
			return;
		}
		echo '<div class="notice notice-warning"><p><strong>Tema GFA:</strong> la homepage non è ancora impostata. <a href="' . esc_url( admin_url( 'themes.php?page=gfa-setup' ) ) . '">Configura il sito in un clic</a>.</p></div>';
	}
);
