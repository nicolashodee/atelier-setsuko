<?php
/**
 * Add options related to this addon.
 *
 * @package     wpum-social-login
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register new settings section for the addon.
 *
 * @param array $sections
 * @return void
 */
function wpumsl_register_settings_subsection( $sections ) {

	$sections['general']['social_login'] = esc_html__( 'Social Login', 'wpum-social-login' );

	return $sections;

}
add_filter( 'wpum_registered_settings_sections', 'wpumsl_register_settings_subsection' );

/**
 * Register new settings for the addon.
 *
 * @param array $settings
 *
 * @return array
 */
function wpumsl_register_settings( $settings ) {

	$settings['social_login'][] = array(
		'id'         => 'social_login_networks',
		'name'       => esc_html__( 'Social Networks', 'wpum-social-login' ),
		'desc'       => esc_html__( 'Select which social networks you wish to enable.', 'wpum-social-login' ),
		'type'       => 'multiselect',
		'multiple'   => true,
		'options'    => array(
			[
				'label' => esc_html__( 'Facebook', 'wpum-social-login' ),
				'value' => 'facebook',
			],
			[
				'label' => esc_html__( 'Twitter', 'wpum-social-login' ),
				'value' => 'twitter',
			],
			[
				'label' => esc_html__( 'Google', 'wpum-social-login' ),
				'value' => 'google',
			],
			[
				'label' => esc_html__( 'Instagram', 'wpum-social-login' ),
				'value' => 'instagram',
			],
			[
				'label' => esc_html__( 'LinkedIn', 'wpum-social-login' ),
				'value' => 'LinkedIn',
			],
		),
	);
	$settings['login'][] = array(
		'id'      => 'social_login_location',
		'name'    => esc_html__( 'Social Buttons Location', 'wpum-social-login' ),
		'desc'    => esc_html__( 'Select where you wish to display the social buttons.', 'wpum-social-login' ),
		'type'       => 'multiselect',
		'multiple'   => true,
		'options' => array(
			[
				'label' => esc_html__( 'Before login form', 'wpum-social-login' ),
				'value' => 'before_login',
			],
			[
				'label' => esc_html__( 'After login form', 'wpum-social-login' ),
				'value' => 'after_login',
			],
		),
	);
	$settings['registration'][] = array(
		'id'      => 'social_login_location',
		'name'    => esc_html__( 'Social Buttons Location', 'wpum-social-login' ),
		'desc'    => esc_html__( 'Select where you wish to display the social buttons.', 'wpum-social-login' ),
		'type'       => 'multiselect',
		'multiple'   => true,
		'options' => array(
			[
				'label' => esc_html__( 'Before registration form', 'wpum-social-login' ),
				'value' => 'before_registration',
			],
			[
				'label' => esc_html__( 'After registration form', 'wpum-social-login' ),
				'value' => 'after_registration',
			],
		),
	);
	$settings['social_login'][] = array(
		'id'   => 'facebook_clientid',
		'name' => esc_html__( 'Facebook Client ID (APP ID)', 'wpum-social-login' ),
		'type' => 'text',
	);
	$settings['social_login'][] = array(
		'id'   => 'facebook_secret',
		'name' => esc_html__( 'Facebook Secret', 'wpum-social-login' ),
		'type' => 'text',
	);

	$settings['social_login'][] = array(
		'id'   => 'twitter_clientid',
		'name' => esc_html__( 'Twitter API Key', 'wpum-social-login' ),
		'type' => 'text',
	);
	$settings['social_login'][] = array(
		'id'   => 'twitter_secret',
		'name' => esc_html__( 'Twitter API Secret', 'wpum-social-login' ),
		'type' => 'text',
	);

	$settings['social_login'][] = array(
		'id'   => 'google_clientid',
		'name' => esc_html__( 'Google Client ID', 'wpum-social-login' ),
		'type' => 'text',
	);
	$settings['social_login'][] = array(
		'id'   => 'google_secret',
		'name' => esc_html__( 'Google Client Secret', 'wpum-social-login' ),
		'type' => 'text',
	);

	$settings['social_login'][] = array(
		'id'   => 'instagram_clientid',
		'name' => esc_html__( 'Instagram Client ID', 'wpum-social-login' ),
		'type' => 'text',
	);
	$settings['social_login'][] = array(
		'id'   => 'instagram_secret',
		'name' => esc_html__( 'Instagram Client Secret', 'wpum-social-login' ),
		'type' => 'text',
	);

	$settings['social_login'][] = array(
		'id'   => 'linkedin_clientid',
		'name' => esc_html__( 'LinkedIn Client ID', 'wpum-social-login' ),
		'type' => 'text',
	);
	$settings['social_login'][] = array(
		'id'   => 'linkedin_secret',
		'name' => esc_html__( 'LinkedIn Client Secret', 'wpum-social-login' ),
		'type' => 'text',
	);

	return $settings;
}
add_action( 'wpum_registered_settings', 'wpumsl_register_settings' );
