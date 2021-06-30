<?php
/**
 * Store functions related to this addon.
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

/**
 * Retrieve a formatted url of a specific social network.
 * The url will start the login or registration process through the
 * given social network.
 *
 * @param string $social
 * @param string $redirect
 * @return void
 */
function wpumsl_get_social_login_url( $social = false, $redirect = false ) {

	if ( ! $social ) {
		return;
	}

	$url = add_query_arg(
		[
			'wpumsl'      => $social,
			'redirect_to' => isset( $redirect ) && ! empty( $redirect ) ? esc_url_raw( $redirect ) : false,
		],
		home_url()
	);

	return $url;

}

/**
 * Verify if a social account has been linked to a given WP User.
 *
 * @param string $user_id
 * @param string $social_id
 * @return bool
 */
function wpumsl_is_account_linked( $user_id, $social_id ) {
	return get_user_meta( $user_id, $social_id . '_verified', true );
}

/**
 * Mark the a social network as verified to the specified WP User.
 *
 * @param string $user_id
 * @param string $social_id
 * @return void
 */
function wpumsl_link_account( $user_id, $social_id ) {
	update_user_meta( $user_id, $social_id . '_verified', true );
}

/**
 * Mark an account as non complete when the social network did not provide an email address.
 *
 * @param string $user_id
 * @return void
 */
function wpumsl_mark_account_as_non_complete( $user_id ) {
	update_user_meta( $user_id, 'social_network_email_missing', true );
}

/**
 * Verify if the account is marked as non complete.
 *
 * @param string $user_id
 * @return void
 */
function wpumsl_is_account_non_complete( $user_id ) {
	return get_user_meta( $user_id, 'social_network_email_missing', true );
}

/**
 * Remove the flag that indicates an account as non complete.
 *
 * @param string $user_id
 * @return void
 */
function wpumsl_mark_account_as_complete( $user_id ) {
	delete_user_meta( $user_id, 'social_network_email_missing' );
}