<?php
/**
 * User field
 *
 * @package     wpum-custom-fields
 * @copyright   Copyright (c) 2021, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param array $options
 * @param WPUM_Field $field
 *
 * @return mixed
 */
function wpum_user_field_get_options( $options, $field ) {
	if ( $field->get_type() !== 'user' ) {
		return $options;
	}

	$role = $field->get_meta( 'role' );

	$args = array();
	if ( $role ) {
		$args['role'] = $role;
	}

	$show_hidden = $field->get_meta( 'show_hidden' );

	if ( $show_hidden ) {
		$args['meta_query']     = array();
		$args['meta_query'][]   = array(
			'relation' => 'OR',
			array(
				'key'     => '_hide_profile_members',
				'value'   => '',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'   => '_hide_profile_members',
				'value' => '',
			),
		);
	}
	remove_action( 'pre_get_users', array(
		Carbon_Fields\Carbon_Fields::service( 'meta_query' ),
		'hook_pre_get_users',
	) );

	$users = get_users($args);

	$options = wp_list_pluck( $users, apply_filters( 'wpum_user_field_type_value_key', 'display_name' ), 'ID' );

	$type = empty( $field->get_meta( 'allow_multiple' ) ) ? 'select' : 'multiselect';

	if ( $type === 'select' ) {
		$name = $field->get_meta( 'type_label' ) ? $field->get_meta( 'type_label' ) : 'User';

		return array( '' => 'Select ' . ucwords( $name ) ) + $options;
	}

	return $options;
}

add_filter( 'wpum_form_custom_field_dropdown_options', 'wpum_user_field_get_options', 10, 2 );
add_filter( 'wpum_admin_cb_fields_registration_field_options', 'wpum_user_field_get_options', 10, 2 );