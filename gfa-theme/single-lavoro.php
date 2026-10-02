<?php
/**
 * Scheda di un lavoro: esigenza, attività e prova, foto, poi gli altri lavori e il preventivo.
 *
 * @package gfa
 */

get_header();
while ( have_posts() ) :
	the_post();
	$gfa_id       = get_the_ID();
	$gfa_servizio = (string) get_post_meta( $gfa_id, 'gfa_servizio', true );
	$gfa_periodo  = (string) get_post_meta( $gfa_id, 'gfa_zona_periodo', true );
	?>
	<section class="page-hero"><div class="wrap stack">
		<p class="eyebrow"><a href="<?php echo esc_url( get_post_type_archive_link( 'lavoro' ) ); ?>"><?php esc_html_e( 'Lavori svolti', 'gfa' ); ?></a><?php echo '' !== $gfa_servizio ? ' · ' . esc_html( $gfa_servizio ) : ''; ?></p>
		<h1><?php the_title(); ?></h1>
		<?php if ( '' !== $gfa_periodo ) : ?>
			<p class="lead"><?php echo esc_html( $gfa_periodo ); ?></p>
		<?php endif; ?>
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
				$gfa_value = trim( (string) get_post_meta( $gfa_id, $gfa_key, true ) );
				if ( '' === $gfa_value ) {
					continue; // campo non compilato: niente titolo vuoto
				}
				?>
				<div><h2 class="is-h3"><?php echo esc_html( $gfa_label ); ?></h2><p><?php echo esc_html( $gfa_value ); ?></p></div>
			<?php endforeach; ?>
			<div class="prose"><?php the_content(); ?></div>
		</div>
		<div>
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'large' );
			} else {
				gfa_photo_slot( get_the_title(), 'Immagine in evidenza da caricare' );
			}
			?>
		</div>
	</div></section>
	<?php
	// Gli altri lavori nella fascia che scorre di lato: sul telefono è uno slider.
	$gfa_others = get_posts(
		array(
			'post_type'    => 'lavoro',
			'post__not_in' => array( $gfa_id ),
			'numberposts'  => 1,
			'fields'       => 'ids',
		)
	);
	if ( $gfa_others ) :
		?>
		<section class="section section--alt" id="altri-lavori"><div class="works-pin"><div class="wrap">
			<div class="works-head">
				<div class="section__head"><p class="eyebrow"><?php esc_html_e( 'Lavori svolti', 'gfa' ); ?></p><h2><?php esc_html_e( 'Altri lavori', 'gfa' ); ?></h2></div>
				<div class="wp-block-buttons"><div class="wp-block-button is-style-gfa-ghost"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_post_type_archive_link( 'lavoro' ) ); ?>"><?php esc_html_e( 'Tutti i lavori', 'gfa' ); ?></a></div></div>
			</div>
			<?php get_template_part( 'parts/lavori', null, array( 'exclude' => $gfa_id ) ); ?>
		</div></div></section>
		<?php
	endif;
endwhile;
if ( gfa_show_quote_form() ) {
	echo do_blocks( gfa_pattern( 'preventivo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
get_footer();
