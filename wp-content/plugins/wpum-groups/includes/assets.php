<?php
/**
 * Register all scripts and styles for WPUM Group.
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load WPUMG scripts on the frontend.
 *
 * @return void
 */
function wpumg_load_scripts() {
	// Load frontend styles.
	wp_enqueue_style( 'wpumg-frontend', WPUMGP_PLUGIN_URL . 'assets/css/wpumg.css', array(), WPUM_VERSION );
	
	global $wp;

	if ( isset( $wp->query_vars['post_type'] ) && 'wpum_group' === $wp->query_vars['post_type'] && isset( $wp->query_vars['tab'] ) && 'edit' === $wp->query_vars['tab']  ) {
		wpum_enqueue_scripts();
	}



}
add_action( 'wp_enqueue_scripts', 'wpumg_load_scripts' );
