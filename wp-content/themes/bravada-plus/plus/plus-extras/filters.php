<?php
/*
 * Filter functions - hooked by constructor() in main Plus class
 *
 * @package Cryout Plus
 */

class Cryout_Plus_Filters {

	function __contruct() {
		// nothing happens here
	} // __contruct()

	function filters_init(){

		$options = cryout_get_option();

		add_filter( 'body_class', array( $this, 'body_classes' ), 20 );
		add_filter( _CRYOUT_THEME_SLUG . '_general_layout', array( $this, 'layout_override' ), 20 );

		// header image filter handling
		add_filter( _CRYOUT_THEME_SLUG . '_header_image_url', array( $this, 'override_header_image' ), 10 );

		// custom post type handler templates
		add_filter( 'single_template', array( $this, 'single_templates' ) );
		add_filter( 'archive_template', array( $this, 'archive_templates' ) );
		
		// custom woocommerce breadcrumb file path
		add_filter( 'wc_get_template', array( $this, 'woocommerce_breadcrumb_template'), 10, 5 );
		
		// landing page filters
		add_filter( _CRYOUT_THEME_SLUG . '_blocks_ids', array( $this, 'override_blocks_ids' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_blocks_icons', array( $this, 'override_blocks_icons' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_blocks_perrow', array( $this, 'override_blocks_perrow' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_block_url', array( $this, 'override_meta_url' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_box_url', array( $this, 'override_meta_url' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_block_target', array( $this, 'override_meta_target' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_box_target', array( $this, 'override_meta_target' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_block_title', array( $this, 'override_meta_hidetitle' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_box_title', array( $this, 'override_meta_hidetitle' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_text_title', array( $this, 'override_meta_hidetitle' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_text_class', array( $this, 'override_meta_style' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_boxes_query_args', array( $this, 'override_boxes_query_args' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_landingpage_main_template', array( $this, 'override_landing_page_template_filename' ), 10, 2 );
		add_filter( _CRYOUT_THEME_SLUG . '_lppostslayout_filter', array( $this, 'override_lppostslayout' ), 10, 2 ); // this filter is used in multiple locations !
		add_filter( _CRYOUT_THEME_SLUG . '_js_options', array( $this, 'override_js_options' ), 10, 2 );

		// landing page main query filter
		add_filter( 'cryout_landingpage_indexquery', array( $this, 'landingpage_index_query'), -1 );

		// custom sanitization for unfiltered js fields
		add_filter( 'cryout_customizer_custom_control_sanitize_callback', array( $this, 'custom_js_sanitization' ), 10, 2 );

		// breadcrumbs exclusion filter
		add_filter( 'cryout_breadcrumbs_excluded_templates', array( $this, 'breadcrumbs_excluded_templates' ) );

		add_image_size( _CRYOUT_THEME_SLUG . '-lpbox-3', $options[_CRYOUT_THEME_PREFIX . '_lpboxwidth3'], $options[_CRYOUT_THEME_PREFIX . '_lpboxheight3'], true );

		// a separate 'square' forced crop for those special edge cases
		add_image_size( _CRYOUT_THEME_SLUG . '-featured-square',
			apply_filters( _CRYOUT_THEME_SLUG . '_featured_image_square_width', 512 ),
			apply_filters( _CRYOUT_THEME_SLUG . '_featured_image_square_height', 512 ),
			array( 'center', 'center' )
		);

		// another, bigger 'square' forced crop for portfolio items
		add_image_size( _CRYOUT_THEME_SLUG . '-featured-square-large',
			apply_filters( _CRYOUT_THEME_SLUG . '_featured_image_square_width', 1024 ),
			apply_filters( _CRYOUT_THEME_SLUG . '_featured_image_square_height', 1024 ),
			array( 'center', 'center' )
		);

		// enable excerpts for pages
		if ( 1 == $options[_CRYOUT_THEME_PREFIX . '_pageexcerpts'] ) {
			add_post_type_support( 'page', 'excerpt' );
		}

	} // filters_init()

	// filters needed on after_setup_theme for correct order
	function filters_after_setup_theme() {
		add_filter( _CRYOUT_THEME_SLUG . '_lppostslayout_filter', array( $this, 'override_lppostslayout' ), 10, 2 ); // this filter is used in multiple locations !
	} // filters_after_setup_theme()

	// override icon block ids to add all 9 blocks; used for all block locations
	function override_blocks_ids( $ids = array(), $sid = 1 ) {
		return array_merge( $ids, array(
			_CRYOUT_THEME_PREFIX . '_lpblockfive' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblocksix' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblockseven' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblockeight' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblocknine' . $sid,
		) );
	} // override_blocks_ids()

	// override icon block icons to add all 9 blocks; used for all block locations
	function override_blocks_icons( $ids = array(), $sid = 1 ) {
		return array_merge( $ids, array(
			_CRYOUT_THEME_PREFIX . '_lpblockfiveicon' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblocksixicon' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblocksevenicon' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblockeighticon' . $sid,
			_CRYOUT_THEME_PREFIX . '_lpblocknineicon' . $sid,
		) );
	} // override_blocks_icons()

	// add blocks per-row dependent class
	function override_blocks_perrow( $default, $sid = 1 ) {
		$perrow = cryout_get_option( _CRYOUT_THEME_PREFIX . '_lpblockperrow' . $sid );
		return absint( $perrow );
	}

	// override block/box url value
	function override_meta_url( $old_url, $id = 0 ) {
		$meta_url = get_post_meta( $id, 'cryout_blob_link', true );
		// CPTs with empty links should not link back to the CPT
		if ( !in_array( get_post_type( $id ), array( 'post', 'page' ) ) && ( $meta_url == '' ) ) return '';
		if ( ! empty( $meta_url ) ) return $meta_url; else return $old_url;
	}

	// override block/box url target value
	function override_meta_target( $oldval = '', $id = 0 ) {
		$meta_target = get_post_meta( $id, 'cryout_blob_target', true );
		if ( ! empty( $meta_target ) ) return ' target="_blank"';
	}

	// override cpt hide title
	function override_meta_hidetitle( $oldval, $id = 1 ) {
		$meta_hidetitle = get_post_meta( $id, 'cryout_blob_hidetitle', true );
		if ( ! empty( $meta_hidetitle ) ) return ''; else return $oldval;
	}

	// override cpt style
	function override_meta_style( $oldval, $id = 1 ) {
		$style = get_post_meta( $id, 'cryout_blob_style', true );
		if ( ! empty( $style ) && ($style == 'reverse') ) return 'style-reverse'; else return $oldval;
	}

	// override boxes query args to include custom taxonomy
	function override_boxes_query_args( $args = array(), $slug = '' ){
		// handle the blobs separately
		if ( preg_match( '/^blob\|(.*)$/i', $slug, $ms ) ) {
			// unset category_name if blob is requested
			if ( isset( $args['category_name'] ) ) unset( $args['category_name'] );
			if ( isset( $args['cat'] ) ) unset( $args['cat'] );
			// add post type
			$args['post_type'] = 'cryout-featured-blob';
			// add taxonomy info if a specific category is selected; * = all
			if ( '*' != $ms[1] ) $args['tax_query'] = array( array(
				'taxonomy' 	=> 'cryout-featured-blob-category',
				'field'		=> 'slug',
				'terms'		=> array( $ms[1] ),
			) );
			$args['meta_query'] = array( array(
				'key' => 'cryout_blob_type',
				'value' => 'box',
			) );
			// add taxonomy parameter to localization call for WPML
			$args['cat'] = cryout_localize_cat( $ms[1], 'cryout-featured-blob' );
		};
		return $args;
	} // override_boxes_query_args()

	// override landing page template filename
	function override_landing_page_template_filename( ){
		return 'plus-templates/landing-page';
	} // override_landing_page_template_filename()

	// override landing page magazine layout setting with plus option
	function override_lppostslayout( $layout = 1 ){
		$lppostslayout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_lppostslayout' );
		if (!empty($lppostslayout) && is_front_page() ) return $lppostslayout;
		return $layout;
	} // override_lppostslayout()

	// override js options array
	function override_js_options( $js_options ){

		// override landing page boxex ratios to support third boxes set
		list( $lpboxwidth3, $lpboxheight3 ) = array_values( cryout_get_option( array( _CRYOUT_THEME_PREFIX . '_lpboxwidth3', _CRYOUT_THEME_PREFIX . '_lpboxheight3' ) ) );
		if ( empty( $lpboxheight3 ) ) $lpboxheight3 = 1; // failsafe
		$js_options['lpboxratios'] = array_merge( $js_options['lpboxratios'], array( round ( $lpboxwidth3 / $lpboxheight3, 3 ) ) );

		// posts layout on the lp
		if (cryout_on_landingpage()) {
			$js_options['magazine'] = cryout_get_option( _CRYOUT_THEME_PREFIX . '_lppostslayout' );
		}

		return $js_options;
	} // override_js_options()

	// override header image url if necessary
	function override_header_image( $url ){
		// meta override
		global $post;
		if ( isset($post->ID) ) {
			$hide_headerimg = intval(get_post_meta( $post->ID, '_cryout_meta_hide_headerimg', true ));
			if (empty($hide_headerimg)) $hide_headerimg = intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_headerimg', true )); // backwards compatibility
			if ($hide_headerimg) return false;
		}

		return $url;
	} // override_header_image()

	// boxes image size
	function lpbox_width_override( $options = array() ) {
		// 1 & 2 are handled in theme_lpbox_width() in core.php
		if ( ( _CRYOUT_THEME_SLUG == 'flu'.'ida' /* to prevent search match */ ) && ( $options[_CRYOUT_THEME_PREFIX . '_lpboxlayout3'] != 1 ) ) {
			$totalwidth = $options[_CRYOUT_THEME_PREFIX . '_sitewidth'] - $options[_CRYOUT_THEME_PREFIX . '_primarysidebar'] - $options[_CRYOUT_THEME_PREFIX . '_secondarysidebar'];
		} else {
			$totalwidth = $options[_CRYOUT_THEME_PREFIX . '_sitewidth'];
		};

		if ( ( _CRYOUT_THEME_SLUG == 'eso'.'tera' /* to prevent search match */ ) && ( $options[_CRYOUT_THEME_PREFIX . '_lpboxanimation3'] == 2 ) ) { $totalwidth = $totalwidth / 2; }

		$options[_CRYOUT_THEME_PREFIX . '_lpboxwidth3'] = intval ( $totalwidth / $options[_CRYOUT_THEME_PREFIX . '_lpboxrow3'] );
		return $options;
	} // lpbox_width_override()

	// disable sanitization for unfiltered custom JS fields
	function custom_js_sanitization( $callback, $id ) {
		if (in_array( $id, array( _CRYOUT_THEME_PREFIX . '_headerjs', _CRYOUT_THEME_PREFIX . '_bodyjs', _CRYOUT_THEME_PREFIX . '_footerjs' ) ))
			return '';
		else return $callback;
	} // custom_js_sanitization()

	// filter landing page posts query to add extra options
	function landingpage_index_query( $args ) {
		$options = cryout_get_option( array(
			_CRYOUT_THEME_PREFIX . '_lppostscount',
			_CRYOUT_THEME_PREFIX . '_lppostscat',
		) );
		return wp_parse_args( array(
			'posts_per_page' => $options[_CRYOUT_THEME_PREFIX . '_lppostscount'],
			'category_name' => $options[_CRYOUT_THEME_PREFIX . '_lppostscat'],
			), $args );
	} // landingpage_index_query()

	// filter body classes for extra options
	function body_classes( $classes ) {

		// meta overrides
		global $post;
		if ( isset($post->ID) ) {
			$hide_mainmenu = 	intval(get_post_meta( $post->ID, '_cryout_meta_hide_mainmenu', true ));
			$hide_headerimg = 	intval(get_post_meta( $post->ID, '_cryout_meta_hide_headerimg', true ));
			$hide_breadcrumbs = intval(get_post_meta( $post->ID, '_cryout_meta_hide_breadcrumbs', true ));
			$hide_title = 		intval(get_post_meta( $post->ID, '_cryout_meta_hide_title', true ));
			$hide_colophon = 	intval(get_post_meta( $post->ID, '_cryout_meta_hide_colophon', true ));
			$hide_footer =		intval(get_post_meta( $post->ID, '_cryout_meta_hide_footer', true ));

			// backwards compatibility
			if (empty($hide_mainmenu)) 	$hide_mainmenu = 	intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_mainmenu', true ));
			if (empty($hide_headerimg)) $hide_headerimg = 	intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_headerimg', true ));
			if (empty($hide_breadcrumbs)) $hide_breadcrumbs = intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_breadcrumbs', true ));
			if (empty($hide_title)) 	$hide_title = 		intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_title', true ));
			if (empty($hide_colophon)) 	$hide_colophon = 	intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_colophon', true ));
			if (empty($hide_footer)) 	$hide_footer =		intval(get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_footer', true ));
			// end backwards compatibility

			if ($hide_mainmenu) $classes[] = _CRYOUT_THEME_SLUG . '-metahide-mainmenu';
			if ($hide_headerimg) $classes[] = _CRYOUT_THEME_SLUG . '-metahide-headerimg';
			if ($hide_breadcrumbs) $classes[] = _CRYOUT_THEME_SLUG . '-metahide-breadcrumbs';
			if ($hide_colophon) $classes[] = _CRYOUT_THEME_SLUG . '-metahide-colophon';
			if ($hide_title) $classes[] = _CRYOUT_THEME_SLUG . '-metahide-title';
			if ($hide_footer) $classes[] = _CRYOUT_THEME_SLUG . '-metahide-footer';
		}

		// if on landing page, check specifics
		if ( in_array( _CRYOUT_THEME_SLUG . '-landing-page', $classes ) ) {

			$lp_layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_lppostslayout' );

			// space saver to not write whole classnames
			$index = array( 0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three' );
			// generate new class based on lp option
			$new_class =  _CRYOUT_THEME_SLUG . '-magazine-' . $index[$lp_layout];

			// generate general layout classname to be replaced
			$lookup_class = '/(' . _CRYOUT_THEME_SLUG . '-magazine-(?!layout)+)\w+/i';
			foreach ($classes as &$class) {
				// if classname is a match, replace the general layout with the lp unique layout
				if (preg_match( $lookup_class, $class) ) $class = preg_replace( $lookup_class, $new_class, $class );
			};
		}

		return $classes;
	} // body_classes()

	// filter layout class for specific sections
	function layout_override( $layout ) {

		// category/archive section
		if ( is_archive() ) {
			$layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_archivelayout' );
		}
		// search results section
		if ( is_search() ) {
			$layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_searchlayout' );
		}

		// jetpack portfolio archive
		if ( taxonomy_exists( 'jetpack-portfolio-type' ) && ( is_tax( 'jetpack-portfolio-type' ) || is_tax( 'jetpack-portfolio-tag' ) ) ) {
			$layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_portlayout' );
		}
		// jetpack single portfolio
		if ( taxonomy_exists( 'jetpack-portfolio-type' ) && is_singular( 'jetpack-portfolio' ) ) {
			$layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_portsinglelayout' );
		}

		// if woocommerce general section, apply general woo layout
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_woolayout' );
		}
		// if woocommerce single product, apply specific layout
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() && function_exists('is_product') && is_product() ) {
			$layout = cryout_get_option( _CRYOUT_THEME_PREFIX . '_woosinglelayout' );
		}
		
		// custom 'full width' page template 
		if ( is_page() && is_page_template( 'plus-templates/template-fullwidth.php' ) ) {
			$layout = '1c';
		}

		return $layout;
	} // layout_override()

	// add support for plus-only post type templates
	function single_templates( $single_template ){
		$object = get_queried_object();
		if ( is_singular( 'jetpack-portfolio' ) ) {
			$special_template = locate_template("plus-templates/single-{$object->post_type}.php");
			if( file_exists( $special_template ) ) {
				return $special_template;
			} else {
				return $single_template;
			}
		}
		return $single_template;
	} // single_templates()
	
	// customize woocommerce's breadcrumb template file path
	function woocommerce_breadcrumb_template( $template, $template_name, $args, $template_path, $default_path ){
		if ($template_name == 'global/breadcrumb.php') $template = wc_locate_template( 'plus-templates/woocommerce-breadcrumb.php', get_template_directory(), $default_path );
		return $template;
	} // woocommerce_breadcrumb_template()

	// add support for plus-only post type templates
	function archive_templates( $archive_template ) {
		$object = get_queried_object();
		if ( empty($object->taxonomy) ) return $archive_template;
		if ( ( $object->taxonomy == 'jetpack-portfolio-type' ) || ( $object->taxonomy == 'jetpack-portfolio-tag' ) ) {
			$special_template = locate_template("plus-templates/archive-jetpack-portfolio.php");
			if( file_exists( $special_template ) ) {
				return $special_template;
			} else {
				return $archive_template;
			}
		}
		return $archive_template;
	} // archive_templates()

	// hide breadcrumbs on specific plus page templates
	function breadcrumbs_excluded_templates( $defaults = array() ) {
		return array_merge( array(
		'plus-templates/template-blog.php'
		), $defaults );
	} // breadcrumbs_excluded_templates()

} // Cryout_Plus_Filters class


// FIN
