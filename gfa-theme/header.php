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
<header class="site-header">
	<div class="wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'GFA, torna alla homepage', 'gfa' ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'thumbnail', false, array( 'class' => 'brand__logo', 'alt' => '' ) ); ?>
			<?php else : ?>
				<span class="brand__mark" aria-hidden="true">GFA</span>
			<?php endif; ?>
			<span><?php echo esc_html( gfa_opt( 'brand' ) ); ?><span class="brand__sub"><?php esc_html_e( 'Volantinaggio e distribuzione', 'gfa' ); ?></span></span>
		</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav"><?php esc_html_e( 'Menu', 'gfa' ); ?></button>
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
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/#preventivo' ) ); ?>"><?php esc_html_e( 'Chiedi un preventivo', 'gfa' ); ?></a>
		</nav>
	</div>
</header>
<main id="main">
