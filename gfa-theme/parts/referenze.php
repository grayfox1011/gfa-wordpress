<?php
/** Referenze indicate da GFA, senza attribuire progetti o risultati specifici. */
$gfa_clienti = array(
	array( 'Fastweb', '' ),
	array( 'Vodafone', 'vodafone.svg' ),
	array( 'Edilizia Acrobatica', '' ),
	array( 'Sky', 'sky.svg' ),
	array( 'Tecnocasa', '' ),
	array( 'Iconacasa', '' ),
	array( 'Mediolanum', 'mediolanum.svg' ),
	array( 'Grandi Agenzie', '' ),
	array( 'Ministero della Sanità', '' ),
);
?>
<div class="clienti" aria-labelledby="clienti-title">
	<h2 id="clienti-title" class="clienti__title">Le nostre referenze</h2>
	<ul class="clienti__list">
		<?php foreach ( $gfa_clienti as $cliente ) : ?>
			<li class="clienti__item<?php echo 'mediolanum.svg' === $cliente[1] ? ' clienti__item--wordmark' : ''; ?>">
				<?php if ( $cliente[1] ) : ?>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/clienti/' . $cliente[1] ); ?>" alt="<?php echo 'mediolanum.svg' === $cliente[1] ? esc_attr( $cliente[0] ) : ''; ?>" loading="lazy" />
				<?php endif; ?>
				<?php if ( 'mediolanum.svg' !== $cliente[1] ) : ?><span><?php echo esc_html( $cliente[0] ); ?></span><?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
