<?php
/*
Plugin Name: WPUM Groups
Plugin URI:  https://wpusermanager.com
Description: Groups addon for WP User Manager
Version:     1.1.1
Author:      WP User Manager
Author URI:  https://wpusermanager.com/
License:     GPLv3+
Text Domain: wpum-groups
Domain Path: /languages
*/

/**
 * WPUM Groups
 *
 * Copyright (c) 2020 WP User Manager
 *
 * WPUM Groups. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPUM Groups. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author     WP User Manager
 * @version    1.0.0
 * @copyright  (c) 2020 WP User Manager
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 * @package    wpum-groups
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPUM_Groups' ) ) :

	/**
	 * Main WPUM_Groups class.
	 */
	final class WPUM_Groups {

		/**
		 * @var string
		 */
		protected $version = '1.1.1';

		/**
		 * WPUMRF Instance.
		 *
		 * @var WPUM_Groups the WPUM Instance
		 */
		protected static $_instance;

		/**
		 * @var WPUM_Group_Editor the Group CPT instance
		 */
		protected $editor;

		/**
		 * @var WPUMG_Template_Loader
		 */
		public $templates;

		/**
		 * Main WPUMRF Instance.
		 *
		 * Ensures that only one instance of WPUMRF exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @return WPUM_Groups
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->run();
			}

			return self::$_instance;
		}

		/**
		 * Only load the addon on the WPUM core hook, ensuring the plugin is active.
		 */
		public function run() {
			$this->setup_constants();
			$this->includes();

			if ( class_exists( 'WPUM_DB_Table' ) ) {
				$this->setup_database_tables();
			}

			$this->editor = new WPUM_Group_Editor();

			add_action( 'before_wpum_init', array( $this, 'init' ) );
			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
		}

		/**
		 * Get things up and running.
		 */
		public function init() {
			if ( ! $this->autoload() ) {
				return;
			}

			// Verify the plugin meets WP and PHP requirements.
			$this->plugin_can_run();

			// Verify the addon can run first. If not, disable the addon automagically.
			$this->addon_can_run();

			// Plugin is activated now proceed.
			$this->license();

			$this->templates = new WPUMG_Template_Loader();

			require_once WPUMGP_PLUGIN_DIR . 'includes/class-wpum-groups-directory-shortcode.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/shortcode.php';

			new WPUM_Shortcode_Group;
			new WPUM_Single_Group_Page();
			new WPUM_Form_New_Group_Page();
			new WPUM_Bulk_Assign_Users_To_Group();

			$this->editor->init();

			$this->init_hooks();
		}

		/**
		 * Autoload composer and other required classes.
		 *
		 * @return bool
		 */
		protected function autoload() {
			if ( ! file_exists( __DIR__ . '/vendor' ) || ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
				add_action( 'admin_notices', array( $this, 'vendor_failed_notice' ) );

				return false;
			}

			return require __DIR__ . '/vendor/autoload.php';
		}

		/**
		 * Load required files for the addon.
		 *
		 * @return void
		 */
		public function includes() {

			require_once WPUMGP_PLUGIN_DIR . 'includes/class-wpum-single-group-page.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/class-wpum-new-group-page.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/permalinks.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/actions.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/profile.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/emails.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/content-restriction.php';

			require_once WPUMGP_PLUGIN_DIR . 'admin/class-wpumg-template-loader.php';

			require_once WPUMGP_PLUGIN_DIR . 'includes/wpumg-database/class-wpumg-db-group-users.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/wpumg-database/class-wpumg-db-table-group-users.php';

			require_once WPUMGP_PLUGIN_DIR . 'includes/settings.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/assets.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/functions.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/class-wpum-groups-editor.php';
			require_once WPUMGP_PLUGIN_DIR . 'includes/class-wpum-bulk-assign-users-to-group.php';

			require_once WPUMGP_PLUGIN_DIR . 'includes/install.php';
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'WPUMGP_VERSION' ) ) {
				define( 'WPUMGP_VERSION', $this->version );
			}

			// Plugin emails version.
			if ( ! defined( 'WPUMGP_EMAILS_VERSION' ) ) {
				define( 'WPUMGP_EMAILS_VERSION', '1.0' );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPUMGP_PLUGIN_DIR' ) ) {
				define( 'WPUMGP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPUMGP_PLUGIN_URL' ) ) {
				define( 'WPUMGP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPUMGP_PLUGIN_FILE' ) ) {
				define( 'WPUMGP_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Slug.
			if ( ! defined( 'WPUMGP_SLUG' ) ) {
				define( 'WPUMGP_SLUG', plugin_basename( __FILE__ ) );
			}
		}

		/**
		 * Hook in our actions and filters.
		 *
		 * @return void
		 */
		private function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 0 );
			add_action( 'admin_init', 'wpumgp_install_emails' );
		}

		/**
		 * Flush rewrite rules upon activation
		 */
		public function plugin_activation() {
			$this->editor->register();
			wpum_group_install();
			flush_rewrite_rules();
		}

		/**
		 * Load plugin textdomain.
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wpum-groups', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Hook into the main plugin and register the addon.
		 *
		 * @return void
		 */
		public function license() {
			$wpumuv_license = new WPUM_License(
				__FILE__,
				'Groups',
				'39079',
				WPUMGP_VERSION,
				'WP User Manager'
			);
		}

		/**
		 * Verify the plugin meets the WP and php requirements.
		 *
		 * @return boolean
		 */
		public function plugin_can_run() {
			$requirements_check = new WP_Requirements_Check( array(
				'title' => 'WPUM Groups',
				'php'   => '5.5',
				'wp'    => '4.7',
				'file'  => __FILE__,
			) );

			return $requirements_check->passes();
		}

		/**
		 * Show the Vendor build issue notice.
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function vendor_failed_notice() { ?>
			<div class="error">
				<p><?php printf( '<strong>WP User Manager</strong> &mdash; The %s addon plugin cannot be activated as it is missing the vendor directory.', esc_html( 'Groups' ) ); ?></p>
			</div>
			<?php
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		/**
		 * Setup all of the custom database tables
		 *
		 * This method invokes all of the classes for each custom database table,
		 * and returns them in an array for easier testing.
		 *
		 * In a normal request, this method is called extremely early in WPUM's load
		 * order, to ensure these tables have been created & upgraded before any
		 * other utility occurs on them (query, migration, etc...)
		 *
		 * @access public
		 * @return array
		 */
		private function setup_database_tables() {
			return array(
				'groupusers' => new WPUMG_DB_Table_Group_Users(),
			);
		}


		/**
		 * Verify that the current environment is supported.
		 *
		 * @return boolean
		 */
		private function addon_can_run() {
			$requirements_check = new WPUM_Extension_Activation(
				array(
					'title'        => 'WPUM Groups',
					'wpum_version' => '2.3.5',
					'file'         => __FILE__,
				)
			);

			return $requirements_check->passes();
		}

	}

endif;

/**
 * Start the addon.
 *
 * @return object
 */
function WPUMGP() {
	return WPUM_Groups::instance();
}

WPUMGP();
