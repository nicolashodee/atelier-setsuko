<?php
/**
 * Actions related to this plugin that will manipulate WPUM.
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
 * Add a new path to WPUM's template loader.
 *
 * @param array $file_paths
 * @return array
 */
function wpumsl_set_template_loader_path( $file_paths ) {
	$file_paths[] = trailingslashit( WPUMSL_PLUGIN_DIR . 'templates' );
	return $file_paths;
}
add_filter( 'wpum_template_paths', 'wpumsl_set_template_loader_path' );

/**
 * Display a warning if the email address has not been changed from the temporary one.
 *
 * @return void
 */
function wpumsl_display_warning_non_complete() {

	$socials = wpum_get_option( 'social_login_networks', array() );

	if ( in_array( 'instagram', $socials ) ) {
		if ( wpumsl_is_account_non_complete( get_current_user_id() ) ) {
			WPUM()->templates
				->set_template_data(
					[
						'message' => apply_filters( 'wpumsl_update_email_message', esc_html__( 'Please update your email address.', 'wpum-social-login' ) ),
					]
				)
				->get_template_part( 'messages/general', 'error' );
		}
	}

}
add_action( 'wpum_before_account_form', 'wpumsl_display_warning_non_complete' );

/**
 * Verify if the email has been updated, if it is, delete the non complete flag.
 *
 * @param object $form
 * @param array $values
 * @param string $user_id
 * @return void
 */
function wpumsl_update_non_complete_status( $form, $values, $user_id ) {

	$socials = wpum_get_option( 'social_login_networks', array() );

	if ( in_array( 'instagram', $socials ) ) {
		if ( isset( $values['account']['user_email'] ) && $values['account']['user_email'] !== 'temp@changeme.com' ) {
			wpumsl_mark_account_as_complete( $user_id );
		}
	}

}
add_action( 'wpum_before_user_update', 'wpumsl_update_non_complete_status', 10, 3 );

function wpumsl_load_icon_font() {

	wp_enqueue_style( 'wpumsl-social-icons', WPUMSL_PLUGIN_URL . 'css/wpum-social-login.css', false, WPUMSL_VERSION );

}
add_action( 'wp_enqueue_scripts', 'wpumsl_load_icon_font' );

function wpumsl_mark_users_as_pending( $user_id ) {
	if ( function_exists( 'wpumuv_mark_users_as_pending' ) ) {
		wpumuv_mark_users_as_pending( $user_id );
	}
}

add_action( 'wpum_before_social_login_registration', 'wpumsl_mark_users_as_pending' );

function wpumsl_set_display_name( $user_id ) {
	if ( ! function_exists( 'wpum_set_displayname_on_registration' ) ) {
		return;
	}
	$user = get_userdata( $user_id );

	$user_data = $user->to_array();

	$user_data['first_name'] = $user->first_name;
	$user_data['last_name']  = $user->last_name;

	$new_user_data = wpum_set_displayname_on_registration( $user_data );

	if ( $user->display_name != $new_user_data['display_name'] ) {
		wp_update_user( $new_user_data );
	}
}

add_action( 'wpum_before_social_login_registration', 'wpumsl_set_display_name' );


function wpumsl_prevent_authentication( $user ) {
	if ( function_exists( 'wpumuv_prevent_authentication' ) ) {
		$check = wpumuv_prevent_authentication( $user, '', '' );

		if ( is_wp_error( $check ) ) {
			$redirect = get_permalink( wpum_get_core_page_id( 'login' ) );

			$redirect = add_query_arg(
				[
					'error' => 'approval',
				],
				$redirect
			);

			wp_safe_redirect( $redirect );
			exit;
		}
	}
}

add_action( 'wpum_before_social_login', 'wpumsl_prevent_authentication' );

function wpumsl_login_errors( $errors, $form_name ) {
	if ( isset( $_REQUEST['error'] ) && 'approval' === $_REQUEST['error'] ) {
		$errors[] = esc_html__( 'Your account is currently pending approval.', 'wpum-user-verification' );
	}

	return $errors;
}

add_filter( 'wpum_form_errors', 'wpumsl_login_errors', 10, 2);