<?php
/**
 * Mappa schematica del Nord Italia per il blocco "GFA · Mappa sedi".
 *
 * Sagoma e città si mettono al loro posto da latitudine e longitudine: una sede aggiunta in
 * Dati GFA → Sedi compare da sola se è un capoluogo del Nord o uno dei centri qui sotto.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

/**
 * Area disegnata (x, y, larghezza, altezza), in unità della mappa.
 */
const GFA_MAP_VIEWBOX = array( 0, -76, 550, 360 );

/**
 * Città che la mappa sa collocare: nome => array( latitudine, longitudine ).
 *
 * Capoluoghi di provincia del Nord e centri della Liguria vicini alle sedi. Per una città che
 * manca basta aggiungere una riga qui.
 *
 * @return array<string,float[]>
 */
function gfa_map_cities() {
	return array(
		// Piemonte e Valle d'Aosta.
		'Aosta'                   => array( 45.7372, 7.3201 ),
		'Torino'                  => array( 45.0703, 7.6869 ),
		'Alessandria'             => array( 44.9124, 8.6154 ),
		'Asti'                    => array( 44.9007, 8.2064 ),
		'Biella'                  => array( 45.5629, 8.0583 ),
		'Cuneo'                   => array( 44.3845, 7.5427 ),
		'Novara'                  => array( 45.4469, 8.6222 ),
		'Verbania'                => array( 45.9214, 8.5517 ),
		'Vercelli'                => array( 45.3202, 8.4185 ),
		// Lombardia.
		'Milano'                  => array( 45.4642, 9.1900 ),
		'Bergamo'                 => array( 45.6983, 9.6773 ),
		'Brescia'                 => array( 45.5416, 10.2118 ),
		'Como'                    => array( 45.8081, 9.0852 ),
		'Cremona'                 => array( 45.1335, 10.0226 ),
		'Lecco'                   => array( 45.8566, 9.3977 ),
		'Lodi'                    => array( 45.3138, 9.5018 ),
		'Mantova'                 => array( 45.1564, 10.7914 ),
		'Monza'                   => array( 45.5845, 9.2744 ),
		'Pavia'                   => array( 45.1847, 9.1582 ),
		'Sondrio'                 => array( 46.1699, 9.8715 ),
		'Varese'                  => array( 45.8206, 8.8251 ),
		// Trentino-Alto Adige.
		'Trento'                  => array( 46.0748, 11.1217 ),
		'Bolzano'                 => array( 46.4983, 11.3548 ),
		// Veneto.
		'Venezia'                 => array( 45.4408, 12.3155 ),
		'Verona'                  => array( 45.4384, 10.9916 ),
		'Padova'                  => array( 45.4064, 11.8768 ),
		'Vicenza'                 => array( 45.5455, 11.5354 ),
		'Treviso'                 => array( 45.6669, 12.2430 ),
		'Rovigo'                  => array( 45.0703, 11.7900 ),
		'Belluno'                 => array( 46.1425, 12.2167 ),
		'Bassano del Grappa'      => array( 45.7656, 11.7344 ),
		// Friuli-Venezia Giulia.
		'Trieste'                 => array( 45.6495, 13.7768 ),
		'Udine'                   => array( 46.0711, 13.2346 ),
		'Pordenone'               => array( 45.9564, 12.6615 ),
		'Gorizia'                 => array( 45.9409, 13.6217 ),
		// Liguria.
		'Genova'                  => array( 44.4056, 8.9463 ),
		'La Spezia'               => array( 44.1025, 9.8241 ),
		'Savona'                  => array( 44.3091, 8.4772 ),
		'Imperia'                 => array( 43.8897, 8.0393 ),
		'Sanremo'                 => array( 43.8159, 7.7761 ),
		'Rapallo'                 => array( 44.3497, 9.2309 ),
		'Chiavari'                => array( 44.3168, 9.3227 ),
		'Santa Margherita Ligure' => array( 44.3349, 9.2108 ),
		'Sestri Levante'          => array( 44.2722, 9.3934 ),
		// Emilia-Romagna.
		'Bologna'                 => array( 44.4949, 11.3426 ),
		'Modena'                  => array( 44.6471, 10.9252 ),
		'Parma'                   => array( 44.8015, 10.3279 ),
		'Reggio Emilia'           => array( 44.6989, 10.6297 ),
		'Piacenza'                => array( 45.0526, 9.6930 ),
		'Ferrara'                 => array( 44.8381, 11.6198 ),
		'Ravenna'                 => array( 44.4184, 12.2035 ),
		'Forlì'                   => array( 44.2227, 12.0407 ),
		'Cesena'                  => array( 44.1391, 12.2431 ),
		'Rimini'                  => array( 44.0678, 12.5695 ),
	);
}

/**
 * Sagoma semplificata del Nord Italia: punti (latitudine, longitudine) in senso orario dal
 * confine francese sulla costa ligure.
 *
 * @return float[][]
 */
function gfa_map_outline() {
	return array(
		array( 43.78, 7.53 ), array( 43.81, 7.78 ), array( 43.88, 8.03 ), array( 44.00, 8.17 ), array( 44.05, 8.22 ), array( 44.30, 8.48 ),
		array( 44.36, 8.58 ), array( 44.40, 8.93 ), array( 44.35, 9.15 ), array( 44.30, 9.21 ), array( 44.35, 9.23 ), array( 44.27, 9.39 ),
		array( 44.17, 9.61 ), array( 44.07, 9.83 ), array( 44.07, 9.91 ), array( 44.05, 10.02 ), array( 44.18, 10.15 ), array( 44.25, 10.45 ),
		array( 44.17, 10.80 ), array( 44.10, 11.05 ), array( 44.20, 11.35 ), array( 44.05, 11.65 ), array( 43.90, 11.90 ), array( 43.80, 12.10 ),
		array( 43.75, 12.35 ), array( 43.90, 12.55 ), array( 43.96, 12.74 ), array( 44.07, 12.57 ), array( 44.20, 12.40 ), array( 44.26, 12.35 ),
		array( 44.42, 12.28 ), array( 44.70, 12.25 ), array( 44.95, 12.53 ), array( 45.22, 12.28 ), array( 45.43, 12.38 ), array( 45.50, 12.65 ),
		array( 45.60, 12.89 ), array( 45.68, 13.12 ), array( 45.68, 13.39 ), array( 45.80, 13.53 ), array( 45.65, 13.77 ), array( 45.59, 13.78 ),
		array( 45.70, 13.85 ), array( 45.95, 13.62 ), array( 46.20, 13.65 ), array( 46.50, 13.58 ), array( 46.60, 13.70 ), array( 46.65, 13.00 ),
		array( 46.68, 12.70 ), array( 46.95, 12.20 ), array( 47.00, 11.50 ), array( 46.85, 11.00 ), array( 46.83, 10.50 ), array( 46.60, 10.25 ),
		array( 46.55, 10.10 ), array( 46.40, 9.90 ), array( 46.50, 9.40 ), array( 46.30, 9.25 ), array( 45.83, 9.03 ), array( 45.95, 8.90 ),
		array( 46.10, 8.75 ), array( 46.45, 8.45 ), array( 46.25, 8.10 ), array( 45.95, 7.90 ), array( 45.87, 7.17 ), array( 45.83, 6.86 ),
		array( 45.68, 6.88 ), array( 45.40, 7.10 ), array( 45.25, 6.95 ), array( 44.93, 6.72 ), array( 44.67, 7.07 ), array( 44.42, 6.89 ),
		array( 44.15, 7.57 ), array( 43.95, 7.65 ),
	);
}

/**
 * Da latitudine e longitudine a coordinate della mappa (stessa scala della prima versione).
 *
 * @param float $lat Latitudine.
 * @param float $lon Longitudine.
 * @return float[] array( x, y )
 */
function gfa_map_xy( $lat, $lon ) {
	return array( 72.43 * $lon - 470.4, 4777.3 - 103.05 * $lat );
}

/**
 * Tracciato SVG della sagoma.
 *
 * @return string
 */
function gfa_map_land_path() {
	$points = array();
	foreach ( gfa_map_outline() as $point ) {
		$xy       = gfa_map_xy( $point[0], $point[1] );
		$points[] = round( $xy[0] ) . ' ' . round( $xy[1] );
	}
	return 'M' . implode( ' L', $points ) . ' Z';
}

/**
 * Sedi da disegnare, con il posto dell'etichetta.
 *
 * Il nome della sede si confronta senza badare a maiuscole e accenti. L'etichetta va a destra del
 * punto; se lì toccherebbe un'altra etichetta, un punto o il bordo prova a sinistra, sotto e sopra,
 * e tra le quattro sceglie quella che si sovrappone meno.
 *
 * @param string[] $sedi Nomi delle sedi (Dati GFA → Sedi).
 * @param string   $home Sede principale, con il punto giallo.
 * @return array[] Ogni voce: name, x, y, home, anchor, tx, ty.
 */
function gfa_map_points( $sedi, $home = 'Rapallo' ) {
	$known = array();
	foreach ( gfa_map_cities() as $name => $coords ) {
		$known[ sanitize_title( $name ) ] = $coords;
	}
	$items = array();
	foreach ( $sedi as $sede ) {
		$slug = sanitize_title( $sede );
		if ( ! isset( $known[ $slug ] ) ) {
			continue; // città che la mappa non conosce: resta solo nell'elenco
		}
		$xy      = gfa_map_xy( $known[ $slug ][0], $known[ $slug ][1] );
		$items[] = array(
			'name' => $sede,
			'x'    => $xy[0],
			'y'    => $xy[1],
			'home' => sanitize_title( $home ) === $slug,
		);
	}
	// Prima la sede principale, poi le altre nell'ordine di Dati GFA.
	usort(
		$items,
		function ( $a, $b ) {
			return (int) $b['home'] - (int) $a['home'];
		}
	);

	$vb     = GFA_MAP_VIEWBOX;
	$boxes  = array();
	$result = array();
	foreach ( $items as $item ) {
		$x     = $item['x'];
		$y     = $item['y'];
		$w     = 7.2 * mb_strlen( $item['name'] ); // JetBrains Mono a 12 px: 7,2 px per carattere
		$tries = array(
			array( 'start', $x + 11, $y + 4, array( $x + 11, $y - 8, $x + 11 + $w, $y + 5 ) ),
			array( 'end', $x - 11, $y + 4, array( $x - 11 - $w, $y - 8, $x - 11, $y + 5 ) ),
			array( 'middle', $x, $y + 21, array( $x - $w / 2, $y + 9, $x + $w / 2, $y + 22 ) ),
			array( 'middle', $x, $y - 12, array( $x - $w / 2, $y - 24, $x + $w / 2, $y - 11 ) ),
		);
		$best = null;
		foreach ( $tries as $try ) {
			$box  = $try[3];
			$cost = 0;
			if ( $box[0] < $vb[0] + 2 || $box[2] > $vb[0] + $vb[2] - 2 || $box[1] < $vb[1] + 2 || $box[3] > $vb[1] + $vb[3] - 2 ) {
				$cost += 100000; // fuori dalla mappa: solo se non c'è altro posto
			}
			foreach ( $boxes as $other ) {
				$cost += max( 0, min( $box[2], $other[2] ) - max( $box[0], $other[0] ) ) * max( 0, min( $box[3], $other[3] ) - max( $box[1], $other[1] ) );
			}
			foreach ( $items as $dot ) {
				$cost += max( 0, min( $box[2], $dot['x'] + 8 ) - max( $box[0], $dot['x'] - 8 ) ) * max( 0, min( $box[3], $dot['y'] + 8 ) - max( $box[1], $dot['y'] - 8 ) );
			}
			if ( null === $best || $cost < $best[0] ) {
				$best = array( $cost, $try );
			}
			if ( 0 === $cost ) {
				break;
			}
		}
		$boxes[]  = $best[1][3];
		$result[] = $item + array(
			'anchor' => $best[1][0],
			'tx'     => $best[1][1],
			'ty'     => $best[1][2],
		);
	}
	return $result;
}
