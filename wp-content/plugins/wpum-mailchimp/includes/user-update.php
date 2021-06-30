<?php
/**
 * Update MailChimp member details when a user updates his account on the frontend.
 *
 * @package     wpum-mailchimp
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

use \DrewM\MailChimp\MailChimp;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect if there's an update in any of the fields defined for MailChimp
 * and update member's details on MailChimp.
 *
 * @param object $form
 * @param array $values
 * @param string $user_id
 * @return void
 */
function wpumchimp_update_member_details( $form, $values, $user_id ) {

	$current_user       = wp_get_current_user();
	$current_first_name = $current_user->user_firstname;
	$current_last_name  = $current_user->user_lastname;
	$current_user_email = $current_user->user_email;

	if ( array_key_exists( 'user_firstname', $values['account'] ) && $current_first_name !== $values['account']['user_firstname'] ) {
		$current_first_name      = sanitize_text_field( $values['account']['user_firstname'] );
		$data_to_update['FNAME'] = $current_first_name;
	}

	if ( array_key_exists( 'user_lastname', $values['account'] ) && $current_last_name !== $values['account']['user_lastname'] ) {
		$current_last_name       = sanitize_text_field( $values['account']['user_lastname'] );
		$data_to_update['LNAME'] = $current_last_name;
	}

	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );

	if ( ! empty( $api_key ) ) {
		try {
			$mailchimp       = new MailChimp( $api_key );
			$subscriber_hash = $mailchimp->subscriberHash( $values['account']['user_email'] );
			$lists           = wpumchimp_get_enabled_lists();

			foreach ( $lists as $list_id => $list_name ) {
				$data_to_update     = [];
				// Detect changes in custom fields.
				$data_to_update = array_merge( $data_to_update, wpumchimp_send_merge_fields( $list_id, $values, 'account' ) );

				$result = $mailchimp->patch( "lists/$list_id/members/$subscriber_hash", array( 'merge_fields' => $data_to_update ) );
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
					error_log( print_r( $result, true ), 0 );
				}
			}
		} catch ( Exception $e ) {
			wp_die( $e );
		}
	}

	// Detect changes in email addres.
	if ( array_key_exists( 'user_email', $values['account'] ) && $current_user_email !== $values['account']['user_email'] && ! email_exists( $values['account']['user_email'] ) ) {
		if ( ! empty( $api_key ) ) {
			try {
				$mailchimp       = new MailChimp( $api_key );
				$subscriber_hash = $mailchimp->subscriberHash( $current_user_email );
				$lists           = wpumchimp_get_enabled_lists();
				foreach ( $lists as $list_id => $list_name ) {
					$result = $mailchimp->patch( "lists/$list_id/members/$subscriber_hash", array( 'email_address' => $values['account']['user_email'], 'status' => 'subscribed' ) );
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
						error_log( print_r( $result, true ), 0 );
					}
				}
			} catch ( Exception $e ) {
				wp_die( $e );
			}
		}
	}

}
add_action( 'wpum_before_user_update', 'wpumchimp_update_member_details', 20, 3 );
