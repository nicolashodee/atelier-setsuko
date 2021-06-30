<?php
/**
 * Handles integration with the admin panel for the registration forms editor.
 *
 * @package     wpum-registration-forms
 * @copyright   Copyright (c) 2019, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store new forms into the database.
 *
 * @return void
 */
function wpumrf_save_registration_form() {

	check_ajax_referer( 'wpum_update_registration_form', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Something went wrong: could not create new registration form.', 'wpum-registration-forms' ), 403 );
	}

	$form_name = isset( $_POST['form_name'] ) && ! empty( $_POST['form_name'] ) ? sanitize_text_field( $_POST['form_name'] ) : false;

	if ( $form_name ) {

		$new_form = WPUM()->registration_forms->insert( [
			'name' => $form_name,
		] );

		$data = [
			'id'   => $new_form,
			'name' => $form_name,
		];

		do_action( 'wpum_registration_form_insert', $data, $new_form );

		wp_send_json_success( $data );


	} else {
		wp_die( esc_html__( 'Something went wrong: could not create new registration form.', 'wpum-registration-forms' ), 403 );
	}

}

add_action( 'wp_ajax_wpum_create_registration_form', 'wpumrf_save_registration_form' );

/**
 * Delete registration form from the database.
 *
 * @return mixed
 */
function wpumrf_delete_registration_form() {

	check_ajax_referer( 'wpum_delete_registration_form', 'nonce' );

	$form_id = isset( $_POST['form_id'] ) && ! empty( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : false;

	if ( ! current_user_can( 'manage_options' ) || empty( $form_id ) ) {
		wp_die( esc_html__( 'Something went wrong: could not delete the registration form.', 'wpum-registration-forms' ), 403 );
	}

	WPUM()->registration_forms->delete( $form_id );

	do_action( 'wpum_registration_form_delete', $form_id );

	$form_meta = WPUM()->registration_form_meta->get_meta( $form_id );

	if ( ! empty( $form_meta ) && is_array( $form_meta ) ) {
		foreach ( $form_meta as $key => $value ) {
			WPUM()->registration_form_meta->delete_meta( $form_id, $key );
		}
	}

	wp_send_json_success( (string) $form_id );

}

add_action( 'wp_ajax_wpum_delete_registration_form', 'wpumrf_delete_registration_form' );

function wpumrf_add_total_signups_to_form_data( $form_data ) {
	$default_signups = 0;
	// if form is default
	if ( isset( $form_data['default'] ) &&  $form_data['default'] ) {
		$users = get_users( array(
			'role'         => $form_data['role'],
			'meta_key'     => 'wpum_form_id',
			'meta_compare' => 'NOT EXISTS',
		) );

		$default_signups = count( $users );
	}

	$users = get_users( array(
		'meta_key'   => 'wpum_form_id',
		'meta_value' => $form_data['id'],
	) );

	$total_signups = count( $users );
	$total_signups = $total_signups + $default_signups;

	$form_data['total_signups'] = $total_signups;

	return $form_data;
}

add_filter( 'wpum_get_registration_form_data_for_table', 'wpumrf_add_total_signups_to_form_data' );

/**
 * Register the script file containing all the blocks for the block editor.
 */
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_script( 'wpum-blocks-registration', WPUMRF_PLUGIN_URL . 'includes/blocks/build/index.js', [
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-components',
			'wp-editor',
		], WPUMRF_VERSION, true );
} );

function wpumrf_add_registration_form_settings( $settings ) {
	$new_settings = array(
		array(
			'id'      => 'registration_redirect',
			'name'    => __( 'Redirect After Registration', 'wpum-registration-forms' ),
			'desc'    => __( 'Select the page where you want to redirect users after they successfully register. (Overrides the global redirect setting)', 'wp-user-manager' ),
			'type'    => 'multiselect',
			'options' => wpum_get_redirect_pages(),
		),
		array(
			'id'      => 'next_button_label',
			'name'    => __( 'Next Step Button Label', 'wpum-registration-forms' ),
			'desc'    => __( 'The button label text for the Next step button', 'wpum-registration-forms' ),
			'type'    => 'text',
			'value' => __( 'Next', 'wpum-registration-forms' )
		),
		array(
			'id'      => 'prev_button_label',
			'name'    => __( 'Previous Step Button Label', 'wpum-registration-forms' ),
			'desc'    => __( 'The button label text for the Previous step button', 'wpum-registration-forms' ),
			'type'    => 'text',
			'value' => __( 'Previous', 'wpum-registration-forms' )
		),
		array(
			'id'      => 'show_step_data',
			'name'    => __( 'Show Step Titles & Description', 'wpum-registration-forms' ),
			'desc'    => __( 'Show Step Titles & Description', 'wpum-registration-forms' ),
			'type'    => 'checkbox',
			'default' => true
		),
		array(
			'id'      => 'show_step_progress',
			'name'    => __( 'Show Step Progress Bar', 'wpum-registration-forms' ),
			'desc'    => __( 'Show Step Progress Bar', 'wpum-registration-forms' ),
			'type'    => 'checkbox',
			'default' => true
		),
		array(
			'id'      => 'show_step_breadcrumb',
			'name'    => __( 'Show Step Breadcrumbs', 'wpum-registration-forms' ),
			'desc'    => __( 'Show Step Breadcrumbs', 'wpum-registration-forms' ),
			'type'    => 'checkbox',
			'default' => true
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter('wpum_registration_form_settings_options', 'wpumrf_add_registration_form_settings' );
