<?php
/**
 * WPUM Extension activation.
 *
 * Copyright (c) 2018 Alessandro Tesoro
 *
 * WPUM Extension activation. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * WPUM Extension activation. is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author     Alessandro Tesoro
 * @version    1.0.0
 * @copyright  (c) 2018 Alessandro Tesoro
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE
 * @package    wpum-extension-activation
 */

class WPUM_Extension_Activation {

    /**
	 * Default name of the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
    private $title = '';

    /**
     * The version of WPUM that is required.
     *
     * @var string
     */
    private $wpum_version = '';

    /**
	 * Path to the main plugin file.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
    private $file;

    /**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args {
	 *     An array of arguments to overwrite the default requirements.
	 *
	 *     @type string $title Name of the plugin.
	 *     @type string $wpum_version Minimum required PHP version.
	 *     @type string $file  Path to the main plugin file.
	 * }
	 */
	public function __construct( $args ) {
		foreach ( array( 'title', 'wpum_version', 'file' ) as $setting ) {
			if ( isset( $args[ $setting ] ) ) {
				$this->$setting = $args[ $setting ];
			}
		}
    }

    /**
	 * Check if the install passes the requirements.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if the install passes the requirements, false otherwise.
	 */
	public function passes() {
		$passes = $this->wpum_passes();
		if ( ! $passes ) {
			//add_action( 'admin_notices', array( $this, 'deactivate' ) );
		}
		return $passes;
    }

    /**
     * Verify the installed version of WPUM is the one required by the addon.
     *
     * @return boolean
     */
    protected function wpum_passes() {
	    if ( ! file_exists( WP_PLUGIN_DIR . '/wp-user-manager/wp-user-manager.php' ) ) {
		    add_action( 'admin_notices', array( $this, 'wpum_not_installed_notice' ) );

		    return false;
	    }

	    if( ! defined( 'WPUM_VERSION' ) ) {
		    add_action( 'admin_notices', array( $this, 'wpum_not_activated_notice' ) );

		    return false;
	    }

        if ( self::_wpum_at_least( $this->wpum_version ) ) {
			return true;
		}

        add_action( 'admin_notices', array( $this, 'wp_version_notice' ) );

        return false;

    }

	/**
	 * Detect installed version of WPUM.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
    protected static function _wpum_at_least( $version ) {
        if( ! defined( 'WPUM_VERSION' ) ) {
            return false;
        }

        return version_compare( WPUM_VERSION, $version, '>=' );
    }

    /**
	 * Deactivates the plugin again.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function deactivate() {
		if ( null !== $this->file ) {
			deactivate_plugins( plugin_basename( $this->file ) );
		}
	}

    /**
	 * Show the WordPress version notice.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wp_version_notice() {
		$basename   = plugin_basename( WPUM_PLUGIN_FILE );
		$update_url = wp_nonce_url( admin_url() . 'update.php?action=upgrade-plugin&plugin=' . urlencode( $basename ), 'upgrade-plugin_' . $basename );
		?>
		<div class="error">
			<p><?php printf( '<strong>WP User Manager</strong> &mdash; %s addon has been deactivated as it cannot run on WP User Manager versions older than %s. Please <a href="%s">update</a> WP User Manager.', esc_html( $this->title ), $this->wpum_version, $update_url ); ?></p>
		</div>
		<?php
	}

	/**
	 * Show the notice to install WPUM
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wpum_not_installed_notice() {
		$slug   = 'wp-user-manager';
		$update_url = wp_nonce_url( admin_url() . 'update.php?action=install-plugin&plugin=' . urlencode( $slug ), 'install-plugin_' . $slug );
		?>
		<div class="error">
			<p><?php printf( '<strong>WP User Manager</strong> &mdash; %s addon has been deactivated as it requires the core WP User Manager plugin. Please <a href="%s">install</a> WP User Manager.', esc_html( $this->title ), $update_url ); ?></p>
		</div>
		<?php
	}

	/**
	 * Show the notice to install WPUM
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wpum_not_activated_notice() {
		$basename   = 'wp-user-manager/wp-user-manager.php';
		$update_url = wp_nonce_url( admin_url() . 'update.php?action=activate-plugin&plugin=' . urlencode( $basename ), 'activate-plugin_' . $basename );
		?>
		<div class="error">
			<p><?php printf( '<strong>WP User Manager</strong> &mdash; %s addon has been deactivated as it requires the core WP User Manager plugin to be activated. Please <a href="%s">activate</a> WP User Manager.', esc_html( $this->title ), $update_url ); ?></p>
		</div>
		<?php
	}

}