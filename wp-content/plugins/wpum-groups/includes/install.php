<?php
/**
 * Install function.
 *
 * @package     wp-user-manager
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Runs on plugin install by setting up the post types, custom taxonomies, flushing rewrite rules to initiate the new
 * slugs and also creates the plugin and populates the settings fields for those plugin pages.
 *
 * @param boolean $network_wide
 * @return void
 */
function wpum_group_install( $network_wide = false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			wpumgp_run_install();
			restore_current_blog();
		}
	} else {
		wpumgp_run_install();
	}

}

/**
 * Run the installation process of the plugin.
 *
 * @return void
 */
function wpumgp_run_install() {
	// Check if all tables are there.
	$tables = array(
		'groupusers' => new WPUMG_DB_Table_Group_Users(),
	);

	foreach ( $tables as $key => $table ) {
		if ( ! $table->exists() ) {
			$table->create();
		}
	}

	// Update current version.
	update_option( 'wpumg_version', WPUMGP_VERSION );
}

/**
 * Install new emails.
 */
function wpumgp_install_emails() {
	if ( WPUMGP_EMAILS_VERSION === get_option( 'wpumg_emails_version' ) ) {
		return;
	}

	$existing_emails = get_option( 'wpum_email' );

	$default_emails = wpumgp_default_emails();

	foreach ( $default_emails as $email_key => $default_email ) {
		if ( isset( $existing_emails[ $email_key ] ) ) {
			continue;
		}

		$existing_emails[ $email_key ] = $default_email;
	}

	update_option( 'wpum_email', $existing_emails );

	update_option( 'wpumg_emails_version', WPUMGP_EMAILS_VERSION );
}
