<?php
/**
 * Integrate MailChimp within the registration form.
 *
 * @package     wpum-mailchimp
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the MailChimp lists checkboxes if enabled.
 *
 * @param array $fields
 *
 * @return array
 */
function wpumchimp_add_optin( $fields ) {
	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );
	if ( ! empty( $api_key ) && carbon_get_theme_option( 'mailchimp_optin_method' ) == 'manual' ) {
		$fields['mailchimp'] = array(
			'label'       => false,
			'type'        => 'multicheckbox',
			'description' => '',
			'priority'    => 9999,
			'required'    => false,
			'options'     => wpumchimp_get_enabled_lists(),
		);
	}

	return $fields;
}
add_filter( 'wpum_get_registration_fields', 'wpumchimp_add_optin' );

/**
 * Add user to Mailchimp list on signup form.
 *
 * @param string $user_id
 * @param array $values
 * @return void
 */
function wpumchimp_opt_user_in( $user_id, $values ) {
	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );

	if ( empty( $api_key ) ) {
		return;
	}

	$optin_method = carbon_get_theme_option( 'mailchimp_optin_method' );

	if ( $optin_method == 'manual' ) {
		$submitted_mailchimp_lists = isset( $values['register']['mailchimp'] ) && ! empty( $values['register']['mailchimp'] ) && is_array( $values['register']['mailchimp'] ) ? $values['register']['mailchimp'] : false;
		if ( $submitted_mailchimp_lists ) {
			foreach ( $submitted_mailchimp_lists as $mailchimp_list_id ) {
				wpumchimp_add_user_to_list_from_form( $mailchimp_list_id, $values, $user_id );
			}
		}
	} else {
		foreach ( wpumchimp_get_enabled_lists() as $list_id => $name ) {
			wpumchimp_add_user_to_list_from_form( $list_id, $values, $user_id );

		}
	}

}
add_action( 'wpum_after_registration', 'wpumchimp_opt_user_in', 20, 2 );

function wpumchimp_opt_user_in_social( $user ) {
	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );

	if ( empty( $api_key ) ) {
		return;
	}

	$optin_method = carbon_get_theme_option( 'mailchimp_optin_method' );

	if ( $optin_method == 'manual' ) {
		return;
	}

	$user = get_user_by( 'id', $user );

	foreach ( wpumchimp_get_enabled_lists() as $list_id => $name ) {
		wpumchimp_add_user_to_list( $list_id, $user );
	}
}
add_action( 'wpum_after_social_login_registration', 'wpumchimp_opt_user_in_social' );


function wpumchimp_subscribe_after_verification( $user_id ) {
	$lists = get_user_meta( $user_id, 'wpum_mailchimp_list_id' );

	if ( empty( $lists ) ) {
		return;
	}

	$user = get_user_by( 'id', $user_id );

	foreach ( $lists as $list_id ) {
		wpumchimp_add_user_to_list( $list_id, $user );
	}

	delete_user_meta( $user_id, 'wpum_mailchimp_list_id' );
}

add_action( 'wpumuv_after_user_approval', 'wpumchimp_subscribe_after_verification' );
add_action( 'wpumuv_after_user_verification', 'wpumchimp_subscribe_after_verification' );
