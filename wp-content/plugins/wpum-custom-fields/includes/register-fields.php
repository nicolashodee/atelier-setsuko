<?php
/**
 * Register new custom fields into the admin panel.
 *
 * @package     wpum-custom-fields
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom fields in the admin panel.
 *
 * @return void
 */
function wpumcf_register_fields_in_admin() {
	global $pagenow;

	if ( ! empty( $pagenow ) && $pagenow === 'users.php' ) {
		return;
	}

	$fields_groups = WPUM()->fields_groups->get_groups(
		[
			'fields'  => true,
			'orderby' => 'group_order',
			'order'   => 'ASC',
		]
	);

	if ( ! empty( $fields_groups ) && is_array( $fields_groups ) ) {
		foreach ( $fields_groups as $group ) {

			$fields        = $group->get_fields();
			$define_fields = [];

			foreach ( $fields as $field ) {

				if ( $field->is_primary() || empty( $field->get_meta( 'user_meta_key' ) ) ) {
					continue;
				}

				$define_fields[] = wpumf_create_carbon_field_by_type( $field );
			}

			Container::make( 'user_meta', $group->get_name() )
				->set_datastore( new WPUM_User_Meta_Custom_Datastore() )
				->add_fields( $define_fields );
		}
	}

}
add_action( 'carbon_fields_register_fields', 'wpumcf_register_fields_in_admin' );


/**
 * Helper get carbon field type from wpum field type
 *
 * @param string     $field_type
 * @param WPUM_Field $field
 *
 * @return string
 */
function wpumcf_field_to_carbon_type( $field_type, $field ){
	if ( $field_type === 'taxonomy' ) {
		$field_type = empty( $field->get_meta( 'field_type' ) ) ? 'select' : $field->get_meta( 'field_type' );
	}

	if ( $field_type === 'user' ) {
		$field_type = empty( $field->get_meta( 'allow_multiple' ) ) ? 'select' : 'multiselect';
	}

	switch ( $field_type ) {
		case 'dropdown':
			$field_type = 'select';
			break;
		case 'email':
		case 'password':
		case 'url':
		case 'number':
		case 'telephone':
			$field_type = 'text';
			break;
		case 'multicheckbox':
			$field_type = 'set';
			break;
		case 'datepicker':
			$field_type = 'date';
			break;
		case 'wysiwyg':
			$field_type = 'rich_text';
			break;
	}

	return $field_type;
}


/**
 * Helper to generate carbon field by wpum field type
 *
 * @param WPUM_Field $field
 *
 * @return \Carbon_Fields\Field\Field
 */
function wpumf_create_carbon_field_by_type( $field ){

	$template = $field->get_parent_type();
	$type 	  = $field->get_type();

	switch( $type ){
		case 'email':
		case 'passsword':
		case 'url':
		case 'number':
			return Field::make( 'text', $field->get_meta( 'user_meta_key' ), $field->get_name() )->set_attribute( 'type', $field->get_type() );
		case 'multicheckbox':
		case 'dropdown':
		case 'radio':
		case 'multiselect':
		case 'taxonomy':
		case 'user':
			$options        = [];
			$stored_options = $field->get_meta( 'dropdown_options' );
			if ( ! empty( $stored_options ) && is_array( $stored_options ) ) {
				foreach ( $stored_options as $option ) {
					$options[ $option['value'] ] = $option['label'];
				}
			}
			$options = apply_filters( 'wpum_admin_cb_fields_registration_field_options', $options, $field );
			return Field::make( wpumcf_field_to_carbon_type( $template, $field ), $field->get_meta( 'user_meta_key' ), $field->get_name() )->add_options( $options );
		case 'datepicker':
			return Field::make( wpumcf_field_to_carbon_type( $field->get_parent_type(), $field ), $field->get_meta( 'user_meta_key' ), $field->get_name() )
					->set_storage_format( get_option( 'date_format' ) )
					->set_input_format( get_option( 'date_format' ), get_option( 'date_format' ) );
		case 'repeater':
			$children = WPUM()->fields->get_fields([
				'group_id' => $field->get_group_id(),
				'parent'   => $field->get_ID(),
				'order'	   => 'ASC'
			]);
			$child_fields = [];
			if( is_array( $children ) ){
				foreach ($children as $child) {
					$child_fields[] = wpumf_create_carbon_field_by_type( $child );
				}
			}
			$label = $field->get_meta( 'button_label' ) ? $field->get_meta( 'button_label' ) : esc_html__( 'Add row', 'wp-user-manager' );
			return Field::make( 'complex', $field->get_meta( 'user_meta_key' ), $field->get_name() )
						->add_fields( $child_fields )
						->setup_labels( array( 'singular_name' => $label ) );
		case 'file':
			return Field::make( wpumcf_field_to_carbon_type( $template, $field ), $field->get_meta( 'user_meta_key' ), $field->get_name() )->set_value_type( 'url' );
			break;						
		default:
			return Field::make( wpumcf_field_to_carbon_type( $template, $field ), $field->get_meta( 'user_meta_key' ), $field->get_name() );
	}
}
