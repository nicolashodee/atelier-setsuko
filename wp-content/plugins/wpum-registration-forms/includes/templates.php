<?php
/**
 * Handles integration with the template for the registration forms.
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

function wpumrf_add_form_id_input( $data ) {
	?>
	<input type="hidden" name="wpum_form_id" value="<?php echo $data->form_id; ?>" />
	<?php
}

add_action( 'wpum_before_submit_button_registration_form', 'wpumrf_add_form_id_input' );

function wpumrf_html_field_template( $field, $key ) {
	if( $field['type'] === 'html_content' ){
		include "templates/form-fields/html.php";
	}
}

add_action( 'wpum_registration_form_field', 'wpumrf_html_field_template', 10, 2 );


function wpumrf_step_progress_breadcrumbs($data){

	if( $data->form !== 'registration-multi' ){
		return;
	}

	$form = wpum_get_registration_form($data->form_id);
	if( !$form->exists() ){
		return;
	}

	$step_fields = array_filter( $data->fields, function( $field ){
		return $field['type'] === 'step';
	});

	if( !count( $step_fields ) ){
		return;
	}

	if( (bool)$form->get_setting( 'show_step_progress' ) ){
		$progress = count( $step_fields ) > 1 ? 100 / count( $step_fields ) : 100;
		echo sprintf( '<div class="step-progress-wrapper"><div class="step-progress-bar" style="width:%s;"></div></div>', $progress.'%' );
	}

	if( (bool)$form->get_setting( 'show_step_breadcrumb' ) ){
		echo '<div class="step-breadcrumbs">';
		foreach( $step_fields as $step ){
			if( !empty( $step['content']['title'] ) ){
				echo sprintf( '<button class="step-breadcrumb button">%s</button>', $step['content']['title'] );
			}
		}
		echo '</div>';
	}
}

add_action( 'wpum_before_registration_form', 'wpumrf_step_progress_breadcrumbs' );

function wpumrf_step_buttons($data){

	if( $data->form !== 'registration-multi' ){
		return;
	}

	$form = wpum_get_registration_form($data->form_id);
	if( !$form->exists() ){
		return;
	}

	$step_fields = array_filter( $data->fields, function( $field ){
		return $field['type'] === 'step';
	});

	if( !count( $step_fields ) ){
		return;
	}

	$prev_label = $form->get_setting('prev_button_label');
	$next_label = $form->get_setting('next_button_label');

	echo sprintf(
		'<div class="step-button-wrappers"><button class="step-previous">%s</button><button class="step-next">%s</button></div>',
		!empty( $prev_label ) ? $prev_label : __( 'Previous', 'wpum-registration-forms' ),
		!empty( $next_label ) ? $next_label : __( 'Next', 'wpum-registration-forms' )
	);

}
add_action( 'wpum_after_registration_form', 'wpumrf_step_buttons' );

function wpumrf_form_message($data){
	if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => apply_filters( 'wpumrf_post_registration_form_success_message', esc_html__( 'Profile successfully updated.', 'wp-user-manager' ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}
}

add_action( 'wpum_before_registration_form', 'wpumrf_form_message' );
