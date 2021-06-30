<?php
/**
 * Handles integration with the shortcode for the registration forms.
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

function wpumrf_shortcode_defined_fields( $fields ){
	$all_forms = WPUM()->registration_forms->get_forms();
	if ( count( $all_forms ) <= 1 ) {
		return $fields;
	}

	$forms = array();
	foreach ( $all_forms as $form ) {
		$forms[ $form->id ] = $form->name;
	}

	$default = array(
		array(
			'type'    => 'listbox',
			'name'    => 'form_id',
			'label'   => esc_html__( 'Select form:', 'wp-user-manager' ),
			'options' => $forms,
		),
	);

	$fields = array_merge( $default, $fields );

	return $fields;
}

add_filter( 'wpum_shortcode_registration_defined_fields', 'wpumrf_shortcode_defined_fields' );

/**
 * Show the registration form through a shortcode.
 *
 * @param array  $atts
 * @param string $content
 * @return void
 */
function wpumrf_registration_form( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'form_id' => '',
				'login_link' => '',
				'psw_link'   => '',
			),
			$atts
		)
	);

	$is_success = isset( $_GET['registration'] ) && $_GET['registration'] == 'success' ? true : false;

	/**
	 * If the form_id isn't supplied as a shortcode argument, check it is supplied as a query string
	 */
	if ( empty( $atts['form_id'] ) ) {
		$form_id_arg_name = apply_filters( 'wpum_registration_form_id_query_arg_name', 'form_id' );
		$form_id          = filter_input( INPUT_GET, $form_id_arg_name, FILTER_VALIDATE_INT );
		if ( ! empty( $form_id ) ) {
			$atts['form_id'] = $form_id;
		}
	}

	ob_start();

	if ( wpum_is_registration_enabled() ) {
		$form = wpum_get_registration_form($form_id);

		$after_registration = (bool) $form->get_setting( 'after_registration_form' );

		if ( is_user_logged_in() && ! $after_registration && ! $is_success && ! ( isset( $_GET['context'] ) && 'edit' === $_GET['context'] ) ) {

			WPUM()->templates
				->get_template_part( 'already-logged-in' );

		} else if ( $after_registration && ! is_user_logged_in() ) {
			WPUM()->templates->set_template_data( [ 'message' => apply_filters( 'wpumrf_post_registration_form_not_logged_in_message', esc_html__( 'Content not available.', 'wp-user-manager' ) ) ] )
			                 ->get_template_part( 'messages/general', 'warning' );
		} elseif ( $is_success ) {

			$success_message = apply_filters( 'wpum_registration_success_message', esc_html__( 'Registration complete. We have sent you a confirmation email with your details.', 'wp-user-manager' ) );

			WPUM()->templates
				->set_template_data(
					[
						'message' => $success_message,
					]
				)
				->get_template_part( 'messages/general', 'success' );

		} else {

			echo WPUM()->forms->get_form( 'registration-multi', $atts );

		}
	} else {

		WPUM()->templates
			->set_template_data(
				[
					'message' => esc_html__( 'Registrations are currently disabled.', 'wp-user-manager' ),
				]
			)
			->get_template_part( 'messages/general', 'error' );

	}

	$output = ob_get_clean();

	return $output;

}

function wpumrf_register_shortcode() {
	remove_shortcode( 'wpum_register' );
	add_shortcode( 'wpum_register', 'wpumrf_registration_form' );
}

add_action( 'wp_loaded', 'wpumrf_register_shortcode' );

/**
 * @param string $callback
 * @param string $block
 *
 * @return string
 */
function wpumrf_wpum_blocks_block_callback( $callback, $block ) {
	if ( 'registration-form' === $block ) {
		return 'wpumrf_registration_form';
	}

	return $callback;
}

add_filter( 'wpum_blocks_block_callback', 'wpumrf_wpum_blocks_block_callback', 10, 2 );

function wpumrf_form_path( $form_file, $form_name ) {
	if ( 'registration-multi' === $form_name ) {
		$form_file = str_replace( '-multi', '', $form_file );
		include_once $form_file;
		$form_file = WPUMRF_PLUGIN_DIR . 'includes/wpum-forms/class-wpum-form-' . $form_name . '.php';
	}

	return $form_file;
}

add_filter( 'wpum_load_form_path', 'wpumrf_form_path', 10, 2 );

function get_registration_forms() {
	$forms = WPUM()->registration_forms->get_forms();
	$registrationForms = [];

	foreach ( $forms as $key => $form ) {
		$registrationForms[] = array(
			'value' => $form->id,
			'label' => $form->name,
		);
	}

	return $registrationForms;
}

add_action('rest_api_init', function () {
	register_rest_route(
		'wp-user-manager',
		'registration-forms',
		array(
			'methods' => 'GET',
			'callback' => 'get_registration_forms',
			'permission_callback' => function() {
				return current_user_can('edit_posts');
			}
		 )
	);
});
