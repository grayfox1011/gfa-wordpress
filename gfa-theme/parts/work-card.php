<?php
/**
 * Card di un lavoro.
 *
 * @package gfa
 */
$gfa_id = get_the_ID();
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
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<dl>
		<dt><?php esc_html_e( 'Esigenza', 'gfa' ); ?></dt><dd><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_esigenza', true ) ); ?></dd>
		<dt><?php esc_html_e( 'Prova', 'gfa' ); ?></dt><dd><?php echo esc_html( get_post_meta( $gfa_id, 'gfa_prova', true ) ); ?></dd>
	</dl>
</article>
