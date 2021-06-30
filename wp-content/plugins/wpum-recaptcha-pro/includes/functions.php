<?php
/**
 * Functions that can be used everywhere.
 *
 * @package     wpum-recaptcha-pro
 * @copyright   Copyright (c) 2019, WP User Manager
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpum_recaptcha_languages() {
	$languages = require_once dirname( __DIR__ ) . '/config/languages.php';

	return apply_filters( 'wpum_recaptcha_languages', $languages );
}

function wpum_recaptcha_badge_locations() {
	$locations = array( 'bottomright' => 'Bottom Right', 'bottomleft' => 'Bottom Left', 'inline' => 'Inline' );

	return apply_filters( 'wpum_recaptcha_locations', $locations );
}