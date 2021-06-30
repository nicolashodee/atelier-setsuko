<?php
/*
Plugin Name: WPUM Custom Fields
Plugin URI:  https://wpusermanager.com
Description: Addon for WP User Manager, lets you visually create and manage custom fields for your users.
Version:     2.3.2
Author:      WP User Manager
Author URI:  https://wpusermanager.com/
License:     GPLv3+
Text Domain: wpum-custom-fields
Domain Path: /languages
*/

/**
 * WPUM Custom Fields.
 *
 * Copyright (c) 2018 Alessandro Tesoro
 *
 * WPUM Custom Fields. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPUM Custom Fields. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author     Alessandro Tesoro
 * @version    2.0.0
 * @copyright  (c) 2018 Alessandro Tesoro
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 * @package    wpum-custom-fields
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPUM_Custom_Fields' ) ) :

	/**
	 * Main WPUM_Custom_Fields class.
	 */
	final class WPUM_Custom_Fields {

		/**
		 * WPUMCF Instance.
		 *
		 * @var WPUM_Custom_Fields the WPUM Instance
		 */
		protected static $_instance;

		/**
		 * @var string
		 */
		protected $version = '2.3.2';

		/**
		 * Main WPUMCF Instance.
		 *
		 * Ensures that only one instance of WPUMCF exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @return WPUM_Custom_Fields
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
			require_once WPUMCF_PLUGIN_DIR . 'includes/register-fields.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/registration-form.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/account-form.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/directories.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/taxonomy-field.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/user-field.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/functions.php';
			require_once WPUMCF_PLUGIN_DIR . 'includes/frontend-hooks.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				require_once WPUMCF_PLUGIN_DIR . 'includes/admin-hooks.php';
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
			if ( ! defined( 'WPUMCF_VERSION' ) ) {
				define( 'WPUMCF_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPUMCF_PLUGIN_DIR' ) ) {
				define( 'WPUMCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPUMCF_PLUGIN_URL' ) ) {
				define( 'WPUMCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPUMCF_PLUGIN_FILE' ) ) {
				define( 'WPUMCF_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Slug.
			if ( ! defined( 'WPUMCF_SLUG' ) ) {
				define( 'WPUMCF_SLUG', plugin_basename( __FILE__ ) );
			}

		}

		/**
		 * Hook in our actions and filters.
		 *
		 * @return void
		 */
		private function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 0 );
			add_filter( 'wpum_fields_editor_has_custom_fields_addon', '__return_true' );
		}

		/**
		 * Load plugin textdomain.
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wpum-custom-fields', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Hook into the main plugin and register the addon.
		 *
		 * @return void
		 */
		public function license() {
			$wpumuv_license = new WPUM_License(
				__FILE__,
				'Custom Fields',
				'15797',
				WPUMCF_VERSION,
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
				'title' => 'WPUM Custom Fields',
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
				<p><?php printf( '<strong>WP User Manager</strong> &mdash; The %s addon plugin cannot be activated as it is missing the vendor directory.', esc_html( 'Custom Fields' ) ); ?></p>
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
					'title'        => 'WPUM Custom Fields',
					'wpum_version' => '2.6',
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
function WPUMCF() {
	return WPUM_Custom_Fields::instance();
}

WPUMCF();
