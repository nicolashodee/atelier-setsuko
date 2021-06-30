<?php

/**
 * Load WPUM scripts on the frontend for registration forms not on the 'registration' page
 */
function wpumrf_load_scripts() {
	global $post;

	if ( ! isset( $post ) ) {
		return;
	}

	if ( is_page( wpum_get_core_page_id( 'register' ) ) ) {
		return;
	}

	if ( ! has_shortcode( $post->post_content, 'wpum_register' ) && ! has_block( 'wpum/registration-form', $post ) ) {
		return;
	}

	wpum_enqueue_scripts();
}

add_action( 'wp_enqueue_scripts', 'wpumrf_load_scripts' );

/**
 * Load scripts and styles of WPUM Form editor
 */
function wpumrf_load_editor_scripts(){

	$screen = get_current_screen();
	if($screen && $screen->id === 'users_page_wpum-registration-forms'){
		wp_enqueue_style( 'wpumrf-editor-css', WPUMRF_PLUGIN_URL . 'includes/fields/assets/css/admin.css' );

		wp_enqueue_editor();
		wp_enqueue_script( 'wpumrf-editor-js', WPUMRF_PLUGIN_URL . 'includes/fields/dist/js/bundle.js', array(), WPUMRF_VERSION, true );

		$labels = array(
			'field_options' 	 		=> esc_html__( 'Field options', 'wp-user-manager' ),
			'field_add_option'   		=> esc_html__( 'Add option', 'wp-user-manager' ),
			'field_option_label' 		=> esc_html__( 'Option label', 'wp-user-manager' ),
			'field_option_value' 		=> esc_html__( 'Option value', 'wp-user-manager' ),
			'save'               		=> esc_html__( 'Save changes', 'wp-user-manager' ),
			'field_edit_settings_error' => esc_html__( 'Something went wrong, could not find the settings for this field type.', 'wp-user-manager' ),
			'field_error_required'      => esc_html__( 'Error: this setting is required.', 'wp-user-manager' ),
			'field_error_special'       => esc_html__( 'Error: this setting cannot contain special characters.', 'wp-user-manager' ),
			'field_error_nosave'        => esc_html__( 'There are some errors with your changes. Please check the errors highlighted below.', 'wp-user-manager' ),
			'field_edit_settings_error' => esc_html__( 'Something went wrong, could not find the settings for this field type.', 'wp-user-manager' ),
			'error_general'             => esc_html__( 'Something went wrong, no changes were saved.', 'wp-user-manager' ),
			'fields_add_new'            => esc_html__( 'Add new custom field', 'wp-user-manager' ),
			'table_drag_tooltip'        => esc_html__( 'Drag and drop the rows below to change the order.', 'wp-user-manager' ),
			'fields_name'               => esc_html__( 'Field name', 'wp-user-manager' ),
			'fields_type'               => esc_html__( 'Type', 'wp-user-manager' ),
			'fields_required'           => esc_html__( 'Required', 'wp-user-manager' ),
			'fields_visibility'         => esc_html__( 'Privacy', 'wp-user-manager' ),
			'fields_edit'               => esc_html__( 'Edit field', 'wp-user-manager' ),
			'fields_delete'             => esc_html__( 'Delete field', 'wp-user-manager' ),
			'fields_editable'           => esc_html__( 'Editable', 'wp-user-manager' ),
			'fields_required_tooltip'   => esc_html__( 'Fields marked as required will be compulsory within the registration and account form.', 'wp-user-manager' ),
			'fields_editable_tooltip'   => esc_html__( 'Fields marked as locked, can only be edited by an administrator and will not be visible in any form.', 'wp-user-manager' ),
			'fields_visibility_tooltip' => esc_html__( 'Hidden fields are not publicly visible within profiles.', 'wp-user-manager' ),
			'table_actions'             => esc_html__( 'Actions', 'wp-user-manager' ),
			'repeater_fields_add_new'	=> esc_html__( 'Add new sub field', 'wp-user-manager' ),
			'repeater_fields_create'	=> esc_html__( 'Add sub field', 'wp-user-manager' ),
		);

		$js_variables = [
			'is_addon_installed'        => apply_filters( 'wpum_fields_editor_has_custom_fields_addon', false ),
			'page_title'                => esc_html__( 'Fields Editor', 'wp-user-manager' ),
			'success_message'           => esc_html__( 'Changes successfully saved.', 'wp-user-manager' ),
			'labels'                    => $labels,
			'groups'                    => array(),
			'ajax'                      => admin_url( 'admin-ajax.php' ),
			'pluginURL'                 => WPUM_PLUGIN_URL,
			'nonce'                     => wp_create_nonce( 'wpum_update_fields_groups' ),
			'delete_fields_group_nonce' => wp_create_nonce( 'wpum_delete_fields_groups' ),
			'get_fields_nonce'          => wp_create_nonce( 'wpum_get_fields' ),
			'create_field_nonce'        => wp_create_nonce( 'wpum_create_field' ),
			'delete_field_nonce'        => wp_create_nonce( 'wpum_delete_field' ),
			'cf_addon_url'              => 'https://wpusermanager.com/addons/custom-fields?utm_source=WP%20User%20Manager&utm_medium=insideplugin&utm_campaign=WP%20User%20Manager&utm_content=custom-fields-editor',
			'fields_types'              => wpum_get_registered_field_types(),
			'edit_dialog_tabs'          => wpum_get_edit_field_dialog_tabs()
		];

		wp_localize_script( 'wpumrf-editor-js', 'wpumFieldsEditor', $js_variables );
	}
}
add_action( 'admin_enqueue_scripts', 'wpumrf_load_editor_scripts', 1 );


function wpumrf_frontend_scripts(){
	wp_enqueue_style( 'wpumrf-frontend-css', WPUMRF_PLUGIN_URL . 'assets/css/frontend.css', false, WPUMRF_VERSION );
	wp_enqueue_script( 'wpumrf-frontend-js', WPUMRF_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), true, WPUMRF_VERSION );
}
add_action( 'wpum_enqueue_frontend_scripts', 'wpumrf_frontend_scripts' );
