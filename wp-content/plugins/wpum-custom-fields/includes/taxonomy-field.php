<?php
/**
 * Taxonomy field
 *
 * @package     wpum-custom-fields
 * @copyright   Copyright (c) 2021, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function wpum_taxonomy_field_add_taxonomies( $settings, $field, $field_group ) {
	if ( $field['type'] !== 'taxonomy' || $field_group !== 'general' ) {
		return $settings;
	}

	$args = array(
		'object_type' => array(
			'user',
		),
	);

	$all_taxonomies = get_taxonomies( $args, 'objects' );

	$taxonomies = array( array( 'id' => '', 'name' => 'Select Taxonomy' ) );
	foreach ( $all_taxonomies as $taxonomy ) {
		$taxonomies[] = array( 'id' => $taxonomy->name, 'name' => $taxonomy->label );
	}

	$settings['taxonomy']['values'] = $taxonomies;

	return $settings;
}

add_filter( 'wpum_fields_editor_field_settings', 'wpum_taxonomy_field_add_taxonomies', 10, 3 );

/**
 * @param array $options
 * @param WPUM_Field $field
 *
 * @return mixed
 */
function wpum_taxonomy_field_get_options( $options, $field ) {
	if ( $field->get_type() !== 'taxonomy' ) {
		return $options;
	}

	$taxonomy_name = $field->get_meta( 'taxonomy' );

	$taxonomy = get_taxonomy( $taxonomy_name );

	$terms = get_terms( [
		'taxonomy'   => $taxonomy_name,
		'hide_empty' => false,
	] );

	$options = wp_list_pluck( $terms, 'name', 'term_id' );

	$type = empty( $field->get_meta( 'field_type' ) ) ? 'select' : $field->get_meta( 'field_type' );

	if ( $type === 'select' ) {
		$name = $taxonomy && isset( $taxonomy->labels->singular_name ) ? $taxonomy->labels->singular_name : $taxonomy_name;

		return array( '' => 'Select ' . $name ) + $options;
	}

	return $options;
}

add_filter( 'wpum_form_custom_field_dropdown_options', 'wpum_taxonomy_field_get_options', 10, 2 );
add_filter( 'wpum_admin_cb_fields_registration_field_options', 'wpum_taxonomy_field_get_options', 10, 2 );

function wpum_taxonomy_field_update( $value, $key, $user_id, $original_value ) {
	$id = str_replace( 'wpum_field_', '', $key );

	$field = new WPUM_Field( $id );

	if ( $field->get_type() !== 'taxonomy' ) {
		return $value;
	}

	if ( empty ( $original_value ) ) {
		return $value;
	}

	if ( ! is_array( $original_value ) ) {
		$original_value = array( $original_value );
	}

	$terms = array_map( 'intval', $original_value );

	wp_set_object_terms( $user_id, $terms, $field->get_meta( 'taxonomy' ) );

	return $value;
}

add_filter( 'wpum_custom_fields_registration_meta_update', 'wpum_taxonomy_field_update', 10, 4 );
add_filter( 'wpum_custom_fields_account_meta_update', 'wpum_taxonomy_field_update', 10, 4 );

function wpum_taxonomy_maybe_field_update( $value, $key, $user_id, $original_value ) {
	if ( is_admin() ) {
		$value = wpum_taxonomy_field_update( $value, $key, $user_id, $original_value );
	}

	return $value;
}

add_filter( 'wpum_custom_field_admin_meta_update', 'wpum_taxonomy_maybe_field_update', 10, 4 );

/**
 * @param mixed      $value
 * @param WPUM_Field $field
 * @param int        $user_id
 *
 * @return int|string|array
 */
function wpum_taxonomy_field_value( $value, $field, $user_id ) {
	if ( $field->get_type() !== 'taxonomy' ) {
		return $value;
	}

	$terms = wp_get_object_terms( $user_id, $field->get_meta( 'taxonomy' ) );

	if ( empty( $terms ) ) {
		return '';
	}

	$type = empty( $field->get_meta( 'field_type' ) ) ? 'select' : $field->get_meta( 'field_type' );
	if ( $type === 'select' ) {
		return $terms[0]->term_id;
	}

	return wp_list_pluck( $terms, 'term_id' );
}

add_filter( 'wpum_custom_field_value', 'wpum_taxonomy_field_value', 10, 3 );