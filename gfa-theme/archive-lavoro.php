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
	<?php if ( have_posts() ) : ?>
		<div class="works works--grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'parts/work-card', null, array( 'heading' => 'h2' ) );
			endwhile;
			?>
		</div>
	<?php else : ?>
		<p class="lead"><mark class="todo">Nessun lavoro pubblicato: aggiungili da Lavori svolti → Aggiungi lavoro. Intanto si vedono gli esempi da completare.</mark></p>
		<?php get_template_part( 'parts/lavori', null, array( 'heading' => 'h2' ) ); ?>
	<?php endif; ?>
	<?php the_posts_pagination(); ?>
</div></section>
<?php
get_footer();
