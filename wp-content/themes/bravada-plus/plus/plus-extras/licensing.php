<?php
/*
 * Plus license functionality
 *
 * @package Cryout Plus
 */
 
// retrieves entered license key from theme options - also used by the dependency plugins for update checks
function cryout_plus_get_license_key() {
	return apply_filters( 'cryout_theme_license_key', get_theme_mod( 'cryout_license_key' ) );
} // cryout_plus_get_license_key()
 
class Cryout_Licensing {
	
	private $theme_page = '';
	private $multisite = FALSE;

	public function __construct() {
		if (is_multisite()) {
			$this->multisite = TRUE;
			return false; // no support for multisite for now
		}
		
		add_action( 'admin_init', array( $this, 'init') );
		if ($this->license_nag()) {
			if ($this->multisite) {
				//add_action( 'network_admin_notices', array( $this, 'license_nag_notice' ), 9 );
			} else {
				add_action( 'admin_notices', array( $this, 'license_nag_notice' ), 9 );
			}
		}
	} // __construct()
	
	function init() {
		register_setting( 'cryout_dashboard_settings', 'cryout_license_key', array( $this, 'license_key_store') );
		if ( !empty($_REQUEST['_cryout_license_nonce']) && wp_verify_nonce( $_REQUEST['_cryout_license_nonce'], 'disable_nag' ) ) {
			// turn off the license nag
			$this->disable_nag();
			wp_safe_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '' ) );
			exit;
		};
	} // init()
	
	function license_key_store( $input ) {
		set_theme_mod('cryout_license_key', esc_attr( $input) );
		set_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_license_transient', 'saved', 300 );
		return $input;
	} // license_key_store
	
	// checks if license key is entered
	function license_nag() {
		$licensekey = get_theme_mod( 'cryout_license_key', '' );
		$disablenag = get_theme_mod( 'cryout_disable_licensenag', false );
		if (empty($licensekey) && !$disablenag) return true;
		return false;
	} // license_nag()
	
	// license empty alert message
	function license_nag_notice() { ?>
		<div class="notice notice-warning is-dismissible">
			<p><?php printf( __( 'To receive %1$s Plus theme updates and install companion plugins you need to enter your license key.', 'cryout' ), 
				_CRYOUT_THEME_LABEL ) ?> </p>
			<p>
				<a href="<?php echo admin_url( _CRYOUT_THEME_PAGE_URL . '#license' ) ?>" class="button button-primary" id="cryout-license-request"><?php _e( 'Enter Key', 	'cryout' ) ?></a>&nbsp;
				<a href="<?php echo wp_nonce_url( admin_url( _CRYOUT_THEME_PAGE_URL . '' ), 'disable_nag', '_cryout_license_nonce') ?>" class="button"><?php _e( 'Don\'t remind me again', 'cryout' ) ?></a>
			</p>
		</div>
	<?php 
	} // license_nag_notice()

	// prevent future nag
	function disable_nag() {
		set_theme_mod( 'cryout_disable_licensenag', TRUE );
	} // disable_nag()
			
} // class

if ( ! is_multisite() ) new Cryout_Licensing;

// FIN