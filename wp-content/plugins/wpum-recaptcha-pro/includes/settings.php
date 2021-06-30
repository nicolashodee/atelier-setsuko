<?php
/**
 * Register options for the addon.
 *
 * @package     wpum-recaptcha
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumr_register_setting( $sections ) {
	$sections['general']['recaptcha'] = __( 'reCAPTCHA', 'wpum-recaptcha' );
	
	return $sections;
}

add_filter( 'wpum_registered_settings_sections', 'wpumr_register_setting' );

/**
 * Register new settings for the addon.
 *
 * @param array $settings
 *
 * @return array
 */
function wpumur_register_settings( $settings ) {
	$settings['recaptcha'][]    = array(
		'id'      => 'recaptcha_version',
		'name'    => __( 'Google reCAPTCHA', 'wp-user-manager' ),
		'type'    => 'select',
		'std'     => 'v2',
		'options' => array(
			'v2' => __( 'Version 2', 'wpum-recaptcha' ),
			'v3' => __( 'Version 3', 'wpum-recaptcha' ),
		),
	);
	$settings['recaptcha'][]    = array(
		'id'   => 'recaptcha_site_key',
		'name' => __( 'Google reCAPTCHA v2 site key', 'wpum-recaptcha' ),
		'desc' => __( 'Enter your site key.', 'wpum-recaptcha' ) . ' ' . sprintf( __( '<a target="_blank" href="%s">Get your reCAPTCHA keys</a> from Google', 'wpum-recaptcha' ), 'https://www.google.com/recaptcha/admin' ),
		'type' => 'text',
		'toggle' => array( 'key' => 'recaptcha_version', 'value' => 'v2' ),
	);
	$settings['recaptcha'][]    = array(
		'id'   => 'recaptcha_secret_key',
		'name' => __( 'Google reCAPTCHA v2 secret key', 'wpum-recaptcha' ),
		'desc' => __( 'Enter your site secret key.', 'wpum-recaptcha' ),
		'type' => 'text',
		'toggle' => array( 'key' => 'recaptcha_version', 'value' => 'v2' ),
	);
	$settings['recaptcha'][]        = array(
		'id'      => 'recaptcha_v2_type',
		'name'    => __( 'reCAPTCHA Type', 'wp-user-manager' ),
		'type'    => 'select',
		'std'     => 'checkbox',
		'options' => array( '' => 'Select Type', 'checkbox' => 'Checkbox', 'invisible' => 'Invisible' ),
		'toggle' => array( 'key' => 'recaptcha_version', 'value' => 'v2' ),
	);
	$settings['recaptcha'][]    = array(
		'id'   => 'recaptcha_site_key_v3',
		'name' => __( 'Google reCAPTCHA v3 site key', 'wpum-recaptcha' ),
		'desc' => __( 'Enter your site key.', 'wpum-recaptcha' ) . ' ' . sprintf( __( '<a target="_blank" href="%s">Get your reCAPTCHA keys</a> from Google', 'wpum-recaptcha' ), 'https://www.google.com/recaptcha/admin' ),
		'type' => 'text',
		'toggle' => array( 'key' => 'recaptcha_version', 'value' => 'v3' ),
	);
	$settings['recaptcha'][]    = array(
		'id'   => 'recaptcha_secret_key_v3',
		'name' => __( 'Google reCAPTCHA v3 secret key', 'wpum-recaptcha' ),
		'desc' => __( 'Enter your site secret key.', 'wpum-recaptcha' ),
		'type' => 'text',
		'toggle' => array( 'key' => 'recaptcha_version', 'value' => 'v3' ),
	);
	$settings['recaptcha'][]    = array(
		'id'      => 'recaptcha_language',
		'name'    => __( 'Google reCAPTCHA Language', 'wpum-recaptcha' ),
		'type'    => 'select',
		'std'     => 'en',
		'options' => wpum_recaptcha_languages(),
	);
	$settings['recaptcha'][]    = array(
		'id'      => 'recaptcha_badge_location',
		'name'    => __( 'Google reCAPTCHA Badge Location', 'wpum-recaptcha' ),
		'type'    => 'select',
		'std'     => 'bottomright',
		'options' => wpum_recaptcha_badge_locations(),
		'toggle' => array( 'key' => 'recaptcha_version', 'value' => 'v2' ),
	);

	return $settings;
}

add_action( 'wpum_registered_settings', 'wpumur_register_settings' );
