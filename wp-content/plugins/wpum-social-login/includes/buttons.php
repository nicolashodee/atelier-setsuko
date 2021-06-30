<?php
/**
 * Add the social login buttons to the WPUM forms.
 *
 * @package     wpum-social-login
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumsl_maybe_display_social_login_buttons( $location, $form_id ) {
	$registration_form = wpum_get_registration_form( $form_id );

	$locations = $registration_form->get_setting( 'social_login_location' );
	if ( empty( $locations ) ) {
		$locations = array();
	}

	if ( in_array( $location . '_registration', $locations ) ) {
		wpumsl_display_social_login_buttons();
	}
}

function wpumsl_maybe_display_social_login_buttons_before_registration( $data ) {
	$form_id = isset( $data->form_id ) ? $data->form_id : null;
	wpumsl_maybe_display_social_login_buttons( 'before', $form_id );
}

function wpumsl_maybe_display_social_login_buttons_after_registration( $data ) {
	$form_id = isset( $data->form_id ) ? $data->form_id : null;
	wpumsl_maybe_display_social_login_buttons( 'after', $form_id );
}

/**
 * Displays social login buttons.
 *
 * @param array $atts
 * @param null  $content
 *
 * @return void
 */
function wpumsl_display_social_login_buttons( $atts = array(), $content = null ) {
	$socials = wpum_get_option( 'social_login_networks', array() );

	if ( empty( $socials ) ){
		return;
	}

	WPUM()->templates
		->set_template_data( [
			'socials' => $socials,
			'atts'    => $atts,
		] )
		->get_template_part( 'social-buttons' );

}
add_shortcode( 'wpum_social_login_buttons', 'wpumsl_display_social_login_buttons' );

$buttons_location = wpum_get_option( 'social_login_location', [] );

if ( in_array( 'before_login', $buttons_location ) ) {
	add_action( 'wpum_before_login_form', 'wpumsl_display_social_login_buttons' );
	add_action( 'wpum_before_two_factor_login_form', 'wpumsl_display_social_login_buttons' );
}

if ( in_array( 'after_login', $buttons_location ) ) {
	add_action( 'wpum_after_login_form', 'wpumsl_display_social_login_buttons' );
	add_action( 'wpum_after_two_factor_login_form', 'wpumsl_display_social_login_buttons' );
}

add_action( 'wpum_before_registration_form', 'wpumsl_maybe_display_social_login_buttons_before_registration' );
add_action( 'wpum_after_registration_form', 'wpumsl_maybe_display_social_login_buttons_after_registration' );