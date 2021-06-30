<?php
/**
 * Handles the display of the gruop directory shortcode generator.
 *
 * @package     wpum-group
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WPUM_Shortcode_Group extends WPUM_Shortcode_Generator {

	/**
	 * Inject the editor for this shortcode.
	 */
	public function __construct() {
		$this->shortcode['title'] = esc_html__( 'Group Directory', 'wpum-groups' );
		$this->shortcode['label'] = esc_html__( 'Group Directory', 'wpum-groups' );
		parent::__construct( 'wpum_group_directory' );
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
				'name'    => 'per_page',
				'label'   => esc_html__( 'Groups per page', 'wpum-groups' ),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'has_search_form',
				'label'   => esc_html__( 'Show search form', 'wpum-groups' ),
				'options' => $this->get_yes_no(),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'show_public',
				'label'   => esc_html__( 'Show public groups only', 'wpum-groups' ),
				'options' => $this->get_yes_no(),
			),
			array(
				'type'    => 'listbox',
				'name'    => 'show_private',
				'label'   => esc_html__( 'Show private groups only', 'wpum-groups' ),
				'options' => $this->get_yes_no(),
			),
		];
	}

	/**
	 * Retrieve the yes or no option for listboxes.
	 *
	 * @return array
	 */
	protected function get_yes_no() {
		return [ 'true' => esc_html__( 'Yes', 'wp-user-manager' ), 'false' => esc_html__( 'No', 'wp-user-manager' ) ];
	}
}
