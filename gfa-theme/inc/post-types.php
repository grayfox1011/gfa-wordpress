<?php
/**
 * Contenuti del tema: Lavori svolti e Zone servite.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'lavoro',
			array(
				'labels'       => array(
					'name'          => __( 'Lavori svolti', 'gfa' ),
					'singular_name' => __( 'Lavoro', 'gfa' ),
					'add_new_item'  => __( 'Aggiungi lavoro', 'gfa' ),
				),
				'public'       => true,
				'has_archive'  => 'lavori',
				'rewrite'      => array( 'slug' => 'lavori' ),
				'menu_icon'    => 'dashicons-portfolio',
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'show_in_rest' => true,
			)
		);
		register_post_type(
			'zona',
			array(
				'labels'       => array(
					'name'          => __( 'Zone servite', 'gfa' ),
					'singular_name' => __( 'Zona', 'gfa' ),
					'add_new_item'  => __( 'Aggiungi zona', 'gfa' ),
				),
				'public'       => true,
				'has_archive'  => 'zone',
				'rewrite'      => array( 'slug' => 'volantinaggio' ),
				'menu_icon'    => 'dashicons-location',
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'show_in_rest' => true,
			)
		);

		// Campi della scheda lavoro: esigenza → zona e periodo → attività → prova.
		foreach ( array( 'esigenza', 'zona_periodo', 'attivita', 'prova', 'servizio' ) as $meta ) {
			register_post_meta(
				'lavoro',
				'gfa_' . $meta,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
		}
		foreach ( array( 'indirizzo', 'comuni', 'telefono' ) as $meta ) {
			register_post_meta(
				'zona',
				'gfa_' . $meta,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
		}
	}
);

/**
 * Box "Scheda del lavoro" e "Dati della zona" nell'editor.
 */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'gfa_lavoro', __( 'Scheda del lavoro', 'gfa' ), 'gfa_lavoro_box', 'lavoro', 'normal', 'high' );
		add_meta_box( 'gfa_zona', __( 'Dati della zona', 'gfa' ), 'gfa_zona_box', 'zona', 'normal', 'high' );
	}
);

/**
 * Campi meta come input semplici.
 *
 * @param WP_Post $post   Post.
 * @param array   $fields Chiave => etichetta.
 */
function gfa_meta_fields( $post, $fields ) {
	wp_nonce_field( 'gfa_meta', 'gfa_meta_nonce' );
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, 'gfa_' . $key, true );
		echo '<p><label for="gfa_' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
		echo '<input class="widefat" type="text" id="gfa_' . esc_attr( $key ) . '" name="gfa_' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '"></p>';
	}
}

/**
 * Box lavoro.
 *
 * @param WP_Post $post Post.
 */
function gfa_lavoro_box( $post ) {
	gfa_meta_fields(
		$post,
		array(
			'servizio'     => 'Servizio (es. Volantinaggio, Grafica, Promozione eventi)',
			'esigenza'     => 'Esigenza del cliente',
			'zona_periodo' => 'Zona e periodo',
			'attivita'     => 'Attività svolte',
			'prova'        => 'Prova disponibile (report GPS, foto, copie)',
		)
	);
}

/**
 * Box zona.
 *
 * @param WP_Post $post Post.
 */
function gfa_zona_box( $post ) {
	gfa_meta_fields(
		$post,
		array(
			'indirizzo' => 'Indirizzo della sede',
			'telefono'  => 'Telefono della sede',
			'comuni'    => 'Comuni serviti, separati da virgola',
		)
	);
}

add_action(
	'save_post',
	function ( $post_id ) {
		if ( ! isset( $_POST['gfa_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gfa_meta_nonce'] ), 'gfa_meta' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array( 'servizio', 'esigenza', 'zona_periodo', 'attivita', 'prova', 'indirizzo', 'telefono', 'comuni' ) as $key ) {
			if ( isset( $_POST[ 'gfa_' . $key ] ) ) {
				update_post_meta( $post_id, 'gfa_' . $key, sanitize_text_field( wp_unslash( $_POST[ 'gfa_' . $key ] ) ) );
			}
		}
	}
);

add_action(
	'after_switch_theme',
	function () {
		flush_rewrite_rules();
	}
);
