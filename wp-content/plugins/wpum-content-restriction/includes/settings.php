<?php
/**
 * Register options for the addon.
 *
 * @package     wpum-content-restriction
 * @copyright   Copyright (c) 2021, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register new settings section for the addon.
 *
 * @param array $sections
 *
 * @return array
 */
function wpumcr_register_settings_subsection( $sections ) {
	$sections['content_restriction'] = esc_html__( 'Content Restriction', 'wpum-content-restriction' );

	return $sections;

}
add_filter( 'wpum_settings_tabs', 'wpumcr_register_settings_subsection' );

/**
 * Register new settings for the addon.
 *
 * @param array $settings
 *
 * @return array
 */
function wpumcr_register_settings( $settings ) {
	$settings['content_restriction'][] = array(
		'id'       => 'content_restriction_post_types',
		'name'     => esc_html__( 'Post Types', 'wpum-content-restriction' ),
		'desc'     => esc_html__( 'Restrict all posts from these post types', 'wpum-content-restriction' ),
		'type'     => 'multiselect',
		'multiple' => true,
		'options'  => wpumcr_get_post_types(),
	);

	$settings['content_restriction'][] = array(
		'id'      => 'content_restriction_type',
		'name'    => esc_html__( 'Who can access the posts', 'wpum-content-restriction' ),
		'desc'    => esc_html__( 'Set the visibility of this restricted content.', 'wpum-content-restriction' ),
		'type'    => 'select',
		'std'     => 'in',
		'options' => array(
			'in'  => esc_html__( 'Logged in users', 'wpum-content-restriction' ),
			'out' => esc_html__( 'Logged out users', 'wpum-content-restriction' ),
		),
	);

	$settings['content_restriction'][] =array(
		'id'       => 'content_restriction_roles',
		'name'     => __( 'Restriction by role', 'wpum-content-restriction' ) ,
		'desc'     => __( 'Choose user roles for restricted content access.', 'wpum-content-restriction' ) ,
		'type'     => 'multiselect',
		'multiple' => true,
		'labels'   => array( 'placeholder' => __( 'Select one or more user roles from the list.', 'wpum-content-restriction' ) ),
		'options'  => wpum_get_roles(),
		'toggle' => array( 'key' => 'content_restriction_type', 'value' => 'in' ),
	);

	$settings['content_restriction'][] = array(
		'id'      => 'content_restriction_behaviour',
		'name'    => esc_html__( 'Restriction behaviour', 'wpum-content-restriction' ),
		'desc'    => esc_html__( 'Set the visibility of this restricted content.', 'wpum-content-restriction' ),
		'type'    => 'select',
		'std'     => 'message',
		'options' => array(
			'message'  => esc_html__( 'Show message', 'wpum-content-restriction' ),
			'redirect' => esc_html__( 'Redirect', 'wpum-content-restriction' ),
		),
	);

	$settings['content_restriction'][] = array(
		'id'      => 'content_restriction_message_type',
		'name'    => esc_html__( 'Restriction behaviour', 'wpum-content-restriction' ),
		'type'    => 'select',
		'std'     => 'global',
		'options' => array(
			'global'  => esc_html__( 'Global default message', 'wpum-content-restriction' ),
			'custom' => esc_html__( 'Custom message', 'wpum-content-restriction' ),
		),
		'toggle' => array( 'key' => 'content_restriction_behaviour', 'value' => 'message' ),
	);

	$settings['content_restriction'][]  = array(
		'id'   => 'content_restriction_message',
		'name' => __( 'Custom message', 'wpum-content-restriction' ),
		'type' => 'textarea',
		'toggle' => array( 'key' => 'content_restriction_message_type', 'value' => 'custom' ),

	);

	$redirect_options   = wpum_get_redirect_pages();
	$redirect_options[] = array(
		'value' => 'wpumcustomredirect',
		'label' => __( 'Custom URL', 'wpum-content-restriction' ),
	);

	$settings['content_restriction'][] = array(
		'id'      => 'content_restriction_redirect_page',
		'name'    => esc_html__( 'Redirection page', 'wpum-content-restriction' ),
		'desc'    => esc_html__( 'Select the page where you want to redirect visitor after they visit this post/page.', 'wpum-content-restriction' ),
		'type'    => 'multiselect',
		'options' => $redirect_options,
		'toggle' => array( 'key' => 'content_restriction_behaviour', 'value' => 'redirect' ),
	);

	$settings['content_restriction'][] = array(
		'id'      => 'content_restriction_redirect_custom_url',
		'name'    => esc_html__( 'Custom URL', 'wpum-content-restriction' ),
		'type'    => 'text',
		'toggle' => array( 'key' => 'content_restriction_redirect_page', 'value' => 'wpumcustomredirect' ),
	);

	$settings['content_restriction'][]  = array(
		'id'   => 'content_restriction_everywhere',
		'name' => __( 'Restrict Everywhere', 'wpum-content-restriction' ),
		'desc' => __( 'Hide from archives, RSS feeds and other places for users who do not have permission to view this content', 'wpum-content-restriction' ),
		'type' => 'checkbox',
	);

	return $settings;
}
add_action( 'wpum_registered_settings', 'wpumcr_register_settings' );
