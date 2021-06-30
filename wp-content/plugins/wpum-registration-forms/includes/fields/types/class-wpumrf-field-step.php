<?php
/**
 * Registers a html content field for the forms.
 *
 * @package     wwpum-registration-forms
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a html field type.
 */
class WPUM_Field_Step extends WPUM_Field_Type {

	public function __construct() {
		$this->name  = esc_html__( 'Step', 'wpum-registration-forms' );
		$this->type  = 'step';
		$this->icon  = 'dashicons-sort';
        $this->order = 3;
        $this->template = null;
        $this->group = 'advanced';
	}

}
