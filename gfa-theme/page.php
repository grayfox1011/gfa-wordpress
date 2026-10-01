<?php
/**
 * Pagina standard (Chi siamo, Stampa e grafica, Privacy…).
 *
 * @package gfa
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="page-hero"><div class="wrap"><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) : ?><p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?></div></section>
	<section class="section"><div class="wrap prose"><?php the_content(); ?></div></section>
	<?php
endwhile;
if ( gfa_show_quote_form() ) {
	echo do_blocks( gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
