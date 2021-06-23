<?php
/**
 * Plugin Autoupdater
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

// don't conflict with other cryout plugins that may use same class
if ( !class_exists('Cryout_Plugin_Autoupdater') ):
class Cryout_Plugin_Autoupdater {

	private $api_url = 'http://plus.cryout.eu/';
	
	public $plugin_data = NULL;
	public $plugin_version = 0;
	public $plugin_base = '';
	public $plugin_slug = '';
	private $license_key = '';

	function __construct( $slug, $base, $key ) {
		$this->api_url = apply_filters('cryout_updates_api_url', $this->api_url);
		$this->plugin_base = $base;
		$this->plugin_slug = $slug;
		$this->license_key = $key;
		$this->get_plugin_info();
		if (!empty($this->license_key)) {
			// only do stuff when a key is filled in
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ), 10 );
			add_filter( 'plugins_api', array( $this, 'plugin_api_call' ), 10, 3);
		}
	} // __construct()

	function get_plugin_info() {
		if(function_exists('get_plugin_data')){
			$this->plugin_data = get_plugin_data( $this->plugin_base, false, false );
			$this->plugin_version = $this->plugin_data['Version'];  
		}
	} // get_plugin_info()

	function check_for_update($checked_data) {
		global $wp_version;
		
		if (empty($checked_data->checked))
			return $checked_data;

		$request = array(
			'slug' => $this->plugin_slug,
			'version' => $this->plugin_version 
		);
		// Start checking for an update
		$send_for_check = array(
			'timeout' => 10,
			'body' => array(
				'action' => 'basic_check', 
				'request' => serialize($request),
				'site-url' => esc_url( home_url() ),
				'key' => $this->license_key,
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . esc_url( home_url() ) 
		);
		$raw_response = wp_remote_post($this->api_url, $send_for_check);
		
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
			$response = unserialize($raw_response['body']);
		
		// Feed the update data into WP updater
		if (!empty($response)) {
			if (!empty($checked_data->response)) $checked_data->response = array_reverse($checked_data->response, true);
			$checked_data->response[$this->plugin_slug . '/' . $this->plugin_slug . '.php'] = $response;
			$checked_data->response = array_reverse($checked_data->response, true);
		}
		return $checked_data;
	} // check_for_update()


	function plugin_api_call($def, $action, $args) {
		global $wp_version;
		
		if ( !isset($args->slug) || ($args->slug != $this->plugin_slug) )
			return false;
		
		// Get the current version
		$args->version = $this->plugin_version;
		
		$request_string = array(
			'timeout' => 10,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
			'body' => array(
				'action' => $action,
				'request' => serialize($args),
				'site-url' => esc_url( home_url() ),
				'key' => $this->license_key,
			)
		);

		$request = wp_remote_post($this->api_url, $request_string);

		if (is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An unexpected HTTP error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'cryout-featured-content'), $request->get_error_message());
		} else {
			$res = @unserialize($request['body']);
			
			if ($res === false)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred while processing the API reply.', 'cryout-featured-content'), $request['body']);
		}
		
		return $res;
	} // plugin_api_call()
	
} // class Cryout_Plugin_Autoupdater
endif;

// uses cryout_plus_get_license_key() from companion theme to retrieve license key needed for api
// will not enable autoupdate if license is not set or function does not exist
add_action( 'admin_init', function() {
	if ( function_exists( 'cryout_plus_get_license_key' ) ) {
		new Cryout_Plugin_Autoupdater( basename(dirname(dirname(__FILE__))), dirname(dirname(__FILE__)) . '/' . basename(dirname(dirname(__FILE__))) . '.php', cryout_plus_get_license_key() );
 	}
} );

// FIN