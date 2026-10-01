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
			<h2 class="is-h3">Sede</h2>
			<p><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_indirizzo', true ) ); ?><br><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_telefono', true ) ); ?></p>
			<?php if ( $gfa_comuni ) : ?>
				<h2 class="is-h3">Comuni serviti</h2>
				<p><?php echo esc_html( implode( ' · ', $gfa_comuni ) ); ?></p>
			<?php endif; ?>
		</div>
	</div></section>
	<?php
endwhile;
if ( gfa_show_quote_form() ) {
	echo do_blocks( gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
