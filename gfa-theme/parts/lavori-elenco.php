<?php
/**
 * Tutti i lavori in griglia (blocco "GFA · Tutti i lavori", pagina Lavori svolti).
 *
 * In /lavori/ usa i lavori della pagina, con i numeri di pagina in fondo; altrove (anteprima
 * nell'editor, una pagina qualsiasi) mostra gli ultimi 12. Senza lavori pubblicati mostra gli
 * esempi da completare. Sul computer è una griglia; sotto i 1024 px uno slider con le frecce,
 * così con molti lavori la pagina non diventa lunghissima.
 *
 * @package gfa
 */

global $wp_query;
$gfa_archive = is_post_type_archive( 'lavoro' );
$gfa_works   = $gfa_archive ? $wp_query : new WP_Query(
	array(
		'post_type'      => 'lavoro',
		'posts_per_page' => 12,
		'no_found_rows'  => true,
	)
);
?>
<?php if ( $gfa_works->have_posts() ) : ?>
	<?php $gfa_track = wp_unique_id( 'gfa-lavori-' ); ?>
	<div class="works-slider" data-works-slider>
		<div class="works works--grid" id="<?php echo esc_attr( $gfa_track ); ?>">
			<?php
			while ( $gfa_works->have_posts() ) :
				$gfa_works->the_post();
				get_template_part( 'parts/work-card' );
			endwhile;
			$gfa_works->rewind_posts();
			wp_reset_postdata();
			?>
		</div>
		<?php get_template_part( 'parts/works-nav', null, array( 'track' => $gfa_track ) ); ?>
	</div>
	<?php
	if ( $gfa_archive ) {
		the_posts_pagination( array( 'mid_size' => 1 ) );
	}
	?>
<?php else : ?>
	<p class="works-empty"><mark class="todo">Nessun lavoro pubblicato: aggiungili da Lavori svolti → Aggiungi lavoro. Intanto si vedono gli esempi da completare.</mark></p>
	<?php get_template_part( 'parts/lavori', null, array( 'layout' => 'grid' ) ); ?>
<?php endif; ?>
