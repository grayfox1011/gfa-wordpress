<?php
/**
 * Footer con dati societari (dal pannello Dati GFA).
 *
 * @package gfa
 */
?>
</main>
<footer class="site-footer">
	<div class="wrap">
		<div class="footer-grid">
			<div class="stack">
				<span class="brand"><span class="brand__mark" aria-hidden="true">GFA</span><?php echo esc_html( gfa_opt( 'brand' ) ); ?></span>
				<p><?php esc_html_e( 'Volantinaggio, stampa e promozione sul territorio.', 'gfa' ); ?> <?php if ( gfa_opt( 'anno' ) ) { printf( esc_html__( 'Dal %s.', 'gfa' ), esc_html( gfa_opt( 'anno' ) ) ); } ?></p>
			</div>
			<div>
				<h2><?php esc_html_e( 'Servizi', 'gfa' ); ?></h2>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php
					// Aspetto → Menu, posizione "Menu footer": sostituisce l'elenco predefinito.
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				<?php else : ?>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/volantinaggio/' ) ); ?>">Volantinaggio</a></li>
						<li><a href="<?php echo esc_url( home_url( '/stampa-e-grafica/' ) ); ?>">Stampa e grafica</a></li>
						<li><a href="<?php echo esc_url( home_url( '/promozione-eventi/' ) ); ?>">Promozione eventi</a></li>
						<li><a href="<?php echo esc_url( home_url( '/lavori/' ) ); ?>">Lavori svolti</a></li>
					</ul>
				<?php endif; ?>
			</div>
			<div>
				<h2><?php esc_html_e( 'Sedi', 'gfa' ); ?></h2>
				<ul>
					<?php foreach ( gfa_sedi() as $sede ) : ?>
						<li><?php echo esc_html( $sede ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div>
				<h2><?php esc_html_e( 'Contatti', 'gfa' ); ?></h2>
				<ul>
					<li><?php gfa_value( 'telefono', 'telefono principale' ); ?></li>
					<li><?php echo esc_html( gfa_opt( 'email' ) ); ?></li>
					<li><?php gfa_value( 'sede_legale', 'indirizzo sede' ); ?></li>
					<li><a href="<?php echo esc_url( home_url( '/franchising/' ) ); ?>"><?php esc_html_e( 'Apri una sede GFA', 'gfa' ); ?></a></li>
				</ul>
			</div>
		</div>
		<div class="legal">
			<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php gfa_value( 'ragione', 'ragione sociale' ); ?></span>
			<span>P.IVA <?php gfa_value( 'piva', 'P.IVA da confermare' ); ?></span>
			<?php if ( gfa_opt( 'rea' ) ) : ?><span>REA <?php echo esc_html( gfa_opt( 'rea' ) ); ?></span><?php endif; ?>
			<?php if ( function_exists( 'get_privacy_policy_url' ) && get_privacy_policy_url() ) : ?>
				<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy', 'gfa' ); ?></a>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>"><?php esc_html_e( 'Cookie', 'gfa' ); ?></a>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
