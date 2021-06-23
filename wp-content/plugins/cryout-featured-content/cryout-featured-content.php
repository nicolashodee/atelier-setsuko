<?php
/**
 * Plugin Name: Cryout Featured Content
 * Plugin URI:  https://www.cryoutcreations.eu/wordpress-plugins/featured-content/
 * Description: This is a companion plugin for our themes which adds the custom post type for the theme's landing page boxes, blocks and text areas.
 * Version:     1.2
 * Author:      Cryout Creations
 * Author URI:  https://www.cryoutcreations.eu/
 * Text Domain: cryout-featured-content
 * Domain Path: /languages
 * Requires at least: 4.5
 * Requires PHP: 5.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

class CryoutFeaturedContent {

	public $name = 'Cryout Featured Content';
	public $version = '1.2';
	public $plugin_dir = '';
	private $required_php_version = '5.3';
	private $required_wp_version = '4.5';

	function __construct() {

		$this->plugin_dir = trailingslashit( dirname( __FILE__ ) );

		// system requirements
		$this->load_file( 'includes/requirements.php' );
		$this->requirements = new CryoutFeaturedContent_Requirements( array (
			'name' 					=> $this->name,
			'required_php_version' 	=> $this->required_php_version,
			'required_wp_version' 	=> $this->required_wp_version,
			'plugin_dir' 			=> $this->plugin_dir,
			'version' 				=> $this->version,
		) );

		// admin notifications
		$this->load_file( 'includes/admin-notice.php' );
		
		// autoupdate
		$this->load_file( 'includes/autoupdate.php' );
		
		if ( $this->requirements->requirements_met() ) {

			// custom post types
			$this->load_file( 'includes/featured-blobs.php' );
			$this->blobs = new CryoutFeaturedContent_Blobs( array(
				'name' 			=> $this->name,
				'plugin_dir' 	=> $this->plugin_dir,
				'version'		=> $this->version,
			) );
			
			register_activation_hook( __FILE__, array( $this, 'rewrite_flush') );
			
			add_action( 'admin_init', array( $this, 'privacy_content') );
			
		} else {
			add_action( 'admin_notices', array( $this->requirements, 'requirements_error' ) );
			add_action( 'admin_init', array( $this, 'deactivate' ) );
		}

	} // __construct()
	
	// update permalinks for cpt
	function rewrite_flush() {
		flush_rewrite_rules();
	} // rewrite_flush()

	// deactivate plugin on requirements failure
	function deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate our plugin
	}

	// load supplementary plugin file
	function load_file( $file ) {
		require_once( $this->plugin_dir . $file );
	}
	
    public function privacy_content(){
	    if ( function_exists('wp_add_privacy_policy_content') ) {
		    wp_add_privacy_policy_content(
		    	__( 'Cryout Featured Content', 'cryout-featured-content' ),
			    __( "This plugin doesn't collect any kind of data from either website visitors or administrators.", 'cryout-featured-content' )
		    );
	    }
    }

} // CryoutFeaturedContent()

new CryoutFeaturedContent();

// FIN