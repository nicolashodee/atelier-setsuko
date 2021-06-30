<?php
/**
 * Handles displaying the private content on the profile to users
 *
 * @package     wpum-private-content
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumpr_register_profile_tab( $tabs ) {
	global $post;
	if ( empty( $post ) ) {
		return $tabs;
	}

	if ( ! isset( $post->ID ) || $post->ID != wpum_get_core_page_id( 'profile' ) ) {
		return $tabs;
	}

	$user_id = get_current_user_id();
	if ( 0 === $user_id ) {
		return $tabs;
	}

	if ( $user_id !== wpum_get_queried_user_id() ) {
		return $tabs;
	}

	$content = wpumpr_get_private_content( get_current_user_id() );
	if ( empty( $content ) ) {
		return $tabs;
	}

	$tabs['private-content'] = [
		'name'     => esc_html( apply_filters( 'wpum_private_content_profile_tab_name', wpum_get_option( 'private_content_tab_name', 'Private Content' ) ) ),
		'priority' => apply_filters( 'wpum_private_content_profile_tab_priority', 10 ),
	];

	return $tabs;
}

add_filter( 'wpum_get_registered_profile_tabs', 'wpumpr_register_profile_tab' );

function wpumpr_profile_content() {
	$user_id = get_current_user_id();
	if ( 0 === $user_id ) {
		return;
	}

	if ( $user_id !== wpum_get_queried_user_id() ) {
		return;
	}

	echo wpumpr_get_private_content( get_current_user_id() );
}

add_action( 'wpum_profile_page_content_private-content', 'wpumpr_profile_content' );


function wpumpr_register_settings( $settings ) {
	$settings['profiles_content'][] = array(
		'id'   => 'private_content_tab_name',
		'name' => __( 'Private Content Tab Name', 'wpum-private-content' ),
		'desc' => __( 'Change the tab name for Private Content', 'wpum-private-content' ),
		'type' => 'text',
		'std'  => __( 'Private Content', 'wpum-private-content' ),
	);

	$settings['profiles_content'][] = array(
		'id'   => 'private_content_global_content',
		'name' => __( 'Private Content For All', 'wpum-private-content' ),
		'desc' => __( 'Private content displayed to all users.', 'wpum-private-content' ),
		'type' => 'textarea',
	);

	return $settings;

}
add_action( 'wpum_registered_settings', 'wpumpr_register_settings' );
