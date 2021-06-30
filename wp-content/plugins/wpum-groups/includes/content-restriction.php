<?php
/**
 * Integration with content restriction addon
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 */

use Carbon_Fields\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wpumcr_restriction_meta_fields', 'wpum_groups_content_restriction' );

function wpum_groups_content_restriction( $fields ) {
	$groups       = wpumgp_get_groups( array( 'public', 'private', 'hidden' ) );
	$groups       = wp_list_pluck( $groups->posts, 'post_title', 'ID' );
	$new_fields   = array();
	$new_fields[] = Field::make( 'multiselect', 'wpum_groups', esc_html__( 'Restriction by Group Membership', 'wpum-groups' ) )
	                     ->add_options( $groups )
	                     ->set_classes( 'wpumcr-condition-type wpumcr-match_in wpumcr-hide' )
	                     ->set_help_text( esc_html__( 'Choose groups for members to get access to restricted content.', 'wp-user-manager' ) );

	$roles = array(
		'wpum_group_member'    => 'Member',
		'wpum_group_moderator' => 'Moderator',
		'wpum_group_admin'     => 'Admin',
	);

	$new_fields[] = Field::make( 'multiselect', 'wpum_groups_roles', esc_html__( 'Group Roles', 'wpum-groups' ) )
	                     ->add_options( $roles )
	                     ->set_classes( 'wpumcr-condition-type wpumcr-match_in wpumcr-hide' )
	                     ->set_help_text( esc_html__( 'Choose group member roles for access to restricted content. Leave blank for any role.', 'wp-user-manager' ) );


	return array_merge( array_slice( $fields, 0, 2, true ), $new_fields, array_slice( $fields, 2, null, true ) );
}

add_filter( 'wpumcr_post_restriction', 'wpum_group_post_restriction', 10, 2 );

function wpum_group_post_restriction( $is_restricted, $post_id ) {
	$allowed_groups      = carbon_get_post_meta( $post_id, 'wpum_groups', 'carbon_fields_container_wpum_content_restriction' );
	$allowed_group_roles = carbon_get_post_meta( $post_id, 'wpum_groups_roles', 'carbon_fields_container_wpum_content_restriction' );

	if ( empty( $allowed_groups ) ) {
		return $is_restricted;
	}

	$user_id = get_current_user_id();

	$is_allowed = false;
	foreach ( $allowed_groups as $allowed_group ) {
		if ( wpumgrp_is_user_group_member( $allowed_group, $user_id ) ) {
			$is_allowed = true;
		} else {
			continue;
		}

		if ( $is_allowed && ! empty( $allowed_group_roles ) ) {
			if ( wpumgrp_group_user_has_role( $allowed_group, $user_id, $allowed_group_roles ) ) {
				return false;
			} else {
				$is_allowed = false;
			}
		}
	}

	return ! $is_allowed;
}
