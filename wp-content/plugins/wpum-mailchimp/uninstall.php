<?php
/**
 * Uninstall addon.
 *
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$options = [
	'_mailchimp_api_key',
	'wpum_mailchimp_lists',
	'wpum_mailchimp_lists_merge_fields',
	'wpum_mailchimp_api_key_error',
	'_mailchimp_user_selection',
];

foreach ( $options as $option ) {
	delete_option( $option );
}
