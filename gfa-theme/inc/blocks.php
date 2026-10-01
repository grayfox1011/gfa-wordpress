<?php
/**
 * Blocchi GFA (dinamici) e registrazioni per l'editor: categorie, stili dei pulsanti, formati.
 *
 * I blocchi dinamici mostrano dati che non si scrivono a mano: lavori, sedi, modulo preventivo,
 * recapiti e dati societari. Tutto il resto delle pagine è fatto con blocchi standard (modelli GFA).
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Include una parte del tema e ne restituisce l'HTML.
 *
 * @param string $part Nome del file in parts/.
 * @return string
 */
function gfa_part_html( $part ) {
	ob_start();
	get_template_part( 'parts/' . $part );
	return (string) ob_get_clean();
}

/**
 * Blocchi dinamici: nome => [titolo, descrizione, icona, callback].
 *
 * @return array<string,array>
 */
function gfa_dynamic_blocks() {
	return array(
		'numeri'         => array( 'GFA · Numeri', 'Anni, sedi e copie dal pannello Dati GFA.', 'chart-bar', 'gfa_render_numeri' ),
		'lavori'         => array( 'GFA · Lavori svolti', 'Gli ultimi lavori pubblicati.', 'portfolio', function () { return gfa_part_html( 'lavori' ); } ),
		'zone-elenco'    => array( 'GFA · Elenco sedi', 'Le sedi con il link alla pagina di ogni zona.', 'location', function () { return gfa_part_html( 'zone-elenco' ); } ),
		'zone-mappa'     => array( 'GFA · Mappa sedi', 'Mappa schematica delle sedi.', 'location-alt', function () { return gfa_part_html( 'zone-mappa' ); } ),
		'preventivo'     => array( 'GFA · Modulo preventivo', 'Modulo in tre passi, invia un\'email a GFA.', 'feedback', function () { return gfa_part_html( 'preventivo' ); } ),
		'contatti'       => array( 'GFA · Recapiti', 'Telefono, email e WhatsApp dal pannello Dati GFA.', 'phone', function () { return gfa_part_html( 'contatti' ); } ),
		'dati-societari' => array( 'GFA · Dati societari', 'Ragione sociale, P.IVA, REA e sede legale.', 'id', 'gfa_render_dati' ),
	);
}

add_action(
	'init',
	function () {
		foreach ( gfa_dynamic_blocks() as $name => $block ) {
			register_block_type(
				'gfa/' . $name,
				array(
					'api_version'     => 3,
					'title'           => $block[0],
					'category'        => 'gfa',
					'render_callback' => $block[3],
					'supports'        => array( 'html' => false ),
				)
			);
		}

		register_block_pattern_category( 'gfa', array( 'label' => __( 'Sezioni GFA', 'gfa' ) ) );
		register_block_pattern_category( 'gfa-pagine', array( 'label' => __( 'Pagine GFA', 'gfa' ) ) );

		foreach ( array(
			'gfa-primary' => 'Giallo GFA',
			'gfa-blue'    => 'Blu GFA',
			'gfa-ghost'   => 'Bordo',
		) as $style => $label ) {
			register_block_style( 'core/button', array( 'name' => $style, 'label' => $label ) );
		}
	}
);

add_filter(
	'block_categories_all',
	function ( $categories ) {
		array_unshift( $categories, array( 'slug' => 'gfa', 'title' => __( 'GFA', 'gfa' ), 'icon' => null ) );
		return $categories;
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		$blocks = array();
		foreach ( gfa_dynamic_blocks() as $name => $block ) {
			$blocks[] = array( 'name' => 'gfa/' . $name, 'title' => $block[0], 'description' => $block[1], 'icon' => $block[2] );
		}
		wp_enqueue_script(
			'gfa-editor',
			get_template_directory_uri() . '/assets/js/editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-rich-text', 'wp-block-editor', 'wp-i18n' ),
			GFA_VERSION,
			true
		);
		wp_add_inline_script( 'gfa-editor', 'window.gfaBlocks = ' . wp_json_encode( $blocks ) . ';', 'before' );
	}
);

/**
 * Blocco Numeri.
 *
 * @return string
 */
function gfa_render_numeri() {
	ob_start();
	?>
	<ul class="facts-list">
		<li><span class="num"><?php echo gfa_opt( 'anno' ) ? esc_html( 'Dal ' . gfa_opt( 'anno' ) ) : '<span class="todo">anno di inizio</span>'; ?></span><span class="lbl"><?php esc_html_e( 'nel volantinaggio', 'gfa' ); ?></span></li>
		<li><span class="num"><?php echo esc_html( (string) count( gfa_sedi() ) ); ?></span><span class="lbl"><?php esc_html_e( 'sedi operative al Nord', 'gfa' ); ?></span></li>
		<li><span class="num"><?php gfa_value( 'copie_anno', 'copie/anno' ); ?></span><span class="lbl"><?php esc_html_e( 'volantini distribuiti in un anno', 'gfa' ); ?></span></li>
		<li><span class="num">3</span><span class="lbl"><?php esc_html_e( 'servizi con un solo referente: grafica, stampa, distribuzione', 'gfa' ); ?></span></li>
	</ul>
	<?php
	return (string) ob_get_clean();
}

/**
 * Blocco Dati societari.
 *
 * @return string
 */
function gfa_render_dati() {
	$rows = array(
		'Ragione sociale' => array( 'ragione', 'ragione sociale' ),
		'Partita IVA'     => array( 'piva', 'P.IVA da confermare' ),
		'REA'             => array( 'rea', 'numero REA' ),
		'Sede legale'     => array( 'sede_legale', 'indirizzo completo' ),
	);
	ob_start();
	echo '<div class="table-scroll"><table class="price-table"><tbody>';
	foreach ( $rows as $label => $row ) {
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
		gfa_value( $row[0], $row[1] );
		echo '</td></tr>';
	}
	echo '</tbody></table></div>';
	return (string) ob_get_clean();
}

/**
 * Contenuto di un modello GFA come markup a blocchi (per le pagine create all'attivazione).
 *
 * @param string $slug Nome del file in patterns/ senza estensione.
 * @return string
 */
function gfa_pattern( $slug ) {
	static $cache = array();
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}
	$file = get_template_directory() . '/patterns/' . $slug . '.php';
	if ( ! file_exists( $file ) ) {
		return '';
	}
	ob_start();
	include $file;
	$cache[ $slug ] = trim( (string) ob_get_clean() );
	return $cache[ $slug ];
}

/**
 * Pagine intere: elenco delle sezioni che le compongono.
 *
 * @return array<string,string[]>
 */
function gfa_page_layouts() {
	return array(
		'home'              => array( 'hero', 'numeri', 'percorsi', 'metodo', 'controllo', 'lavori', 'zone', 'recensioni', 'preventivo' ),
		'volantinaggio'     => array( 'volantinaggio-intro', 'volantinaggio-modalita', 'controllo', 'volantinaggio-prezzi', 'volantinaggio-faq', 'preventivo' ),
		'stampa-e-grafica'  => array( 'stampa-intro', 'stampa-prodotti', 'stampa-processo', 'lavori', 'stampa-faq', 'preventivo' ),
		'promozione-eventi' => array( 'eventi-intro', 'eventi-percorso', 'lavori', 'preventivo' ),
		'chi-siamo'         => array( 'chi-intro', 'chi-storia', 'chi-persone', 'chi-impegni', 'zone', 'chi-dati', 'preventivo' ),
		'franchising'       => array( 'franchising-intro', 'franchising-offerta', 'franchising-percorso', 'zone', 'franchising-candidatura' ),
		'contatti'          => array( 'contatti-intro', 'preventivo', 'zone' ),
	);
}

/**
 * Markup completo di una pagina GFA.
 *
 * @param string $page Slug della pagina.
 * @return string
 */
function gfa_page_content( $page ) {
	$layouts = gfa_page_layouts();
	if ( ! isset( $layouts[ $page ] ) ) {
		return '';
	}
	return implode( "\n\n", array_filter( array_map( 'gfa_pattern', $layouts[ $page ] ) ) );
}

/**
 * Registra anche le pagine intere come modelli, per ricominciare da capo una pagina.
 *
 * Servono solo all'editor: si registrano nelle richieste dell'amministrazione e della REST API
 * (da cui l'editor legge i modelli), non a ogni pagina del sito, perché comporle include tutte
 * le sezioni.
 */
function gfa_register_page_patterns() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	$titles = array(
		'home'              => 'Homepage',
		'volantinaggio'     => 'Volantinaggio',
		'stampa-e-grafica'  => 'Stampa e grafica',
		'promozione-eventi' => 'Promozione eventi',
		'chi-siamo'         => 'Chi siamo',
		'franchising'       => 'Franchising',
		'contatti'          => 'Contatti',
	);
	foreach ( $titles as $slug => $title ) {
		register_block_pattern(
			'gfa/pagina-' . $slug,
			array(
				'title'      => 'Pagina ' . $title,
				'categories' => array( 'gfa-pagine' ),
				'blockTypes' => array( 'core/post-content' ),
				'postTypes'  => array( 'page' ),
				'content'    => gfa_page_content( $slug ),
			)
		);
	}
}
add_action( 'admin_init', 'gfa_register_page_patterns' );
add_action( 'rest_api_init', 'gfa_register_page_patterns' );
