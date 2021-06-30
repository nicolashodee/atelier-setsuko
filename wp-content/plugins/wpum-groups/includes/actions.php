<?php
/**
 * Handles actions and filters for the addon.
 *
 * @package     wpum-delete-account
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumwgr_template_paths( $paths ) {
	$paths[] = WPUMGP_PLUGIN_DIR . 'templates';

	return $paths;
}
add_filter( 'wpum_template_paths', 'wpumwgr_template_paths' );

/**
 * Tell WPUM to load the form for this addon from within this plugin's path.
 *
 * @param string $path
 * @param string $form_name
 *
 * @return string
 */
function wpumgp_register_form_path( $path, $form_name ) {
	if ( $form_name == 'group' ) {
		$path = WPUMGP_PLUGIN_DIR . 'includes/class-wpum-form-group.php';
	}

	return $path;
}

add_filter( 'wpum_load_form_path', 'wpumgp_register_form_path', 20, 2 );

add_action( 'template_redirect', 'wpumgp_handle_join_group' );
add_action( 'template_redirect', 'wpumgp_handle_leave_group' );


function wpumgp_handle_actions() {
	if ( ! isset( $_GET['wpum-action'] ) ) {
		return;
	}

	$action = sanitize_text_field( $_GET['wpum-action'] );

	$nonce = sanitize_text_field( $_GET['_wpnonce'] );

	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		return;
	}

	if ( 'approve_group_user' === $action ) {
		$user_id  = filter_input( INPUT_GET, 'user_id', FILTER_VALIDATE_INT );
		$group_id = filter_input( INPUT_GET, 'group_id', FILTER_VALIDATE_INT );

		wpumgr_approve_group_member( $group_id, $user_id );

		$url = get_permalink( $group_id ) . 'moderation?approved=success';

		wp_redirect( $url );
		exit;
	}

	if ( 'reject_group_user' === $action ) {
		$user_id  = filter_input( INPUT_GET, 'user_id', FILTER_VALIDATE_INT );
		$group_id = filter_input( INPUT_GET, 'group_id', FILTER_VALIDATE_INT );

		wpumgr_reject_group_member( $group_id, $user_id );

		$url = get_permalink( $group_id ) . 'moderation?rejected=success';

		wp_redirect( $url );
		exit;
	}

	if ( 'remove_group_user' === $action ) {
		$user_id  = filter_input( INPUT_GET, 'user_id', FILTER_VALIDATE_INT );
		$group_id = filter_input( INPUT_GET, 'group_id', FILTER_VALIDATE_INT );

		if (empty(wpumgrp_group_user_has_role( $group_id, get_current_user_id(), 'wpum_group_admin' ))) {
			$url = get_permalink( $group_id ) . 'members?removed=error';
			wp_redirect( $url );
			exit;
		}

		do_action ( 'wpumgp_user_leave_group', $group_id, $user_id );
		$user_info  = get_userdata( $user_id );
		$url = get_permalink( $group_id ) . 'members?user_name=' . $user_info->display_name . '&removed=success';

		wp_redirect( $url );
		exit;
	}


	if ( 'export_group_user' === $action ) {
		$group_id = filter_input( INPUT_GET, 'group_id', FILTER_VALIDATE_INT );

		if ( empty( wpumgrp_group_user_has_role( $group_id, get_current_user_id(), 'wpum_group_admin' ) ) ) {
			return;
		}

		do_action ( 'wpum_export_group_members', $group_id );
	}
}

add_action( 'init', 'wpumgp_handle_actions' );
