<?php
/*
Plugin Name: WPUM Private Content
Plugin URI:  https://wpusermanager.com
Description: Private Content addon for WP User Manager
Version:     1.0
Author:      WP User Manager
Author URI:  https://wpusermanager.com/
License:     GPLv3+
Text Domain: wpum-content-restriction
Domain Path: /languages
*/

/**
 * WPUM Private Content
 *
 * Copyright (c) 2020 WP User Manager
 *
 * WPUM Private Content. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPUM Private Content. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author     WP User Manager
 * @version    1.0.0
 * @copyright  (c) 2020 WP User Manager
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 * @package    wpum-private-content
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPUM_Private_Content' ) ) :

	/**
	 * Main WPUM_Private_Content class.
	 */
	final class WPUM_Private_Content {

		/**
		 * @var string
		 */
		protected $version = '1.0';

		/**
		 * @var WPUM_Private_Content
		 */
		protected static $_instance;

		/**
		 * @return WPUM_Private_Content
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
			add_action( 'before_wpum_init', array( $this, 'init' ) );
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
			$this->setup_constants();
			$this->includes();
			$this->license();
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
			require_once WPUMPR_PLUGIN_DIR . 'includes/admin.php';
			require_once WPUMPR_PLUGIN_DIR . 'includes/profile.php';
			require_once WPUMPR_PLUGIN_DIR . 'includes/shortcode.php';
			require_once WPUMPR_PLUGIN_DIR . 'includes/class-wpum-shortcode-private-content.php';
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
			if ( ! defined( 'WPUMPR_VERSION' ) ) {
				define( 'WPUMPR_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPUMPR_PLUGIN_DIR' ) ) {
				define( 'WPUMPR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPUMPR_PLUGIN_URL' ) ) {
				define( 'WPUMPR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPUMPR_PLUGIN_FILE' ) ) {
				define( 'WPUMPR_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Slug.
			if ( ! defined( 'WPUMPR_SLUG' ) ) {
				define( 'WPUMPR_SLUG', plugin_basename( __FILE__ ) );
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
			load_plugin_textdomain( 'wpum-private-content', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Hook into the main plugin and register the addon.
		 *
		 * @return void
		 */
		public function license() {
			new WPUM_License(
				__FILE__,
				'Private Content',
				'39245',
				WPUMPR_VERSION,
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
				'title' => 'WPUM Private Content',
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
				<p><?php printf( '<strong>WP User Manager</strong> &mdash; The %s addon plugin cannot be activated as it is missing the vendor directory.', esc_html( 'Private Content' ) ); ?></p>
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
			$requirements_check = new WPUM_Extension_Activation(
				array(
					'title'        => 'WPUM Private Content',
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
 * @return WPUM_Private_Content
 */
function WPUMPR() {
	return WPUM_Private_Content::instance();
}

WPUMPR();
