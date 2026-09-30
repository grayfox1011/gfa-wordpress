<?php
/**
 * Archivio Zone servite.
 *
 * @package gfa
 */

get_header();
?>
<section class="page-hero"><div class="wrap stack"><p class="eyebrow">Zone servite</p><h1>Volantinaggio nelle nostre zone</h1></div></section>
<?php
echo do_blocks( gfa_pattern( 'zone' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
get_footer();
