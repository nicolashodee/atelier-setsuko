<?php
/**
 * Handles integration with the registration forms.
 *
 * @package     wpum-registration-forms
 * @copyright   Copyright (c) 2019, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumrf_add_form_id_to_user_meta( $new_user_id ) {
	$form_id = filter_input( INPUT_POST, 'wpum_form_id', FILTER_VALIDATE_INT );
	if ( empty( $form_id ) ) {
		return;
	}

	update_user_meta( $new_user_id, 'wpum_form_id', $form_id );
}

add_action( 'wpum_before_registration_end', 'wpumrf_add_form_id_to_user_meta' );

/**
 * Override WPUM Registration form field to add form_id
 */
function wpumrf_replace_registration_form_widget(){
	global $wp_widget_factory;

	if( array_key_exists( 'WPUM_Registration_Form_Widget', $wp_widget_factory->widgets ) ){
		unset( $wp_widget_factory->widgets['WPUM_Registration_Form_Widget'] );
	}
	register_widget( 'WPUMRF_Registration_Form_Widget' );
}
add_action( 'widgets_init', 'wpumrf_replace_registration_form_widget', 10 );
