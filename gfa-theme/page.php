<?php
/**
 * Pagina standard (Chi siamo, Stampa e grafica, Privacy…).
 *
 * Una pagina composta con le sezioni GFA ma rimasta su questo template si mostra come
 * "Pagina a sezioni GFA": solo il contenuto, senza titolo automatico e modulo aggiunto.
 *
 * @package gfa
 */

get_header();
$gfa_sections = gfa_has_sections( get_queried_object_id() );
while ( have_posts() ) :
	the_post();
	if ( $gfa_sections ) :
		the_content();
	else :
		?>
		<section class="page-hero"><div class="wrap"><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) : ?><p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?></div></section>
		<section class="section"><div class="wrap prose"><?php the_content(); ?></div></section>
		<?php
	endif;
endwhile;
if ( ! $gfa_sections && gfa_show_quote_form() ) {
	echo do_blocks( gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
