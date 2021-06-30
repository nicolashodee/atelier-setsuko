<?php
/*
Plugin Name: WPUM Mailchimp
Plugin URI:  https://wpusermanager.com
Description: Addon for WP User Manager, provides simple and flexible Mailchimp integration.
Version:     2.0.5
Author:      WP User Manager
Author URI:  https://wpusermanager.com/
License:     GPLv3+
Text Domain: wpum-mailchimp
Domain Path: /languages
*/

/**
 * WPUM Mailchimp.
 *
 * Copyright (c) 2018 Alessandro Tesoro
 *
 * WPUM Mailchimp. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPUM Mailchimp. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author     Alessandro Tesoro
 * @version    2.0.0
 * @copyright  (c) 2018 Alessandro Tesoro
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 * @package    wpum-mailchimp
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPUM_Mailchimp' ) ) :

	/**
	 * Main WPUM_Mailchimp class.
	 */
	final class WPUM_Mailchimp {

		/**
		 * WPUM_Mailchimp Instance.
		 *
		 * @var WPUM_Mailchimp the WPUM Instance
		 */
		protected static $_instance;

		protected $version = '2.0.5';

		/**
		 * Main Instance.
		 *
		 * Ensures that only one instance of WPUMCHIMP exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @return WPUM_Mailchimp
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
			add_action( 'after_wpum_init', array( $this, 'init' ) );
		}

		/**
		 * Get things up and running.
		 */
		public function init() {
			if ( ! $this->autoload() ) {
				return;
			}

			// Verify the plugin meets WP and PHP requirements.
			if ( ! $this->plugin_can_run() ) {
				return;
			}

			// Verify the addon can run first. If not, disable the addon automagically.
			if ( ! $this->addon_can_run() ) {
				return;
			}

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
			require_once WPUMCHIMP_PLUGIN_DIR . 'includes/upgrade.php';
			require_once WPUMCHIMP_PLUGIN_DIR . 'includes/functions.php';
			require_once WPUMCHIMP_PLUGIN_DIR . 'includes/registration.php';
			require_once WPUMCHIMP_PLUGIN_DIR . 'includes/user-update.php';
			require_once WPUMCHIMP_PLUGIN_DIR . 'includes/account-tab.php';
			require_once WPUMCHIMP_PLUGIN_DIR . 'includes/class-wpum-mailchimp-settings.php';
			if ( is_admin() ) {
				require_once WPUMCHIMP_PLUGIN_DIR . 'includes/sync.php';
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
			if ( ! defined( 'WPUMCHIMP_VERSION' ) ) {
				define( 'WPUMCHIMP_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPUMCHIMP_PLUGIN_DIR' ) ) {
				define( 'WPUMCHIMP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPUMCHIMP_PLUGIN_URL' ) ) {
				define( 'WPUMCHIMP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File.
			if ( ! defined( 'WPUMCHIMP_PLUGIN_FILE' ) ) {
				define( 'WPUMCHIMP_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Slug.
			if ( ! defined( 'WPUMCHIMP_SLUG' ) ) {
				define( 'WPUMCHIMP_SLUG', plugin_basename( __FILE__ ) );
			}

		}

		/**
		 * Hook in our actions and filters.
		 *
		 * @return void
		 */
		private function init_hooks() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 0 );
			add_action( 'admin_init', 'wpummc_plugin_upgrade' );
			add_action( 'admin_init', function () {
				$message = get_option( 'wpum_mailchimp_upgrade_message' );
				if ( $message ) {
					$message = '<strong>WP User Manager Mailchimp</strong> &mdash; ' . $message;
					if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'wpum-mailchimp' ) {
						$message .= sprintf( ' <a href="%s">%s &#8594;</a>', admin_url( 'users.php?page=wpum-mailchimp' ), __( 'View settings', 'wpum-mailchimp' ) );
					}

					WPUM()->notices->register_notice( 'wpummc_upgrade', 'warning', $message, [ 'dismissible' => false ] );
				}
			} );
		}

		/**
		 * Load plugin textdomain.
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wpum-mailchimp', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Hook into the main plugin and register the addon.
		 *
		 * @return void
		 */
		public function license() {
			$wpumuv_license = new WPUM_License(
				__FILE__,
				'MailChimp',
				'15793',
				WPUMCHIMP_VERSION,
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
				'title' => 'WPUM Mailchimp',
				'php'   => '5.5',
				'wp'    => '4.7',
				'file'  => __FILE__,
			) );

			return $requirements_check->passes();
		}

		/**
		 * Verify that the current environment is supported.
		 *
		 * @return boolean
		 */
		private function addon_can_run() {
			$requirements_check = new WPUM_Extension_Activation(
				array(
					'title'        => 'WPUM Mailchimp',
					'wpum_version' => '2.0.0',
					'file'         => __FILE__,
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
				<p><?php printf( '<strong>WP User Manager</strong> &mdash; The %s addon plugin cannot be activated as it is missing the vendor directory.', esc_html( 'Mailchimp' ) ); ?></p>
			</div>
			<?php
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

	}

endif;

/**
 * Start the addon.
 *
 * @return object
 */
function WPUMCHIMP() {
	return WPUM_Mailchimp::instance();
}

WPUMCHIMP();
