<?php
/**
 * Registration Form Widget Extended.
 *
 * @package     wpum-registration-forms
 * @copyright   Copyright (c) 2020, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUMRF_Registration_Form_Widget Class
 *
 * @since 1.0.0
 */
class WPUMRF_Registration_Form_Widget extends WPUM_Registration_Form_Widget {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$forms = wpumrf_registration_forms();

		// Configure widget array
		$args = array(
			'label'       => __( '[WPUM] Registration Form', 'wp-user-manager' ),
			'description' => __( 'Display the registration form.', 'wp-user-manager' ),
		);

		$args['fields'] = array(
			array(
				'name'   => __( 'Title', 'wp-user-manager' ),
				'id'     => 'title',
				'type'   => 'text',
				'class'  => 'widefat',
				'std'    => __( 'Register', 'wp-user-manager' ),
				'filter' => 'strip_tags|esc_attr'
			),
			array(
				'name'   => __( 'Form Name', 'wpum-registration-forms' ),
				'id'     => 'form_id',
				'type'   => 'select',
				'std'    => 1,
				'filter' => 'strip_tags|esc_attr',
				'fields' => array_merge(
					array(
						array(
							'name' 	=> __( 'Select a Form',  'wpum-registration-forms'),
							'value' => 0
						)
					),
					array_map(
						function( $value, $name ){
							return array ( 'name' => $name, 'value' => $value );
						},
						array_keys( $forms ),
						$forms
					)
				)
			),
			array(
				'name'   => __( 'Display login link', 'wp-user-manager' ),
				'id'     => 'login_link',
				'type'   =>'checkbox',
				'std'    => 1,
				'filter' => 'strip_tags|esc_attr',
			),
			array(
				'name'   => __( 'Display password recovery link', 'wp-user-manager' ),
				'id'     => 'psw_link',
				'type'   =>'checkbox',
				'std'    => 1,
				'filter' => 'strip_tags|esc_attr',
			)
		);

		// create widget
		$this->create_widget( $args );

	}

	/**
	 * Display widget content.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	public function widget( $args, $instance ) {

		ob_start();

		echo $args['before_widget'];
		echo $args['before_title'];
		echo $instance['title'];
		echo $args['after_title'];

		$form_id_attr = !empty( $instance['form_id'] ) ? 'form_id = "'.$instance['form_id'].'"' : '';
		$psw_link     = $instance['psw_link'] ? 'yes':   false;
		$login_link   = $instance['login_link'] ? 'yes': false;

		echo do_shortcode( '[wpum_register '.$form_id_attr.' psw_link="'.$psw_link.'" login_link="'.$login_link.'"]' );

		echo $args['after_widget'];

		$output = ob_get_clean();

		echo $output;

	}

}
