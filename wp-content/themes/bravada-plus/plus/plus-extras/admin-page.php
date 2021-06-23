<?php
/*
 * Plus admin page
 *
 * @package Cryout Plus
 */

// used in admin-page.php, admin-page-migration.php, migration.php and options-xml.php
define( '_CRYOUT_THEME_PAGE_URL', 'themes.php?page='.cryout_sanitize_tn(_CRYOUT_THEME_NAME).'-theme' );


class Plus_Admin_Page {

	function __construct() {

		// theme page hooked in Plus->theme_setup() function

		// plugins auto (de)activation handler
		add_action( 'admin_init', array( $this, 'plugins_auto_handling' ) );

		// options export handler
		if ( isset( $_POST['cryout_export'] ) ){
			add_action( 'admin_init', 'cryout_export_options' );
		}
		// options export handler
		if ( isset( $_POST['cryout_import'] ) ){
			add_action( 'admin_init', 'cryout_import_process' );
		}

		// redirect on activation
		add_action('after_switch_theme', array( $this, 'redirect_to_theme_page' ) );

		// admin page scripts
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_scripts' );

	} // __construct()

	// plugins auto (de)activation handling
	function plugins_auto_handling() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			if ( isset( $_GET['cryout-deactivate'] ) && 'deactivate-plugin' == $_GET['cryout-deactivate'] ) {
				check_admin_referer( 'cryout-deactivate', 'cryout-deactivate-nonce' );
				$plugins = TGM_Plugin_Activation::$instance->plugins;

				foreach ( $plugins as $plugin ) {
					if ( $plugin['slug'] == $_GET['plugin'] ) {
						deactivate_plugins( $plugin['file_path'] );
						wp_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '#plugins' ) );
					}
				}
			} if ( isset( $_GET['cryout-activate'] ) && 'activate-plugin' == $_GET['cryout-activate'] ) {
				check_admin_referer( 'cryout-activate', 'cryout-activate-nonce' );
				$plugins = TGM_Plugin_Activation::$instance->plugins;

				foreach ( $plugins as $plugin ) {
					if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {
						activate_plugin( $plugin['file_path'] );
						wp_redirect( admin_url( _CRYOUT_THEME_PAGE_URL . '#plugins' ) );
						exit;
					}
				}
			}
		}
	} //plugins_auto_handling()

	// redirect on activation
	function redirect_to_theme_page() {
		global $pagenow;
		if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
			wp_redirect( _CRYOUT_THEME_PAGE_URL );
		}
	} // redirect_to_theme_page()

	// Create admin subpages
	static function theme_page_handler() {
		$the_page = add_theme_page(
			sprintf( __( '%s Theme', 'cryout' ), cryout_sanitize_tnl(_CRYOUT_THEME_NAME) ),
			sprintf( __( '%s Theme', 'cryout' ), cryout_sanitize_tnl(_CRYOUT_THEME_NAME) ),
			'edit_theme_options', cryout_sanitize_tn(_CRYOUT_THEME_NAME).'-theme', __CLASS__ . '::theme_page'
		);	
	} // theme_page_handler()

	static function admin_scripts( $hook ) {
		
		// only enqueue scripts on theme's page
		if( 'appearance_page_'.cryout_sanitize_tn(_CRYOUT_THEME_NAME).'-theme' != $hook ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-dialog');
		wp_enqueue_script( 'jquery-ui-tabs');
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		
		$strings = array( 'reset_confirmation' => esc_html( __( 'Reset theme options to default values?', 'cryout' ) ) );
		
		wp_enqueue_script( _CRYOUT_THEME_SLUG . '-admin-general-js', get_template_directory_uri() . '/plus/resources/admin/general.js', array( 'jquery', 'jquery-ui-core' ), _CRYOUT_THEME_VERSION );
		wp_localize_script( _CRYOUT_THEME_SLUG . '-admin-general-js', 'cryout_admin_settings', $strings );

		wp_enqueue_script( 'cryout-admin-js', get_template_directory_uri() . '/admin/js/admin.js', array( 'jquery-ui-tabs' ), _CRYOUT_THEME_VERSION );
		wp_enqueue_style( 'cryout-admin-style-plus', get_template_directory_uri() . '/plus/resources/admin/admin-page.css', NULL, _CRYOUT_THEME_VERSION );
		wp_enqueue_style( 'cryout-admin-style-meta', get_template_directory_uri() . '/plus/resources/admin/meta.css', NULL, _CRYOUT_THEME_VERSION );

	} // admin_scripts()

	static function theme_page() { ?>
	<div class="wrap">
	<?php
		if (!current_user_can('edit_theme_options'))  {
			wp_die( __( 'Sorry, but you do not have sufficient permissions to access this page.', 'cryout') );
		}
		$options = cryout_get_option();
	?>

		<?php

		// License save result
		if (FALSE !== ($license_result = get_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_license_transient' ) ) ) {
			delete_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_license_transient' );	?>
			<div class="notice updated fade is-dismissible">
				<p><?php _e('License key saved successfully.', 'cryout') ?></p>
			</div> <?php
		}

		// Load the import form page if the import button has been pressed
		if ( isset($_POST['cryout_import']) ) {
			cryout_import_result();
			cryout_import_form();
			return;
		}
		
		// Display successful import result -- keep after import form
		if (FALSE !== ($import_result = get_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_import_result', 0 ) ) ) {
			delete_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_import_result' );	
			if ( 1 == $import_result ) { ?>
				<div class="notice updated fade is-dismissible">
					<p><?php _e('Theme options have been loaded successfully.', 'cryout') ?></p>
				</div> <?php 
			};
		}

		// Reset options to defaults if the reset button has been pressed
		if ( isset( $_POST['cryout_reset_defaults'] ) ) {
			delete_option( _CRYOUT_THEME_NAME . '_settings' ); ?>
			<div class="notice updated fade is-dismissible">
				<p><?php _e('Theme options have been reset successfully.', 'cryout') ?></p>
			</div> <?php
		}

		// Migration results
		if (FALSE !== ($migrate_result = get_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_migrate_result_transient' ) ) ) {
			delete_transient( '_cryout_' . _CRYOUT_THEME_SLUG . '_migrate_result_transient' );
			if ( $migrate_result == 'success') { ?>
				<div class="updated notice fade is-dismissible">
					<p><?php _e('Theme options have been migrated successfully.', 'cryout') ?></p>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.') ?></span></button>
				</div> <?php
			} elseif ($migrate_result == 'fail') { ?>
				<div class="notice-error notice error fade">
					<p><?php _e('Theme options migration has failed or options already exist.', 'cryout') ?></p>
				</div> <?php
			}
		} ?>

		<h2 title="<?php printf( __('%s Theme', 'cryout'), _CRYOUT_THEME_LABEL ) ?>">&nbsp;</h2>

		<div id="main-page"><!-- Admin wrap page -->

			<div id="admin-header">
				<div id="admin-screenshot">
					<img src="<?php echo wp_get_theme( wp_get_theme()->Template )->get_screenshot(); ?>">
				</div>
				<div id="admin-title">
					<h2>Thank you for purchasing <?php echo cryout_sanitize_tnl(_CRYOUT_THEME_NAME); ?>!</h2>
					<div id="admin-version">
					Version <?php echo _CRYOUT_THEME_VERSION; ?> |
						<a href="https://www.cryoutcreations.eu/wordpress-themes/<?php echo _CRYOUT_THEME_SLUG ?>" target="_blank"><?php _e( 'Theme page', 'cryout' ) ?></a> |
						<a href="https://www.cryoutcreations.eu/priority-support" target="_blank"><?php _e( 'Priority Support', 'cryout' ) ?></a>
						<br><?php do_action( 'cryout_admin_version' ); ?>
					</div>


					<div id="admin-description">
						<p> Congratulations! You've made one hell of a purchase. On this page you will find all the information you need for taking full advantage of your new favorite theme. </p>
							<p>So don't let us get in the way. Get to it and remember to have fun! </p>
					</div>

				</div>
			</div>

			<div id="cryout_appearance_meta" class="admin-tabs">
				<div id="cryout-tabs">
				  <ul class="nav nav-tabs" role="tablist">
					<li role="presentation" id="tab-welcome" class="active"><a href="#welcome" aria-controls="welcome" role="tab" data-toggle="tab"><?php _e('Welcome','cryout') ?></a></li>
					<li role="presentation" id="tab-plugins"><a href="#plugins" aria-controls="plugins" role="tab" data-toggle="tab"><?php _e('Plugins','cryout') ?></a></li>
					<li role="presentation" id="tab-options"><a href="#options" aria-controls="options" role="tab" data-toggle="tab"><?php _e('Theme Options','cryout') ?></a></li>
					<li role="presentation" id="tab-migrate"><a href="#migrate" aria-controls="migrate" role="tab" data-toggle="tab"><?php _e('Migrate','cryout') ?></a></li>
					<li role="presentation" id="tab-license"><a href="#license" aria-controls="license" role="tab" data-toggle="tab"><?php _e('License','cryout') ?></a></li>
					<li role="presentation" id="tab-changelog"><a href="#changelog" aria-controls="changelog" role="tab" data-toggle="tab"><?php _e('Changelog','cryout') ?></a></li>
				  </ul>

			  	<!-- Tab panes -->
				<div role="tabpanel" class="tab-pane active" id="welcome">

					<div id="description">
						<h3>Welcome</h3>
						<p><?php echo ucwords( cryout_sanitize_tnp(_CRYOUT_THEME_NAME) ); ?> is already a feature heavy theme and <?php echo cryout_sanitize_tnl(_CRYOUT_THEME_NAME); ?> adds even more bells and whistles which will certainly come in handy. </p>
						<p>Here are some of the most important features you can now take advantage of:</p><br>
						<div id="description-features">
							<div class="description-panel">
								<h3>Landing Page</h3>
								<ul>
									<li>Independent <strong>magazine layout option for the landing page</strong> posts</li>
									<li>Extra filtering options for landing page posts (category, number)</li>
									<li>A <strong>second icon block</strong> section</li>
									<li>Up to <strong>9 icons</strong> in each block section with icons per row option</li>
									<li>A <strong>third featured boxes</strong> section</li>
									<li><strong>2 extra featured box layout/design</strong> options</li>
									<li><strong>4 extra text area</strong> sections</li>
									<li>Individual <strong>background color options for all landing page sections</strong></li>
									<li><strong>Custom post type for the landing page featured elements</strong> (icon blocks, featured boxes, textareas)</li>
									<li><strong>Drag & Drop interface for landing page elements ordering</strong></li>
								</ul>
							</div>
							<div class="description-panel">
								<h3>Content</h3>
								<ul>
									<li><strong>Advanced shortcodes</strong>: Grid, Tabs, Accordion, Alerts, Buttons, Labels, Well, Lead, Pullquote, Tooltip, Clear, Divider, Map, Icons, Portfolio!</li>
									<li><strong>Advanced Widgets</strong>: About us, Contact with address and map, Socials, Tabbed Featured Content (Recent posts, Popular posts, Comments, Tags), Featured Posts, Jetpack Portfolio</li>
									<li><strong>Page templates</strong>: Portfolio, Contact, About us, Blog with intro, Blank</li>
									<li><strong>Related posts</strong> block for single post view</li>
									<li>WYSWYG editor support for category description</li>
									<li><strong>Individual layout option for posts</strong></li>
									<li>Configurable <strong>color, background image and position for individual posts and pages</strong></li>
									<li><strong>Hide/show individual elements for individual posts and pages</strong> (header, main navigation, breadcrumbs, footer widgets, footer)</li>
									<li><strong>Portfolios</strong> (built-in support for Jetpack Portfolios)</li>
								</ul>
							</div>

							<div class="description-panel">
								<h3> General</h3>
								<ul>
									<li>Configurable <strong>responsiveness breakpoint</strong> for menu</li>
									<li>Bundled <strong>custom post type plugin for the landing page</strong></li>
									<li><strong>3 JavaScript (unfiltered) input fields</strong> for ad, tracking or analytics scripts</li>
								</ul>
								<br>
								<h3>Admin Features</h3>
								<ul>
									<li>XML <strong>Export/Import option</strong></li>
									<li><strong>Options migration</strong> support</li>
									<li>Quick install/activate addon plugins</li>
								</ul>
							</div>
						</div>
					</div>

				</div><!--welcome-->
				<div role="tabpanel" class="tab-pane" id="plugins" style="display: none;">

					<?php require_once( get_template_directory() . '/plus/plus-extras/admin-page-plugins.php' ) ?>

				</div><!--plugins-->
				<div role="tabpanel" class="tab-pane" id="options" style="display: none;">

					<div id="cryout-export">

					<h3 class="hndle"><?php _e( 'Customization', 'cryout' ); ?></h3>
					<p> <?php _e( 'The theme\'s over 250 options are located in the Customizer. Go check them out.', 'cryout') ;?></p>
					<a class="button button-primary" href="customize.php" id="customizer"> <?php echo __( 'Customize', 'cryout' ) . ' ' . cryout_sanitize_tnl(_CRYOUT_THEME_NAME); ?> </a>

					<br><br>
					<h3 class="hndle"><?php _e( 'Options Management', 'cryout' ); ?></h3>
					<p> <?php _e( 'Quickly migrate theme options between sites, create and restore backups or reset everything to the theme\'s defaults.', 'cryout') ;?></p>
					<div class="panel-wrap inside">

						<form class="export-button" action="<?php echo admin_url( _CRYOUT_THEME_PAGE_URL ) ?>" method="post">
							<?php wp_nonce_field('cryout-export', 'cryout-export'); ?>
							<input type="hidden" name="cryout_export" value="true" />
							<input type="submit" class="button" value="<?php _e('Export Theme Options', 'cryout'); ?>" />
						</form>

						<form class="export-button" action="<?php echo admin_url( _CRYOUT_THEME_PAGE_URL ) ?>" method="post">
							<input type="hidden" name="cryout_import" value="true" />
							<input type="submit" class="button" value="<?php _e('Import Theme Options', 'cryout'); ?>" />
						</form>

						<form class="export-button" action="<?php echo admin_url( _CRYOUT_THEME_PAGE_URL ) ?>#options" method="post">
							<input type="hidden" name="cryout_reset_defaults" value="true" />
							<input type="submit" class="button" id="cryout_reset_defaults" value="<?php _e( 'Reset to Defaults', 'cryout' ); ?>" onclick=""/>
						</form>

					</div><!-- inside -->

					</div><!--cryout-export-->

				</div><!--options-->
				<div role="tabpanel" class="tab-pane" id="migrate" style="display: none;">

					<h3 class="hndle"><?php _e( 'Options Migration', 'cryout' ); ?></h3>

					<?php require_once( get_template_directory() . '/plus/plus-extras/admin-page-migration.php' ) ?>

				</div><!--migrate-->
				<div role="tabpanel" class="tab-pane" id="license" style="display: none;">

					<h3 class="hndle"><?php _e( 'License Management', 'cryout' ); ?></h3>

					<?php require_once( get_template_directory() . '/plus/plus-extras/admin-page-license.php' ) ?>

				</div><!--license-->
				<div role="tabpanel" class="tab-pane" id="changelog" style="display: none;">

					<?php

					$request = wp_remote_get( trailingslashit( get_template_directory_uri() ) . 'readme.txt' );
					$response = wp_remote_retrieve_body( $request );
					$response_code = wp_remote_retrieve_response_code( $request );
					if (200 == $response_code) {
						$response = substr( $response, strpos( $response, '== Changelog ==') + 16, strlen($response));
						?>
						<textarea readonly="readonly">
							<?php echo $response ?>
						</textarea>
						<?php
					} else {
						_e( 'Unable to display the changelog.', 'cryout' );
					} ?>

				</div><!--changelog-->

			</div><!--cryout-tabs-->
			</div>

		<noscript> <strong><?php _e('This page requires JavaScript to function.', 'cryout') ?></strong> </noscript>
		<span class="theme-thanks"> <?php _e('Thank you for choosing our creations!', 'cryout') ?></span>

		</div><!-- main -->
	</div><!-- wrap -->

	<?php
	} // theme_page()

} // Plus_Admin_Page class

$cryout_plus_admin_page = new Plus_Admin_Page;


// FIN
