<?php
/**
 * Handles upgrade routines.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2019, WP User Manager
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that handles the upgrade.
 */
class WPUMSL_Plugin_Updates {

	/**
	 * Start things.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', [ $this, 'maybe_perform_upgrade' ] );
	}

	/**
	 * Perform minor database upgrades without prompting the user.
	 */
	public function maybe_perform_upgrade() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$installed_version = get_option( 'wpumsl_version');

		$latest_version = WPUMSL_VERSION;

		if ( 0 === version_compare( $installed_version, $latest_version ) ) {
			// Latest version already installed
			return;
		}

		if ( version_compare( $installed_version, '2.0.3', '<' ) ) {
			$this->upgrade_v2_0_3();
		}

		update_option( 'wpumsl_version', $latest_version );
	}

	protected function upgrade_v2_0_3() {
		// Get default registration form
		$registration_forms = WPUM()->registration_forms->get_forms();
		$form               = false;
		foreach ( $registration_forms as $registration_form ) {
			if ( $registration_form->is_default() ) {
				$form = $registration_form;
				break;
			}
		}

		if ( ! $form ) {
			return;
		}

		$social_login_location = wpum_get_option( 'social_login_location', array() );

		$migrate_locations = array();
		foreach ( $social_login_location as $key => $location ) {
			if ( in_array( $location, array( 'before_registration', 'after_registration' ) ) ) {
				$migrate_locations[] = $location;

				unset( $social_login_location[ $key ] );
			}
		}

		if ( empty( $migrate_locations ) ) {
			return;
		}

		$form->update_meta( 'social_login_location', $migrate_locations );
		wpum_update_option( 'social_login_location', $social_login_location );
	}
}

new WPUMSL_Plugin_Updates();