<?php
/**
 * 404.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero"><div class="wrap stack">
	<h1><?php esc_html_e( 'Questa pagina non c\'è più.', 'gfa' ); ?></h1>
	<p class="lead"><?php esc_html_e( 'Il sito è stato riorganizzato. Parti da qui:', 'gfa' ); ?></p>
	<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/volantinaggio/' ) ); ?>">Volantinaggio</a> <a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">Homepage</a></p>
</div></section>
<?php
get_footer();
