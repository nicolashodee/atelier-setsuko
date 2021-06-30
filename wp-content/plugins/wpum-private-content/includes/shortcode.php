<?php
/**
 * Handles integration with the admin panel for the edit users
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


/**
 * Handles display of the private content for a user shortcode.
 *
 * @param array  $atts
 * @param string $content
 *
 * @return string
 */
function wpumpr_private_content( $atts, $content = null ) {
	$user_id = get_current_user_id();
	if ( 0 === $user_id ) {
		return;
	}

	return wpumpr_get_private_content( $user_id );
}
add_shortcode( 'wpum_private_content', 'wpumpr_private_content' );

function wpumpr_get_private_content( $user_id ) {
	$content = '';
	$global_content = wpum_get_option( 'private_content_global_content', '' );

	if ( $global_content ) {
		$content .= '<p>' . $global_content . '</p>';
	}

	$user_content = get_user_meta( $user_id, 'wpum_private_content', true );

	if ( $user_content ) {
		$content .= '<p>' . $user_content . '</p>';
	}

	return $content;
}
