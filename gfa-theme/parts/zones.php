<?php
/**
 * Zone servite: mappa schematica del Nord + elenco sedi.
 *
 * @package gfa
 */

// Posizioni approssimate delle città su una mappa schematica del Nord Italia (viewBox 520x300).
$gfa_pos = array(
	'Milano'             => array( 205, 95 ),
	'Como'               => array( 188, 58 ),
	'Bassano del Grappa' => array( 382, 62 ),
	'Modena'             => array( 318, 168 ),
	'Reggio Emilia'      => array( 288, 160 ),
	'Genova'             => array( 180, 205 ),
	'Rapallo'            => array( 208, 218 ),
	'Sanremo'            => array( 82, 262 ),
);
$gfa_zone_posts = get_posts( array( 'post_type' => 'zona', 'numberposts' => 20, 'orderby' => 'title', 'order' => 'ASC' ) );
$gfa_links      = array();
foreach ( $gfa_zone_posts as $gfa_zone ) {
	$gfa_links[ $gfa_zone->post_title ] = get_permalink( $gfa_zone );
}
?>
<section class="section section--alt" id="zone">
	<div class="wrap zones">
		<div>
			<div class="section__head">
				<p class="eyebrow"><?php esc_html_e( 'Zone servite', 'gfa' ); ?></p>
				<h2>Otto sedi, dal Ponente alla pianura.</h2>
				<p class="lead">Ogni sede conosce le sue vie. Scegli la tua zona per vedere comuni coperti, modalità e referente.</p>
			</div>
			<ul class="zone-list">
				<?php foreach ( gfa_sedi() as $gfa_sede ) : ?>
					<?php $gfa_url = isset( $gfa_links[ $gfa_sede ] ) ? $gfa_links[ $gfa_sede ] : home_url( '/volantinaggio/' . sanitize_title( $gfa_sede ) . '/' ); ?>
					<li><a href="<?php echo esc_url( $gfa_url ); ?>"><?php echo esc_html( $gfa_sede ); ?> <small>volantinaggio →</small></a></li>
				<?php endforeach; ?>
			</ul>
			<p style="margin-top:1rem"><span class="todo">Confermare per ogni sede: attiva, diretta o in franchising, indirizzo</span></p>
		</div>
		<svg class="north-map" viewBox="0 0 520 300" role="img" aria-label="<?php esc_attr_e( 'Mappa schematica delle sedi GFA nel Nord Italia', 'gfa' ); ?>">
			<path class="land" d="M30 250 C60 230 100 250 140 222 C170 200 210 222 245 230 C270 236 280 215 300 200 C330 182 380 190 420 175 C455 160 490 120 480 80 C470 45 420 35 380 30 C320 22 270 40 230 30 C190 22 150 20 120 40 C95 60 80 100 60 130 C40 160 20 200 30 250 Z"/>
			<?php foreach ( $gfa_pos as $gfa_city => $gfa_xy ) : ?>
				<?php if ( in_array( $gfa_city, gfa_sedi(), true ) ) : ?>
					<circle class="dot<?php echo 'Rapallo' === $gfa_city ? ' dot--home' : ''; ?>" cx="<?php echo (int) $gfa_xy[0]; ?>" cy="<?php echo (int) $gfa_xy[1]; ?>" r="<?php echo 'Rapallo' === $gfa_city ? 8 : 6; ?>"/>
					<?php $gfa_left = in_array( $gfa_city, array( 'Genova', 'Reggio Emilia' ), true ); ?><text x="<?php echo (int) $gfa_xy[0] + ( $gfa_left ? -11 : 11 ); ?>" y="<?php echo (int) $gfa_xy[1] + ( 'Rapallo' === $gfa_city ? 18 : 4 ); ?>" text-anchor="<?php echo $gfa_left ? 'end' : 'start'; ?>"><?php echo esc_html( $gfa_city ); ?></text>
				<?php endif; ?>
			<?php endforeach; ?>
		</svg>
	</div>
</section>
