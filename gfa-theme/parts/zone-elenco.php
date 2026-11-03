<?php
/**
 * Zone servite: mappa schematica del Nord + elenco sedi.
 *
 * @package gfa
 */

$gfa_zone_posts = get_posts( array( 'post_type' => 'zona', 'numberposts' => 20, 'orderby' => 'title', 'order' => 'ASC' ) );
$gfa_links      = array();
$gfa_indirizzi  = array(
	'Milano'  => gfa_opt( 'indirizzo_milano' ),
	'Rapallo' => gfa_opt( 'indirizzo_rapallo' ),
);
foreach ( $gfa_zone_posts as $gfa_zone ) {
	$gfa_links[ $gfa_zone->post_title ] = get_permalink( $gfa_zone );
}
?>

			<ul class="zone-list">
				<?php foreach ( gfa_sedi() as $gfa_sede ) : ?>
					<?php $gfa_url = isset( $gfa_links[ $gfa_sede ] ) ? $gfa_links[ $gfa_sede ] : home_url( '/contatti/' ); ?>
					<li><a href="<?php echo esc_url( $gfa_url ); ?>"><span><?php echo esc_html( $gfa_sede ); ?></span> <small>volantinaggio →</small><?php if ( ! empty( $gfa_indirizzi[ $gfa_sede ] ) ) : ?><span class="zone-list__address"><?php echo esc_html( $gfa_indirizzi[ $gfa_sede ] ); ?></span><?php endif; ?></a></li>
				<?php endforeach; ?>
			</ul>

