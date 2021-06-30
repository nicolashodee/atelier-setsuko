<?php
/**
 * Handles integration with the directories.
 *
 * @package     wpum-registration-forms
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */

use Carbon_Fields\Field;

function wpumrf_registration_forms() {
	$registration_forms = WPUM()->registration_forms->get_forms( array( 'number' => - 1 ) );

	$forms = array();

	foreach ( $registration_forms as $registration_form ) {
		$forms[ $registration_form->id ] = $registration_form->name;
	}

	return $forms;
}

function wpumrf_directory_general_settings( $fields ) {
	$registration_forms_field = Field::make( 'multiselect', 'directory_registration_forms', esc_html__( 'Registration Forms', 'wp-user-manager' ) )
	                                 ->set_help_text( esc_html__( 'Only display users who have registered through specific registration forms. Leave blank to display all users.', 'wp-user-manager' ) )
	                                 ->add_options( wpumrf_registration_forms() );


	return array_merge( array( $registration_forms_field ), $fields );
}

add_filter( 'wpum_directory_general_settings', 'wpumrf_directory_general_settings' );

function wpumrf_post_type_columns( $columns ) {
	$columns['registration_forms'] = esc_html__( 'Registration Forms', 'wp-user-manager' );

	return $columns;
}

add_filter( 'manage_edit-wpum_directory_columns', 'wpumrf_post_type_columns', 11 );

function wpumrf_post_type_columns_content( $columns ) {
	global $post;
	switch ( $columns ) {
		case 'registration_forms':
			$forms = carbon_get_post_meta( $post->ID, 'directory_registration_forms' );
			if ( $forms ) {
				$registration_forms = wpumrf_registration_forms();
				$form_names         = array();
				foreach ( $registration_forms as $id => $name ) {
					if ( in_array( $id, $forms ) ) {
						$form_names[] = $name;
					}
				}
				echo implode( ', ', $form_names );
			} else {
				echo esc_html__( 'All', 'wp-user-manager' );
			}
			break;
	}
}

add_action( 'manage_wpum_directory_posts_custom_column', 'wpumrf_post_type_columns_content' );

function wpumrf_directory_search_query_args( $args, $directory_id ) {
	$registration_forms = carbon_get_post_meta( $directory_id, 'directory_registration_forms' );

	if ( empty( $registration_forms ) || ! is_array( $registration_forms ) ) {
		return $args;
	}

	$meta_query = array(
		'key'     => 'wpum_form_id',
		'value'   => $registration_forms,
		'compare' => 'IN',
	);

	if ( ! isset( $args['meta_query' ] ) ) {
		$args['meta_query'] = array();
	}

	$args['meta_query'] = array_merge( $args['meta_query'], array( $meta_query ) );

	return $args;
}
add_filter( 'wpum_directory_search_query_args', 'wpumrf_directory_search_query_args', 10, 2 );