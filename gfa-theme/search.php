<?php
/**
 * Risultati della ricerca.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero"><div class="wrap stack">
	<h1>
		<?php
		/* translators: %s: le parole cercate. */
		printf( esc_html__( 'Risultati per «%s»', 'gfa' ), esc_html( get_search_query() ) );
		?>
	</h1>
	<?php get_search_form(); ?>
</div></section>
<section class="section"><div class="wrap stack">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="stack"><h2 class="is-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nessun risultato. Prova con un\'altra parola, oppure chiedi un preventivo: ti rispondiamo noi.', 'gfa' ); ?></p>
	<?php endif; ?>
</div></section>
<?php
get_footer();
