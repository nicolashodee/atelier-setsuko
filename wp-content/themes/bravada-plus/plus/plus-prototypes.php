<?php
/**
 * Plus prototypes
 *
 * @package Cryout Plus
 */

/**
 * Retrieves prettified featured content categories list
 */
function cryout_featured_cats_for_customizer( $how = 0, $what = '', $label_all = '', $label_off = '', $all = TRUE, $off = TRUE ) {
	$categories = array();	$labels = array();
	$cats = get_categories( array( 'taxonomy' => 'cryout-featured-blob-category' ) );

	if ( count( $cats ) > 0 ):
		if ($off) {
			$categories[] = '-1';
			$labels[] = $label_off;
		};
		if ($all) {
			$categories[] = 'blob|*';
			$labels[] = $label_all;
		};
		foreach ($cats as $category) {
			$categories[] = 'blob|'.$category->category_nicename;
			$labels[] = $category->name; // . ' (' . $category->category_count . ')';
		}
	endif;
	switch ($how) {
		case 2: // labels only
			return $labels;
		break;
		case 1: // cats only
			return $categories;
		break;
		case 0: // both
		default:
			if ( !empty( $categories) && !empty($labels) ) return array_combine($categories,$labels);
												     else return array();
		break;
	}
} // cryout_featured_cats_for_customizer()

/**
 * Retrieves prettified featured content list
 */
function cryout_featured_for_customizer( $how = 0, $what = '', $label_off = '', $off = TRUE ) {
	$elems = array(); $labels = array();
    $content = get_posts( $args = array(
		'post_type'            => 'cryout-featured-blob',
		'numberposts'		   => -1, // unset post count limit
		'meta_query'           => array(
			array(
				'key' => 'cryout_blob_type',
				'value' => $what
				)
			)
		)
	);
	if ( count( $content ) > 0 ):
		if ($off) {
			$elems = array( 0 );
			$labels = array( $label_off );
		};
		foreach ($content as $item) {
			$elems[] = $item->ID;
			if (!empty($item->post_parent))  $labels[] = "&nbsp;&ndash;&nbsp;".$item->post_title;
										else $labels[] = $item->post_title;
		}
	endif;
	switch ($how) {
		case 2: // labels only
			return $labels;
		break;
		case 1: // ids only
			return $elems;
		break;
		case 0: // both
		default:
			if ( !empty( $elems) && !empty($labels) ) return array_combine($elems,$labels);
												else return array();
		break;
	}
} // cryout_featured_for_customizer()


function cryout_portfolio_for_customizer( $how = 0, $what = '', $label_all = '', $label_off = '', $all = TRUE, $off = TRUE ) {
	$categories = array();	$labels = array();
	$items = get_categories( array( 'taxonomy' => 'jetpack-portfolio-type' ) );

	if ( ( ! is_wp_error( $items ) ) & count( $items ) > 0 ):
		if ($off) {
			$categories[] = '-';
			$labels[] = $label_off;
		};
		if ($all) {
			$categories[] = '';
			$labels[] = $label_all;
		};
		foreach ($items as $item) {
			$categories[] = $item->category_nicename;
			$labels[] = $item->name; // . ' (' . $category->category_count . ')';
		}
	endif;
	switch ($how) {
		case 2: // labels only
			return $labels;
		break;
		case 1: // cats only
			return $categories;
		break;
		case 0: // both
		default:
			if ( !empty( $categories) && !empty($labels) ) return array_combine($categories,$labels);
												     else return array();
		break;
	}

} // cryout_portfolio_for_customizer()


function cryout_cpt_exists( $current_post_type = NULL ) {

    $all_custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );

    if ( empty ( $all_custom_post_types ) )
		// there are no custom post types
        return FALSE;

    $custom_types      = array_keys( $all_custom_post_types );

    // could not detect current type
    if ( ! $current_post_type )
        return FALSE;

    return in_array( $current_post_type, $custom_types );
} // cryout_cpt_exists()


// FIN