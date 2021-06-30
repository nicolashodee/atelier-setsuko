<?php
/**
 * Handles the display of the sync interface.
 *
 * @package     wpum-mailchimp
 * @copyright   Copyright (c) 2021, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.3
 */

function wpummc_plugin_upgrade() {
	$current_version = get_option( 'wpummc_emails_version' );
	if ( WPUMCHIMP_VERSION === $current_version ) {
		return;
	}

	if ( empty( $current_version ) || version_compare( $current_version, '2.0.2', '<=' ) ) {
		wpummc_merge_tag_upgrade();
	}

	update_option( 'wpummc_emails_version', WPUMCHIMP_VERSION );
}

function wpummc_merge_tag_upgrade() {

	$api_key = get_option( '_mailchimp_api_key' );

	if ( empty( $api_key ) ) {
		return;
	}

	$lists = get_option( 'wpum_mailchimp_lists' );
	if ( ! $lists ) {
		return;
	}

	$lists_to_process = array();
	$i                = 0;
	foreach ( $lists as $list_key => $list ) {
		if ( empty( get_option( "_selected_mailchimp_lists|||$i|value" ) ) ) {
			continue;
		}
		$lists_to_process[ $i ] = $list_key;
		$i ++;
	}

	if ( empty( $lists_to_process ) ) {
		return;
	}

	$fields_to_delete = array();
	for ( $i = 0; $i <= 10000000000; $i ++ ) {
		$option = get_option( '_mailchimp_custom_fields|||' . $i . '|value' );
		if ( empty( $option ) ) {
			break;
		}

		$mergetag = get_option( '_mailchimp_custom_fields|merge_field|' . $i . '|0|value' );
		$field    = get_option( '_mailchimp_custom_fields|custom_field|' . $i . '|0|value' );

		foreach ( $lists_to_process as $list_key => $list_id ) {
			update_option( "_selected_mailchimp_lists|mailchimp_custom_fields|$list_key|$i|value", '_' );
			update_option( "_selected_mailchimp_lists|mailchimp_custom_fields:custom_field|$list_key:$i|0|value", $field );
			update_option( "_selected_mailchimp_lists|mailchimp_custom_fields:{$list_id}_merge_field|$list_key:$i|0|value", $mergetag );
		}

		$fields_to_delete[] = $i;
	}

	foreach ( $fields_to_delete as $i ) {
		delete_option( '_mailchimp_custom_fields|||' . $i . '|value' );
		delete_option( '_mailchimp_custom_fields|merge_field|' . $i . '|0|value' );
		delete_option( '_mailchimp_custom_fields|custom_field|' . $i . '|0|value' );
	}

	if ( count( $lists ) > 1 ) {
		update_option( 'wpum_mailchimp_upgrade_message', __( 'The mapping between custom fields and Mailchimp mergetags needs to be reviewed and updated where necessary.', 'wpum-mailchimp' ) );
	}


	// Get the merge tags available per list
	WPUM_Mailchimp_Settings::update_mailchimp_api_data( $api_key );
}
