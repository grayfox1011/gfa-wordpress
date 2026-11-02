<?php
/** Campi SEO per le pagine e i contenuti GFA, senza cambiare il testo visibile. @package gfa */
defined( 'ABSPATH' ) || exit;

function gfa_seo_meta_boxes() {
	if ( ! gfa_seo_enabled() ) { return; }
	foreach ( array( 'page', 'post', 'lavoro', 'zona' ) as $type ) {
		add_meta_box( 'gfa-seo', 'SEO GFA', 'gfa_seo_meta_box', $type, 'normal', 'default' );
	}
}
add_action( 'add_meta_boxes', 'gfa_seo_meta_boxes' );

function gfa_seo_meta_box( $post ) {
	wp_nonce_field( 'gfa_seo_save', 'gfa_seo_nonce' );
	$key = (int) get_option( 'page_on_front' ) === (int) $post->ID ? 'home' : $post->post_name;
	$all = gfa_seo_defaults();
	$defaults = isset( $all[ $key ] ) ? $all[ $key ] : array( $post->post_title . ' | GFA', $post->post_excerpt );
	$title = get_post_meta( $post->ID, '_gfa_seo_title', true );
	$desc = get_post_meta( $post->ID, '_gfa_seo_description', true );
	echo '<p>Lascia i campi vuoti per usare i testi proposti. Questi campi modificano i metadati, non il titolo visibile nella pagina.</p>';
	echo '<p><label for="gfa-seo-title"><strong>Titolo SEO</strong></label><br><input class="widefat" id="gfa-seo-title" name="gfa_seo_title" type="text" value="' . esc_attr( $title ) . '" placeholder="' . esc_attr( $defaults[0] ) . '"></p>';
	echo '<p><label for="gfa-seo-description"><strong>Descrizione per i motori di ricerca</strong></label><br><textarea class="widefat" rows="3" id="gfa-seo-description" name="gfa_seo_description" placeholder="' . esc_attr( $defaults[1] ) . '">' . esc_textarea( $desc ) . '</textarea></p>';
	echo '<p><label><input type="checkbox" name="gfa_seo_noindex" value="1" ' . checked( '1', get_post_meta( $post->ID, '_gfa_seo_noindex', true ), false ) . '> Escludi questa pagina dai motori di ricerca e dalla sitemap</label></p>';
	if ( ! get_option( 'blog_public' ) ) {
		echo '<p><strong>Sito di prova:</strong> l’indicizzazione dell’intero sito è disattivata nelle impostazioni di lettura. Questi campi non la attivano.</p>';
	}
}

function gfa_seo_save( $post_id ) {
	if ( ! gfa_seo_enabled() || ! isset( $_POST['gfa_seo_nonce'] ) || ! is_string( $_POST['gfa_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gfa_seo_nonce'] ) ), 'gfa_seo_save' ) ) { return; }
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || ! in_array( get_post_type( $post_id ), array( 'page', 'post', 'lavoro', 'zona' ), true ) ) { return; }
	foreach ( array( 'title', 'description' ) as $field ) {
		if ( ! isset( $_POST[ 'gfa_seo_' . $field ] ) || ! is_string( $_POST[ 'gfa_seo_' . $field ] ) ) { continue; }
		$value = sanitize_text_field( wp_unslash( $_POST[ 'gfa_seo_' . $field ] ) );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_gfa_seo_' . $field );
		} else {
			update_post_meta( $post_id, '_gfa_seo_' . $field, $value );
		}
	}
	if ( isset( $_POST['gfa_seo_noindex'] ) && '1' === $_POST['gfa_seo_noindex'] ) {
		update_post_meta( $post_id, '_gfa_seo_noindex', '1' );
	} else {
		delete_post_meta( $post_id, '_gfa_seo_noindex' );
	}
}
add_action( 'save_post', 'gfa_seo_save' );
