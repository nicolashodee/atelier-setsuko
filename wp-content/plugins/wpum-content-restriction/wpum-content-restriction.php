<?php
/*
Plugin Name: WPUM Content Restriction
Plugin URI:  https://wpusermanager.com
Description: Content Restriction addon for WP User Manager
Version:     1.1.1
Author:      WP User Manager
Author URI:  https://wpusermanager.com/
License:     GPLv3+
Text Domain: wpum-content-restriction
Domain Path: /languages
*/

/**
 * WPUM Content Restriction
 *
 * Copyright (c) 2020 WP User Manager
 *
 * WPUM Security. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPUM Security. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author         WP User Manager
 * @version        1.0.0
 * @copyright  (c) 2020 WP User Manager
 * @license        http://www.gnu.org/licenses/gpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 * @package        wpum-content-restriction
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPUM_Content_Restriction' ) ) :

	/**
	 * Main WPUM_Content_Restriction class.
	 */
	final class WPUM_Content_Restriction {

		/**
		 * @var string
		 */
		protected $version = '1.1.1';

		/**
		 * @var WPUM_Content_Restriction
		 */
		protected static $_instance;

		/**
		 * @return WPUM_Content_Restriction
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

			add_action( 'before_wpum_init', array( $this, 'init' ) );

			new WPUMCR_Access();
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
			$this->init_hooks();

			new WPUMCR_Restriction_Manager;

			( new WPUMCR_Post_Types() )->init();
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
			require_once WPUMCR_PLUGIN_DIR . 'includes/class-wpumcr-access.php';
			require_once WPUMCR_PLUGIN_DIR . 'includes/class-wpumcr-post.php';
			require_once WPUMCR_PLUGIN_DIR . 'includes/class-wpumcr-restriction-manager.php';
			require_once WPUMCR_PLUGIN_DIR . 'includes/class-wpumcr-post-types.php';
			require_once WPUMCR_PLUGIN_DIR . 'includes/functions.php';
			require_once WPUMCR_PLUGIN_DIR . 'includes/settings.php';
			require_once WPUMCR_PLUGIN_DIR . 'includes/woocommerce.php';
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @return void
		 * @since  1.0.0
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'WPUMCR_VERSION' ) ) {
				define( 'WPUMCR_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPUMCR_PLUGIN_DIR' ) ) {
				define( 'WPUMCR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPUMCR_PLUGIN_URL' ) ) {
				define( 'WPUMCR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPUMCR_PLUGIN_FILE' ) ) {
				define( 'WPUMCR_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Slug.
			if ( ! defined( 'WPUMCR_SLUG' ) ) {
				define( 'WPUMCR_SLUG', plugin_basename( __FILE__ ) );
			}
		}

		/**
		 * Hook in our actions and filters.
		 *
		 * @return void
		 */
		private function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 0 );
		}

		/**
		 * Load plugin textdomain.
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wpum-content-restriction', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Hook into the main plugin and register the addon.
		 *
		 * @return void
		 */
		public function license() {
			new WPUM_License( __FILE__, 'Content Restriction', '39223', WPUMCR_VERSION, 'WP User Manager' );
		}

		/**
		 * Verify the plugin meets the WP and php requirements.
		 *
		 * @return boolean
		 */
		public function plugin_can_run() {
			$requirements_check = new WP_Requirements_Check( array(
				'title' => 'WPUM Content Restriction',
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
				<p><?php printf( '<strong>WP User Manager</strong> &mdash; The %s addon plugin cannot be activated as it is missing the vendor directory.', esc_html( 'Content Restriction' ) ); ?></p>
			</div>
			<?php
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		/**
		 * Verify that the current environment is supported.
		 *
		 * @return boolean
		 */
		private function addon_can_run() {
			$requirements_check = new WPUM_Extension_Activation( array(
					'title'        => 'WPUM Content Restriction',
					'wpum_version' => '2.3.5',
					'file'         => __FILE__,
				) );

			return $requirements_check->passes();
		}

	}

endif;

/**
 * Start the addon.
 *
 * @return WPUM_Content_Restriction
 */
function WPUMCR() {
	return WPUM_Content_Restriction::instance();
}

WPUMCR();
