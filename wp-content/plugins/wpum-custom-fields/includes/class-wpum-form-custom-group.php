<?php
/**
 * Handles the form where users can update their details for custom fields.
 *
 * @package     wpum-delete-account
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Form_Custom_Group extends WPUM_Form {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'custom-group';

	public $fields_group_id = false;

	/**
	 * Stores static instance of class.
	 *
	 * @access protected
	 */
	protected static $_instance = null;

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

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'process' ) );

		$this->steps = (array) apply_filters(
			'custom_group_steps',
			array(
				'submit' => array(
					'name'     => false,
					'view'     => array( $this, 'submit' ),
					'handler'  => array( $this, 'submit_handler' ),
					'priority' => 10,
				),
			)
		);

		uasort( $this->steps, array( $this, 'sort_by_priority' ) );

		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

	}

	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields( $fields_group_id = false ) {

		if ( $fields_group_id ) {
			$this->fields_group_id = $fields_group_id;
		}

		if ( $this->fields ) {
			return;
		}

		$fields_from_group = WPUM()->fields->get_fields(
			[
				'group_id' => $this->fields_group_id,
			]
		);

		$fields = [ 'custom_group_form_fields' => [] ];

		foreach ( $fields_from_group as $field ) {

			if ( $field->get_meta( 'editing' ) !== 'public' ) {
				continue;
			}

			$options        = [];
			$stored_options = $field->get_meta( 'dropdown_options' );
			if ( ! empty( $stored_options ) && is_array( $stored_options ) ) {
				foreach ( $stored_options as $option ) {
					$options[ $option['value'] ] = $option['label'];
				}
			}

			$data = [
				'id' 			=> $field->get_ID(),
				'label'         => $field->get_name(),
				'description'   => $field->get_description(),
				'type'          => $field->get_type(),
				'required'      => $field->is_required(),
				'placeholder'   => $field->get_meta( 'placeholder' ),
				'read_only'     => $field->get_meta( 'read_only' ),
				'max_file_size' => $field->get_meta( 'max_file_size' ),
				'value'         => apply_filters( 'wpum_custom_field_value', carbon_get_user_meta( get_current_user_id(), $field->get_meta( 'user_meta_key' ) ), $field, get_current_user_id() ),
				'priority'      => $field->get_field_order(),
				'options'       => apply_filters( 'wpum_form_custom_field_dropdown_options', $options, $field ),
				'template'      => $field->get_parent_type(),
			];

			$data = array_merge( $data, $field->get_field_data() );

			$fields['custom_group_form_fields'][ $field->get_meta( 'user_meta_key' ) ] = $data;

			if ( $field->get_parent_type() == 'file' && $field->get_meta( 'max_file_size' ) ) {
				$fields['custom_group_form_fields'][ $field->get_meta( 'user_meta_key' ) ]['max_file_size'] = $field->get_meta( 'max_file_size' );
			}
		}

		$this->fields = apply_filters( 'wpum_custom_fields_group_fields', $fields, $fields_group_id );

	}

	/**
	 * Show the form.
	 *
	 * @return void
	 */
	public function submit( $atts ) {

		$this->init_fields();

		$data = [
			'form'      => $this->form_name,
			'action'    => $this->get_action(),
			'fields'    => $this->get_fields( 'custom_group_form_fields' ),
			'step'      => $this->get_step(),
			'group_id'  => $this->fields_group_id,
			'step_name' => $atts['group']->get_name(),
		];

		WPUM()->templates
			->set_template_data( $data )
			->get_template_part( 'forms/custom-group-form' );

	}

	/**
	 * Handle submission of the form.
	 *
	 * @return void
	 */
	public function submit_handler() {
		try {

			$this->init_fields( $_POST['group_id'] );

			$values = $this->get_posted_fields();

			if ( ! wp_verify_nonce( $_POST['account_custom_nonce'], 'verify_custom_account_form' ) ) {
				return;
			}
			if ( empty( $_POST['submit_custom_account'] ) ) {
				return;
			}
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}

			$user = wp_get_current_user();

			if ( $user instanceof WP_User ) {

				$user_id = $user->ID;

				$registered_fields = $this->get_fields( 'custom_group_form_fields' );

				foreach ( $values['custom_group_form_fields'] as $key => $value ) {

					$field_type = isset( $registered_fields[ $key ]['template'] ) ? $registered_fields[ $key ]['template'] : false;

					if ( $field_type === 'file' ) {
						$currently_uploaded_file = isset( $_POST[ 'current_' . $key ] ) && ! empty( $_POST[ 'current_' . $key ] ) ? esc_url_raw( $_POST[ 'current_' . $key ] ) : false;
						$filepath                = get_user_meta( $user_id, $key . '_path', true );
						$fileurl                = get_user_meta( $user_id, $key, true );

						if ( is_array( $value ) && array_key_exists( 'url', $value ) && $currently_uploaded_file !== $value['url'] ) {
							if ( $filepath !== $value['path'] ) {
								wp_delete_file( $filepath );
							}
							carbon_set_user_meta( $user_id, $key, $value['url'] );
							update_user_meta( $user_id, $key . '_path', $value['path'] );
						}

						if ( ! $currently_uploaded_file && $filepath ) {
							wp_delete_file( $filepath );
							carbon_set_user_meta( $user_id, $key, false );
							delete_user_meta( $user_id, $key . '_path' );
						}

						if ( ! $currently_uploaded_file && $fileurl && ! $filepath ) {
							carbon_set_user_meta( $user_id, $key, false );
						}
					} elseif ( strpos( $key, 'wpum_' ) === 0 && $field_type !== 'file' ) {

						$original_value = $value;
						if ( $value == '1' ) {
							$value = true;
						}

						$value = apply_filters( 'wpum_custom_fields_account_meta_update', $value, $key, $user_id, $original_value );

						carbon_set_user_meta( $user_id, $key, $value );
					}
				}

				do_action( 'wpum_after_custom_user_update', $this, $values, $user_id );

				$redirect = get_permalink();
				$tab      = get_query_var( 'tab' );
				$redirect = rtrim( $redirect, '/' ) . '/' . $tab;
				$redirect = add_query_arg(
					[
						'updated' => 'success',
					],
					$redirect
				);

				wp_safe_redirect( $redirect );
				exit;

			} else {
				throw new Exception( esc_html__( 'Something went wrong while updating your details.', 'wpum-custom-fields' ) );
			}
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

}
