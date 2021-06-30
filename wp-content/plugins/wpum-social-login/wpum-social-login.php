<?php
/**
 * Plugin Name: WPUM Social Login
 * Plugin URI:  http://wpusermanager.com
 * Description: Addon for WP User Manager, provides simple and flexible login and registration through social networks.
 * Version:     2.0.7
 * Author:      WP User Manager
 * Author URI:  http://wpusermanager.com
 * Text Domain: wpum-social-login
 * Domain Path: /languages
 */

/**
* Copyright (c) 2018 Alessandro Tesoro
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License, version 2 or, at
* your discretion, any later version, as published by the Free
* Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPUM_Social_Login' ) ) :
	/**
	 * Main WPUM_Social_Login class.
	 */
	final class WPUM_Social_Login {

		/**
		 * WPUMSL Instance.
		 *
		 * @var WPUM_Social_Login the WPUM Instance
		 */
		protected static $_instance;

		/**
		 * @var string
		 */
		protected $version = '2.0.7';

		/**
		 * Main WPUMSL Instance.
		 *
		 * Ensures that only one instance of WPUMSL exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @return WPUM_Social_Login
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();	self::$_instance->run();
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
			require_once WPUMSL_PLUGIN_DIR . 'includes/settings.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/actions.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/abstract-wpumsl-integration.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/class-wpumsl-facebook.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/class-wpumsl-google.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/class-wpumsl-linkedin.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/class-wpumsl-instagram.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/class-wpumsl-twitter.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/functions.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/buttons.php';
			require_once WPUMSL_PLUGIN_DIR . 'includes/class-plugin-updates.php';

			if ( is_admin() ) {
				require_once WPUMSL_PLUGIN_DIR . 'includes/class-wpumsl-shortcode.php';
			}
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
			if ( ! defined( 'WPUMSL_VERSION' ) ) {
				define( 'WPUMSL_VERSION', $this->version );
			}
			// Plugin Folder Path.
			if ( ! defined( 'WPUMSL_PLUGIN_DIR' ) ) {
				define( 'WPUMSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}
			// Plugin Folder URL.
			if ( ! defined( 'WPUMSL_PLUGIN_URL' ) ) {
				define( 'WPUMSL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}
			// Plugin Root File.
			if ( ! defined( 'WPUMSL_PLUGIN_FILE' ) ) {
				define( 'WPUMSL_PLUGIN_FILE', __FILE__ );
			}
			// Plugin Slug.
			if ( ! defined( 'WPUMSL_SLUG' ) ) {
				define( 'WPUMSL_SLUG', plugin_basename( __FILE__ ) );
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
			load_plugin_textdomain( 'wpum-social-login', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Hook into the main plugin and register the addon.
		 *
		 * @return void
		 */
		public function license() {
			$wpumuv_license = new WPUM_License(
				__FILE__,
				'Social Login',
				'17978',
				WPUMSL_VERSION,
				'WP User Manager'
			);
		}

		/**
		 * Verify the plugin meets the WP and php requirements.
		 *
		 * @return boolean
		 */
		public function plugin_can_run() {
			$requirements_check = new WP_Requirements_Check(
				array(
					'title' => 'WPUM Social Login',
					'php'   => '5.5',
					'wp'    => '4.7',
					'file'  => __FILE__,
				)
			);

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
				<p><?php printf( '<strong>WP User Manager</strong> &mdash; The %s addon plugin cannot be activated as it is missing the vendor directory.', esc_html( 'Social Login' ) ); ?></p>
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
					'title'        => 'WPUM Social Login',
					'wpum_version' => '2.2',
					'file'         => __FILE__,
				) );

			return $requirements_check->passes();
		}
	}
endif;

/**
 * Start the addon.
 *
 * @return object
 */
function WPUMSL() {
	return WPUM_Social_Login::instance();
}
WPUMSL();
