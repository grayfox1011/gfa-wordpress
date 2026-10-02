<?php
/**
 * Header.
 *
 * @package gfa
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Vai al contenuto', 'gfa' ); ?></a>
<div class="page-curtain" aria-hidden="true"></div>
<div class="preloader" aria-hidden="true">
	<div class="preloader__inner">
		<div class="preloader__box"><span class="preloader__letter">G</span><span class="preloader__letter">F</span><span class="preloader__letter">A</span></div>
		<svg class="preloader__route" viewBox="0 0 240 24" focusable="false"><path d="M6 12 H226"/><circle cx="230" cy="12" r="6"/></svg>
		<p class="preloader__claim"><?php esc_html_e( 'Volantinaggio che si può verificare', 'gfa' ); ?></p>
	</div>
</div>
<header class="site-header">
	<div class="wrap">
		<?php // Un logo largo (almeno 2:1) contiene già il nome: il testo accanto non si ripete. ?>
		<a class="brand<?php echo has_custom_logo() && gfa_logo_ratio() >= 2 ? ' brand--logo-wide' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'GFA, torna alla homepage', 'gfa' ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo gfa_brand_logo(); // phpcs:ignore WordPress.Security.EscapeOutput -- immagine di WordPress. ?>
			<?php else : ?>
				<span class="brand__mark" aria-hidden="true">GFA</span>
			<?php endif; ?>
			<?php // «e» resta attaccata alla parola dopo: su due righe il sottotitolo non lascia la «e» da sola. ?>
			<span class="brand__text"><?php echo esc_html( gfa_opt( 'brand' ) ); ?><span class="brand__sub"><?php echo esc_html( str_replace( ' e ', " e\u{a0}", __( 'Volantinaggio e distribuzione', 'gfa' ) ) ); ?></span></span>
		</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-label-closed="<?php esc_attr_e( 'Menu', 'gfa' ); ?>" data-label-open="<?php esc_attr_e( 'Chiudi', 'gfa' ); ?>"><?php esc_html_e( 'Menu', 'gfa' ); ?></button>
		<nav class="nav" id="site-nav" aria-label="<?php esc_attr_e( 'Principale', 'gfa' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'gfa_fallback_menu',
				)
			);
			?>
			<?php if ( gfa_opt( 'telefono' ) ) : ?>
				<a class="nav__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', gfa_opt( 'telefono' ) ) ); ?>"><?php echo esc_html( gfa_opt( 'telefono' ) ); ?></a>
			<?php endif; ?>
			<?php // In homepage il link resta nella pagina; altrove main.js scorre al modulo se la pagina lo contiene già. ?>
			<a class="btn btn--primary" href="<?php echo esc_url( is_front_page() ? '#preventivo' : home_url( '/#preventivo' ) ); ?>" data-gfa-local="preventivo"><?php esc_html_e( 'Chiedi un preventivo', 'gfa' ); ?></a>
		</nav>
	</div>
</header>
<main id="main">
