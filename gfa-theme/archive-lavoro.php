<?php
/**
 * Archivio Lavori svolti.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero"><div class="wrap stack"><p class="eyebrow">Lavori svolti</p><h1>Cosa abbiamo fatto, e come</h1><p class="lead">Ogni scheda dice l'esigenza del cliente, la zona, cosa abbiamo fatto e quale prova esiste.</p></div></section>
<section class="section"><div class="wrap">
	<div class="works" style="flex-wrap:wrap;overflow:visible">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'parts/work-card' );
		endwhile;
		?>
	</div>
	<?php the_posts_pagination(); ?>
</div></section>
<?php
get_footer();
