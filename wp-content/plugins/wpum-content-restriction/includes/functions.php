<?php
/**
 * @package     wpum-content-restriction
 * @copyright   Copyright (c) 2021, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpumcr_get_post_types( $args = array() ) {
	$defaults = array(
		'public' => true
	);

	$args = array_merge( $defaults, $args );

	$all_post_types = get_post_types( apply_filters( 'wpum_post_type_args', $args ), 'objects' );

	$post_types = array();

	foreach ( $all_post_types as $post_type ) {
		$post_types[] = array(
			'value' => $post_type->name,
			'label' => $post_type->label,
		);
	}

	return $post_types;
}
