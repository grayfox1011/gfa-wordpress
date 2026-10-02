<?php
/**
 * Fascia dei lavori: dal contenuto "Lavoro" se esiste, altrimenti i casi già noti come segnaposto.
 *
 * Dove la fascia scorre di lato (telefono, tablet, schermi bassi) frecce, contatore e barra dicono
 * che ci sono altri lavori; sul computer, con la fascia fissata da GSAP, restano nascosti.
 *
 * $args['heading'] = 'h2' quando l'elenco viene subito dopo l'h1.
 * $args['layout']  = 'grid' per la pagina Lavori svolti: griglia sul computer, slider sul telefono.
 * $args['exclude'] = lavoro da non ripetere (scheda di un lavoro).
 *
 * @package gfa
 */
$gfa_args  = array( 'heading' => isset( $args['heading'] ) && 'h2' === $args['heading'] ? 'h2' : 'h3' );
$gfa_open  = 'h2' === $gfa_args['heading'] ? '<h2 class="is-h3">' : '<h3>';
$gfa_close = 'h2' === $gfa_args['heading'] ? '</h2>' : '</h3>';
$gfa_grid  = isset( $args['layout'] ) && 'grid' === $args['layout'];
$gfa_track = wp_unique_id( 'gfa-lavori-' );
$gfa_works = new WP_Query(
	array(
		'post_type'      => 'lavoro',
		'posts_per_page' => 6,
		'post__not_in'   => empty( $args['exclude'] ) ? array() : array( (int) $args['exclude'] ),
		'no_found_rows'  => true,
	)
);
?>

<div class="works-slider" data-works-slider>
<div class="works<?php echo $gfa_grid ? ' works--grid' : ''; ?>" id="<?php echo esc_attr( $gfa_track ); ?>">
	<?php if ( $gfa_works->have_posts() ) : ?>
		<?php
		while ( $gfa_works->have_posts() ) :
			$gfa_works->the_post();
			get_template_part( 'parts/work-card', null, $gfa_args );
		endwhile;
		wp_reset_postdata();
		?>
	<?php else : ?>
		<article class="work">
			<?php gfa_photo_slot( 'Locandina "Cammin di Fiaba"', 'File originale senza logo sovrapposto' ); ?>
			<div class="work__meta"><span>Grafica · Evento</span><span>Santa Margherita Ligure · dic 2021</span></div>
			<?php echo $gfa_open; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?>Cammin di Fiaba – Christmas Edition<?php echo $gfa_close; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?>
			<dl><dt>Committente</dt><dd>Comune e associazione culturale</dd><dt>Attività</dt><dd>Locandina <span class="todo">distribuzione? da confermare</span></dd></dl>
		</article>
		<article class="work">
			<?php gfa_photo_slot( 'Locandina "Lady Killer"', 'File originale senza logo sovrapposto' ); ?>
			<div class="work__meta"><span>Grafica · Evento</span><span>Rapallo · giu 2022</span></div>
			<?php echo $gfa_open; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?>Lady Killer – giallo interattivo<?php echo $gfa_close; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?>
			<dl><dt>Committente</dt><dd>Associazione, con il sostegno dei Comuni di Rapallo e Zoagli</dd><dt>Attività</dt><dd>Locandina <span class="todo">permesso di pubblicazione</span></dd></dl>
		</article>
		<article class="work">
			<?php gfa_photo_slot( 'Volantinaggio per una catena', 'Foto del materiale, report anonimizzato' ); ?>
			<div class="work__meta"><span>Volantinaggio</span><span class="todo">zona e periodo</span></div>
			<?php echo $gfa_open; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?><span class="todo">Cliente da documentare</span><?php echo $gfa_close; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?>
			<dl><dt>Esigenza</dt><dd><span class="todo">da raccogliere</span></dd><dt>Prova</dt><dd>Report del giro</dd></dl>
		</article>
		<article class="work">
			<?php gfa_photo_slot( 'Volantini per un\'attività locale', 'Ristorante, negozio o agenzia del Tigullio' ); ?>
			<div class="work__meta"><span>Grafica + stampa + distribuzione</span><span class="todo">zona e periodo</span></div>
			<?php echo $gfa_open; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?><span class="todo">Cliente da documentare</span><?php echo $gfa_close; // phpcs:ignore WordPress.Security.EscapeOutput -- valore fisso. ?>
			<dl><dt>Esigenza</dt><dd><span class="todo">da raccogliere</span></dd><dt>Copie</dt><dd><span class="todo">numero reale</span></dd></dl>
		</article>
	<?php endif; ?>
</div>
<?php get_template_part( 'parts/works-nav', null, array( 'track' => $gfa_track ) ); ?>
</div>
