<?php
/**
 * Archivio Lavori svolti.
 *
 * @package gfa
 */

get_header();
$gfa_has_works = have_posts();
?>
<section class="page-hero"><div class="wrap stack"><p class="eyebrow">Portfolio</p><h1><?php echo $gfa_has_works ? esc_html__( 'Lavori e referenze', 'gfa' ) : esc_html__( 'Clienti e referenze', 'gfa' ); ?></h1></div></section>
<section class="section"><div class="wrap">
	<?php if ( $gfa_has_works ) : ?>
		<div class="works works--grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'parts/work-card' );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php endif; ?>
	<?php get_template_part( 'parts/referenze' ); ?>
</div></section>
<?php
get_footer();

