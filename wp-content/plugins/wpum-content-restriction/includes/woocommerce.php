<?php
/**
 * WooCommercr Integration

 */

use Carbon_Fields\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

add_filter( 'wpumcr_restriction_meta_fields', 'wpum_woocommerce_content_restriction' );

function wpum_woocommerce_content_restriction( $fields ) {
	$args     = array( 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'published' );
	$products = get_posts( $args );
	$products = wp_list_pluck( $products, 'post_title', 'ID' );

	$new_fields   = array();
	$new_fields[] = Field::make( 'multiselect', 'woocommerce_products', esc_html__( 'Restriction by Products Purchased', 'wpum-groups' ) )
	                     ->add_options( $products )
	                     ->set_classes( 'wpumcr-condition-type wpumcr-match_in wpumcr-hide' )
	                     ->set_help_text( esc_html__( 'Only show content for users who have purchased these WooCommerce products.', 'wp-user-manager' ) );

	return array_merge( array_slice( $fields, 0, 2, true ), $new_fields, array_slice( $fields, 2, null, true ) );
}

add_filter( 'wpumcr_post_restriction', 'wpum_woocomerce_post_restriction', 10, 2 );

function wpum_woocomerce_post_restriction( $is_restricted, $post_id ) {
	$allowed_products = carbon_get_post_meta( $post_id, 'woocommerce_products', 'carbon_fields_container_wpum_content_restriction' );

	if ( empty( $allowed_products ) ) {
		return $is_restricted;
	}

	$current_user = wp_get_current_user();

	$is_allowed = false;
	foreach ( $allowed_products as $allowed_product ) {
		if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, $allowed_product ) ) {
			$is_allowed = true;
		} else {
			return true;
		}
	}

	return ! $is_allowed;
}
