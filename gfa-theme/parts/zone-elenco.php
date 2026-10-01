<?php
/**
 * Zone servite: elenco delle sedi, con il link alla pagina di ogni zona pubblicata.
 *
 * Una sede senza zona pubblicata compare senza link: la zona in bozza darebbe "pagina non trovata".
 * Il titolo della zona deve coincidere con il nome della sede (Dati GFA → Sedi).
 *
 * @package gfa
 */

$gfa_links = array();
$gfa_zones = get_posts(
	array(
		'post_type'   => 'zona',
		'post_status' => 'publish',
		'numberposts' => 50,
		'orderby'     => 'title',
		'order'       => 'ASC',
	)
);
foreach ( $gfa_zones as $gfa_zone ) {
	$gfa_links[ $gfa_zone->post_title ] = get_permalink( $gfa_zone );
}
?>

			<ul class="zone-list">
				<?php foreach ( gfa_sedi() as $gfa_sede ) : ?>
					<?php if ( isset( $gfa_links[ $gfa_sede ] ) ) : ?>
						<li><a href="<?php echo esc_url( $gfa_links[ $gfa_sede ] ); ?>"><?php echo esc_html( $gfa_sede ); ?> <small>volantinaggio →</small></a></li>
					<?php else : ?>
						<li><span class="zone-list__sede"><?php echo esc_html( $gfa_sede ); ?></span></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<p class="zone-note"><span class="todo">Confermare per ogni sede: attiva, diretta o in franchising, indirizzo</span></p>
