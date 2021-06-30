<?php
/**
 * Register options for the addon.
 *
 * @package     wpum-group
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register new settings section for the addon.
 *
 * @param array $sections
 *
 * @return array
 */
function wpumg_register_settings_subsection( $sections ) {
	$sections['groups'] = esc_html__( 'Groups', 'wpum-groups' );

	return $sections;

}
add_filter( 'wpum_settings_tabs', 'wpumg_register_settings_subsection' );

/**
 * Register new settings for the addon.
 *
 * @param array $settings
 *
 * @return array
 */
function wpumg_register_settings( $settings ) {

	$settings['groups']['create_groups_roles'] = array(
		'id'      => 'create_groups_roles',
		'name'    => esc_html__( 'Allow users to create Groups', 'wpum-groups' ),
		'desc'    => esc_html__( 'Select the role for users who can create groups.', 'wpum-groups' ),
		'type'    => 'multiselect',
		'multiple' => true,
		'labels'   => array( 'placeholder' => __( 'Select roles', 'wpum-groups' ) ),
		'std'     => 'administrator',
		'options'  => wpum_get_roles(),
	);

	$settings['groups']['group_singular'] = array(
		'id'      => 'group_singular',
		'name'    => esc_html__( 'Group Singular Name', 'wpum-groups' ),
		'desc'    => esc_html__( 'Change the singular term used throughout the site.', 'wpum-groups' ),
		'type'    => 'text',
		'labels'   => array( 'placeholder' => __( 'Group', 'wpum-groups' ) ),
		'std'     => 'Group',
	);

	$settings['groups']['group_plural'] = array(
		'id'      => 'group_plural',
		'name'    => esc_html__( 'Group Plural Name', 'wpum-groups' ),
		'desc'    => esc_html__( 'Change the plural term used throughout the site.', 'wpum-groups' ),
		'type'    => 'text',
		'labels'   => array( 'placeholder' => __( 'Groups', 'wpum-groups' ) ),
		'std'     => 'Groups',
	);


	return $settings;
}
add_action( 'wpum_registered_settings', 'wpumg_register_settings' );
