<?php
/**
 * Add a new tab to the account page.
 *
 * @package     wpum-mailchimp
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a new tab to the account page.
 *
 * @param array $tabs
 *
 * @return array
 */
function wpumchimp_register_accoun_tab( $tabs ) {
	if ( carbon_get_theme_option( 'mailchimp_edit_account' ) ) {
		$tabs['email-subscriptions'] = [
			'name'     => esc_html__( 'Email subscriptions', 'wpum-mailchimp' ),
			'priority' => 0,
		];
	}

	return $tabs;
}

add_filter( 'wpum_get_account_page_tabs', 'wpumchimp_register_accoun_tab' );

/**
 * Tell WPUM to load the form for this addon from within this plugin's path.
 *
 * @param string $path
 * @param string $form_name
 *
 * @return string
 */
function wpumchimp_register_form_path( $path, $form_name ) {
	if ( $form_name == 'email-subscriptions' ) {
		$path = WPUMCHIMP_PLUGIN_DIR . 'includes/class-wpum-form-email-subscriptions.php';
	}

	return $path;
}

add_filter( 'wpum_load_form_path', 'wpumchimp_register_form_path', 20, 2 );

/**
 * Add a new path to WPUM's template loader.
 *
 * @param array $file_paths
 *
 * @return array
 */
function wpumchimp_set_template_loader_path( $file_paths ) {
	$file_paths[14] = trailingslashit( WPUMCHIMP_PLUGIN_DIR . 'templates' );

	return $file_paths;
}

add_filter( 'wpum_template_paths', 'wpumchimp_set_template_loader_path' );

/**
 * Load custom content for email subscriptions tab.
 *
 * @return void
 */
function wpumchimp_subscriptions_account_tab_content() {
	echo WPUM()->forms->get_form( 'email-subscriptions', [] );
}

add_action( 'wpum_account_page_content_email-subscriptions', 'wpumchimp_subscriptions_account_tab_content' );