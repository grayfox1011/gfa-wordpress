<?php
/**
 * Homepage: il contenuto della pagina impostata come homepage (modelli GFA).
 * Se la pagina è vuota mostra il modello "Pagina Homepage".
 *
 * @package gfa
 */

get_header();
if ( have_posts() ) {
	the_post();
}
if ( is_page() && '' !== trim( get_the_content() ) ) {
	the_content();
} else {
	echo do_blocks( gfa_page_content( 'home' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- markup a blocchi del tema.
}
get_footer();
