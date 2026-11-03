<?php
/**
 * SEO essenziale GFA. URL derivati da WordPress, nessun dominio di prova incorporato.
 * Un plugin SEO riconosciuto prende interamente il controllo dei metadati.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

function gfa_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' )
		|| function_exists( 'aioseo' ) || function_exists( 'seopress_init' );
}

function gfa_seo_enabled() {
	return ! gfa_seo_plugin_active() && (bool) apply_filters( 'gfa_seo_enabled', true );
}

/** Testi iniziali: i campi SEO della singola pagina hanno la precedenza. */
function gfa_seo_defaults() {
	return array(
		'home' => array( 'Volantinaggio a Milano e Rapallo | GFA', 'GFA: volantinaggio, grafica, stampa e promozione eventi a Milano e Rapallo. Dal 1985 al fianco delle attività locali. Chiedi un preventivo.' ),
		'volantinaggio' => array( 'Volantinaggio e distribuzione volantini | GFA', 'Distribuzione di volantini a Milano e Rapallo per negozi, attività ed eventi. Scegli con GFA zone, materiale e modalità. Richiedi un preventivo.' ),
		'stampa-e-grafica' => array( 'Stampa e grafica a Milano e Rapallo | GFA', 'Grafica e stampa di volantini, locandine e materiale promozionale. GFA ti segue dal progetto alla distribuzione a Milano e Rapallo. Chiedi un preventivo.' ),
		'promozione-eventi' => array( 'Promozione eventi a Milano e Rapallo | GFA', 'Fai conoscere il tuo evento sul territorio con GFA: grafica, locandine, volantini e distribuzione a Milano e Rapallo. Richiedi un preventivo.' ),
		'chi-siamo' => array( 'Chi siamo: comunicazione sul territorio dal 1985 | GFA', 'GFA si occupa di volantinaggio, stampa e promozione sul territorio dal 1985. Scopri i nostri servizi e le sedi di Milano e Rapallo.' ),
		'contatti' => array( 'Contatti e preventivi: Milano e Rapallo | GFA', 'Contatta GFA per volantinaggio, stampa e promozione eventi: 3471174430, info@gfamarketing.com. Sedi a Milano e Rapallo. Richiedi il tuo preventivo.' ),
		'privacy-policy' => array( 'Privacy Policy | GFA Comunicazione', 'Informativa sul trattamento dei dati personali di GFA Comunicazione: finalità, diritti e contatti per le richieste relative alla privacy.' ),
		'cookie-policy' => array( 'Cookie Policy | GFA Comunicazione', 'Informazioni su cookie, archiviazione locale e preferenze di navigazione del sito GFA Comunicazione. Scopri come gestire le tue scelte.' ),
		'lavori' => array( 'Clienti e referenze | GFA Comunicazione', 'Scopri i clienti e le referenze di GFA Comunicazione per attività di comunicazione e promozione sul territorio. Contattaci per il tuo progetto.' ),
		'zone' => array( 'Sedi e zone servite: Milano e Rapallo | GFA', 'Le sedi GFA di Milano e Rapallo per volantinaggio, stampa e promozione sul territorio. Trova i recapiti della tua sede e chiedi un preventivo.' ),
		'milano' => array( 'Volantinaggio a Milano | GFA Comunicazione', 'Volantinaggio a Milano con GFA. Sede in Viale Monte Ceneri 62, Milano. Contattaci al 3471174430 per organizzare la distribuzione e chiedere un preventivo.' ),
		'rapallo' => array( 'Volantinaggio a Rapallo | GFA Comunicazione', 'Volantinaggio a Rapallo con GFA. La sede di Via Libertà 46 ti segue nella promozione sul territorio. Chiedi un preventivo per la tua attività o evento.' ),
	);
}

function gfa_seo_text( $value ) {
	return trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' ) ) ) );
}

function gfa_seo_key() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_post_type_archive( 'lavoro' ) ) {
		return 'lavori';
	}
	if ( is_post_type_archive( 'zona' ) ) {
		return 'zone';
	}
	return is_singular() ? (string) get_post_field( 'post_name', get_queried_object_id() ) : '';
}

/** Questo contesto può descrivere solo contenuti pubblicati, anche durante una sessione admin. */
function gfa_seo_public_context() {
	if ( is_admin() || is_feed() || is_preview() || is_404() || is_search() || is_attachment() ) {
		return false;
	}
	if ( is_singular() ) {
		return 'publish' === get_post_status( get_queried_object_id() ) && '' === (string) get_post_field( 'post_password', get_queried_object_id() );
	}
	return is_front_page() || is_post_type_archive( array( 'lavoro', 'zona' ) );
}

/** Il canonical delle pagine singole resta quello stampato da WordPress. */
function gfa_seo_canonical() {
	if ( ! gfa_seo_public_context() ) {
		return '';
	}
	if ( is_singular() ) {
		return (string) wp_get_canonical_url( get_queried_object_id() );
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_post_type_archive( array( 'lavoro', 'zona' ) ) ) {
		$base = get_post_type_archive_link( is_post_type_archive( 'lavoro' ) ? 'lavoro' : 'zona' );
		$page = (int) get_query_var( 'paged' );
		if ( $page > 1 ) {
			// get_pagenum_link conserva anche query di campagna: non sono parte del canonical.
			return get_option( 'permalink_structure' ) ? strtok( get_pagenum_link( $page ), '?' ) : add_query_arg( 'paged', $page, $base );
		}
		return $base;
	}
	return '';
}

function gfa_seo_metadata() {
	$key      = gfa_seo_key();
	$defaults = gfa_seo_defaults();
	$id       = is_singular() ? get_queried_object_id() : 0;
	$initial  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : array( get_the_title( $id ) . ' | GFA', '' );
	$title    = $id ? (string) get_post_meta( $id, '_gfa_seo_title', true ) : '';
	$desc     = $id ? (string) get_post_meta( $id, '_gfa_seo_description', true ) : '';
	if ( '' === trim( $desc ) ) {
		$desc = $initial[1];
		if ( '' === $desc && $id ) {
			// Solo estratto scritto dall'editor: niente testo di moduli o segnaposto ricavato dai blocchi.
			$desc = (string) get_post_field( 'post_excerpt', $id );
		}
	}
	$title = gfa_seo_text( '' !== trim( $title ) ? $title : $initial[0] );
	if ( (int) get_query_var( 'paged' ) > 1 ) {
		$title .= ' — Pagina ' . (int) get_query_var( 'paged' );
	}
	$image_id = $id ? (int) get_post_thumbnail_id( $id ) : 0;
	$image_id = $image_id ?: (int) get_theme_mod( 'custom_logo' );
	$image    = $image_id ? wp_get_attachment_image_src( $image_id, 'full' ) : false;
	$alt      = $image_id ? gfa_seo_text( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) : '';
	return array(
		'title' => $title,
		'description' => gfa_seo_text( $desc ),
		'url' => gfa_seo_canonical(),
		'image' => $image,
		'image_alt' => '' !== $alt ? $alt : ( $image_id === (int) get_theme_mod( 'custom_logo' ) ? 'Logo GFA Comunicazione' : get_the_title( $id ) ),
	);
}

function gfa_seo_document_title( $title ) {
	// pre_get_document_title bypassa l'escaping finale del core: il valore deve essere già sicuro.
	return gfa_seo_enabled() && gfa_seo_public_context() ? esc_html( gfa_seo_metadata()['title'] ) : $title;
}
add_filter( 'pre_get_document_title', 'gfa_seo_document_title', 20 );

function gfa_seo_robots( $robots ) {
	if ( ! gfa_seo_enabled() ) {
		return $robots;
	}
	$private = is_singular() && ( 'publish' !== get_post_status( get_queried_object_id() ) || '' !== (string) get_post_field( 'post_password', get_queried_object_id() ) || '1' === (string) get_post_meta( get_queried_object_id(), '_gfa_seo_noindex', true ) );
	if ( is_post_type_archive( array( 'lavoro', 'zona' ) ) && ! gfa_seo_archive_indexable( is_post_type_archive( 'lavoro' ) ? 'lavoro' : 'zona' ) ) {
		$private = true;
	}
	if ( ! get_option( 'blog_public' ) || is_preview() || is_search() || is_404() || is_attachment() || is_author() || is_date() || is_category() || is_tag() || is_tax() || $private ) {
		unset( $robots['index'] );
		$robots['noindex'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'gfa_seo_robots', 20 );

function gfa_seo_phone( $value = null ) {
	$value = null === $value ? gfa_opt( 'telefono' ) : (string) $value;
	$number = preg_replace( '/\D/', '', $value );
	$international = 0 === strpos( trim( $value ), '+' ) || 0 === strpos( $number, '00' );
	if ( 0 === strpos( $number, '00' ) ) {
		$number = substr( $number, 2 );
	}
	if ( ! $international && ( ( 10 === strlen( $number ) && '3' === substr( $number, 0, 1 ) ) || ( '0' === substr( $number, 0, 1 ) && strlen( $number ) >= 6 && strlen( $number ) <= 11 ) ) ) {
		$number = '39' . $number;
	}
	return preg_match( '/^[1-9][0-9]{7,14}$/D', $number ) ? '+' . $number : '';
}

/** I dettagli dell'indirizzo sono quelli confermati; gli altri campi restano assenti. */
function gfa_seo_offices() {
	$offices = array(
		'milano' => array( 'city' => 'Milano', 'region' => 'MI' ),
		'rapallo' => array( 'city' => 'Rapallo' ),
	);
	foreach ( $offices as $slug => &$office ) {
		$page = get_page_by_path( $slug, OBJECT, 'zona' );
		$published = $page && 'publish' === $page->post_status;
		// Stessa priorità di single-zona.php: scheda della sede, poi Dati GFA.
		$address = $published ? trim( (string) get_post_meta( $page->ID, 'gfa_indirizzo', true ) ) : '';
		$phone = $published ? trim( (string) get_post_meta( $page->ID, 'gfa_telefono', true ) ) : '';
		$address = '' !== $address ? $address : trim( gfa_opt( 'indirizzo_' . $slug ) );
		$office['phone'] = gfa_seo_phone( '' !== $phone ? $phone : gfa_opt( 'telefono' ) );
		$office['street'] = $address;
		$pattern = '/,?\s+(\d{5})\s+' . preg_quote( $office['city'], '/' ) . '(?:\s+([A-Z]{2}))?\s*$/iu';
		if ( preg_match( $pattern, $address, $parts ) ) {
			$office['street'] = trim( preg_replace( $pattern, '', $address ), " ,\t\n\r\0\x0B" );
			$office['postal'] = $parts[1];
			if ( ! empty( $parts[2] ) ) { $office['region'] = strtoupper( $parts[2] ); }
		} elseif ( 'milano' === $slug && preg_match( '/^Viale Monte Ceneri,?\s*62$/iu', $address ) ) {
			$office['postal'] = '20155';
		}
		$office['url'] = $published && '1' !== (string) get_post_meta( $page->ID, '_gfa_seo_noindex', true ) ? get_permalink( $page->ID ) : '';
	}
	unset( $office );
	return $offices;
}

function gfa_seo_schema( $metadata ) {
	if ( ! gfa_seo_public_context() || ( is_singular() && '1' === (string) get_post_meta( get_queried_object_id(), '_gfa_seo_noindex', true ) ) ) {
		return array();
	}
	if ( is_post_type_archive( array( 'lavoro', 'zona' ) ) && ! gfa_seo_archive_indexable( is_post_type_archive( 'lavoro' ) ? 'lavoro' : 'zona' ) ) {
		return array();
	}
	$home     = home_url( '/' );
	$org_id   = $home . '#organization';
	$site_id  = $home . '#website';
	$key      = gfa_seo_key();
	$legal    = trim( gfa_opt( 'ragione' ) );
	$vat      = preg_replace( '/^IT/i', '', trim( gfa_opt( 'piva' ) ) );
	$org      = array_filter( array(
		'@type' => 'Organization', '@id' => $org_id, 'name' => 'GFA Comunicazione',
		'legalName' => $legal ?: 'GFA COMUNICAZIONE DI BATTISTONI EDOARDO',
		'url' => $home, 'telephone' => gfa_seo_phone(), 'email' => gfa_opt( 'email' ), 'vatID' => $vat ? 'IT' . $vat : '',
		'areaServed' => array( array( '@type' => 'City', 'name' => 'Milano' ), array( '@type' => 'City', 'name' => 'Rapallo' ) ),
	) );
	$logo = wp_get_attachment_image_src( (int) get_theme_mod( 'custom_logo' ), 'full' );
	if ( $logo ) {
		$org['logo'] = array( '@type' => 'ImageObject', 'url' => $logo[0], 'width' => (int) $logo[1], 'height' => (int) $logo[2] );
	}
	$page_type = 'chi-siamo' === $key ? 'AboutPage' : ( 'contatti' === $key ? 'ContactPage' : 'WebPage' );
	$graph = array(
		$org,
		array( '@type' => 'WebSite', '@id' => $site_id, 'name' => 'GFA Comunicazione', 'url' => $home, 'inLanguage' => 'it-IT', 'publisher' => array( '@id' => $org_id ) ),
		array_filter( array( '@type' => $page_type, '@id' => $metadata['url'] . '#webpage', 'url' => $metadata['url'], 'name' => $metadata['title'], 'description' => $metadata['description'], 'inLanguage' => 'it-IT', 'isPartOf' => array( '@id' => $site_id ), 'about' => array( '@id' => $org_id ) ) ),
	);
	$offices = gfa_seo_offices();
	if ( in_array( $key, array( 'home', 'zone', 'contatti', 'milano', 'rapallo' ), true ) ) {
		foreach ( $offices as $slug => $office ) {
			if ( '' === $office['street'] || ( is_singular( 'zona' ) && $slug !== $key ) ) {
				continue;
			}
			$address = array( '@type' => 'PostalAddress', 'streetAddress' => $office['street'], 'addressLocality' => $office['city'], 'addressCountry' => 'IT' );
			if ( isset( $office['postal'] ) ) { $address['postalCode'] = $office['postal']; }
			if ( isset( $office['region'] ) ) { $address['addressRegion'] = $office['region']; }
			$graph[] = array_filter( array( '@type' => 'LocalBusiness', '@id' => $home . '#sede-' . $slug, 'name' => 'GFA Comunicazione — ' . $office['city'], 'url' => $office['url'], 'parentOrganization' => array( '@id' => $org_id ), 'address' => $address, 'telephone' => $office['phone'], 'email' => gfa_opt( 'email' ) ) );
		}
	}
	$services = array( 'volantinaggio' => 'Volantinaggio e distribuzione volantini', 'stampa-e-grafica' => 'Stampa e grafica', 'promozione-eventi' => 'Promozione eventi', 'milano' => 'Volantinaggio a Milano', 'rapallo' => 'Volantinaggio a Rapallo' );
	if ( isset( $services[ $key ] ) ) {
		$local = is_singular( 'zona' ) && isset( $offices[ $key ] );
		$graph[] = array( '@type' => 'Service', '@id' => $metadata['url'] . '#service', 'name' => $services[ $key ], 'serviceType' => $services[ $key ], 'url' => $metadata['url'], 'provider' => array( '@id' => $local && '' !== $offices[ $key ]['street'] ? $home . '#sede-' . $key : $org_id ), 'areaServed' => $local ? array( array( '@type' => 'City', 'name' => $offices[ $key ]['city'] ) ) : $org['areaServed'] );
	}
	return array( '@context' => 'https://schema.org', '@graph' => $graph );
}

function gfa_seo_head() {
	if ( ! gfa_seo_enabled() || ! gfa_seo_public_context() ) {
		return;
	}
	$m = gfa_seo_metadata();
	if ( '' !== $m['description'] ) {
		echo '<meta name="description" content="' . esc_attr( $m['description'] ) . '">' . "\n";
	}
	if ( ! is_singular() && $m['url'] ) {
		echo '<link rel="canonical" href="' . esc_url( $m['url'] ) . '">' . "\n";
	}
	$tags = array( 'og:type' => 'website', 'og:locale' => 'it_IT', 'og:site_name' => 'GFA Comunicazione', 'og:title' => $m['title'], 'og:description' => $m['description'], 'og:url' => $m['url'] );
	if ( $m['image'] ) {
		$tags += array( 'og:image' => $m['image'][0], 'og:image:width' => (int) $m['image'][1], 'og:image:height' => (int) $m['image'][2], 'og:image:alt' => $m['image_alt'] );
	}
	foreach ( $tags as $property => $value ) {
		if ( '' !== (string) $value ) {
			echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( (string) $value ) . '">' . "\n";
		}
	}
	$twitter = array( 'twitter:card' => $m['image'] ? 'summary_large_image' : 'summary', 'twitter:title' => $m['title'], 'twitter:description' => $m['description'] );
	if ( $m['image'] ) {
		$twitter += array( 'twitter:image' => $m['image'][0], 'twitter:image:alt' => $m['image_alt'] );
	}
	foreach ( $twitter as $name => $value ) {
		if ( '' !== (string) $value ) {
			echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $value ) . '">' . "\n";
		}
	}
	$schema = gfa_seo_schema( $m );
	if ( $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'gfa_seo_head', 5 );

/** Archivi di autori e tassonomie sono noindex: non li proponiamo nella sitemap. */
function gfa_seo_sitemap_providers( $provider, $name ) {
	return gfa_seo_enabled() && in_array( $name, array( 'users', 'taxonomies' ), true ) ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'gfa_seo_sitemap_providers', 10, 2 );

/** Questo filtro è usato dal core sia per le URL sia per il conteggio delle pagine. */
function gfa_seo_sitemap_query( $args, $post_type ) {
	if ( ! gfa_seo_enabled() ) { return $args; }
	$exclude = array( 'relation' => 'OR', array( 'key' => '_gfa_seo_noindex', 'compare' => 'NOT EXISTS' ), array( 'key' => '_gfa_seo_noindex', 'value' => '1', 'compare' => '!=' ) );
	$args['meta_query'] = empty( $args['meta_query'] ) ? array( $exclude ) : array( 'relation' => 'AND', $args['meta_query'], $exclude );
	$args['has_password'] = false;
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'gfa_seo_sitemap_query', 10, 2 );

function gfa_seo_archive_indexable( $type ) {
	return (bool) apply_filters( 'gfa_seo_archive_indexable', true, $type );
}

function gfa_seo_archive_urls() {
	if ( ! gfa_seo_enabled() || ! get_option( 'blog_public' ) ) { return array(); }
	$entries = array();
	foreach ( array( 'lavoro', 'zona' ) as $type ) {
		$object = get_post_type_object( $type );
		$url    = get_post_type_archive_link( $type );
		if ( $object && $object->public && $object->has_archive && $url && gfa_seo_archive_indexable( $type ) ) {
			$entries[] = array( 'loc' => $url );
		}
	}
	return $entries;
}

function gfa_seo_register_sitemap() {
	if ( ! gfa_seo_enabled() || ! get_option( 'blog_public' ) || ! class_exists( 'WP_Sitemaps_Provider' ) ) { return; }
	$provider = new class() extends WP_Sitemaps_Provider {
		public function __construct() {
			$this->name = 'gfa';
			$this->object_type = 'gfa-archive';
		}
		public function get_url_list( $page_num, $object_subtype = '' ) {
			return 1 === (int) $page_num ? gfa_seo_archive_urls() : array();
		}
		public function get_max_num_pages( $object_subtype = '' ) {
			return gfa_seo_archive_urls() ? 1 : 0;
		}
	};
	wp_register_sitemap_provider( 'gfa', $provider );
}
add_action( 'wp_sitemaps_init', 'gfa_seo_register_sitemap' );
