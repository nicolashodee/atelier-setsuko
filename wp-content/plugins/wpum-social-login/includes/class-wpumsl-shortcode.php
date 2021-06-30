<?php
/**
 * Handles the display of social login buttons generator.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add login shortcode window to the editor.
 */
class WPUM_Shortcode_Social_Login_Buttons extends WPUM_Shortcode_Generator {

	/**
	 * Inject the editor for this shortcode.
	 */
	public function __construct() {
		$this->shortcode['title'] = esc_html__( 'Social login buttons', 'wpum-social-login' );
		$this->shortcode['label'] = esc_html__( 'Social login buttons', 'wpum-social-login' );
		parent::__construct( 'wpum_social_login_buttons' );
	}

	/**
	 * Setup fields for the login shortcode window.
	 *
	 * @return array
	 */
	public function define_fields() {
		return [
			array(
				'type'    => 'textbox',
				'name'    => 'redirect',
				'label'   => esc_html__( 'Redirect URL (optional)', 'wpum-social-login' ),
				'tooltip' => esc_html__( 'Optionally redirect the user after login or registration', 'wpum-social-login' )
			),
		];
	}

}

new WPUM_Shortcode_Social_Login_Buttons;
