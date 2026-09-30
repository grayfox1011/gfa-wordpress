<?php
/**
 * Fallback generico.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero"><div class="wrap"><h1><?php echo is_home() ? esc_html__( 'Notizie', 'gfa' ) : wp_kses_post( get_the_archive_title() ); ?></h1></div></section>
<section class="section"><div class="wrap stack">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="stack"><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nessun contenuto.', 'gfa' ); ?></p>
	<?php endif; ?>
</div></section>
<?php
get_footer();
