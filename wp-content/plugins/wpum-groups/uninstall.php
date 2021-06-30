<?php
/**
 * Uninstall WPUM Groups.
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load WPUM file.
include_once( 'wpum-groups.php' );

global $wpdb;

// Delete post type contents
$wpum_post_types = array( 'wpum_group' );

foreach ( $wpum_post_types as $post_type ) {
	$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item, true );
		}
	}
}

// Delete options from the database.
$options_to_delete = [];

foreach( $options_to_delete as $option ) {
	delete_option( $option );
}

// Delete tables created by the plugin.
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpum_group_users" );
