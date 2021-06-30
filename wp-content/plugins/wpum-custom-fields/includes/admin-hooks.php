<?php
/**
 * Handles integration with the admin panel for the fields editor.
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
 * Store new groups into the database.
 *
 * @return void
 */
function wpumcf_save_custom_fields_groups() {

	check_ajax_referer( 'wpum_update_fields_groups', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Something went wrong: could not create new fields group.', 'wpum-custom-fields' ), 403 );
	}

	$group_name        = isset( $_POST['group_name'] ) && ! empty( $_POST['group_name'] ) ? sanitize_text_field( $_POST['group_name'] ) : false;
	$group_description = isset( $_POST['group_description'] ) && ! empty( $_POST['group_description'] ) ? wp_kses_post( $_POST['group_description'] ) : '';

	if ( $group_name ) {

		$new_group = WPUM()->fields_groups->insert(
			[
				'name'        => $group_name,
				'description' => $group_description,
			]
		);

		$data = [
			'id'          => $new_group,
			'name'        => $group_name,
			'description' => $group_description,
		];

		do_action( 'wpum_field_group_insert', $data, $new_group );
		wp_send_json_success( $data );

	} else {
		wp_die( esc_html__( 'Something went wrong: could not create new fields group.', 'wpum-custom-fields' ), 403 );
	}

}
add_action( 'wp_ajax_wpum_create_fields_group', 'wpumcf_save_custom_fields_groups' );

/**
 * Delete fields groups from the database.
 *
 * @return mixed
 */
function wpumcf_delete_custom_fields_group() {

	check_ajax_referer( 'wpum_delete_fields_groups', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Something went wrong: could not delete fields group.', 'wpum-custom-fields' ), 403 );
	}

	$group_id = isset( $_POST['group_id'] ) && ! empty( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : false;

	$delete_group = WPUM()->fields_groups->delete( $group_id );

	do_action( 'wpum_field_group_delete', $group_id );

	$fields = WPUM()->fields->get_fields(
		[
			'group_id' => $group_id,
		]
	);

	if ( ! empty( $fields ) && is_array( $fields ) ) {
		foreach ( $fields as $field ) {
			WPUM()->fields->delete( $field->get_ID() );

			do_action( 'wpum_field_delete', $field->get_ID() );
		}
	}

	wp_send_json_success( (string) $group_id );

}
add_action( 'wp_ajax_wpum_delete_field_group', 'wpumcf_delete_custom_fields_group' );

/**
 * Create a custom field and save it into the database.
 *
 * @return void
 */
function wpumcf_save_custom_field() {

	check_ajax_referer( 'wpum_create_field', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( null, 403 );
	}

	$group_id   = isset( $_POST['group_id'] ) && ! empty( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : false;
	$field_name = isset( $_POST['field_name'] ) && ! empty( $_POST['field_name'] ) ? sanitize_text_field( $_POST['field_name'] ) : false;
	$field_type = isset( $_POST['field_type'] ) && ! empty( $_POST['field_type'] ) ? sanitize_text_field( $_POST['field_type'] ) : false;

	$data = [
		'group_id' => $group_id,
		'type'     => $field_type,
		'name'     => $field_name,
	];

	// Create field into the database.
	$new_field = WPUM()->fields->insert( $data );

	do_action( 'wpum_field_group_insert', $data, $new_field );

	// Assign a custom user meta key.
	$prefix   = 'wpum_field_';

	$field = new WPUM_Field( $new_field );
	if ( $field->get_type() == 'file' ) {
		$meta_key = $prefix . 'file_' . $new_field;
	} else {
		$meta_key = $prefix . $new_field;
	}

	$meta_key = preg_replace( "/[^A-Za-z0-9_]/", '', $meta_key );

	$field->update_meta( 'user_meta_key', $meta_key );

	// Make field visible.
	$field->update_meta( 'visibility', 'public' );
	$field->update_meta( 'editing', 'public' );

	if( !empty( $_POST['parent'] ) ){
		$field->update_meta( 'parent_id', intval( $_POST['parent'] ) );
	}

	$data = [
		'field_id'   => $new_field,
		'field_name' => $field_name,
		'field_type' => $field_type,
		'default_id' => false,
	];

	do_action( 'wpum_field_insert', $data, $new_field );

	wp_send_json_success( $data );

}
add_action( 'wp_ajax_wpum_create_field', 'wpumcf_save_custom_field' );

/**
 * Delete a field from the database.
 *
 * @return void
 */
function wpumcf_delete_field() {

	check_ajax_referer( 'wpum_delete_field', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( null, 403 );
	}

	$field_id = isset( $_POST['field_id'] ) && ! empty( $_POST['field_id'] ) ? absint( $_POST['field_id'] ) : false;

	if ( $field_id ) {

		WPUM()->fields->delete( $field_id );

		$forms = WPUM()->registration_forms->get_forms();

		if ( ! empty( $forms ) && is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				$fields = $form->get_fields();
				$fields = array_flip( $fields );
				if ( array_key_exists( $field_id, $fields ) ) {
					unset( $fields[ $field_id ] );
				}
				$fields = array_flip( $fields );
				$form->update_meta( 'fields', $fields );
			}
		}

		do_action( 'wpum_field_delete', $field_id );

		wp_send_json_success( [ 'field_id' => (string) $field_id ] );

	} else {
		wp_send_json_error( null, 403 );
	}

}
add_action( 'wp_ajax_wpum_delete_field', 'wpumcf_delete_field' );

/**
 * Modify the output of the file field in the admin panel when the data is serialized.
 *
 * @param mixed $null
 * @param int $object_id
 * @param string $meta_key
 * @param bool $single
 * @return void
 */
function wpumcf_adjust_file_output_in_admin( $null, $object_id, $meta_key, $single ) {

	if ( strpos( $meta_key, 'wpum_file_field_' ) === 0 && is_admin() ) {
		remove_filter( 'get_user_metadata', 'wpumcf_adjust_file_output_in_admin', 100 );
		$meta = carbon_get_user_meta( $object_id, $meta_key );
		$meta = maybe_unserialize( $meta );
		if ( is_array( $meta ) && isset( $meta['url'] ) ) {
			$meta = $meta['url'];
		}
		add_filter( 'get_user_metadata', 'wpumcf_adjust_file_output_in_admin', 100, 4 );
		return $meta;
	}

	return $null;

}
add_filter( 'get_user_metadata', 'wpumcf_adjust_file_output_in_admin', 100, 4 );


/**
 * Modify the carbon locals of admin.
 *
 * @param array $l10n
 * @return array
 */
function wpumcf_carbon_admin_l10n( $l10n ){

	if( isset( $l10n['field']['complexAddButton'] ) ){
		$l10n['field']['complexAddButton'] = '%s';
	}

	return $l10n;
}
add_filter( 'carbon_fields_l10n', 'wpumcf_carbon_admin_l10n', 99, 1 );


/**
 * New conditions tab for field editor
 *
 * @return Array
 */
function wpumcf_fields_editor_tabs( $tabs ){

	$tabs[] = array(
		'id'   => 'conditions',
		'name' => esc_html__( 'Conditional Logic', 'wpum-custom-fields' )
	);

	return $tabs;
}
add_filter( 'wpum_get_fields_editor_edit_tabs', 'wpumcf_fields_editor_tabs');


/**
 * Condition field settings
 *
 * @param array $settings
 * @param $type
 *
 * @return array
 */
function wpumcf_conditional_field_settings( $settings ){
	$settings['conditions'] = array(
		'enable' => array(
			'default' => false,
			'hint' 	  => esc_html__( 'Enable conditional logic', 'wpum-custom-fields' ),
			'label'   => '',
			'model'	  => 'enable_condition',
			'type'	  => 'checkbox'

		),
		'conditions' => array(
			'model' => 'conditions',
			'type'	=> 'conditional'
		)
	);

	return $settings;
}
add_filter( 'wpum_register_field_type_settings', 'wpumcf_conditional_field_settings', 99 );


/**
 * Condition Field Admin script and locals
 *
 */
function wpumcf_admin_scripts(){
	$screen 		 = get_current_screen();
	$allowed_screens = apply_filters( 'wpumcf_conditional_script_screens', [ 'users_page_wpum-custom-fields', 'users_page_wpum-registration-forms' ] );

	if ( !in_array( $screen->base, $allowed_screens ) ) {
		return;
	}

	wp_enqueue_script( 'wpumcf-admin-js', WPUMCF_PLUGIN_URL . 'dist/js/bundle.js', array(), WPUMCF_VERSION, true );

	$labels = array(
		'show_field_if'  => esc_html__( 'Show this field if', 'wpum-custom-fields' ),
		'or' 			 => esc_html__( 'Or', 'wpum-custom-fields' ),
		'add_rule_group' => esc_html__( 'Add rule group', 'wpum-custom-fields' ),
		'and' 			 => esc_html__( 'and', 'wpum-custom-fields' )
	);

	wp_localize_script( 'wpumcf-admin-js', 'wpumcfFieldEditor', array( 'labels' => $labels, 'conditions' => wpumcf_field_conditions() ) );
}
add_action( 'admin_enqueue_scripts', 'wpumcf_admin_scripts', 9 );


function wpumcf_get_conditions(){
	wp_send_json_success( wpumcf_field_conditions() );
}
add_action( 'wp_ajax_wpumcf_get_conditions', 'wpumcf_get_conditions' );


/**
 * Saving the ruleset before handling field data
 * This is not an override for `wpum_update_field` ajax method
 * But this gets called before the `wpum_update_field` ajax method
 *
 * @return Void
 */
function wpumcf_save_ruleset(){

	if( ! check_ajax_referer( 'wpum_get_fields', 'nonce', false ) ){
		return;
	}

	if ( ! current_user_can( apply_filters( 'wpum_admin_pages_capability', 'manage_options' ) ) ) {
		return;
	}

	$field_id = !empty( $_POST['field_id'] ) ? absint( $_POST['field_id'] ) : false;
	$data     = !empty( $_POST['data'] ) && !empty( $_POST['data']['conditions'] ) ? $_POST['data']['conditions'] : false;

	if( $field_id && $data ){
		update_metadata( 'wpum_field', $field_id, 'conditions', maybe_serialize(  $_POST['data']['conditions'] ) );
		unset( $_POST['data']['conditions'] );
	}
}
add_action( 'wp_ajax_wpum_update_field', 'wpumcf_save_ruleset', 9 );


/**
 * Unserialize the conditions array
 *
 * @return Array
 */
function wpumcf_ruleset_unserialize( $model, $id ){

	if( isset( $model['conditions'] ) ){
		$model['conditions'] = array_filter( (array) maybe_unserialize( $model['conditions'] ) );
	}

	return $model;
}
add_filter( 'wpum_fields_editor_deregister_model', 'wpumcf_ruleset_unserialize', 10, 2 );


/**
 * Conditional fields
 *
 * @return Array
 *
 */
function wpumcf_get_conditional_fields(){

	check_ajax_referer( 'wpum_get_fields', 'nonce', true );

	// Parsing args to keep minimum route data
	$field_id = !empty( $_POST['field_id'] ) ? intval( $_POST['field_id'] ) : 0;
	$field 	  = new WPUM_Field( $field_id );
	$fields   = [];

	if( !$field ){
		wp_send_json_error();
	}

	$group_id 	  = $field->get_group_id();
	$group_fields = WPUM()->fields->get_fields(
		[
			'group_id' => $group_id,
			'orderby'  => 'field_order',
			'order'    => 'ASC',
			'parent'   => 0
		]
	);

	foreach ( $group_fields as $_field ) {
		$fields[] = [
			'id'   => $_field->get_ID(),
			'name' => $_field->get_name()
		];
	}

	wp_send_json_success( apply_filters( 'wpumcf_conditional_fields', $fields ) );
}
add_action( 'wp_ajax_wpumcf_get_conditional_fields', 'wpumcf_get_conditional_fields' );
