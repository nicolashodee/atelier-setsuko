<?php
/**
 * Store values of custom fields added to the registration form.
 *
 * @package     wpum-custom-fields
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Save custom fields within the registration form to the user.
 *
 * @param string $user_id
 * @param array $values
 * @return void
 */
function wpumcf_save_meta_to_user( $user_id, $values ) {

	if ( ! $user_id ) {
		return;
	}

	if ( empty( $values ) || ! is_array( $values ) ) {
		return;
	}

	foreach ( $values['register'] as $key => $value ) {
		if ( strpos( $key, 'wpum_' ) !== 0 ) {
			continue;
		}

		if ( is_array( $value ) && isset( $value['path'] ) && isset( $value['url'] ) ) {
			carbon_set_user_meta( $user_id, $key, $value['url'] );
			update_user_meta( $user_id, $key . '_path', $value['path'] );

			continue;
		}

		$original_value = $value;
		if ( $value == '1' ) {
			$value = true;
		}

		$value = apply_filters( 'wpum_custom_fields_registration_meta_update', $value, $key, $user_id, $original_value );

		carbon_set_user_meta( $user_id, $key, $value );
	}

}
add_action( 'wpum_before_registration_end', 'wpumcf_save_meta_to_user', 20, 2 );
