<?php
/**
 * Template Name: Pagina a sezioni GFA
 *
 * Pagina larga, senza titolo automatico: il contenuto è fatto con i modelli "Sezioni GFA".
 *
 * @package gfa
 */

get_header();
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
get_footer();
