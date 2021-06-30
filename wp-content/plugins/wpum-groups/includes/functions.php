<?php
/**
 * Functions collection to work with directories.
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieve the list of users.
 *
 * @return array
 */
function wpumgp_get_users() {

	$list = array();

	$args = get_terms([
		'role__in'   => array( 'wpum_group_member', 'wpum_group_moderator', 'wpum_group_admin' ),
		'order'      => 'ASC',
    	'orderby'    => 'display_name',
	]);

	$wp_user_query = new WP_User_Query( $args );
	$users = $wp_user_query->get_results();

	if ($users && is_array($users) && count($users) > 0) {
		foreach ( $users as $user ) {
			$list[$user->ID] = $user->display_name;
		}
	}

	return apply_filters( 'wpumgp_get_user_list', $list );
}

function wpum_can_user_create_group( $user_id ) {
	$user_data     = get_userdata( $user_id );
	$roles         = isset( $user_data->roles ) ? $user_data->roles : array();
	$allowed_roles = (array) wpum_get_option( 'create_groups_roles', array( 'administrator' ) );

	return ! empty( array_intersect( $roles, $allowed_roles ) );
}

function wpumgrp_prepare_group( $group, $user_id, $db ) {
	$group_id = apply_filters( 'wpum_group_id', $group->ID );
	$privacy_method           = get_post_meta( $group_id, '_group_privacy_method', true );
	$group->privacy_method    = $privacy_method;
	$group->user_id           = $user_id;
	$group->is_member         = false;
	$group->hide_buttons      = false;
	$group->user_status = false;
	if ( $user_id ) {
		$group_user = $db->get_row( $group_id, $user_id );
		if ( $group_user && $group_user->status === 'approved' ) {
			$group->is_member    = true;
			$group->hide_buttons = false;
		}

		if ( $group_user && $group_user->status === 'rejected' ) {
			$group->is_member    = false;
			$group->hide_buttons = true;
		}

		if ( $group_user && $group_user->status === 'pending' ) {
			$group->is_member    = false;
			$group->hide_buttons = true;
		}

		if ( $group_user && $group_user->status ) {
			$group->user_status = $group_user->status;
		}
	}
	$members_arr   = $db->get_users_by_status( $group_id );
	$count_members = count( $members_arr );

	$group->members_count = $count_members;

	return $group;
}

function wpumgrp_is_user_group_member( $group_id, $user_id ) {
	$group_id = apply_filters( 'wpum_group_id', $group_id );
	$db = new WPUMG_DB_Group_Users;

	return $db->is_group_member( $group_id, $user_id );
}

function wpumgrp_get_user_group_ids( $user_id ) {
	$db     = new WPUMG_DB_Group_Users;
	$groups = $db->get_groups_by( $user_id );

	if ( empty( $groups ) ) {
		return array();
	}

	return wp_list_pluck( $groups, 'group_id' );
}

function wpumgrp_group_user_has_role( $group_id, $user_id, $roles ) {
	$group_id = apply_filters( 'wpum_group_id', $group_id );
	$db         = new WPUMG_DB_Group_Users();
	$user_roles = $db->get_user_roles( $group_id, $user_id );

	if ( ! is_array( $roles ) ) {
		$roles = array( $roles );
	}

	return array_intersect( $user_roles, $roles );
}

function wpumgp_get_groups($privacy = array( 'public', 'private' ) ) {
	$args = apply_filters( 'wpum_get_groups_args', [
		'post_type'   => 'wpum_group',
		'post_status' => 'publish',
		'limit'       => - 1,
	] );

	$meta_query = array();
	if ( in_array( 'public', $privacy ) ) {
		$meta_query[] = array(
			'key'   => '_group_privacy_method',
			'value' => 'public',
		);
	} else {
		$meta_query[] = array(
			'key'     => '_group_privacy_method',
			'value'   => 'public',
			'compare' => '!=',
		);
	}
	if ( in_array( 'private', $privacy ) ) {
		$meta_query[] = array(
			'key'   => '_group_privacy_method',
			'value' => 'private',
		);
	} else {
		$meta_query[] = array(
			'key'     => '_group_privacy_method',
			'value'   => 'private',
			'compare' => '!=',
		);
	}
	if ( in_array( 'hidden', $privacy ) ) {
		$meta_query[] = array(
			'key'   => '_group_privacy_method',
			'value' => 'hidden',
		);
	} else {
		$meta_query[] = array(
			'key'     => '_group_privacy_method',
			'value'   => 'hidden',
			'compare' => '!=',
		);
	}

	$args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $meta_query );

	return new WP_Query( $args );
}

function wpumgp_get_user_groups( $user_id, $privacy = array( 'public', 'private' ) ) {
	if ( ! $user_id ) {
		return false;
	}

	$group_ids = wpumgrp_get_user_group_ids( $user_id );

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = apply_filters( 'wpum_get_groups_for_profile', [
			'post_type' => 'wpum_group',
			'post_status' => 'publish',
			'paged'     => $paged,
			'post__in'  => $group_ids,
		] );

	$meta_query = array();
	if ( in_array( 'public', $privacy ) ) {
		$meta_query[] = array(
			'key'   => '_group_privacy_method',
			'value' => 'public',
		);
	} else {
		$meta_query[] = array(
			'key'       => '_group_privacy_method',
			'value'     => 'public',
			'compare'   => '!=',
		);
	}
	if ( in_array( 'private', $privacy ) ) {
		$meta_query[] = array(
			'key'   => '_group_privacy_method',
			'value' => 'private',
		);
	} else {
		$meta_query[] = array(
			'key'       => '_group_privacy_method',
			'value'     => 'private',
			'compare'   => '!=',
		);
	}
	if ( in_array( 'hidden', $privacy ) ) {
		$meta_query[] = array(
			'key'   => '_group_privacy_method',
			'value' => 'hidden',
		);
	} else {
		$meta_query[] = array(
			'key'       => '_group_privacy_method',
			'value'     => 'hidden',
			'compare'   => '!=',
		);
	}

	$args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $meta_query );

	return new WP_Query( $args );
}

/**
 * Retrieve a group's members.
 *
 * @param int  $group_id
 *
 * @param null $status
 *
 * @return array
 */
function wpumgp_get_group_members( $group_id, $status = null ) {
	$group_id = apply_filters( 'wpum_group_id', $group_id );
	$db = new WPUMG_DB_Group_Users();

	return $db->get_users_by( $group_id, $status );
}

/**
 * Leave group
 */
 function wpumgp_handle_leave_group() {
	 if ( isset( $_POST['wpumg-group-leave'] ) && isset( $_POST['wpumg-group-id'] ) && isset( $_POST['wpumg-user-id'] ) && isset( $_POST['_wpnonce'] ) ) {
		 if ( wp_verify_nonce( $_POST['_wpnonce'], 'group_participation_action' ) ) {

			 $group_id = $_POST['wpumg-group-id'];
			 $user_id = $_POST['wpumg-user-id'];

			 do_action ( 'wpumgp_user_leave_group', $group_id, $user_id );

			 $redirect = get_permalink( $group_id );
			 $redirect = add_query_arg(
				 [
					 'left' => 'success',
				 ],
				 $redirect
			 );

			 do_action( 'wpumgp_after_member_leave', $group_id, $user_id );

			 wp_safe_redirect( $redirect );
			 exit;

		 }
	 }
 }

add_action( 'wpumgp_user_leave_group', 'wpumgp_leave_group', 10, 2 );

 function wpumgp_leave_group( $group_id, $user_id ) {
	 $group_id = apply_filters( 'wpum_group_id', $group_id );
	 $db = new WPUMG_DB_Group_Users;
	 $get_row_id = $db->get_row_by( $group_id, $user_id );

	 $db->delete( $get_row_id );

	 do_action( 'wpumgp_after_member_leave', $group_id, $user_id );
 }

/**
* Join group
*/
function wpumgp_handle_join_group() {

	 if ( isset( $_POST['wpumg-group-join'] ) && isset( $_POST['wpumg-group-id'] ) && isset( $_POST['wpumg-user-id'] ) && isset( $_POST['_wpnonce'] ) ) {
		 if ( wp_verify_nonce( $_POST['_wpnonce'], 'group_participation_action' ) ) {

			 $group_id = $_POST['wpumg-group-id'];
			 $user_id = $_POST['wpumg-user-id'];

			 do_action( 'wpumgp_user_join_group', $group_id, $user_id, NULL );

			 $privacy_method = get_post_meta( $group_id, '_group_privacy_method', true );

			 $redirect = get_permalink( $group_id );
			 $redirect = add_query_arg(
				 [
					 'joined' => 'public' === $privacy_method ? 'success' : 'pending',
				 ],
				 $redirect
			 );

			 wp_safe_redirect( $redirect );
			 exit;

		 }
	 }
}

add_action( 'wpumgp_user_join_group', 'wpumgp_join_group', 10, 3 );

function wpumgp_join_group( $group_id, $user_id, $group_status = null ) {
	// If user is already assigned to the group then return.
	$already_member = wpumgrp_is_user_group_member( $group_id, $user_id );
	if( !empty($already_member) ){
		return false;
	}
	$group_id = apply_filters( 'wpum_group_id', $group_id );
	$user_meta  = get_userdata( $user_id );
	$user_roles = $user_meta->roles;

	$new_members_role = array( 'wpum_group_member' );
	$user_roles = array_filter( $user_roles, function( $v ) { return ( $v != 'wpum_group_member' && $v != 'wpum_group_moderator' && $v != 'wpum_group_admin' ); } );
	$user_roles = array_values( array_unique( array_merge( $user_roles, $new_members_role ) ) );

	$privacy_method = get_post_meta( $group_id, '_group_privacy_method', true );
	$status = 'pending';
	if ( 'public' === $privacy_method ) {
		$status = 'approved';
	}

	if( !empty($group_status) ){
		$status = $group_status;
	}
	$data = array(
		'group_id'  => $group_id,
		'user_id'   => $user_id,
		'role'      => maybe_serialize( $user_roles ),
		'joined_at' => current_time( 'mysql' ),
		'status'    => $status,
	);

	$db = new WPUMG_DB_Group_Users;
	$type = 'wpumg_user_to_group';

	$db->insert( $data, $type );

	do_action( 'wpumgp_after_member_join', $group_id, $user_id, $privacy_method  );

	return $privacy_method;
}

/**
 * @param int $group_id
 * @param int $user_id
 * @param bool $is_member
 * @param bool $hide_buttons
 */
function wpumgp_group_member_actions( $group_id, $user_id, $is_member, $hide_buttons ) {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$singular = WPUM_Group_Editor::singular()
	?>

	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" name="wpumg-group-leave">
		<?php wp_nonce_field( 'group_participation_action', '_wpnonce', false, true ); ?>
		<input type="hidden" name="wpumg-group-id" value="<?php echo intval( $group_id ); ?>"/>
		<input type="hidden" name="wpumg-user-id" value="<?php echo $user_id; ?>"/>
		<?php if ( false == $is_member && ( ! isset( $hide_buttons ) || ! $hide_buttons ) ) : ?>
			<input type="submit" id="wpumg-group-join" name="wpumg-group-join"
			       value="<?php echo esc_html( sprintf( __( 'Join %s', 'wpum-groups' ), $singular ) ); ?>"/>
		<?php elseif ( false != $is_member && ( ! isset( $hide_buttons ) || ! $hide_buttons ) ) : ?>
			<input type="submit" id="wpumg-group-leave" name="wpumg-group-leave"
			       value="<?php echo esc_html( sprintf( __( 'Leave %s', 'wpum-groups' ), $singular ) ); ?>"/>
		<?php endif; ?>
	</form>
	<?php
}

/**
 * Retrieve the list of categories for the sort by dropdown on the user group.
 *
 * @param bool $add
 *
 * @return array
 */
function wpumgp_get_group_categories( $add = true  ) {

	if ( $add ) {
		$options = [
			'all' => esc_html__( 'All Categories', 'wpum-groups' ),
		];
	} else {
		$options = [
			'' => esc_html__( 'Select Category', 'wpum-groups' ),
		];
	}

	$terms = get_terms([
	    'taxonomy'		=> 'wpum_group_cat',
	    'hide_empty'	=> false,
	]);

	foreach ( $terms as $term ) {
		$options[$term->slug] = $term->name;
	}

	return apply_filters( 'wpumgp_get_category_list', $options );

}


/**
 * Retrieve the list of tags for the sort by dropdown on the user group.
 *
 * @return array
 */
function wpumgp_get_group_tags() {

	$options = [
		'all'	=> esc_html__( 'All Tags', 'wpum-groups' ),
	];

	$terms = get_terms([
		'taxonomy'		=> 'wpum_group_tag',
		'hide_empty'	=> false,
	]);

	foreach ( $terms as $term ) {
		$options[$term->slug] = $term->name;
	}

	return apply_filters( 'wpumgp_get_tag_list', $options );

}

/**
 * Display pagination for group directory.
 *
 * @param object $data
 * @return void
 */
function wpumgp_group_directory_pagination( $data ) {

	echo '<div class="wpum-group-pagination">';

	$big          = 9999999;
	$search_for   = array( $big, '#038;' );
	$replace_with = array( '%#%', '&' );

	echo paginate_links( array(
			'base'      => str_replace( $search_for, $replace_with, esc_url( get_pagenum_link( $big ) ) ),
			'current'   => $data->paged,
			'total'     => $data->total_pages,
			'prev_text' => __( 'Previous page', 'wpum-groups' ),
			'next_text' => __( 'Next page', 'wpum-groups' )
		) );

	echo '</div>';

}

/**
 * Retrieve the URL of a group tab.
 *
 * @param \WP_Post $group
 * @param string   $tab
 *
 * @return string
 */
function wpum_get_group_tab_url( $group, $tab ) {
	$url = trailingslashit( get_permalink( $group ) );
	$url .= $tab;

	return apply_filters( 'wpum_get_group_tab_url', $url, $tab, $group );
}

/**
 * Retrieve the currently active group tab.
 * If no profile tab is found active, automatically set the first found tab as active.
 *
 * @param int $group_id
 *
 * @return string
 */
function wpum_get_active_group_tab( $group_id ) {
	$first_tab = key( wpum_get_registered_group_tabs( $group_id ) );

	return get_query_var( 'tab', $first_tab );
}

/**
 * @param int $group_id
 *
 * @return array
 */
function wpum_get_registered_group_tabs( $group_id ) {
	$tabs = [
		'about'   => [
			'name'     => esc_html__( 'About', 'wpum-groups' ),
			'priority' => 0,
		],
		'members' => [
			'name'     => esc_html__( 'Members', 'wpum-groups' ),
			'priority' => 1,
		],
	];

	if ( is_user_logged_in() ) {
		$db    = new WPUMG_DB_Group_Users();
		$roles = $db->get_user_roles( apply_filters( 'wpum_group_id', $group_id ), get_current_user_id() );

		if ( array_intersect( $roles, array(
			'wpum_group_moderator',
			'wpum_group_admin',
		) ) ) {
			$tabs['moderation'] = [
				'name'     => esc_html__( 'Moderation', 'wpum-groups' ),
				'priority' => 2,
			];
		}

		if ( array_intersect( $roles, array(
			'wpum_group_admin',
		) ) ) {
			$tabs['tools'] = [
				'name'     => esc_html__( 'Tools', 'wpum-groups' ),
				'priority' => 3,
			];
		}
	}

	$tabs = apply_filters( 'wpum_get_registered_group_tabs', $tabs, $group_id );

	uasort( $tabs, 'wpum_sort_array_by_priority' );

	return $tabs;
}


function wpum_get_group_page_content() {
	global $post;

	$tab = wpum_get_active_group_tab( $post->ID );

	$db      = new WPUMG_DB_Group_Users;
	$user_id = get_current_user_id();

	$group = wpumgrp_prepare_group( $post, $user_id, $db );

	$roles = array();
	if ( $user_id ) {
		$roles = $db->get_user_roles( apply_filters( 'wpum_group_id', $post->ID ), $user_id );
	}

	if ( 'edit' === $tab && in_array( 'wpum_group_admin', $roles ) ){
		echo WPUM()->forms->get_form( 'group' );
		return;
	}

	if ( isset( $_GET['joined'] ) && $_GET['joined'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( sprintf( __( 'Successfully joined %s.', 'wpum-groups' ), WPUM_Group_Editor::singular() ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	if ( ( isset( $_GET['joined'] ) && $_GET['joined'] == 'pending' ) || $group->user_status === 'pending' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( sprintf( __( 'Your membership is currently pending approval', 'wpum-groups' ), WPUM_Group_Editor::singular() ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	if ( isset( $_GET['left'] ) && $_GET['left'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( sprintf( __( 'Successfully left %s.', 'wpum-groups' ), WPUM_Group_Editor::singular() ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( sprintf( __( '%s updated successfully.', 'wpum-groups' ), WPUM_Group_Editor::singular() ) ) ] )
			->get_template_part( 'messages/general', 'success' );
	}

	if ( isset( $_GET['approved'] ) && $_GET['approved'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( __( 'Member approved successfully.', 'wpum-groups' ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	if ( isset( $_GET['rejected'] ) && $_GET['rejected'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( __( 'Member rejected successfully.', 'wpum-groups' ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	if ( isset( $_GET['removed'] ) && $_GET['removed'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( __( 'Member '.$_GET['user_name'].' successfully removed.', 'wpum-groups' ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	if ( isset( $_GET['removed'] ) && $_GET['removed'] == 'error' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( __( 'Invalid permission.', 'wpum-groups' ) ) ] )
		                 ->get_template_part( 'messages/general', 'warning' );
	}

	if ( isset( $_GET['created'] ) && $_GET['created'] == 'success' ) {
		WPUM()->templates->set_template_data( [ 'message' => esc_html( sprintf( __( '%s created successfully.', 'wpum-groups' ), WPUM_Group_Editor::singular() ) ) ] )
		                 ->get_template_part( 'messages/general', 'success' );
	}

	WPUM()->templates->set_template_data( [
		'group'   => $group,
		'current_user_id' => $user_id,
		'roles' => $roles
	] )->get_template_part( 'group' );
}

function wpumgr_approve_group_member( $group_id, $user_id ) {
	$group_id = apply_filters( 'wpum_group_id', $group_id );
	$db = new WPUMG_DB_Group_Users();
	$row_id = $db->get_row_by( $group_id, $user_id );

	$data = array( 'status' => 'approved' );
	$db->update( (int) $row_id, $data, 'id' );

	$moderator_id = get_current_user_id();
	update_user_meta( $user_id, 'wpum_group_approved_by_' . $group_id, $moderator_id );
	update_user_meta( $user_id, 'wpum_group_approved_on_' . $group_id, (string) strtotime( date( 'Y-m-d', time() ) ) );

	do_action( 'wpumgp_after_membership_approved', $group_id, $user_id  );
}

function wpumgr_reject_group_member( $group_id, $user_id ) {
	$group_id = apply_filters( 'wpum_group_id', $group_id );
	$db = new WPUMG_DB_Group_Users();
	$row_id = $db->get_row_by( $group_id, $user_id );

	$data = array( 'status' => 'rejected' );
	$db->update( (int) $row_id, $data, 'id' );

	$moderator_id = get_current_user_id();
	update_user_meta( $user_id, 'wpum_group_rejected_by_' . $group_id, $moderator_id );
	update_user_meta( $user_id, 'wpum_group_rejected_on_' . $group_id, (string) strtotime( date( 'Y-m-d', time() ) ) );

	do_action( 'wpumgp_after_membership_rejected', $group_id, $user_id  );
}

function wpumgp_get_action_member_url( $action, $group_id, $user_id ) {
	return wp_nonce_url( get_permalink( $group_id ) . 'moderation?wpum-action=' . $action . '_group_user&group_id=' . $group_id . '&user_id=' . $user_id, $action . '_group_user' );
}

function wpumgp_get_remove_member_url( $action, $group_id, $user_id ) {
	return wp_nonce_url( get_permalink( $group_id ) . 'members?wpum-action=' . $action . '_group_user&group_id=' . $group_id . '&user_id=' . $user_id, $action . '_group_user' );
}

function wpumgp_get_reject_member_url( $group_id, $user_id ) {
	return wp_nonce_url( get_permalink( $group_id ) . 'moderation?wpum-action=reject_group_user&group_id=' . $group_id . '&user_id=' . $user_id, 'reject_group_user' );
}
function wpum_clean( $var ) {
	if ( is_array( $var ) ) {
		 return array_map( 'wpum_clean', $var );
	}else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

add_action( 'wpum_export_group_members', 'wpum_export_group_members', 10 );
function wpum_export_group_members( $group_id ){

	$group_id     = apply_filters( 'wpum_group_id', esc_html( $group_id ) );
	$group        = get_post( $group_id );
	$members      = wpumgp_get_group_members( $group_id );
	$members_list = array();
	$headers      = array(
		'Email',
		'Firstname',
		'Lastname',
	);

	$headers = apply_filters( 'wpum_prepare_csv_header', $headers, $group_id );

	foreach ( $members as $member ) {
		$member_data = get_userdata( $member->user_id );

		$data          = array(
			$member_data->user_email,
			$member_data->first_name,
			$member_data->last_name,
		);
		$filtered_data = apply_filters( 'wpum_prepare_member_data', $data, $member_data, $member->user_id, $group_id );

		$members_list[] = $filtered_data;
	}

	$members_list = apply_filters( 'wpum_group_prepare_csv_members', $members_list, $members, $group_id );
	header( 'Content-Type: text/csv' );
	$filename = apply_filters( 'wpum_group_csv_filename', 'group-members-' . sanitize_file_name( $group->post_name ), $group_id, $group );
	header( 'Content-Disposition: attachment;filename=' . $filename . '.csv' );
	$fp = fopen( 'php://output', 'w' );

	fputcsv( $fp, $headers );

	foreach ( $members_list as $member ) {
		fputcsv( $fp, $member );
	}
	fclose( $fp );
	exit;
}

function wpum_group_display_user( $group_id, $user_id ) {
	$show = true;
	if ( ! is_user_logged_in() && ! wpum_guests_can_view_profiles( $user_id ) ) {
		$show = false;
	}

	if ( is_user_logged_in() && ! wpum_members_can_view_profiles( $user_id ) ) {
		$show = false;
	}

	if ( $user_id === get_current_user_id() ) {
		$show = true;
	}

	return apply_filters( 'wpum_group_display_user', $show, $user_id, $group_id );
}

