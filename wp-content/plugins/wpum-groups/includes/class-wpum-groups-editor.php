<?php
/**
 * Handles all registration of users groups.
 *
 * @package     wp-groups
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The class that handles the user groups.
 */
class WPUM_Group_Editor {

	/**
	 * Get things started.
	 */
	public function init() {
		add_action( 'init', [ $this, 'register' ], 0 );
		add_action( 'admin_init', [ $this, 'handle_actions' ] );
		add_action( 'save_post', [ $this, 'save_to_table' ], 100, 2 );
		add_filter( 'post_updated_messages', [ $this, 'group_updated_messages' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'register_group_settings' ] );

		add_filter( 'wpumcr_restrict_meta_exclude_post_types', function( $types ) {
			$types[] = 'wpum_group';

			return $types;
		} );
	}

	public static function singular() {
		return apply_filters( 'wpum_group_cpt_singular', wpum_get_option( 'group_singular', 'Group' ) );
	}

	public static function plural() {
		$plural = wpum_get_option( 'group_plural', self::singular() . 's' );

		return apply_filters( 'wpum_group_cpt_plural', $plural );
	}

	/**
	 * Handle user actions
	 */
	public function handle_actions() {

		if ( ! isset( $_GET['post'] ) ) {
			return;
		}

		if ( isset( $_GET['remove_user'] ) && $_GET['remove_user'] == 'true' && isset( $_GET['user_id'] ) && isset( $_GET['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( $_GET['_wpnonce'] );
			if ( wp_verify_nonce( $nonce, 'remove_user_group' ) ) {

				$user_id = sanitize_text_field( $_GET['user_id'] );
				$group_id = sanitize_text_field( $_GET['post'] );

				do_action ( 'wpumgp_user_leave_group', $group_id, $user_id );

				$users = wpumgp_get_users();
				for ( $i = 0; $i < count( $users ); $i++ ) {

					$member = get_post_meta( (int) $group_id, '_group_new_member|||' . $i . '|value', true );
					if ( $member == $user_id ) {

						delete_post_meta( $group_id, '_group_new_member|||' . $i . '|value' );
					}
				}

				$url = admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&message=11' );
				wp_safe_redirect( $url );
				exit;
			}
		}

		if ( isset( $_GET['approve_user'] ) && $_GET['approve_user'] == 'true' && isset( $_GET['user_id'] ) && isset( $_GET['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( $_GET['_wpnonce'] );
			if ( wp_verify_nonce( $nonce, 'approve_user_group' ) ) {

				$user_id = sanitize_text_field( $_GET['user_id'] );
				$group_id = sanitize_text_field( $_GET['post'] );

				wpumgr_approve_group_member( $group_id, $user_id );

				$url = admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&message=12' );
				wp_safe_redirect( $url );
				exit;
			}
		}

		if ( isset( $_GET['reject_user'] ) && $_GET['reject_user'] == 'true' && isset( $_GET['user_id'] ) && isset( $_GET['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( $_GET['_wpnonce'] );
			if ( wp_verify_nonce( $nonce, 'reject_user_group' ) ) {

				$user_id = sanitize_text_field( $_GET['user_id'] );
				$group_id = sanitize_text_field( $_GET['post'] );

				wpumgr_reject_group_member( $group_id, $user_id );

				$url = admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&message=13' );
				wp_safe_redirect( $url );
				exit;
			}
		}
	}

	/**
	 * Save data to db table
	 *
	 * @param $post_id
	 */
	public function save_to_table( $post_id ) {
		if ( ! isset( $_POST['post_type'] ) || 'wpum_group' !== $_POST['post_type'] ) {
			return;
		}

		if ( 'trash' == get_post_status( $post_id ) ) {
			return;
		}

		$db = new WPUMG_DB_Group_Users();
		$new_members = array();

		$new_members_str = ( isset( $_POST['_group_new_member'] ) ? sanitize_text_field( $_POST['_group_new_member'] ) : false );
		if ( $new_members_str ) {

			$new_members = explode( '|', $new_members_str );
			if ( is_array( $new_members ) && count( $new_members ) > 0 ) {

				foreach ( $new_members as $member ) {

					$user_meta  = get_userdata( $member );
					$user_roles = $user_meta->roles;


					$row_id = $db->get_row_by( $post_id, $member );
					if ( $row_id != false ) {

						$new_members_role = ( isset( $_POST[ 'wpum-group-user-role-' . $member ] ) ? array( sanitize_text_field( $_POST[ 'wpum-group-user-role-' . $member ] ) ) : array() );
						$user_roles       = array_filter( $user_roles, function ( $v ) {
							return ( $v != 'wpum_group_member' && $v != 'wpum_group_moderator' && $v != 'wpum_group_admin' );
						} );
						$user_roles       = array_merge( $user_roles, array( $new_members_role ) );

						$data  = array(
							'role' => maybe_serialize( $user_roles ),
						);
						$where = 'id';

						$db->update( (int) $row_id, $data, $where );
					} else {

						$privacy_method = get_post_meta( $post_id, '_group_privacy_method', true );
						$user_roles = array_merge( $user_roles, array( 'wpum_group_member' ) );
						$data       = array(
							'group_id'  => $post_id,
							'user_id'   => $member,
							'role'      => maybe_serialize( $user_roles ),
							'joined_at' => current_time( 'mysql' ),
							'status'    => 'approved',
						);

						$type = 'wpumg_user_to_group';

						$db->insert( $data, $type );

						do_action( 'wpumgp_after_member_join', $post_id, $member, $privacy_method );
					}
				}
			}
		}

		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'wpum-group-existing-user-role-' ) === 0 ) {
				$user_id = str_replace('wpum-group-existing-user-role-', '', $key );
				if ( in_array( $user_id, $new_members ) || empty( $_POST[ $key ] ) ) {
					continue;
				}

				$existing_role = $_POST[ $key ];
				$new_role      = $_POST[ 'wpum-group-user-role-' . $user_id ];

				if ( $new_role === $existing_role ) {
					continue;
				}

				$user_meta  = get_userdata( $user_id );
				$user_roles = $user_meta->roles;

				$user_roles = array_filter( $user_roles, function ( $v ) {
					return ( $v != 'wpum_group_member' && $v != 'wpum_group_moderator' && $v != 'wpum_group_admin' );
				} );
				$user_roles = array_merge( $user_roles, array( $new_role ) );

				$data  = array(
					'role' => maybe_serialize( $user_roles ),
				);
				$where = 'id';

				$row_id = $db->get_row_by( $post_id, $user_id );

				$db->update( (int) $row_id, $data, $where );
			}
		}

		$users = wpumgp_get_users();
		for ( $i = 0; $i < count( $users ); $i ++ ) {

			delete_post_meta( $post_id, '_group_new_member|||' . $i . '|value' );
		}
	}

	/**
	 * Modify the group messages
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	public function group_updated_messages( $messages ) {

	  global $post, $post_ID;

	  $singular = self::singular();

	  $messages['wpum_group'] = array(
	    0 => '',
	    1 => sprintf( __( '%1$s updated. <a href="%2$s">View %1$s</a>', 'wpum-groups' ), $singular, esc_url( get_permalink( $post_ID ) ) ),
	    2 => sprintf( __( '%s updated.', 'wpum-groups' ), $singular ),
	    3 => sprintf( __( '%s deleted.', 'wpum-groups' ), $singular ),
	    4 => sprintf( __( '%s updated.', 'wpum-groups' ), $singular ),
	    5 => ( isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s', 'wpum-groups' ), $singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false ),
	    6 => sprintf( __( '%1$s published. <a href="%2$s">View %1$s</a>', 'wpum-groups' ), $singular, esc_url( get_permalink( $post_ID ) ) ),
	    7 => sprintf( __( '%s saved.', 'wpum-groups' ), $singular ),
	    8 => sprintf( __( '%1$s submitted. <a target="_blank" href="%2$s">Preview %1$s</a>', 'wpum-groups' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
	    9 => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %1$s</a>', 'wpum-groups' ), $singular, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
	    10 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview group</a>', 'wpum-groups' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
	    11 => sprintf( __( 'Member has been removed successfully.', 'wpum-groups' ), $singular ),
	    12 => sprintf( __( 'Member has been approved.', 'wpum-groups' ), $singular ),
	    13 => sprintf( __( 'Member has been rejected.', 'wpum-groups' ), $singular ),
	  );

	  return $messages;
	}

	/**
	 * Register settings for the groups
	 *
	 * @return void
	 */
	public function register_group_settings() {

		$post_ID = (isset($_GET['post']) ? $_GET['post'] : false);

		do_action( 'wpumgp_before_register_group_settings' );

		$singular = self::singular();

		$privacy_fields = array(
			Field::make( 'radio', 'group_privacy_method', esc_html__( 'Privacy', 'wpum-groups' ) )
			     ->set_help_text( esc_html__( 'Select the privacy method.', 'wpum-groups' ) )
			     ->add_options( array(
				     'public'    => esc_html__( 'Public (Anyone can see the group and any user can join)', 'wpum-groups' ),
				     'private'   => esc_html__( 'Private (Anyone can see the group and any user can ask to join but must be approved)', 'wpum-groups' ),
				     'hidden'    => esc_html__( 'Hidden (Only members can see the group and need to be added by a site admin)', 'wpum-groups' ),
			     ) ),
//			 Field::make( 'select', 'group_invitation_control', esc_html__( 'Invitation Control', 'wpum-groups' ) )
// 			     ->set_help_text( esc_html__( 'Select the invitation control.', 'wpum-groups' ) )
// 			     ->add_options( array(
// 				     'all'    		=> esc_html__( 'All users', 'wpum-groups' ),
// 				     'moderator'    => esc_html__( 'Moderators', 'wpum-groups' ),
// 				     'admin'      	=> esc_html__( 'Admins', 'wpum-groups' ),
// 			     ) ),
		);

		Container::make( 'post_meta', esc_html( sprintf( __( '%s Settings', 'wpum-groups' ), $singular ) ) )
		         ->where( 'post_type', '=', 'wpum_group' )
		         ->add_fields( apply_filters( 'wpum_group_privacy_settings', $privacy_fields ) );


		$new_memebers_fields = array(
			Field::make( 'multiselect', 'group_new_member', esc_html__( 'Add Members', 'wpum-groups' ) )
			     ->set_help_text( esc_html__( 'Select the users to add.', 'wpum-groups' ) )
			     ->add_options( wpumgp_get_users() ),
		);

	 	Container::make( 'post_meta', esc_html__( 'Add New Members', 'wpum-groups' ) )
		         ->where( 'post_type', '=', 'wpum_group' )
		         ->add_fields( apply_filters( 'wpum_group_add_new_members_settings', $new_memebers_fields ) );

		$memebers_html = array(
			Field::make( 'html', 'group_list_members' )
    			 ->set_html( '<table class="widefat"><tr><th>' . __( 'User', 'wpum-groups' ) . '</th><th>' . __( 'Joined at', 'wpum-groups' ) . '</th><th>' . __( 'Role', 'wpum-groups' ) . '</th><th>' . __( 'Status', 'wpum-groups' ) . '</th><th>' . __( 'Actions', 'wpum-groups' ) . '</th></tr><tr>' . $this->get_group_members( $post_ID ) . '</tr></table>' )
		);

		Container::make( 'post_meta', esc_html__( 'Members', 'wpum-groups' ) )
		 		 ->where( 'post_type', '=', 'wpum_group' )
		 		 ->add_fields( $memebers_html );

		do_action( 'wpumgp_after_register_group_settings' );
	}

	protected function get_group_members( $post_ID ) {
		$names = array();

		$users = wpumgp_get_group_members( $post_ID );

		$privacy_method = get_post_meta( $post_ID, '_group_privacy_method', true );

		if ( $users && count( $users ) > 0 ) {

			foreach ( $users as $item ) {

				$roles = maybe_unserialize( $item->role );

				$roles_arr  = array(
					array( 'wpum_group_member', 'Member' ),
					array( 'wpum_group_moderator', 'Moderator' ),
					array( 'wpum_group_admin', 'Admin' ),
				);
				$selected = 'wpum_group_member';
				$roles_html = '<select name="wpum-group-user-role-' . $item->user_id . '" autocomplete="off">';
				foreach ( $roles_arr as $role ) {
					if ( in_array( $role[0], $roles ) ) {
						$selected = $role[0];
					}
					$roles_html .= '<option value="' . $role[0] . '"' . ( in_array( $role[0], $roles ) ? 'selected="selected"' : false ) . '>' . $role[1] . '</option>';
				}
				$roles_html .= '</select>';
				$roles_html .= '<input type="hidden" name="wpum-group-existing-user-role-' . $item->user_id .'" value="' . $selected . '">';

				$remove_link = wp_nonce_url( admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&remove_user=true&user_id=' . $item->user_id ), 'remove_user_group' );

				$actions = array();
				$actions[ $remove_link ] = __( 'Remove', 'wpum-groups' );

				if ( $item->status === 'pending' ) {
					$approve_link = wp_nonce_url( admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&approve_user=true&user_id=' . $item->user_id ), 'approve_user_group' );

					$actions[ $approve_link ] = __( 'Approve', 'wpum-groups' );

					$reject_link = wp_nonce_url( admin_url( 'post.php?post=' . sanitize_text_field( $_GET['post'] ) . '&action=edit&reject_user=true&user_id=' . $item->user_id ), 'reject_user_group' );

					$actions[ $reject_link ] = __( 'Reject', 'wpum-groups' );
				}

				$actions_html = array();
				foreach( $actions as $url => $label ) {
					$actions_html[]  = '<a href="' . $url . '">' . $label . '</a>';
				}

				$names[] = '<td><a href="' . get_edit_user_link( $item->user_id ) . '">' . get_the_author_meta( 'display_name', $item->user_id ) . '</a></td><td>' . date( get_option( 'date_format' ), strtotime( $item->joined_at ) ) . '</td><td>' . $roles_html . '</td><td>' . ucwords( $item->status ) . '</td><td>' . implode( ' | ',  $actions_html ) . '</td>';
			}
		}

		if ( count( $names ) > 0 ) {
			return implode( '</tr><tr>', $names );
		} else {
			return '<td colspan="3">' . __( 'No users found', 'wpum-groups' ) . '</td>';
		}

	}

	/**
	 * Register the group post type.
	 *
	 * @return void
	 */
	public function register() {
		$singular = self::singular();
		$plural = self::plural();

		$title = apply_filters( 'wpum_group_cpt_title', ' User ' . $plural );

		$labels = array(
			'name'                  => $title,
			'singular_name'         => $singular,
			'menu_name'             => $title,
			'name_admin_bar'        => $singular,
			'archives'              => sprintf( __( '%s Archives', 'wpum-groups' ), $singular ),
			'attributes'            => sprintf( __( '%s Attributes', 'wpum-groups' ), $singular) ,
			'parent_item_colon'     => sprintf( __( '%s Group:', 'wpum-groups' ), $singular ),
			'all_items'             => $plural,
			'add_new_item'          => sprintf( __( 'Add new %s', 'wpum-groups' ), strtolower( $singular ) ),
			'add_new'               => __( 'Add New', 'wpum-groups' ),
			'new_item'              => sprintf( __( 'New %s', 'wpum-groups' ), $singular ),
			'edit_item'             => sprintf( __( 'Edit %s', 'wpum-groups' ), $singular ),
			'update_item'           => sprintf( __( 'Update %s', 'wpum-groups' ), $singular ),
			'view_item'             => sprintf( __( 'View %s', 'wpum-groups' ), $singular ),
			'view_items'            => sprintf( __( 'View %s', 'wpum-groups' ), $plural ),
			'search_items'          => sprintf( __( 'Search %s', 'wpum-groups' ), $singular ),
			'not_found'             => __( 'Not found', 'wpum-groups' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wpum-groups' ),
			'featured_image'        => __( 'Featured Image', 'wpum-groups' ),
			'set_featured_image'    => __( 'Set featured image', 'wpum-groups' ),
			'remove_featured_image' => __( 'Remove featured image', 'wpum-groups' ),
			'use_featured_image'    => __( 'Use as featured image', 'wpum-groups' ),
			'insert_into_item'      => sprintf( __( 'Insert into %s', 'wpum-groups' ), strtolower( $singular ) ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'wpum-groups' ), strtolower( $singular ) ),
			'items_list'            => sprintf( __( '%s list', 'wpum-groups' ), $plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'wpum-groups' ), $plural ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'wpum-groups' ), strtolower( $plural ) ),
		);
		$args = array(
			'label'               => $singular,
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies'          => array( 'wpum_group_cat', 'wpum_group_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'rewrite'             => array( 'slug' => apply_filters( 'wpum_group_cpt_slug', strtolower( $singular ) ) ),
			'show_ui'             => true,
			'menu_position'       => 71,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-groups',
		);
		register_post_type( 'wpum_group', $args );

		register_taxonomy(
			'wpum_group_cat',
			'wpum_group',
			apply_filters(
				'wpum_group_cat_args',
				array(
					'hierarchical'          => true,
					'label'                 => __( 'Categories', 'wpum-groups' ),
					'labels'                => array(
						'name'              => sprintf( __( '%s categories', 'wpum-groups' ), $singular ) ,
						'singular_name'     => __( 'Category', 'wpum-groups' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'wpum-groups' ),
						'search_items'      => __( 'Search categories', 'wpum-groups' ),
						'all_items'         => __( 'All categories', 'wpum-groups' ),
						'parent_item'       => __( 'Parent category', 'wpum-groups' ),
						'parent_item_colon' => __( 'Parent category:', 'wpum-groups' ),
						'edit_item'         => __( 'Edit category', 'wpum-groups' ),
						'update_item'       => __( 'Update category', 'wpum-groups' ),
						'add_new_item'      => __( 'Add new category', 'wpum-groups' ),
						'new_item_name'     => __( 'New category name', 'wpum-groups' ),
						'not_found'         => __( 'No categories found', 'wpum-groups' ),
					),
					'show_ui'               => true,
					'query_var'             => true,
				)
			)
		);

		register_taxonomy(
			'wpum_group_tag',
			'wpum_group',
			apply_filters(
				'wpum_group_tag_args',
				array(
					'hierarchical'          => false,
					'label'                 => sprintf( __( '%s tags', 'wpum-groups' ), $singular ),
					'labels'                => array(
						'name'                       => sprintf( __( '%s tags', 'wpum-groups' ), $singular ),
						'singular_name'              => __( 'Tag', 'wpum-groups' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'wpum-groups' ),
						'search_items'               => __( 'Search tags', 'wpum-groups' ),
						'all_items'                  => __( 'All tags', 'wpum-groups' ),
						'edit_item'                  => __( 'Edit tag', 'wpum-groups' ),
						'update_item'                => __( 'Update tag', 'wpum-groups' ),
						'add_new_item'               => __( 'Add new tag', 'wpum-groups' ),
						'new_item_name'              => __( 'New tag name', 'wpum-groups' ),
						'popular_items'              => __( 'Popular tags', 'wpum-groups' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'wpum-groups' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'wpum-groups' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'wpum-groups' ),
						'not_found'                  => __( 'No tags found', 'wpum-groups' ),
					),
					'show_ui'               => true,
					'query_var'             => true,
				)
			)
		);
	}
}
