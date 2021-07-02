<?php
/**
 * Plus Theme Autoupdater
 */

// cryout_plus_get_license_key() moved to licensing.php
 
// autoupdate class - theme
class Cryout_Plus_Theme_Autoupdater {

	private $api_url = 'https://plus.cryout.eu/';
	
	public $theme_data = NULL;
	public $theme_version = 0;
	public $theme_base = '';
	private $license_key = '';

	function __construct( $key = '' ) {
		$this->api_url = apply_filters('cryout_updates_api_url', $this->api_url);
		$this->license_key = $key;
		$this->get_theme_info();
		add_filter('pre_set_site_transient_update_themes', array( $this, 'check_for_update' ), 10 );
		add_filter('themes_api', array( $this, 'theme_api_call' ), 10, 3);	// Take over the Theme info screen on WP multisite
	} // __construct()

	function get_theme_info() {
		if(function_exists('wp_get_theme')){
			$this->theme_data = wp_get_theme( get_option('template') );
			$this->theme_version = $this->theme_data->Version;  
		}    
		$this->local_base = get_option('template');
		
		$this->theme_base = _CRYOUT_THEME_NAME;
		$this->theme_version = _CRYOUT_THEME_VERSION;
	} // get_theme_info()

	function check_for_update($checked_data) {
		global $wp_version;

		$request = array(
			'slug' => $this->theme_base,
			'version' => $this->theme_version 
		);
		// Start checking for an update
		$data = array(
			'body' => array(
				'action' => 'theme_update', 
				'request' => serialize($request),
				'site-url' => esc_url( home_url() ),
				'key' => $this->license_key,
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . esc_url( home_url() ) 
		);
		
		$raw_response = wp_remote_post($this->api_url, $data);
		
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
			$response = maybe_unserialize($raw_response['body']);

		// Feed the update data into WP updater
		if (!empty($response)) {
			if (!empty($checked_data->response)) $checked_data->response = array_reverse($checked_data->response, true); // flip array if is defined
			$checked_data->response[$this->local_base] = $response;
			$checked_data->response = array_reverse($checked_data->response, true); // flip array back to original order
		}
		return $checked_data;
	} // check_for_update()


	function theme_api_call($def, $action, $args) {
		global $wp_version;
		
		if ($args->slug != $this->theme_base)
			return false;
		
		// Get the current version
		$args->version = $this->theme_version;
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
			$res = new WP_Error('themes_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'cryout'), $request->get_error_message());
		} else {
			$res = unserialize($request['body']);
			
			if ($res === false)
				$res = new WP_Error('themes_api_failed', __('An unknown error occurred', 'cryout'), $request['body']);
		}
		
		return $res;
	} // theme_api_call()
	
} // class Cryout_Plus_Theme_Autoupdater

new Cryout_Plus_Theme_Autoupdater( cryout_plus_get_license_key() ) ;

// FIN