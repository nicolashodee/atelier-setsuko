<?php
/**
 * Holds MailChimp related functions.
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
 * Retrieve the enabled Mailchimp lists.
 *
 * @return array
 */
function wpumchimp_get_enabled_lists() {

	$lists = [];

	$enabled_lists = carbon_get_theme_option( 'selected_mailchimp_lists' );
	$stored_lists  = get_option( 'wpum_mailchimp_lists' );

	if ( ! empty( $enabled_lists ) && is_array( $enabled_lists ) ) {
		foreach ( $enabled_lists as $list ) {
			$name = $stored_lists[ $list['list'] ];
			if ( ! empty( $list['list_description'] ) ) {
				$name = $list['list_description'];
			}
			$lists[ $list['list'] ] = $name;
		}
	}

	return $lists;

}

/**
 * Register a user to a MailChimp list.
 *
 * @param string $list_id
 * @param WP_User $user
 * @return void
 */
function wpumchimp_add_user_to_list( $list_id, $user ) {
	if ( function_exists( 'wpumuv_get_verification_method' ) && carbon_get_theme_option( 'mailchimp_after_verification' ) && get_user_meta( $user->ID, 'wpumuv_needs_verification', true ) ) {
		update_user_meta( $user->ID, 'wpum_mailchimp_list_id', $list_id );

		return;
	}

	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );

	if ( empty( $api_key ) ) {
		return;
	}
	try {
		$mailchimp = new MailChimp( $api_key );

		$merge_fields = wpumchimp_get_merge_fields( $list_id, $user );
		$args         = [
			'email_address' => $user->user_email,
			'status'        => 'subscribed',
		];

		if ( ! empty( $merge_fields ) ) {
			$args['merge_fields'] = $merge_fields;
		}

		$result = $mailchimp->post( "lists/$list_id/members", $args );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			error_log( print_r( $result, true ), 0 );
		}
	} catch ( Exception $e ) {
		wp_die( $e );
	}

}

/**
 * Register a user to a MailChimp list.
 *
 * @param string $list_id
 * @param array  $values
 * @param int    $user_id
 *
 * @return void
 */
function wpumchimp_add_user_to_list_from_form( $list_id, $values, $user_id ) {

	if ( function_exists( 'wpumuv_get_verification_method' ) && carbon_get_theme_option( 'mailchimp_after_verification' ) && get_user_meta( $user_id, 'wpumuv_needs_verification', true ) ) {
		update_user_meta( $user_id, 'wpum_mailchimp_list_id', $list_id );

		return;
	}

	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );

	if ( ! empty( $api_key ) ) {
		try {

			$mailchimp = new MailChimp( $api_key );

			$merge_fields = wpumchimp_send_merge_fields( $list_id, $values, 'register' );
			$args         = [
				'email_address' => $values['register']['user_email'],
				'status'        => 'subscribed',
			];

			if ( ! empty( $merge_fields ) ) {
				$args['merge_fields'] = $merge_fields;
			}

			$result = $mailchimp->post( "lists/$list_id/members", $args );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log( print_r( $result, true ), 0 );
			}
		} catch ( Exception $e ) {
			wp_die( $e );
		}
	}

}

/**
 * @param string   $list_id
 * @param \WP_User $user
 *
 * @return array
 */
function wpumchimp_get_merge_fields( $list_id, $user ) {
	$merge_fields = [];

	if ( $user->user_firstname ) {
		$merge_fields['FNAME'] = $user->user_firstname ;
	}

	if ( $user->user_lastname ) {
		$merge_fields['LNAME'] = $user->user_lastname;
	}

	$lists_merge_fields = get_option( 'wpum_mailchimp_lists_merge_fields' );
	$enabled_lists      = carbon_get_theme_option( 'selected_mailchimp_lists' );

	$additional_merge_fields = [];
	foreach ( $enabled_lists as $enabled_list ) {
		if ( ! isset( $enabled_list['list'] ) || $list_id != $enabled_list['list'] ) {
			continue;
		}

		if ( ! isset( $enabled_list['mailchimp_custom_fields'] ) ) {
			continue;
		}

		$additional_merge_fields = $enabled_list['mailchimp_custom_fields'];
	}

	if ( ! empty( $additional_merge_fields ) && is_array( $additional_merge_fields ) ) {
		foreach ( $additional_merge_fields as $custom_merge_field ) {
			$merge_field_id  = $custom_merge_field[ $list_id . '_merge_field' ];
			$merge_field_tag = $lists_merge_fields[ $list_id ][ $merge_field_id ]['tag'];
			if ( isset( $merge_fields[ $merge_field_tag ] ) ) {
				continue;
			}

			$user_custom_field = new WPUM_Field( $custom_merge_field['custom_field'] );
			if ( $user_custom_field->exists() ) {
				if ( $user_custom_field->is_primary() && isset( $user->{$user_custom_field->get_primary_id()} ) ) {
					$merge_fields[ $merge_field_tag ] =  $user->{$user_custom_field->get_primary_id()};
				} else {
					if ( isset( $user->{$user_custom_field->get_meta( 'user_meta_key' ) } ) ) {
						$merge_fields[ $merge_field_tag ] = $user->{$user_custom_field->get_meta( 'user_meta_key' ) };
					}
				}
			}
		}
	}

	return $merge_fields;
}

/**
 * Pass merge fields to MailChimp based on data given within a form.
 *
 * @param string      $list_id
 * @param array       $values
 * @param bool|string $form
 *
 * @return array
 */
function wpumchimp_send_merge_fields( $list_id, $values, $form = false ) {
	if ( ! $form ) {
		wp_die( 'Missing form parameter for wpumchimp_send_merge_fields' );
	}

	$merge_fields = [];

	$first_name = isset( $values[ $form ]['user_firstname'] ) ? $values[ $form ]['user_firstname'] : false;
	$last_name  = isset( $values[ $form ]['user_lastname'] ) ? $values[ $form ]['user_lastname'] : false;

	if ( $first_name ) {
		$merge_fields['FNAME'] = $first_name;
	}

	if ( $last_name ) {
		$merge_fields['LNAME'] = $last_name;
	}

	$lists_merge_fields = get_option( 'wpum_mailchimp_lists_merge_fields' );
	$enabled_lists      = carbon_get_theme_option( 'selected_mailchimp_lists' );

	$additional_merge_fields = [];
	foreach ( $enabled_lists as $enabled_list ) {
		if ( ! isset( $enabled_list['list'] ) || $list_id != $enabled_list['list'] ) {
			continue;
		}

		if ( ! isset( $enabled_list['mailchimp_custom_fields'] ) ) {
			continue;
		}

		$additional_merge_fields = $enabled_list['mailchimp_custom_fields'];
	}

	if ( ! empty( $additional_merge_fields ) && is_array( $additional_merge_fields ) ) {
		foreach ( $additional_merge_fields as $custom_merge_field ) {
			$merge_field_id  = $custom_merge_field[ $list_id . '_merge_field' ];
			$merge_field_tag = $lists_merge_fields[ $list_id ][ $merge_field_id ]['tag'];
			if ( isset( $merge_fields[ $merge_field_tag ] ) ) {
				continue;
			}

			$user_custom_field = new WPUM_Field( $custom_merge_field['custom_field'] );
			if ( $user_custom_field->exists() ) {
				if ( $user_custom_field->is_primary() && isset( $values[ $form ][ $user_custom_field->get_primary_id() ] ) ) {
					$merge_fields[ $merge_field_tag ] = $values[ $form ][ $user_custom_field->get_primary_id() ];
				} else {
					if ( isset( $values[ $form ][ $user_custom_field->get_meta( 'user_meta_key' ) ] ) ) {
						$merge_fields[ $merge_field_tag ] = $values[ $form ][ $user_custom_field->get_meta( 'user_meta_key' ) ];
					}
				}
			}
		}
	}

	return $merge_fields;
}

/**
 * Check with Mailchimp to which lists the current user is signed up.
 *
 * @return array
 */
function wpumchimp_get_current_users_lists() {

	$lists = [];

	$api_key = carbon_get_theme_option( 'mailchimp_api_key' );

	if ( ! empty( $api_key ) ) {
		try {

			$mailchimp = new MailChimp( $api_key );
			$user      = wp_get_current_user();

			if ( $user instanceof WP_User ) {

				$available_lists = wpumchimp_get_enabled_lists();
				$subscriber_hash = $mailchimp->subscriberHash( $user->user_email );

				foreach ( $available_lists as $list_id => $list_name ) {
					$result = $mailchimp->get( "lists/$list_id/members/$subscriber_hash" );
					if ( in_array( $result['status'], array( 'archived', '404' ) ) ) {
						continue;
					}
					$lists[] = $list_id;
				}

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
					error_log( print_r( $result, true ), 0 );
				}
			}
		} catch ( Exception $e ) {
			wp_die( $e );
		}
	}

	return $lists;

}
