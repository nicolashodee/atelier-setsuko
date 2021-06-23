<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'CryoutFeaturedContent_Blobs' ) ):

/**
 * Creates plugin's custom post type and associated taxonomies
 */
class CryoutFeaturedContent_Blobs {

	const POST_TYPE_SLUG = 'cryout-featured-blob'; // 20 chars!!!
	const TAG_SLUG       = 'cryout-featured-blob-category';
	static $name 		 = '';
	static $plugin_dir 	 = '';
	static $version  	 = '';
	
	/**
	 * Constructor
	 */
	function __construct( $params=array() ) {
		if (!empty($params))
			foreach ($params as $key => $value)
				self::${$key} = $value;
		$this->register_hooks();
	} // __construct()

	/**
	 * Register callbacks for actions and filters
	 */
	public function register_hooks() {
		add_action( 'init',                     __CLASS__ . '::create_post_type' );
		add_action( 'setup_theme',              __CLASS__ . '::create_taxonomies' ); // setup_theme hook needed for compatibility with theme customizer taxonomy selectors
		add_action( 'init',              		__CLASS__ . '::create_taxonomies' );
		add_action( 'admin_menu',               __CLASS__ . '::admin_menus', 1 );
		add_action( 'save_post',                __CLASS__ . '::save_post', 10, 2 );
		add_action( 'admin_enqueue_scripts',    __CLASS__ . '::admin_enqueue_scripts' );
		add_action( 'restrict_manage_posts',	__CLASS__ . '::admin_posts_filter' );
		add_filter( 'parse_query', 				__CLASS__ . '::posts_filter' );
		add_filter( 'admin_url',				__CLASS__ . '::new_post_button_filter', 10, 3 );
		
		// force support in polylang - no longer needed as of v1.2; post type is now registered public
		//add_filter( 'pll_get_post_types', 		__CLASS__ . '::pll_post_types' );
		//add_filter( 'pll_get_taxonomies', 		__CLASS__ . '::pll_taxonomies' );

		// extra columns in the custom posts list
		add_action( 'manage_edit-' . self::POST_TYPE_SLUG . '_columns',  			__CLASS__ . '::blob_columns' );
		add_action( 'manage_' . self::POST_TYPE_SLUG . '_posts_custom_column', 		__CLASS__ . '::blob_columns_data', 10, 2);
		add_filter( 'manage_edit-' . self::POST_TYPE_SLUG . '_sortable_columns', 	__CLASS__ . '::order_column_register_sortable' );
		
		// admin notice for undefined type
		add_action( 'admin_notices', __CLASS__ . '::meta_notice' );
	} // register_hooks()

	/**
	 * Registers the custom post type
	 */
	public static function create_post_type() {
		if ( ! post_type_exists( self::POST_TYPE_SLUG ) ) {
			$post_type_params = self::get_post_type_params();
			$post_type        = register_post_type( self::POST_TYPE_SLUG, $post_type_params );

			if ( is_wp_error( $post_type ) ) {
				add_notice( __METHOD__ . ' error: ' . $post_type->get_error_message(), 'error' );
			}
		}
	} // create_post_type()

	/**
	 * Defines the parameters for the custom post type
	 */
	protected static function get_post_type_params() {
		$labels = array(
			'name'               =>			 __( 'Featured Content', 		'cryout-featured-content' ),
			'singular_name'      => 		 __( 'Featured Content', 		'cryout-featured-content' ),
			'menu_name'     	 => 		 __( 'Featured Content', 		'cryout-featured-content' ),
			'name_admin_bar'     => 		 __( 'Featured Content', 		'cryout-featured-content' ),
			'all_items'          => 		 __( 'All Content', 			'cryout-featured-content' ),
			'add_new'            => 		 __( 'Add New', 				'cryout-featured-content' ),
			'add_new_item'       => sprintf( __( 'Add New %1$s',			'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'edit'               => 		 __( 'Edit', 					'cryout-featured-content' ),
			'edit_item'          => sprintf( __( 'Edit %1$s', 				'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'new_item'           => sprintf( __( 'New %1$s', 				'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'view'               => sprintf( __( 'View %1$s', 				'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'view_item'          => sprintf( __( 'View %1$s', 				'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'search_items'       => sprintf( __( 'Search %1$s', 			'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'not_found'          => sprintf( __( 'No %1$s found', 			'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'not_found_in_trash' => sprintf( __( 'No %1$s found in Trash', 	'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
			'parent'             => sprintf( __( 'Parent %1$s', 			'cryout-featured-content' ), __( 'Featured Content', 'cryout-featured-content' ) ),
		);

		$post_type_params = array(
			'labels'               => $labels,
			'singular_label'       => __( 'Featured Content', 'cryout-featured-content' ),
			'public'               => true, // needs public for polylang/wpml support
			'exclude_from_search'  => true, // disable visibility in search results
			'publicly_queryable'   => false, // disable visibility on the frontend
			'show_ui'              => true, // enable dashboard admin screens 
			'show_in_menu'         => true, // enable in the dashboard menu
			'show_in_nav_menus'    => false, // hide from selection in menus/widgets
			'register_meta_box_cb' => __CLASS__ . '::add_meta_boxes',
			'taxonomies'           => array( self::TAG_SLUG ),
			'menu_position'        => 20,
			'menu_icon' 		   => 'dashicons-megaphone', //plugins_url( '/resources/icon.png', __FILE__ ),
			'hierarchical'         => true,
			'capability_type'      => 'page',
			'has_archive'          => false,
			'rewrite'              => array( 'slug' => 'featured' ),
			'query_var'            => false,
			'supports'             => array( 'title', 'excerpt', 'editor', 'thumbnail', 'revisions' )
		);

		return apply_filters( 'cryout_featured_blob_posttype_params', $post_type_params );
	} // get_post_type_params()

	/**
	 * Registers the category taxonomy
	 */
	public static function create_taxonomies() {
		if ( ! taxonomy_exists( self::TAG_SLUG ) ) {
			$tag_taxonomy_params = self::get_tag_taxonomy_params();
			register_taxonomy( self::TAG_SLUG, self::POST_TYPE_SLUG, $tag_taxonomy_params );
		}
	} // create_taxonomies()

	/**
	 * Defines the parameters for the custom taxonomy
	 */
	protected static function get_tag_taxonomy_params() {
		$tag_taxonomy_params = array(
			'label'                 => __( 'Featured Category', 'cryout-featured-content' ),
			'labels'                => array(
				'name'              => _x( 'Featured Categories', 'taxonomy general name', 'cryout-featured-content' ),
				'singular_name'     => _x( 'Featured Category', 'taxonomy singular name', 'cryout-featured-content' ),
				'search_items'      => __( 'Search', 'cryout-featured-content' ),
				'all_items'         => __( 'All Categories', 'cryout-featured-content' ),
				'edit_item'         => __( 'Edit', 'cryout-featured-content' ),
				'update_item'       => __( 'Update', 'cryout-featured-content' ),
				'add_new_item'      => __( 'Add New', 'cryout-featured-content' ),
				'new_item_name'     => __( 'New Category', 'cryout-featured-content' ),
			),
			'public'                => true, // needs public for polylang/wpml support
			'exclude_from_search'   => true, // disable visibility in search results
			'publicly_queryable'    => false, // disable visibility on the frontend
			'show_ui'               => true, // enable dashboard admin screens 
			'show_in_menu'          => true, // enable in the dashboard menu
			'show_in_nav_menus'     => false, // hide from selection in menus/widgets
			'hierarchical'          => true,
			'rewrite'               => array( 'slug' => self::TAG_SLUG ),
			'update_count_callback' => '_update_post_term_count'
		);

		return apply_filters( 'cryout_featured_blob_taxonomy_params', $tag_taxonomy_params );
	} // get_tag_taxonomy_params()

	/**
	 * Adds meta boxes for the custom post type
	 */
	public static function add_meta_boxes() {
		add_meta_box(
			'cryout_featured_blob_metas',
			__( 'Featured Content Attributes', 'cryout-featured-content' ),
			__CLASS__ . '::markup_meta_boxes',
			self::POST_TYPE_SLUG,
			'normal',
			'core'
		);
	} // add_meta_boxes()

	/**
	 * Builds the markup for all meta boxes
	 */
	public static function markup_meta_boxes( $post, $box ) {
		$variables = array();

		switch ( $box['id'] ) {
			case 'cryout_featured_blob_metas':
				$variables['cryout_blob_type'] = get_post_meta( $post->ID, 'cryout_blob_type', true );
				if ( empty($variables['cryout_blob_type']) && isset($_GET['blobtype']) ) {
					$variables['cryout_blob_type'] = esc_attr($_GET['blobtype']);
				}
				$variables['cryout_blob_link'] = get_post_meta( $post->ID, 'cryout_blob_link', true );
				$variables['cryout_blob_target'] = get_post_meta( $post->ID, 'cryout_blob_target', true );
				$variables['cryout_blob_style'] = get_post_meta( $post->ID, 'cryout_blob_style', true );
				$variables['cryout_blob_hidetitle'] = get_post_meta( $post->ID, 'cryout_blob_hidetitle', true );
				$view = 'metabox.php';
				break;
			default:
				$view = false;
				break;
		}

		echo self::render_template( $view, $variables );
	} // markup_meta_boxes()

	/**
	 * Validates and saves values of the the custom post type's metas
	 */
	protected static function save_custom_meta( $post_id, $new_values ) {
		if ( !empty( $new_values[ 'cryout_blob_type' ] ) ) 	update_post_meta( $post_id, 'cryout_blob_type', esc_attr( $new_values[ 'cryout_blob_type' ] ) );
													else	delete_post_meta( $post_id, 'cryout_blob_type' );
		if ( !empty( $new_values[ 'cryout_blob_link' ] ) ) 	update_post_meta( $post_id, 'cryout_blob_link', esc_url( $new_values[ 'cryout_blob_link' ] ) );
													else 	delete_post_meta( $post_id, 'cryout_blob_link' );
		if ( !empty( $new_values[ 'cryout_blob_target' ] )) update_post_meta( $post_id, 'cryout_blob_target', 1 );
													else	delete_post_meta( $post_id, 'cryout_blob_target' );
		if ( !empty( $new_values[ 'cryout_blob_style' ] ) ) update_post_meta( $post_id, 'cryout_blob_style', esc_attr( $new_values[ 'cryout_blob_style' ] ) );
													else	delete_post_meta( $post_id, 'cryout_blob_style' );
		if ( !empty( $new_values[ 'cryout_blob_hidetitle' ] )) update_post_meta( $post_id, 'cryout_blob_hidetitle', 1 );
													else	delete_post_meta( $post_id, 'cryout_blob_hidetitle' );
	} // save_custom_meta()
	
	/**
	 * Displays notice if post has no type meta set
	 */
	public static function meta_notice() {
		global $post;
		if (!empty($post)) { 
			// only check the plugin's cpt
			$post_type = get_post_type( $post );
			if (empty($post_type)) return;
			if ($post_type != self::POST_TYPE_SLUG) return; 
		
			// only check published (or pending) posts
			$post_status = get_post_status( $post->ID );
			if (empty($post_status)) return;
			if ( !in_array( $post_status, array( 'publish', 'pending', 'future', 'private') ) ) return;
			
			// finally check the type meta
			$type = get_post_meta( $post->ID, 'cryout_blob_type', true );
			if (empty($type)) { ?>
				<div class="cryout_message notice notice-warning is-dismissible">
					<p><?php printf( '<strong>%1$s</strong><br>%2$s', __( 'Remember to select a type for this item.', 'cryout-featured-content' ), __('Featured Content without a Type is not visible to the theme.', 'cryout-featured-content' ) ) ?></p>
					<button class="notice-dismiss" type="button">
						<span class="screen-reader-text"><?php _e('Dismiss this notice.', 'cryout-featured-content' ) ?></span>
					</button>
				</div>
			<?php }
		}
	} // meta_notice()

	/**
	 * Saves values of the the custom post type's extra fields
	 */
	public static function save_post( $post_id, $revision ) {
		global $post;
		$ignored_actions = array( 'trash', 'untrash', 'restore' );

		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], $ignored_actions ) ) {
			return;
		}

		if ( ! $post || $post->post_type != self::POST_TYPE_SLUG || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $post->post_status == 'auto-draft' ) {
			return;
		}

		self::save_custom_meta( $post_id, $_POST );
	} // save_post()

	/**
	 * Add columns in the custom posts list
	 */
	static function blob_columns($columns) {
		return array_merge(
			array_slice( $columns, 0, 2),
			array( self::TAG_SLUG => __('Categories', 'cryout-featured-content'), 'type' => __('Content Type', 'cryout-featured-content') ),
			array_slice( $columns, 2, count($columns) )
		);
	} // blob_columns()

	/**
	 * Add content for the new columns in the custom posts list
	 */
	static function blob_columns_data($column_name, $post_ID) {
		global $post;

		switch ($column_name) {
			case self::TAG_SLUG:

				$terms = get_the_terms( $post->ID, self::TAG_SLUG );
				if ( !empty( $terms ) ) {

					$out = array();
					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%1$s">%2$s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, self::TAG_SLUG => $term->slug ), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, self::TAG_SLUG, 'display' ) )
						);

					}
					echo join( ', ', $out );

				}

				else {
					echo '-';
				}

			break;

			case 'type':
				$type = get_post_meta( $post_ID, 'cryout_blob_type', true );;
				$url = esc_url( add_query_arg( array( 'post_type' => self::POST_TYPE_SLUG, 'cryout_blob_type' => $type ), 'edit.php' ) );
				switch ($type) {
					case 'block': 		$label = __( 'Block', 'cryout-featured-content' );		break;
					case 'box': 		$label = __( 'Box', 'cryout-featured-content' ); 		break;
					case 'text': 		$label = __( 'Text', 'cryout-featured-content' ); 		break;
					default:
						$label = '-';
						$url = esc_url( add_query_arg( array( 'post_type' => self::POST_TYPE_SLUG ), 'edit.php' ) );
					break;
				} // switch
				printf( '<a href="%1$s">%2$s</a>', $url, $label );
			break;

			/* case 'menu_order': // order field is not used

				$order = $post->menu_order;
				echo $order;

			break; */
		}
	} // blob_columns_data()

	/**
	* Add sort by columns support
	*/
	static function order_column_register_sortable($columns){
	  //$columns['menu_order'] = 'menu_order';
	  //$columns[self::TAG_SLUG] = self::TAG_SLUG; // doesn't really work, yet
	  return $columns;
	} // order_column_register_sortable()

	/**
	 * Handles dashboard menus order and arrangement
	 */
	static function admin_menus() {
		remove_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 'edit.php?post_type=' . self::POST_TYPE_SLUG );
		remove_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 'post-new.php?post_type=' . self::POST_TYPE_SLUG );
		remove_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 'edit-tags.php?taxonomy=' . self::POST_TYPE_SLUG . '-category&amp;post_type=' . self::POST_TYPE_SLUG );

		add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 
			__( 'All Content', 'cryout-featured-content' ),
			__( 'All Content', 'cryout-featured-content' ),
			'edit_others_posts',
			'edit.php?post_type=' . self::POST_TYPE_SLUG 
		);
		add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 
			__( 'Icon Blocks', 'cryout-featured-content' ),
			__( 'Icon Blocks', 'cryout-featured-content' ),
			'edit_others_posts',
			'edit.php?post_type=' . self::POST_TYPE_SLUG . '&cryout_blob_type=block'
		);
		add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 
			__( 'Featured Boxes', 'cryout-featured-content' ),
			__( 'Featured Boxes', 'cryout-featured-content' ),
			'edit_others_posts',
			'edit.php?post_type=' . self::POST_TYPE_SLUG . '&cryout_blob_type=box' 
		);
		add_submenu_page( 'edit.php?post_type='.self::POST_TYPE_SLUG, 
			__( 'Text Areas', 'cryout-featured-content' ),
			__( 'Text Areas', 'cryout-featured-content' ),
			'edit_others_posts',
			'edit.php?post_type=' . self::POST_TYPE_SLUG . '&cryout_blob_type=text' 
		);

		add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 
			__( 'Add new', 'cryout-featured-content' ),
			__( 'Add new', 'cryout-featured-content' ), 
			'edit_others_posts', 
			'post-new.php?post_type=' . self::POST_TYPE_SLUG 
		);
		add_submenu_page( 'edit.php?post_type=' . self::POST_TYPE_SLUG, 
			__( 'Categories', 'cryout-featured-content' ),
			__( 'Categories', 'cryout-featured-content' ),
			'edit_others_posts',
			'edit-tags.php?taxonomy=' . self::POST_TYPE_SLUG . '-category&amp;post_type=' . self::POST_TYPE_SLUG
		);
	} // admin_menus()

	/**
	 * Enqueues admin script on the custom post type pages
	 */
	static function admin_enqueue_scripts($hook){
		global $post_type;
		if( self::POST_TYPE_SLUG === $post_type ) {
			wp_enqueue_script( 'cryout-featured-content-backend', plugins_url(  'resources/backend.js', dirname(__FILE__) ), NULL, self::$version );
			wp_enqueue_style( 'cryout-featured-content-backend', plugins_url( 'resources/backend.css', dirname(__FILE__) ), self::$version );
		};
	} // admin_enqueue_scripts()
	
	/**
	 * Register post type in Polylang
	 */
	static function pll_post_types( $post_types ){
		return array_merge( $post_types, array( self::POST_TYPE_SLUG ) );
	} // pll_post_types()
	/**
	 * Register taxonomy in Polylang
	 */
	static function pll_taxonomies( $taxonomies ){
		return array_merge( $taxonomies, array( self::TAG_SLUG ) );
	} // pll_taxonomies()

	/**
	 * Creates dropdown filter in custom posts edit page
	 */
	static function admin_posts_filter(){
		$type = 'post';
		if (isset($_GET['post_type'])) {
			$type = $_GET['post_type'];
		}

		// only add filter to the correct post types
		if ( self::POST_TYPE_SLUG == $type ){
			$values = array(
				__( 'Icon Blocks', 'cryout-featured-content' ) => 'block',
				__( 'Featured Boxes', 'cryout-featured-content' ) => 'box',
				__( 'Text Areas', 'cryout-featured-content' ) => 'text',
			);
			?>
			<select name="cryout_blob_type">
			<option value=""><?php _e( 'Filter by Type', 'cryout-featured-content' ) ?></option>
			<?php
				$selected = isset($_GET['cryout_blob_type']) ? esc_attr($_GET['cryout_blob_type']) : '';
				foreach ($values as $label => $value) {
					printf
						(
							'<option value="%s"%s>%s</option>',
							$value,
							$value == $selected? ' selected="selected"':'',
							$label
						);
					}
			?>
			</select>
			<?php
		}
	} // admin_posts_filter()

	/**
	 * Filter custom posts by featured type
	 */
	static function posts_filter( $query ){
		global $pagenow;
		$type = 'post';
		if (isset($_GET['post_type'])) {
			$type = esc_attr($_GET['post_type']);
		}
		if ( self::POST_TYPE_SLUG == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['cryout_blob_type']) && esc_attr($_GET['cryout_blob_type']) != '') {
			$query->query_vars['meta_key'] = 'cryout_blob_type';
			$query->query_vars['meta_value'] = esc_attr($_GET['cryout_blob_type']);
		}
	} // posts_filter()
	
	/**
	 * Filter the admin url on New Post button to add pre-set blob type
	 */
	static function new_post_button_filter( $url, $path, $blog_id ){
		if ( isset($_GET['cryout_blob_type']) && in_array( $_GET['cryout_blob_type'], array( 'block', 'box', 'text' ) ) ){
			$type = esc_attr( $_GET['cryout_blob_type'] );
			return $url . '&blobtype=' . $type;
		}	
		return $url;
	} // new_post_button_filter

	/**
	 * Renders metaboxes HTML using external view/ files
	 */
	protected static function render_template( $default_template_path = false, $variables = array(), $require = 'once' ) {
		do_action( 'cryout_featured_content_before_render_'.$default_template_path, $default_template_path, $variables );

		$template_path = locate_template( basename( $default_template_path ) );
		if ( ! $template_path ) {
			$template_path = self::$plugin_dir . '/view/' . $default_template_path;
		}

		if ( is_file( $template_path ) ) {
			extract( $variables );
			ob_start();

			if ( 'always' == $require ) {
				require( $template_path );
			} else {
				require_once( $template_path );
			}

			$template_content = apply_filters( 'cryout_featured_content_'.$default_template_path, ob_get_clean(), $default_template_path, $template_path, $variables );
		} else {
			$template_content = '';
		}

		do_action( 'cryout_featured_content_after_render_'.$default_template_path, $variables, $template_path, $template_content );
		return $template_content;
	} // render_template()

} // class CryoutFeaturedContent_Blobs

endif;

// FIN