<?php
/**
 * Header.
 *
 * @package gfa
 */
$gfa_has_logo = has_custom_logo();
$gfa_logo_id  = (int) get_theme_mod( 'custom_logo' );
$gfa_whatsapp_number = preg_replace( '/\D/', '', gfa_opt( 'whatsapp' ) ?: gfa_opt( 'telefono' ) );
if ( 0 === strpos( $gfa_whatsapp_number, '00' ) ) {
	$gfa_whatsapp_number = substr( $gfa_whatsapp_number, 2 );
}
if ( 10 === strlen( $gfa_whatsapp_number ) && '3' === substr( $gfa_whatsapp_number, 0, 1 ) ) {
	$gfa_whatsapp_number = '39' . $gfa_whatsapp_number;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="light only">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Vai al contenuto', 'gfa' ); ?></a>
<div class="page-curtain" aria-hidden="true"></div>
<div class="preloader" aria-hidden="true">
	<div class="preloader__inner">
		<div class="preloader__box<?php echo $gfa_has_logo ? ' preloader__box--logo' : ''; ?>">
			<?php if ( $gfa_has_logo ) : ?>
				<?php echo wp_get_attachment_image( $gfa_logo_id, 'full', false, array( 'class' => 'preloader__logo', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 525px) 80vw, 420px' ) ); ?>
			<?php else : ?>
				<span class="preloader__letter">G</span><span class="preloader__letter">F</span><span class="preloader__letter">A</span>
			<?php endif; ?>
		</div>
		<svg class="preloader__route" viewBox="0 0 240 24" focusable="false"><path d="M6 12 H226"/><circle cx="230" cy="12" r="6"/></svg>
<p class="preloader__claim"><?php esc_html_e( 'Volantinaggio che dà visibilità alla tua attività.', 'gfa' ); ?></p>
	</div>
</div>
<header class="site-header">
	<div class="wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'GFA, torna alla homepage', 'gfa' ); ?>">
			<?php if ( $gfa_has_logo ) : ?>
				<?php echo wp_get_attachment_image( $gfa_logo_id, 'full', false, array( 'class' => 'brand__logo', 'alt' => '', 'loading' => 'eager', 'sizes' => '(max-width: 600px) 150px, 180px' ) ); ?>
			<?php else : ?>
				<span class="brand__mark" aria-hidden="true">GFA</span>
				<span><?php echo esc_html( gfa_opt( 'brand' ) ); ?><span class="brand__sub"><?php esc_html_e( 'Volantinaggio e distribuzione', 'gfa' ); ?></span></span>
			<?php endif; ?>
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
			<?php if ( preg_match( '/^[1-9][0-9]{7,14}$/D', $gfa_whatsapp_number ) ) : ?>
				<a class="btn btn--whatsapp" href="<?php echo esc_url( 'https://wa.me/' . $gfa_whatsapp_number ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( __( 'Scrivici su WhatsApp al +%s (si apre in una nuova scheda)', 'gfa' ), $gfa_whatsapp_number ) ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M20.52 3.48A11.91 11.91 0 0 0 12.05 0C5.47 0 .12 5.35.12 11.93c0 2.1.55 4.15 1.6 5.96L0 24l6.28-1.65a11.9 11.9 0 0 0 5.77 1.47h.01c6.57 0 11.92-5.35 11.93-11.93a11.86 11.86 0 0 0-3.47-8.41ZM12.05 21.8a9.9 9.9 0 0 1-5.05-1.38l-.36-.21-3.73.98 1-3.64-.24-.38a9.9 9.9 0 0 1-1.51-5.24c0-5.46 4.44-9.9 9.91-9.9a9.84 9.84 0 0 1 7.02 2.9 9.83 9.83 0 0 1 2.9 7.02c0 5.46-4.45 9.9-9.94 9.9Zm5.44-7.42c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.8-1.49-1.78-1.67-2.08-.17-.3-.02-.46.13-.61l.45-.52c.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.21-.24-.58-.48-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.27.49 1.7.63.72.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z"/></svg>
					<span><?php esc_html_e( 'WhatsApp', 'gfa' ); ?></span>
				</a>
			<?php endif; ?>
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/#preventivo' ) ); ?>"><?php esc_html_e( 'Chiedi un preventivo', 'gfa' ); ?></a>
		</nav>
	</div>
</header>
<main id="main">

