<?php
/**
 * Lavori svolti: dal contenuto "Lavoro" se esiste, altrimenti i casi già noti come segnaposto.
 *
 * @package gfa
 */
$gfa_works = new WP_Query(
	array(
		'post_type'      => 'lavoro',
		'posts_per_page' => 6,
		'no_found_rows'  => true,
	)
);
?>
<section class="section" id="lavori">
	<div class="works-pin">
		<div class="wrap">
			<div class="works-head">
				<div class="section__head" style="margin:0">
					<p class="eyebrow"><?php esc_html_e( 'Lavori svolti', 'gfa' ); ?></p>
					<h2>Prima di chiederci un preventivo, guarda cosa abbiamo fatto.</h2>
				</div>
				<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/lavori/' ) ); ?>"><?php esc_html_e( 'Tutti i lavori', 'gfa' ); ?></a>
			</div>
			<div class="works">
				<?php if ( $gfa_works->have_posts() ) : ?>
					<?php
					while ( $gfa_works->have_posts() ) :
						$gfa_works->the_post();
						get_template_part( 'parts/work-card' );
					endwhile;
					wp_reset_postdata();
					?>
				<?php else : ?>
					<article class="work">
						<?php gfa_photo_slot( 'Locandina "Cammin di Fiaba"', 'File originale senza logo sovrapposto' ); ?>
						<div class="work__meta"><span>Grafica · Evento</span><span>Santa Margherita Ligure · dic 2021</span></div>
						<h3>Cammin di Fiaba – Christmas Edition</h3>
						<dl><dt>Committente</dt><dd>Comune e associazione culturale</dd><dt>Attività</dt><dd>Locandina <span class="todo">distribuzione? da confermare</span></dd></dl>
					</article>
					<article class="work">
						<?php gfa_photo_slot( 'Locandina "Lady Killer"', 'File originale senza logo sovrapposto' ); ?>
						<div class="work__meta"><span>Grafica · Evento</span><span>Rapallo · giu 2022</span></div>
						<h3>Lady Killer – giallo interattivo</h3>
						<dl><dt>Committente</dt><dd>Associazione, con il sostegno dei Comuni di Rapallo e Zoagli</dd><dt>Attività</dt><dd>Locandina <span class="todo">permesso di pubblicazione</span></dd></dl>
					</article>
					<article class="work">
						<?php gfa_photo_slot( 'Volantinaggio per una catena', 'Foto del materiale, report anonimizzato' ); ?>
						<div class="work__meta"><span>Volantinaggio</span><span class="todo">zona e periodo</span></div>
						<h3><span class="todo">Cliente da documentare</span></h3>
						<dl><dt>Esigenza</dt><dd><span class="todo">da raccogliere</span></dd><dt>Prova</dt><dd>Report del giro</dd></dl>
					</article>
					<article class="work">
						<?php gfa_photo_slot( 'Volantini per un\'attività locale', 'Ristorante, negozio o agenzia del Tigullio' ); ?>
						<div class="work__meta"><span>Grafica + stampa + distribuzione</span><span class="todo">zona e periodo</span></div>
						<h3><span class="todo">Cliente da documentare</span></h3>
						<dl><dt>Esigenza</dt><dd><span class="todo">da raccogliere</span></dd><dt>Copie</dt><dd><span class="todo">numero reale</span></dd></dl>
					</article>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
