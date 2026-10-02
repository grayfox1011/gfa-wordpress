<?php
/**
 * Lavori svolti (/lavori/).
 *
 * Mostra la pagina "Lavori svolti" (slug lavori), fatta con le sezioni GFA e modificabile con
 * Gutenberg: il blocco "GFA · Tutti i lavori" mette le schede al suo posto. Senza quella pagina,
 * o se è vuota, si vede il modello "Pagina Lavori svolti".
 *
 * @package gfa
 */

get_header();
$gfa_page = get_page_by_path( 'lavori' );
if ( $gfa_page && 'publish' === $gfa_page->post_status && '' !== trim( $gfa_page->post_content ) ) {
	echo apply_filters( 'the_content', $gfa_page->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput -- contenuto della pagina, filtrato da WordPress come in the_content().
} else {
	echo do_blocks( gfa_page_content( 'lavori' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- markup a blocchi del tema.
}
get_footer();
