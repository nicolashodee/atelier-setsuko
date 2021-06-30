<?php

/**
 * Register new settings for the addon.
 *
 * @param array $settings
 *
 * @return array
 */
function wpumrf_register_settings( $settings ) {
	$settings[] = array(
		'id'   => 'after_registration_form',
		'name' => __( 'Existing User Registration Form', 'wp-user-manager' ),
		'desc' => __( 'This form is used to collect data from an already registered user.', 'wp-user-manager' ),
		'type' => 'checkbox',
	);

	return $settings;
}

add_action( 'wpum_registration_form_settings_options', 'wpumrf_register_settings', 100 );
