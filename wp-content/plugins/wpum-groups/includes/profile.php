<?php
/**
 * Register all the profile hooks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function wpumgp_add_profile_tab( $tabs ) {
	$plural = WPUM_Group_Editor::plural();
	$slug   = strtolower( $plural );

	$tabs[ $slug ] = [
		'name'     => esc_html( apply_filters( 'wpum_groups_profile_tab_label', $plural ) ),
		'priority' => 2,
	];

	if ( $slug !== 'groups' ) {
		add_action( 'wpum_profile_page_content_' . $slug, 'wpumgp_add_profile_content' );
	}


	return $tabs;
}

add_filter( 'wpum_get_registered_profile_tabs', 'wpumgp_add_profile_tab' );

function wpumgp_add_profile_content( $data ) {
	WPUM()->templates->set_template_data( [
		'user'            => $data->user,
		'current_user_id' => $data->current_user_id,
	] )->get_template_part( "profiles/groups" );
}
