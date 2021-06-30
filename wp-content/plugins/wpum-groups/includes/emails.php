<?php
/**
 * Register new emails for the emails editor.
 *
 * @package     wpum-groups
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumgp_default_emails() {
	$default_emails = array();

	$default_emails['group-approval'] = [
		'subject' => 'Your {group} membership has been approved',
		'title'   => '{group}',
		'content' => '<p>Hello {username},</p> <p>You are now a member of {group}, your membership has now been approved.</p>',
	];

	$default_emails['group-rejected'] = [
		'subject' => 'Your {group} membership has been rejected',
		'title'   => '{group}',
		'content' => '<p>Hello {username},</p> <p>Unfortunately, your membership to {group} has been rejected.</p>',
	];

	$default_emails['pending-member'] = [
		'subject' => 'A new pending member of {group} needs approval',
		'title'   => '{group}',
		'content' => '<p>Hello,</p> <p>A new member of {group} needs your approval.</p><p><strong>{group_member_email}</strong> <p><a href="{group_approval_url}">Approve</a></p>',
	];

	return $default_emails;
}

/**
 * Register new emails within the editor.
 *
 * @param array $emails
 *
 * @return array
 */
function wpumgp_register_emails( $emails ) {
	$singular = WPUM_Group_Editor::singular();

	$emails['group-approval'] = [
		'status'      => 'manual',
		'name'        => sprintf( esc_html__( '%s membership approved', 'wpum-groups' ), $singular ),
		'description' => sprintf( esc_html__( 'The email sent to the user when their membership to a %s is approved.', 'wpum-groups' ), strtolower( $singular ) ),
		'recipient'   => esc_html__( 'Email address of the user.', 'wpum-groups' ),
	];

	$emails['group-rejected'] = [
		'status'      => 'manual',
		'name'        => sprintf( esc_html__( '%s membership rejected', 'wpum-groups' ), $singular ),
		'description' => sprintf( esc_html__( 'The email sent to the user when their membership to a %s is rejected.', 'wpum-groups' ), strtolower( $singular ) ),
		'recipient'   => esc_html__( 'Email address of the user.', 'wpum-user-verification' ),
	];

	$emails['pending-member'] = [
		'status'      => 'manual',
		'name'        => sprintf( esc_html__( 'New %s pending member', 'wpum-groups' ), $singular ),
		'description' => sprintf( esc_html__( 'The email sent %s admins and moderators when a member joins and needs to be approved.', 'wpum-groups' ), strtolower( $singular ) ),
		'recipient'   => esc_html__( 'Email addresses of the group moderators.', 'wpum-groups' ),
	];

	return $emails;
}

add_filter( 'wpum_registered_emails', 'wpumgp_register_emails' );

/**
 * Register the group email tags.
 *
 * @param array $tags
 *
 * @return array
 */
function wpumgp_add_new_group_tags( $tags ) {
	$tags[] = [
		'name'        => esc_html__( 'Group Name', 'wpum-groups' ),
		'description' => esc_html__( 'Display the group name.', 'wpum-groups' ),
		'tag'         => 'group',
		'function'    => 'wpumgp_email_tag_group_name',
	];

	$tags[] = [
		'name'        => esc_html__( 'Group Member Email', 'wpum-groups' ),
		'description' => esc_html__( 'Email address of member who joined a group.', 'wpum-groups' ),
		'tag'         => 'group_member_email',
		'function'    => 'wpumgp_email_tag_group_member_email',
	];

	$tags[] = [
		'name'        => esc_html__( 'Group Approval URL', 'wpum-groups' ),
		'description' => esc_html__( 'URL to approve a pending group member.', 'wpum-groups' ),
		'tag'         => 'group_approval_url',
		'function'    => 'wpumgp_email_tag_group_approval_url',
	];

	return $tags;

}
add_filter( 'wpum_email_tags', 'wpumgp_add_new_group_tags' );

/**
 * @param int         $user_id
 * @param string      $password_reset_key
 * @param string      $plain_text_password
 * @param string      $tag
 * @param WPUM_Emails $email
 *
 * @return string
 */
function wpumgp_email_tag_group_name( $user_id, $password_reset_key, $plain_text_password, $tag, $email ) {
	return get_the_title( $email->group_id );
}

/**
 * @param int $user_id
 *
 * @return string
 */
function wpumgp_email_tag_group_member_email( $user_id ) {
	return get_user_by( 'id', $user_id )->user_email;
}

/**
 * @param int         $user_id
 * @param string      $password_reset_key
 * @param string      $plain_text_password
 * @param string      $tag
 * @param WPUM_Emails $email
 *
 * @return string
 */
function wpumgp_email_tag_group_approval_url( $user_id, $password_reset_key, $plain_text_password, $tag, $email ) {
	return get_permalink( $email->group_id ) . 'moderation';
}

/**
 * Send an email to the user after the group membership has been approved.
 *
 * @param     $group_id
 * @param int $user_id
 *
 * @return void
 */
function wpumgp_send_email_after_membership_approval( $group_id, $user_id ) {
	wpumgp_send_email( 'group-approval', $group_id, $user_id );
}

add_action( 'wpumgp_after_membership_approved', 'wpumgp_send_email_after_membership_approval', 10, 2 );

/**
 * Send an email to the user after the group membership has been rejected.
 *
 * @param     $group_id
 * @param int $user_id
 *
 * @return void
 */
function wpumgp_send_email_after_after_membership_rejected( $group_id, $user_id ) {
	wpumgp_send_email( 'group-rejected', $group_id, $user_id );
}

add_action( 'wpumgp_after_membership_rejected', 'wpumgp_send_email_after_after_membership_rejected', 10, 2 );

/**
 * Send an email to the group moderators when a pending member needs approval
 *
 * @param int    $group_id
 * @param int    $user_id
 * @param string $group_privacy
 *
 * @return void
 */
function wpumgp_send_email_after_membership_pending_signup( $group_id, $user_id, $group_privacy ) {
	if ( 'private' === $group_privacy && apply_filters( 'wpum_group_admin_email_pending_member', true ) ) {
		$db = new WPUMG_DB_Group_Users;
		$moderators = $db->get_users_by_role( $group_id, array( 'wpum_group_moderator', 'wpum_group_admin' ) );
		$emails = wp_list_pluck( $moderators, 'user_email' );

		if ( empty( $emails ) ) {
			return;
		}

		wpumgp_send_email( 'pending-member', $group_id, $user_id, $emails );
	}
}

add_action( 'wpumgp_after_member_join', 'wpumgp_send_email_after_membership_pending_signup', 10, 3 );

/**
 * @param string $email
 * @param int   $group_id
 * @param int   $user_id
 * @param null|array"string $to
 */
function wpumgp_send_email( $email, $group_id, $user_id, $to = null ) {
	$email = wpum_get_email( $email, $user_id );

	if( ! $user_id || ! $group_id  ) {
		return;
	}

	if ( is_array( $email ) && ! empty( $email ) ) {

		$user = get_user_by( 'id', $user_id );

		$emails = new WPUM_Emails;
		$emails->__set( 'user_id', $user_id );
		$emails->__set( 'group_id', $group_id );
		$emails->__set( 'heading', $email['title'] );

		if ( empty( $to ) ) {
			$to = $user->data->user_email;
		}
		$emails->send( $to, $email['subject'], $email['content'] );
	}
}
