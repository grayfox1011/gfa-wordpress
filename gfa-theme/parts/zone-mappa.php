<?php
/**
 * Zone servite: mappa schematica del Nord con le sedi di Dati GFA → Sedi.
 *
 * Posizioni, sagoma ed etichette vengono da inc/map.php: una sede nuova compare da sola se la
 * mappa conosce la città.
 *
 * @package gfa
 */

$gfa_vb = GFA_MAP_VIEWBOX;
?>
		<svg class="north-map" viewBox="<?php echo esc_attr( implode( ' ', $gfa_vb ) ); ?>" role="img" aria-label="<?php esc_attr_e( 'Mappa schematica delle sedi GFA nel Nord Italia', 'gfa' ); ?>">
			<path class="land" d="<?php echo esc_attr( gfa_map_land_path() ); ?>"/>
			<?php foreach ( gfa_map_points( gfa_sedi() ) as $gfa_point ) : ?>
				<circle class="dot<?php echo $gfa_point['home'] ? ' dot--home' : ''; ?>" cx="<?php echo esc_attr( round( $gfa_point['x'] ) ); ?>" cy="<?php echo esc_attr( round( $gfa_point['y'] ) ); ?>" r="<?php echo $gfa_point['home'] ? 8 : 6; ?>"/>
				<text x="<?php echo esc_attr( round( $gfa_point['tx'] ) ); ?>" y="<?php echo esc_attr( round( $gfa_point['ty'] ) ); ?>" text-anchor="<?php echo esc_attr( $gfa_point['anchor'] ); ?>"><?php echo esc_html( $gfa_point['name'] ); ?></text>
			<?php endforeach; ?>
		</svg>
