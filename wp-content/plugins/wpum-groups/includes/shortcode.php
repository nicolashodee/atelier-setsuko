<?php
/**
 * Register all the shortcodes for WPUM-group.
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The shortcode to display the directory.
 *
 * @param array $atts
 * @param null  $content
 *
 * @return void
 */
function wpum_group_directory( $atts, $content = null ) {

	$atts = shortcode_atts(
			array(
				'per_page' => 10,
				'has_search_form' => 'true',
				'show_public' => 'true',
				'show_private' => 'true',
			),
			$atts,
			'wpum_group_directory'
		);

	ob_start();

	$db = new WPUMG_DB_Group_Users;
	$user_id = get_current_user_id();

	// Modify the number argument if changed from the search form.
	$posts_per_page = ( isset( $atts['per_page'] ) ? $atts['per_page'] : 10 );

	// Prepare query arguments.
	$args = [
		'post_type' => 'wpum_group',
		'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
	];

	// Update pagination and offset users.
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	if ( $paged == 1 ) {
		$offset = 0;
	} else {
		$offset = ( $paged - 1 ) * $posts_per_page;
	}

	// Set sort by method if any specified from the search form.
	$sort_by_default = '';
	$sortby = false;
	if ( isset( $_GET['searchby'] ) && ! empty( $_GET['searchby'] ) ) {
		$sortby = esc_attr( $_GET['searchby'] );
	}

	// Setup search if anything specified.
	$tax_query = array();
	if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
		if (wp_verify_nonce( sanitize_text_field( esc_attr( trim( $_GET['_wpnonce'] ) ) ), 'group_search_action' )) {

			if ( isset( $_GET['group-search'] ) && ! empty( $_GET['group-search'] ) ) {
				$search_string  = sanitize_text_field( trim( $_GET['group-search'] ) );
				$args['s']      = esc_attr( $search_string );
			}

			if ( isset( $_GET['category'] ) && ! empty( $_GET['category'] ) && esc_attr($_GET['category']) != 'all' ) {
				$cat_string  = sanitize_text_field( trim( $_GET['category'] ) );
				$tax_query[] = array(
					'tax_query' => array(
					        array(
					            'taxonomy' => 'category',
					            'field'    => 'slug',
					            'terms'    => esc_attr($cat_string),
					        ),
					    ),
				);
			}

			if ( isset( $_GET['tag'] ) && ! empty( $_GET['tag'] )  && esc_attr($_GET['tag']) != 'all') {
				$tag_string  = sanitize_text_field( trim( $_GET['tag'] ) );
				$tax_query[] = array(
					'tax_query' => array(
					        array(
					            'taxonomy' => 'post_tag',
					            'field'    => 'slug',
					            'terms'    => esc_attr($tag_string),
					        ),
					    ),
				);
			}

			if ( count( $tax_query ) > 1 ) {
				$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query );
			} elseif ( count( $tax_query ) == 1 ) {
				$args['tax_query'] = $tax_query;
			}
		}
	}

	$meta_query = array();
	if ( $atts['show_public'] == 'true' ) {
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
	if ( $atts['show_private'] == 'true' ) {
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

	$args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $meta_query );

	$args['offset'] = $offset;
	$args           = apply_filters( 'wpum_group_search_query_args', $args );

	$post_query     = new WP_Query( $args );
	$all_posts      = $post_query->posts;
	$total_posts    = $post_query->found_posts;
	$total_pages    = ceil( $total_posts / $posts_per_page );

	$count_user_groups = 0;
	$groups = $db->get_groups_by( $user_id );
	if ( ! empty( $groups ) && is_array( $groups ) ) {
		$count_user_groups = count( $groups );
	}

	if ( isset( $_GET['show-my-groups'] ) ) {
		foreach( $all_posts as $key => $post ) {
			if( ! in_array( $post->ID, array_column( $groups, 'group_id' ) ) ) {
				unset( $all_posts[$key] );
			}
		}
	}

	$new_all_posts_array = array();
	foreach( $all_posts as $count => $item ) {
		$members_arr = $db->get_users_by_status( $item->ID );
		$count_members = count( $members_arr );

		$item->members_count = $count_members;

		$item = wpumgrp_prepare_group( $item, $user_id, $db );

		$new_all_posts_array[] = $item;
	}

	$has_sort_by         = true;
	$has_search_form     = ( isset( $atts['has_search_form'] ) ? $atts['has_search_form'] : true );
	$category_by_default = 'all';
	$tag_by_default      = 'all';
	$directory_template  = 'groups';

	$create_new_form = false;
	if ( is_user_logged_in() ) {
		$create_new_form = wpum_can_user_create_group( $user_id );
	}

	$plural = WPUM_Group_Editor::plural();
	$singular = WPUM_Group_Editor::singular();

	if ( class_exists( 'WPUMG_Template_Loader' ) ) {

		WPUMGP()->templates
			->set_template_data(
				[
					'singular' => $singular,
					'plural' => $plural,
					'create_new_form'     => $create_new_form,
					'has_sort_by'         => $has_sort_by,
					'sort_by_default'     => $sort_by_default,
					'has_search_form'     => $has_search_form,
					'category_by_default' => $category_by_default,
					'tag_by_default'	  => $tag_by_default,
					'results'             => $new_all_posts_array,
					'total'               => $total_posts,
					'template'            => $directory_template,
					'paged'               => $paged,
					'total_pages'         => $total_pages,
					'count_user_groups'   => $count_user_groups,
				]
			)
			->get_template_part( $directory_template );
	}

	return ob_get_clean();
}
add_shortcode( 'wpum_group_directory', 'wpum_group_directory' );
