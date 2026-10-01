<?php
/**
 * Articolo singolo e altri contenuti senza un template proprio: titolo, testo completo, preventivo.
 *
 * Pagine, lavori e zone hanno i loro template (page.php, single-lavoro.php, single-zona.php).
 *
 * @package gfa
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<section class="page-hero"><div class="wrap stack">
			<?php if ( 'post' === get_post_type() ) : ?>
				<p class="eyebrow"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div></section>
		<section class="section"><div class="wrap prose"><?php the_content(); ?><?php wp_link_pages(); ?></div></section>
	</article>
	<?php
endwhile;
if ( gfa_show_quote_form() ) {
	echo do_blocks( gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
