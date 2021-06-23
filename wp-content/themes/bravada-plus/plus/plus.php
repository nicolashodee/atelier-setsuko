<?php
/*
 * Plus master file
 *
 * @package Cryout Plus
 */

// theme name constant used in the plus code
define( '_CRYOUT_THEME_LABEL', cryout_sanitize_tnl( _CRYOUT_THEME_SLUG ));

// extra customizer controls
require_once( get_template_directory() . '/plus-specifics.php' );
require_once( get_template_directory() . '/plus/plus-controls.php' );

// admin page
if (is_admin()){
	require_once( get_template_directory() . '/plus/plus-extras/admin-page.php' );
	require_once( get_template_directory() . '/plus/plus-extras/migration.php' );
	require_once( get_template_directory() . '/plus/plus-extras/licensing.php' );
	require_once( get_template_directory() . '/plus/plus-extras/options-xml.php' );
	require_once( get_template_directory() . '/plus/plus-extras/tgmpa.php' );
	@require_once( get_template_directory() . '/plus/plus-extras/autoupdate.php' );

}

class Cryout_Plus {

	public $defaults = array(); // loaded in the constructor
	public $structure = array(); // loaded in the constructor
	public $scheme_defaults = array(); // loaded in the constructor

	public function __construct() {

		// schemes
		$this->handle_schemes();
		add_action( 'after_setup_theme', array( $this, 'handle_schemes' ) );
		// after_setup_theme is early enough to take effect in the customizer;

		// the structure array and necessary functions
		require_once( get_template_directory() . '/plus/plus-prototypes.php' );
		require_once( get_template_directory() . '/plus/plus-functions.php' );

		$theme_data = cryout_plus_theme_specifics();
		$this->defaults = $theme_data['defaults'];
		$this->structure = $theme_data['structure'];
		unset($theme_data);

		// plus functionality
		require_once( get_template_directory() . '/plus/plus-extras/actions.php' );
		require_once( get_template_directory() . '/plus/plus-extras/filters.php' );
		require_once( get_template_directory() . '/plus/plus-extras/meta.php' );
		require_once( get_template_directory() . '/plus/plus-extras/widgets.php' );
		require_once( get_template_directory() . '/plus/plus-extras/shortcodes.php' );
		require_once( get_template_directory() . '/plus/plus-extras/editor.php' );

		$this->filters = new Cryout_Plus_Filters;

		add_filter( _CRYOUT_THEME_SLUG . '_theme_options_array', array( $this->filters, 'lpbox_width_override' ) );
		add_action( 'init', array( $this->filters, 'filters_init' ) );
		add_action( 'after_setup_theme', array( $this->filters, 'filters_after_setup_theme' ), 9 ); // some filters are needier than others

		// footer links
		add_action( 'wp_loaded', _CRYOUT_THEME_SLUG . '_footer_actions' ); // functions moved to plus-specifics.php

		// theme structure/options/defaults overload
		add_filter( _CRYOUT_THEME_SLUG . '_theme_structure_array', array($this, 'plus_structure_filter') );
		add_filter( _CRYOUT_THEME_SLUG . '_option_defaults_array', array($this, 'plus_defaults_filter') );
		add_filter( _CRYOUT_THEME_SLUG . '_custom_styles', array($this, 'plus_custom_styles') );

		// backwards compatibility filter for some values that changed format
		// uses the same function as the base theme, but needs to be re-applied to the Plus' options array using WordPress' 'option_{$option}' filter
		//add_filter( 'option_' . _CRYOUT_THEME_SLUG . '-plus_settings', _CRYOUT_THEME_SLUG . '_options_back_compat', 11 ); // not currently needed

		add_action( 'customize_controls_enqueue_scripts', array($this, 'customizer_enqueues') );
		add_action( 'after_setup_theme', array($this, 'theme_setup') );

		// lp_order refresh/reset backend
		add_action( 'wp_ajax_cryout_plus_lporder_refresh', array( $this, 'retrieve_lporder_sortable_data' ) );
		add_action( 'wp_ajax_cryout_plus_lporder_default', array( $this, 'retrieve_lporder_sortable_default' ) );

		// tgmpa plus activation
		add_action( 'tgmpa_register', 'cryout_register_required_plugins' );

	} // __construct()

	// main theme setup
	function theme_setup() {

		global $cryout_plus_admin_page;

		// plus filters are applied in filter class theme_setup()

		// replace theme page
		remove_action( 'admin_menu', _CRYOUT_THEME_SLUG.'_add_page_fn' );
		add_action( 'admin_menu', array( $cryout_plus_admin_page, 'theme_page_handler' ) );

		// replace tgmpa activation
		remove_action( 'tgmpa_register', _CRYOUT_THEME_SLUG.'_register_required_plugins' );

		// load Plus translation
		load_theme_textdomain( _CRYOUT_THEME_SLUG, get_template_directory() . '/plus/languages' );

	} // theme_setup()

	// schemes
	function handle_schemes() {
		global $wp_customize;
		if (!empty($wp_customize)) {
			// customizer preview
			$changeset_data = $wp_customize->changeset_data();
			if (! empty($changeset_data[_CRYOUT_THEME_NAME . '_settings[' . _CRYOUT_THEME_PREFIX . '_scheme]']['value'])) {
				$scheme = $changeset_data[_CRYOUT_THEME_NAME . '_settings[' . _CRYOUT_THEME_PREFIX . '_scheme]']['value'];
			} else {
				$scheme = cryout_get_option( _CRYOUT_THEME_PREFIX . '_scheme' );
			}
		} else {
			// frontend
			$scheme = cryout_get_option( _CRYOUT_THEME_PREFIX . '_scheme' );
		}

		if (!empty($scheme) && ($scheme != 'default')) {
			include_once( sprintf( get_template_directory() . '/plus/schemes/%s.php', $scheme ) );
			if ( function_exists( sprintf( '%s_defaults', $scheme ) ) ) $this->scheme_defaults = call_user_func( sprintf( '%s_defaults', $scheme ) );
		}
	} // handle_schemes()

	// lp order refresh backend
	function retrieve_lporder_sortable_data() {
		$statuses = array(
			// this array is defined in multiple locations:
			// 	- plus.php / retrieve_lporder_sortable_data()
			// 	- plus-specifics.php / master options array
			//  - plus-functions.php / cryout_master_customize_hook_plus()
			'slider' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpslider'),
			'text-zero' => cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextzero'),
			'blocks-1' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpblockscontent1'),
			'text-one' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextone'),
			'boxes-1' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpboxcat1'),
			'text-two' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptexttwo'),
			'blocks-2' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpblockscontent2'),
			'text-three'=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextthree'),
			'boxes-2' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpboxcat2'),
			'text-four' => cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextfour'),
			'boxes-3' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpboxcat3'),
			'text-five' => cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextfive'),
			'portfolio' => cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpport'),
			'testimonials' => cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptt'),
			'index' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpposts'),
			'text-six' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextsix'),
		);
		echo json_encode($statuses);
		wp_die();
	} // retrieve_lporder_sortable_data()

	// lp order reset backend
	function retrieve_lporder_sortable_default() {
		echo json_encode($this->defaults[_CRYOUT_THEME_PREFIX . '_lporder']);
		wp_die();
	} // retrieve_lporder_sortable_default()

	// filter theme structure array
	function plus_structure_filter( $big ) {
		foreach ($this->structure as $key => $set) {
			if (!empty($big[$key])) $big[$key] = array_merge( $big[$key], $set );
		}
		return $big;
	} // plus_structure_filter()

	// filter theme defaults array
	function plus_defaults_filter( $defaults ) {
		// check if scheme is used and apply its defaults to base defaults
		if ( !empty($this->scheme_defaults) ) $defaults = wp_parse_args( $this->scheme_defaults, $defaults );

		// add plus defaults
		return wp_parse_args( $this->defaults, $defaults );
	} // plus_defaults_filter()

	// customizer styles and scripts
	function customizer_enqueues() {
		wp_enqueue_style( _CRYOUT_THEME_SLUG . 'plus-customizer-css', get_template_directory_uri() . '/plus/resources/admin/customizer.css', array(), _CRYOUT_THEME_VERSION );
		wp_enqueue_script( _CRYOUT_THEME_SLUG . 'plus-customizer-js', get_template_directory_uri() . '/plus/resources/admin/customizer.js', array( 'jquery' ), _CRYOUT_THEME_VERSION, true );
		wp_localize_script( _CRYOUT_THEME_SLUG . 'plus-customizer-js', 'cryout_plus_ajax_backend', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_add_inline_style( 'cryout-customizer-css', preg_replace( "/[\n\r\t\s]+/", " ", cryout_customizer_styles() ) );
	} // customizer_enqueues()

	// plus custom style
	function plus_custom_styles( $style = '' ) {
		return $style . cryout_plus_custom_styling();
	} // plus_custom_styles()

	// footer links functions moved to specifics

} // Cryout_Plus class

new Cryout_Plus;

// FIN
