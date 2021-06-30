<?php
/**
 * Handles the WPUM account page.
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Form_Group extends WPUM_Form {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'group';

	/**
	 * Determine if there's a referrer.
	 *
	 * @var mixed
	 */
	protected $referrer;

	/**
	 * Stores static instance of class.
	 *
	 * @access protected
	 * @var WPUM_Form_Login The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Holds the currently logged in user.
	 *
	 * @var integer
	 */
	protected $user = null;

	/**
	 * @var WP_Post
	 */
	protected $group;

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
		if ( is_singular( 'wpum_group' ) ) {
			global $post;

			$this->group = $post;
		}

		$this->user = wp_get_current_user();

		add_action( 'wp', array( $this, 'process' ) );

		$singular = WPUM_Group_Editor::singular();

		$this->steps = (array) apply_filters(
			'group_steps',
			array(
				'group' => array(
					'name'     => sprintf( esc_html__( 'New %s', 'wpum-groups' ), $singular ),
					'view'     => array( $this, 'show_form' ),
					'handler'  => array( $this, 'submit_handler' ),
					'priority' => 10,
				),
				'submit' => array(
					'name'     => sprintf( esc_html__( 'New %s submited', 'wpum-groups' ), strtolower( $singular ) ),
					'view'     => array( $this, 'submit' ),
					'handler'  => array( $this, 'submit_handler' ),
					'priority' => 10
				)
			)
		);

	}


	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}

		$this->fields = apply_filters(
			'group_page_form_fields',
			array(
				'group' => $this->get_group_fields(),
			)
		);
	}

	/**
	 * Retrieve the list of fields for the account page.
	 *
	 * @return array
	 */
	private function get_group_fields() {
		$fields = [
			'group-name'        => [
				'type'     => 'text',
				'name'     => 'group-name',
				'label'    => __( 'Name', 'wpum-groups' ),
				'required' => true,
				'template' => 'text',
				'value'         => $this->get_group_field_value( 'post_title' ),
			],
			'group-description' => [
				'type'     => 'textarea',
				'name'     => 'group-description',
				'label'    => __( 'Description', 'wpum-groups' ),
				'required' => false,
				'template' => 'wysiwyg',
				'value'    => $this->get_group_field_value( 'post_content', '' ),
			],
			'group-image'       => [
				'type'     => 'file',
				'name'     => 'group-image',
				'label'    => __( 'Image', 'wpum-groups' ),
				'required' => false,
				'template' => 'file',
				'value'    => $this->get_group_field_value( 'featured_image', '' ),
			],
			'group-category' => [
				'type'     => 'dropdown',
				'name'     => 'group-category',
				'label'    => __( 'Category', 'wpum-groups' ),
				'required' => false,
				'template' => 'select',
				'options'  => wpumgp_get_group_categories( false ),
				'value'    => $this->get_group_field_value( 'category', '' ),
			],
			'group-tags'        => [
				'type'        => 'textarea',
				'name'        => 'group-tags',
				'label'       => __( 'Tags', 'wpum-groups' ),
				'required'    => false,
				'template'    => 'textarea',
				'description' => 'Add tags on each line',
				'value' => $this->get_group_field_value( 'tags', '' ),
			],
			'group-privacy'    => [
				'type'     => 'dropdown',
				'name'     => 'group-privacy',
				'label'    => __( 'Privacy', 'wpum-groups' ),
				'required' => false,
				'template' => 'select',
				'options'  =>  array(
					'public'    => esc_html__( 'Public', 'wpum-groups' ),
					'private'   => esc_html__( 'Private', 'wpum-groups' ),
					'hidden'    => esc_html__( 'Hidden', 'wpum-groups' ),
				),
				'value' => $this->get_group_field_value( '_group_privacy_method', 'public' ),
			],
//			'group-invitation' => [
//				'type'     => 'dropdown',
//				'name'     => 'group-invitation',
//				'label'    => __( 'Invitation Control', 'wpum-groups' ),
//				'required' => false,
//				'template' => 'select',
//				'options'  => array(
//					'all'       => esc_html__( 'All users', 'wpum-groups' ),
//					'moderator' => esc_html__( 'Moderators', 'wpum-groups' ),
//					'admin'     => esc_html__( 'Admins', 'wpum-groups' ),
//				),
//				'value' => $this->get_group_field_value( '_group_invitation_control', 'all' ),
//			],
		];

		$fields = apply_filters( 'wpumg_group_fields', $fields );

		return $fields;

	}

	protected function get_group_field_value( $key, $default = false ) {
		if ( ! $this->group ) {
			return $default;
		}

		if ( $key === 'featured_image' ) {
			return get_the_post_thumbnail_url( $this->group->ID, 'large' );
		}

		if ( $key === 'category' ) {
			$cats = wp_get_object_terms( $this->group->ID, 'wpum_group_cat' );

			if ( $cats ) {
				return $cats[0]->slug;
			}
		}

		if ( $key === 'tags' ) {
			$tags = wp_get_object_terms( $this->group->ID, 'wpum_group_tag' );

			if ( $tags ) {
				return implode( "\n", wp_list_pluck( $tags, 'name') );
			}
		}

		return $this->group->{$key};
	}

	/**
	 * Display the account form.
	 *
	 * @return void
	 */
	public function show_form() {
		if ( ! wpum_can_user_create_group( $this->user->ID ) ) {
			return;
		}

		$this->init_fields();

		$data = [
			'form'      => $this->form_name,
			'action'    => home_url() . '/groups/submit',
			'fields'    => $this->fields,
			'user_id'   => $this->user->ID,
			'step_name' => '',
			'group_id'  => $this->group ? $this->group->ID : false,
		];

		WPUMGP()->templates
			->set_template_data( $data )
			->get_template_part( 'forms/form', 'group' );

	}


	/**
	 * Update the user profile.
	 *
	 * @return void
	 */
	public function submit_handler() {

		try {

			$this->init_fields();

			$values = $this->get_posted_fields();

			if ( ! wpum_can_user_create_group( $this->user->ID ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['group_nonce'], 'verify_group_form' ) ) {
				return;
			}

			if ( empty( $_POST['submit_group'] ) ) {
				return;
			}

			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}

			$group_data = [
				'post_type' => 'wpum_group',
				'post_status' => 'publish',
			];

			$existing_group_id = filter_input( INPUT_POST, 'wpum_group_id', FILTER_VALIDATE_INT );
			$action            = $existing_group_id ? 'update' : 'create';

			do_action( 'wpumg_before_' . $action . '_group', $this, $values, $this->user->ID );

			if ( isset( $values['group']['group-name'] ) ) {
				$group_data['post_title'] = sanitize_text_field( $values['group']['group-name'] );
			}

			if ( isset( $values['group']['group-description'] ) ) {
				$group_data['post_content'] = wp_kses_post( $values['group']['group-description'] );
			}


			if ( ! $existing_group_id ) {
				$post_id = wp_insert_post( $group_data );

				if ( is_wp_error( $post_id ) ) {
					throw new Exception( $post_id->get_error_message() );
				}
			} else {
				$post_id          = $existing_group_id;
				$group_data['ID'] = $post_id;
				wp_update_post( $group_data );
			}

			$new_uploaded_file = isset( $values['group']['group-image']['path'] ) && ! empty( $values['group']['group-image']['url'] ) ? esc_url_raw( $values['group']['group-image']['path'] ) : false;
			$currently_uploaded_file = isset( $values['group']['group-image'] ) && ! is_array( $values['group']['group-image'] ) ? $values['group']['group-image'] : false;
			$existing_file = $existing_group_id ? get_the_post_thumbnail_url( $existing_group_id, 'large' ) : false;

			if ( ! $currently_uploaded_file && $existing_file ) {
				delete_post_meta( $existing_group_id, '_thumbnail_id' );
			}

			if ( $new_uploaded_file ) {
				preg_match( '/[^\?]+\.(jpe?g|jpe|png)\b/i', $new_uploaded_file, $matches );
			    if ( ! $matches ) {
				    throw new Exception(  __( 'Invalid image type', 'wpum-groups' ) );
			    }

				$attach_id = $this->create_attachment( $new_uploaded_file );

    			if ( is_wp_error( $attach_id ) ) {
				    throw new Exception( $attach_id->get_error_message() );
    			}

				set_post_thumbnail( $post_id, $attach_id );
			}

			if ( ! empty( $values['group']['group-category'] ) ) {
				$category = sanitize_text_field( $values['group']['group-category'] );
				wp_set_object_terms( $post_id, $category, 'wpum_group_cat' );
			}

			if ( ! empty( $values['group']['group-tags'] ) ) {
				$tags = $values['group']['group-tags'];
				$tags = explode( "\n", str_replace( "\r", "", $tags ) );
				if ( $tags ) {
					$tags = array_map( 'sanitize_text_field', $tags );
					wp_set_object_terms( $post_id, null, 'wpum_group_tag' );
					wp_set_object_terms( $post_id, $tags, 'wpum_group_tag' );
				}
			}

			$privacy = empty( $values['group']['group-privacy'] ) ? 'public' :  $values['group']['group-privacy'];
			update_post_meta( $post_id, '_group_privacy_method', $privacy );

			$invitation = empty( $values['group']['group-invitation'] ) ? 'public' :  $values['group']['group-invitation'];
			update_post_meta( $post_id, '_group_invitation_control', $invitation );

			if ( ! $existing_group_id ) {
				//Insert current user as admin
				$user_roles = array( 'wpum_group_admin' );
				$db         = new WPUMG_DB_Group_Users();
				$data       = array(
					'group_id'  => $post_id,
					'user_id'   => $this->user->ID,
					'role'      => maybe_serialize( $user_roles ),
					'joined_at' => current_time( 'mysql' )
				);
				$type       = 'wpumg_user_to_group';
				$db->insert( $data, $type );
			}

			do_action( 'wpumg_after_group_update', $this, $values, $this->user->ID );

			// Successful, the success message now.
			$redirect = get_permalink( $post_id );
			$redirect = add_query_arg(
				[
					$action . 'd' => 'success',
				],
				$redirect
			);

			wp_safe_redirect( $redirect );
			exit;

		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage(), 'group_handler' );
			return;
		}

	}

	protected function create_attachment( $file, $file_name = null ) {
		if ( is_null( $file_name ) ) {
			$file_name = basename( $file );
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$wp_filetype = wp_check_filetype( $file_name );
		$attachment  = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

}
