<?php
/**
 * Handles custom form editor fields.
 *
 * @package     wpum-registration-forms
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WPUMRF_Fields {

    /**
	 * Get things started.
	 */
	public function __construct() {
        $this->init_hooks();
        $this->includes();
    }

    /**
     * Hooks to work with WPUM Form editor
     */
    public function init_hooks(){
        add_action( 'after_wpum_init', array( $this, 'insert_fields' ) );
        add_filter( 'wpum_non_allowed_fields', array( $this, 'exclude_wpumrf_fields' ) );
        add_filter( 'wpum_get_primary_field_types', array( $this, 'include_wpumrf_fields' ) );
        add_filter( 'wpum_load_fields', array( $this, 'load_wpumrf_fields' ) );
        add_action( 'wp_ajax_get_wpumrf_fields', array( $this, 'get_wpumrf_fields_data' ) );
        add_action( 'wp_ajax_save_wpumrf_field_meta', array( $this, 'save_wpumrf_field_meta' ) );
        add_action( 'wp_ajax_get_wpumrf_field_content', array( $this, 'get_wpumrf_field_content' ) );
    }

    /**
     * Includes
     */
    public function includes(){
        require_once WPUMRF_PLUGIN_DIR . 'includes/fields/types/class-wpumrf-field-html-content.php';
        require_once WPUMRF_PLUGIN_DIR . 'includes/fields/types/class-wpumrf-field-step.php';
    }

    /**
     * Inserting HTML field to DB
     */
    public function insert_fields(){

        global $wpdb;

		$fields  = array(
			'html_content' => 'HTML',
			'step' 		   => 'Step'
		);

        $results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."wpum_fields WHERE type IN ('html_content', 'step')" );
        if( is_array( $results ) ){
			foreach( $results as $result ){
				if( isset( $fields[$result->type] ) ){
					unset( $fields[$result->type] );
				}
			}
        }

		if( !count( $fields ) ){
			return;
		}

		foreach( $fields as $type => $name ){
			WPUM()->fields->insert(
				array(
					'group_id'    => 0,
					'field_order' => 0,
					'type'        => $type,
					'name'        => $name
				)
			);
		}
    }

    /**
     * Exclude HTML fields from WPUM Form editor right side list
     */
    public function exclude_wpumrf_fields($fields){

        $fields[] = 'html_content';
        $fields[] = 'step';

        return $fields;
    }

    /**
     * Include HTML field as a Field Type
     */
    public function include_wpumrf_fields($types){

        $types[] = 'html_content';
        $types[] = 'step';

        return $types;
    }

    /**
     * Include HTML field to load when needed
     */
    public function load_wpumrf_fields($fields){

        $fields['html_content'] = 'HTML';
        $fields['step'] = 'Step';

        return $fields;
    }

    /**
     * Ajax method to get fields data
     */
    public function get_wpumrf_fields_data(){

        check_ajax_referer( 'wpum_save_registration_form', 'nonce' );

		$fields   = WPUM()->fields->get_fields();
		$response = array();

		foreach( $fields as $field ){

			$response[] = array(
				'id'            => $field->get_ID(),
				'group_id'      => $field->get_group_id(),
				'field_order'   => $field->get_field_order(),
				'type'          => $field->get_type(),
				'type_nicename' => $field->get_type_nicename(),
				'name'          => $field->get_name(),
				'description'   => $field->get_description(),
				'visibility'    => $field->get_visibility(),
				'editable'      => $field->get_editable(),
				'default'       => $field->is_primary(),
				'default_id'    => $field->get_primary_id(),
				'required'      => $field->is_required(),
			);
		}

		wp_send_json_success( $response );
    }

    /**
     * Ajax method to save HTML field user input
     */
    public function save_wpumrf_field_meta(){

        global $wpdb;

        check_ajax_referer( 'wpum_save_registration_form', 'nonce' );

        $form_id    = $_POST['form_id'];
        $field_data = $_POST['field'];
		$field      = $_POST['field_id'];

		$form 			 = new WPUM_Registration_Form( $form_id );
		$invalid_indexes = array_filter( array_keys( (array) $form->get_fields() ), function($key)use($form,$field){
			return $form->get_fields()[$key] != $field;
		});

		if( count( $invalid_indexes ) ){
			$invalid_indexes = array_map( function( $index )use($field){
				return "field_{$field}_{$index}_content";
			}, $invalid_indexes );
		}

		$in_placeholders =  implode( ', ', array_fill( 0, count( $invalid_indexes ), '%s' ) );

        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}wpum_registration_formmeta WHERE wpum_registration_form_id = %d AND meta_key IN ( {$in_placeholders} )",
                array_merge( array( intval($form_id) ), $invalid_indexes )
            )
		);

        $field_data = stripslashes($field_data);
		$field_data = json_decode($field_data);

		$response = array();

		if( isset( $field_data->index, $field_data->content ) ){
			$content = is_string( $field_data->content ) ? $field_data->content : (array)$field_data->content;
			WPUM()->registration_form_meta->update_meta($form_id, "field_{$field}_{$field_data->index}_content", maybe_serialize( $content ) );

			$response = array(
				"id"      => $field,
				"index"   => $field_data->index,
				"content" => get_metadata( 'wpum_registration_form', $form_id, "field_{$field}_{$field_data->index}_content", true )
			);
		}

        wp_send_json_success( $response );
    }

    /**
     * Ajax method to get HTML field content
     */
    public function get_wpumrf_field_content(){

        check_ajax_referer( 'wpum_save_registration_form', 'nonce' );

        $form_id  = $_POST['form_id'];
        $field_id = $_POST['field_id'];

        $fields      = get_metadata( 'wpum_registration_form', $form_id, "fields", true );
        $field_data  = array();

        if( is_array($fields) ){
            foreach($fields as $index => $id){
                if( $id == $field_id ){

                    $field_data[] = array(
                        "id"      => $id,
                        "index"   => $index,
                        "content" => maybe_unserialize( get_metadata( 'wpum_registration_form', $form_id, "field_{$id}_{$index}_content", true ) )
                    );
                }
            }
        }

        wp_send_json_success( $field_data );
    }
}

new WPUMRF_Fields();
