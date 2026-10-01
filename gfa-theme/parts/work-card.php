<?php
/**
 * Card di un lavoro.
 *
 * Titolo h3 sotto il titolo di una sezione; h2 nell'archivio, dove viene subito dopo l'h1
 * (get_template_part( 'parts/work-card', null, array( 'heading' => 'h2' ) )).
 *
 * @package gfa
 */
$gfa_id      = get_the_ID();
$gfa_h2      = isset( $args['heading'] ) && 'h2' === $args['heading'];
?>
<article class="work">
	<a href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'gfa-card', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<?php gfa_photo_slot( get_the_title(), 'Immagine in evidenza da caricare' ); ?>
		<?php endif; ?>
	</a>
	<div class="work__meta"><span><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_servizio', true ) ); ?></span><span><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_zona_periodo', true ) ); ?></span></div>
	<?php echo $gfa_h2 ? '<h2 class="is-h3">' : '<h3>'; ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php echo $gfa_h2 ? '</h2>' : '</h3>'; ?>
	<dl>
		<dt><?php esc_html_e( 'Esigenza', 'gfa' ); ?></dt><dd><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_esigenza', true ) ); ?></dd>
		<dt><?php esc_html_e( 'Prova', 'gfa' ); ?></dt><dd><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_prova', true ) ); ?></dd>
	</dl>
</article>
