<?php
/**
 * Lavori pubblicati e referenze clienti, senza esempi dimostrativi.
 *
 * @package gfa
 */
$gfa_works = new WP_Query(
	array(
		'post_type'      => 'lavoro',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'no_found_rows'  => true,
	)
);
?>

<?php if ( $gfa_works->have_posts() ) : ?>
	<div class="works">
		<?php
		while ( $gfa_works->have_posts() ) :
			$gfa_works->the_post();
			get_template_part( 'parts/work-card' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
<?php endif; ?>
<?php get_template_part( 'parts/referenze' ); ?>

