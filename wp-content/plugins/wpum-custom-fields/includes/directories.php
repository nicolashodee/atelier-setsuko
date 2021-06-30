<?php

add_filter( 'wpum_directory_search_fields_help_text', function () {
	return __( 'Select the fields to search in.', 'wpum-custom-fields' );
} );

/**
 * Add Custom Fields to the Directory search fields dropdown in the Directory edit screen.
 *
 * @param array $fields
 *
 * @return array
 */
function wpumcf_wpum_directory_search_fields( $fields ) {
	$excluded_types = apply_filters( 'wpum_custom_fields_directory_search_excluded_types', array(
		'file',
		'number',
		'datepicker',
	) );

	$groups = WPUM()->fields_groups->get_groups();

	foreach ( $groups as $group ) {
		$group_fields = WPUM()->fields->get_fields( [
			'group_id' => $group->id,
		] );

		foreach ( $group_fields as $field ) {
			$key = $field->get_meta( 'user_meta_key' );
				if ( empty( $field->field_type ) || in_array( $field->get_parent_type(), $excluded_types ) ) {
					continue;
				}

			$fields[ $key ] = $group->get_name() . ' - ' . $field->get_name();
		}
	}


	return $fields;
}

add_filter( 'wpum_directory_search_fields', 'wpumcf_wpum_directory_search_fields' );