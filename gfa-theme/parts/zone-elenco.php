<?php
/**
 * Zone servite: mappa schematica del Nord + elenco sedi.
 *
 * @package gfa
 */

$gfa_zone_posts = get_posts( array( 'post_type' => 'zona', 'numberposts' => 20, 'orderby' => 'title', 'order' => 'ASC' ) );
$gfa_links      = array();
foreach ( $gfa_zone_posts as $gfa_zone ) {
	$gfa_links[ $gfa_zone->post_title ] = get_permalink( $gfa_zone );
}
?>

			<ul class="zone-list">
				<?php foreach ( gfa_sedi() as $gfa_sede ) : ?>
					<?php $gfa_url = isset( $gfa_links[ $gfa_sede ] ) ? $gfa_links[ $gfa_sede ] : home_url( '/volantinaggio/' . sanitize_title( $gfa_sede ) . '/' ); ?>
					<li><a href="<?php echo esc_url( $gfa_url ); ?>"><?php echo esc_html( $gfa_sede ); ?> <small>volantinaggio →</small></a></li>
				<?php endforeach; ?>
			</ul>
			<p class="zone-note"><span class="todo">Confermare per ogni sede: attiva, diretta o in franchising, indirizzo</span></p>

