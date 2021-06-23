<?php
/*
 * Plus options migration
 *
 * @package Cryout Plus
 */
 
class Cryout_Migrate_Options {
	
	// base to plus options equivalency list
	public $equivalency = array(
		//'_base_key' => '_plus_key', // !!! remember to include leading underscore
		'_lpblocksbg' => '_lpcolorblocks1', // icon blocks
		'_lpblocksbg' => '_lpcolorblocks2',
		'_lpboxesbg' => '_lpcolorboxes1', // boxes
		'_lpboxesbg' => '_lpcolorboxes2',
		'_lpboxesbg' => '_lpcolorboxes3',
		'_lptextsbg' => '_lpcolortextzero', // text areas
		'_lptextsbg' => '_lpcolortextone',
		'_lptextsbg' => '_lpcolortexttwo',
		'_lptextsbg' => '_lpcolortextthree',
		'_lptextsbg' => '_lpcolortextfour',
		'_lptextsbg' => '_lpcolortextfive',
		'_lptextsbg' => '_lpcolortextsix',
	);
	
	function __construct(){
		add_action('admin_init', array($this, 'init'));
		$this->migrate_nag();
	} // __construct()
	
	function init() {
		if ( !empty($_REQUEST['_cryout_migrate_nonce']) && wp_verify_nonce( $_REQUEST['_cryout_migrate_nonce'], 'perform_migration' ) ) {
			// perform options migration	
			if ($this->perform_migration()) { 
				set_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_migrate_result_transient', 'success', 300 );
				wp_safe_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '#migrate' ) );
				exit;
			} else { 
				set_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_migrate_result_transient', 'fail', 300 );
				wp_safe_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '#migrate') );
				exit;
			}
		};
		if ( !empty($_REQUEST['_cryout_migrate_nonce']) && wp_verify_nonce( $_REQUEST['_cryout_migrate_nonce'], 'disable_nag' ) ) {
			// turn off the migration nag
			$this->disable_nag();
			wp_safe_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '' ) );
			exit;
		}; 
	} // init()

	// migration alert hook
	function migrate_nag(){
		$base_options = get_option( _CRYOUT_THEME_SLUG . '_settings' );
		$plus_options = get_option( _CRYOUT_THEME_NAME . '_settings' );
		$migrate_nag = get_theme_mod( 'cryout_disable_migratenag' );
		if (!empty($base_options) && empty($plus_options) && !$migrate_nag) {
			add_action( 'admin_notices', array( $this, 'migrate_nag_notice' ) );
		}
	} // migrate_nag()

	// migration alert message
	function migrate_nag_notice() { ?>
		<div class="notice notice-info is-dismissible">
			<p><?php printf( __( 'It appears you have used %1$s theme before. %1$s Plus can migrate compatible options from it. Would you like to perform the migration now?', 'cryout' ), 	_CRYOUT_THEME_LABEL ) ?> </p>
			<p>
				<a href="<?php echo admin_url( _CRYOUT_THEME_PAGE_URL . '#migrate' ) ?>" class="button button-primary" id="cryout-migration-request"><?php _e( 'Yes, migrate', 'cryout' ) ?></a>&nbsp;
				<a href="<?php echo wp_nonce_url( admin_url( _CRYOUT_THEME_PAGE_URL . '' ), 'disable_nag', '_cryout_migrate_nonce') ?>" class="button"><?php _e( 'Don\'t remind me again', 'cryout' ) ?></a>
			</p>
		</div>
	<?php 
	} // migrate_nag_notice()

	// checks if base theme options exist, meaning options are migrateable
	function migrateable_options() {
		$base_options = get_option( _CRYOUT_THEME_SLUG . '_settings' );
		if (!empty($base_options)) return true;
		return false;
	} // migrateable_options()

	// performs actual options migration
	function perform_migration() {
		$base_options = get_option( _CRYOUT_THEME_SLUG . '_settings' );
		if (empty($base_options)) return false;
		$new_options = $base_options;
		foreach ($this->equivalency as $base_key => $new_key) {
			$new_options[_CRYOUT_THEME_SLUG . $new_key] = $base_options[_CRYOUT_THEME_SLUG . $base_key];
		}
		$this->disable_nag();
		delete_option( _CRYOUT_THEME_NAME . '_settings');
		return update_option( _CRYOUT_THEME_NAME . '_settings', $new_options );
	} // perform_migration()

	// prevent future migration nag
	function disable_nag() {
		set_theme_mod( 'cryout_disable_migratenag', TRUE );
	} // disable_nag()

} // Cryout_Migrate_Options class

// used in the migration admin page
$cryout_migrate_options = new Cryout_Migrate_Options;


// FIN