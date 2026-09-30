<?php
/**
 * Scheda di un lavoro.
 *
 * @package gfa
 */

get_header();
while ( have_posts() ) :
	the_post();
	$gfa_id = get_the_ID();
	?>
	<section class="page-hero"><div class="wrap stack">
		<p class="eyebrow"><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_servizio', true ) ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="lead"><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_zona_periodo', true ) ); ?></p>
	</div></section>
	<section class="section"><div class="wrap control">
		<div class="stack">
			<?php
			$gfa_rows = array(
				'Esigenza' => 'gfa_esigenza',
				'Attività' => 'gfa_attivita',
				'Prova'    => 'gfa_prova',
			);
			foreach ( $gfa_rows as $gfa_label => $gfa_key ) :
				?>
				<div><h3><?php echo esc_html( $gfa_label ); ?></h3><p><?php echo esc_html( get_post_meta( $gfa_id, $gfa_key, true ) ); ?></p></div>
			<?php endforeach; ?>
			<div class="prose"><?php the_content(); ?></div>
		</div>
		<div><?php the_post_thumbnail( 'large' ); ?></div>
	</div></section>
	<?php
endwhile;
get_template_part( 'parts/quote' );
get_footer();
