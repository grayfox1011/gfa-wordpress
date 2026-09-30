<?php
/**
 * Pannello "Dati GFA" nel Personalizza: GFA aggiorna da sola i dati aziendali.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'gfa_dati',
			array(
				'title'       => __( 'Dati GFA', 'gfa' ),
				'description' => __( 'Lascia vuoto un campo finché il dato non è confermato: il sito mostrerà un segnaposto.', 'gfa' ),
				'priority'    => 30,
			)
		);

		$fields = array(
			'ragione'          => array( 'Ragione sociale', 'text' ),
			'piva'             => array( 'Partita IVA del soggetto che eroga il servizio', 'text' ),
			'rea'              => array( 'Numero REA', 'text' ),
			'sede_legale'      => array( 'Sede legale (indirizzo completo)', 'text' ),
			'telefono'         => array( 'Telefono principale', 'text' ),
			'email'            => array( 'Email pubblica', 'email' ),
			'whatsapp'         => array( 'Numero WhatsApp (solo cifre, con prefisso 39)', 'text' ),
			'anno'             => array( 'Anno di inizio attività', 'text' ),
			'sedi'             => array( 'Sedi operative, separate da virgola', 'text' ),
			'copie_anno'       => array( 'Copie distribuite in un anno (dato reale)', 'text' ),
			'email_preventivi' => array( 'Email che riceve le richieste di preventivo', 'email' ),
		);

		$defaults = gfa_defaults();
		foreach ( $fields as $key => $field ) {
			$wp_customize->add_setting(
				'gfa_' . $key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => 'email' === $field[1] ? 'sanitize_email' : ( 'textarea' === $field[1] ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
				)
			);
			$wp_customize->add_control(
				'gfa_' . $key,
				array(
					'label'   => $field[0],
					'section' => 'gfa_dati',
					'type'    => $field[1],
				)
			);
		}
	}
);
