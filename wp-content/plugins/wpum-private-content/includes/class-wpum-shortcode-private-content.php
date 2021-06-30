<?php
/**
 * Handles the display of private content shortcode generator.
 *
 * @package     wpum-private-content
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WPUM_Shortcode_Private_Content extends WPUM_Shortcode_Generator {

	/**
	 * Inject the editor for this shortcode.
	 */
	public function __construct() {
		$this->shortcode['title'] = esc_html__( 'Private Content', 'wp-private-content' );
		$this->shortcode['label'] = esc_html__( 'Private Content', 'wp-private-content' );
		parent::__construct( 'wpum_private_content' );
	}

}

new WPUM_Shortcode_Private_Content;
