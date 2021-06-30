<?php
/**
 * Handles integration with the admin panel for the edit users
 *
 * @package     wpum-private-content
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
use Carbon_Fields\Container;
use Carbon_Fields\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function wpumpr_register_fields_in_admin() {
	$define_fields = [];

	$define_fields[] = Field::make( 'rich_text', 'wpum_private_content', 'Private Content' );

	Container::make( 'user_meta', 'WP User Manager - Private Content' )
	         ->set_datastore( new WPUM_User_Meta_Custom_Datastore() )
	         ->add_fields( $define_fields );
}

add_action( 'carbon_fields_register_fields', 'wpumpr_register_fields_in_admin' );
