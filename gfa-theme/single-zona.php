<?php
/**
 * Pagina città: "Volantinaggio a <città>" (URL /volantinaggio/<città>/).
 *
 * @package gfa
 */

get_header();
while ( have_posts() ) :
	the_post();
	$gfa_id     = get_the_ID();
	$gfa_comuni = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $gfa_id, 'gfa_comuni', true ) ) ) );
	$gfa_indirizzo = (string) get_post_meta( $gfa_id, 'gfa_indirizzo', true );
	$gfa_telefono = (string) get_post_meta( $gfa_id, 'gfa_telefono', true );
	// I dati specifici della sede hanno priorità; i recapiti confermati sono un ripiego.
	if ( 'rapallo' === get_post_field( 'post_name', $gfa_id ) && '' === $gfa_indirizzo ) {
		$gfa_indirizzo = gfa_opt( 'indirizzo_rapallo' );
	}
	if ( 'milano' === get_post_field( 'post_name', $gfa_id ) && '' === $gfa_telefono ) {
		$gfa_telefono = gfa_opt( 'telefono' );
	}
	?>
	<section class="page-hero"><div class="wrap stack">
		<p class="eyebrow">Sede di <?php the_title(); ?></p>
		<h1>Volantinaggio a <?php the_title(); ?></h1>
		<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<p><a class="btn btn--primary" href="#preventivo">Chiedi un preventivo per <?php the_title(); ?></a></p>
	</div></section>
	<section class="section"><div class="wrap control">
		<div class="prose"><?php the_content(); ?></div>
		<div class="stack">
			<h3>Sede</h3>
			<?php if ( $gfa_indirizzo || $gfa_telefono ) : ?>
				<p><?php echo esc_html( $gfa_indirizzo ); ?><?php if ( $gfa_indirizzo && $gfa_telefono ) : ?><br><?php endif; ?><?php if ( $gfa_telefono ) : ?><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gfa_telefono ) ); ?>"><?php echo esc_html( $gfa_telefono ); ?></a><?php endif; ?></p>
			<?php endif; ?>
			<?php if ( $gfa_comuni ) : ?>
				<h3>Comuni serviti</h3>
				<p><?php echo esc_html( implode( ' · ', $gfa_comuni ) ); ?></p>
			<?php endif; ?>
		</div>
	</div></section>
	<?php
endwhile;
echo do_blocks( gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
get_footer();
