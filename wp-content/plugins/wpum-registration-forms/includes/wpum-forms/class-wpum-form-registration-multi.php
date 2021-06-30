<?php
/**
 * Handles the WPUM registration form.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Form_Registration_Multi extends WPUM_Form_Registration {

	use WPUM_Form_Account;

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'registration-multi';

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * Processed steps
	 *
	 * @var int
	 */
	protected $processed_steps = 0;

	/**
	 * Holds the currently logged in user.
	 *
	 * @var \WP_User
	 */
	protected $user = null;

	/**
	 * Returns static instance of class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		if ( is_user_logged_in() ) {
			$this->user = wp_get_current_user();
		}

		parent::__construct();
	}


	public function submit( $atts ) {
		$this->init_fields();
		$register_with = $this->get_register_by();

		$data = $this->get_submit_data();

		$form =  $this->get_registration_form();
		$after_registration = (bool)$form->get_setting( 'after_registration_form' );

		if ( $register_with || $after_registration ) {

			WPUM()->templates->set_template_data( $data )->get_template_part( 'forms/form', 'registration' );

			WPUM()->templates->set_template_data( $atts )->get_template_part( 'action-links' );

		} else {

			$admin_url = admin_url( 'users.php?page=wpum-registration-forms#/' );

			WPUM()->templates->set_template_data( [ 'message' => __( 'The registration form cannot be used because either a username or email field is required to process registrations. Please edit the form and add at least the email field.', 'wp-user-manager' ) . ' ' . '<a href="' . esc_url_raw( $admin_url ) . '">' . $admin_url . '</a>' ] )
			                 ->get_template_part( 'messages/general', 'error' );

		}
	}

	/**
	 * Retrieve the registration form from the database.
	 *
	 * @return \WPUM_Registration_Form
	 */
	public function get_registration_form() {
		if ( $this->registration_form ) {
			return $this->registration_form;
		}

		$form_id = filter_input( INPUT_POST, 'wpum_form_id', FILTER_VALIDATE_INT );
		if ( ! empty( $form_id ) && empty( $this->form_id ) ) {
			$this->form_id = $form_id;
		}

		if ( empty( $this->form_id ) ) {
			$form = WPUM()->registration_forms->get_forms();
			$form = $form[0];
		} else {
			$form = new \WPUM_Registration_Form( $this->form_id );
		}

		$this->registration_form = $form;

		return $form;
	}

	/**
	 * Retrieve the registration form fields.
	 *
	 * @return array
	 */
	protected function get_registration_fields(){
		$fields = parent::get_registration_fields();

		$form = $this->get_registration_form();

		$after_registration = (bool) $form->get_setting( 'after_registration_form' );

		if ( $after_registration && $this->user ) {
			foreach ( $fields as $key => $field ) {
				if ( $key == 'robo' ) {
					unset( $fields[ $key ] );
					continue;
				}

				$fieldObject = new WPUM_Field( $field['id'] );

				$fields[ $key ]['value'] = $this->get_user_field_value( $this->user, $fieldObject );
			}
		}

		return $this->inject_html_fields( $fields, $form );
	}

	public function output( $atts = array() ) {
		if ( isset( $atts['form_id'] ) ) {
			$this->form_id = $atts['form_id'];
		}
		parent::output( $atts );
	}

	protected function get_submit_data() {
		$data = parent::get_submit_data();

		$data['form_id'] = $this->form_id;
		$form = $this->get_registration_form();

		$after_registration = (bool) $form->get_setting( 'after_registration_form' );

		if ( $after_registration ) {
			$data['submit_label'] = apply_filters( 'wpumrf_post_registration_form_submit_label', __( 'Save Changes', 'wpum-registration-forms' ), $this->form_id );
		}

		return $data;
	}

	/**
	 * Get form specific redirect page after registration
	 *
	 * @return mixed|string
	 */
	protected function get_redirect_page() {
		$form          = $this->get_registration_form();
		$redirect_page = $form->get_setting( 'registration_redirect' );

		if ( $redirect_page ) {
			return get_permalink( $redirect_page[0] );
		}

		return parent::get_redirect_page();
	}

	/**
	 * Inject html fields with content to appropiate position
	 *
	 * @var $fields
	 * @var $form
	 */
	private function inject_html_fields( $fields, $form ){

		$multi_types = array( 'html_content', 'step' );

		foreach( $fields as $type => $field ) {
			if( in_array( $type, $multi_types ) ){
				unset($fields[$type]);
			}
		}

		$multi_fields = array_filter(
			$form->get_fields(),
			function($field_id)use($multi_types){
				$field = new WPUM_Field( $field_id );
				return in_array( $field->get_type(), $multi_types );
			}
		);

		foreach($multi_fields as $index => $field_id){

			$field = new WPUM_Field( $field_id);
			$data = array(
				'label'       => $field->get_name(),
				'type'        => $field->get_type(),
				'required'    => $field->get_meta( 'required' ),
				'placeholder' => $field->get_meta( 'placeholder' ),
				'description' => $field->get_description(),
				'priority'    => $index,
				'primary_id'  => $field->get_primary_id(),
				'options'     => $this->get_custom_field_dropdown_options( $field ),
				'template'    => $field->get_parent_type(),
			);
			$data = array_merge( $data, $field->get_field_data() );
			$data = array_merge( $data, array( 'content' => maybe_unserialize( $form->get_meta( "field_{$field_id}_{$index}_content", true ) ) ) );

			/**
			 * Merging $fields array with `+` sign
			 */
			$fields = array_slice( $fields, 0, ( $index + 1 ), true ) +
					array( $this->get_parsed_id( $field->get_name(), $field->get_primary_id(), $field )."_{$index}" => $data ) +
					array_slice( $fields, ( $index + 1 ), count( $fields ) - 1, true );
		}

		return $fields;
	}

	public function render_registration_form_fields( $field, $key, $fields ){
		if ( $field['type'] === 'step' ) {

			if ( $this->processed_steps > 0 ) {
				echo '</div>';
			}

			$this->processed_steps ++;

			echo sprintf( '<div class="step" data-step="%s" %s>', $this->processed_steps, $this->processed_steps > 1 ? 'style="display:none;"' : '' );

			$form = $this->get_registration_form();

			if ( (bool) $form->get_setting( 'show_step_data' ) ) {
				echo ! empty( $field['content']['title'] ) ? sprintf( '<h2 class="step-title">%s</h2>', $field['content']['title'] ) : '';
				echo ! empty( $field['content']['description'] ) ? sprintf( '<p class="step-description">%s</p>', $field['content']['description'] ) : '';
			}
		}

		parent::render_registration_form_fields( $field, $key, $fields );

		$registration_fields = array_keys( $fields );
		if ( array_pop( $registration_fields ) === $key && $this->processed_steps > 0 ) {
			echo '</div>';
		}
	}

	public function submit_handler() {
		$form = $this->get_registration_form();

		if ( (bool) $form->get_setting( 'after_registration_form' ) ) {
			try {

				$this->init_fields();

				$values = $this->get_posted_fields();

				if ( ! wp_verify_nonce( $_POST['registration_nonce'], 'verify_registration_form' ) ) {
					return;
				}

				if ( empty( $_POST['submit_registration'] ) ) {
					return;
				}

				if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
					throw new Exception( $return->get_error_message() );
				}

				$values['account'] = $values['register'];
				unset( $values['register'] );

				do_action( 'wpum_before_user_update', $this, $values, $this->user->ID );

				$updated_user_id = $this->update_account_values( $this->user, $values, true );

				do_action( 'wpum_after_user_update', $this, $values, $updated_user_id );

				// Successful, the success message now.
				$redirect = get_permalink();
				$redirect = add_query_arg(
					[
						'updated' => 'success',
					],
					$redirect
				);

				$redirect_page = $this->get_redirect_page();

				if ( $redirect_page ) {
					wp_safe_redirect( $redirect_page );
					exit;
				} else {
					wp_safe_redirect( $redirect );
					exit;
				}

			} catch ( Exception $e ) {
				$this->add_error( $e->getMessage(), 'account_handler' );

				return;
			}
		}

		return parent::submit_handler();
	}
}
