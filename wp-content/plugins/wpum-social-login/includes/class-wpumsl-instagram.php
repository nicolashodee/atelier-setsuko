<?php
/**
 * Instagram login & registration integration.
 *
 * @package     wpum-social-login
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Instagram integration class.
 */
class WPUMSL_Instagram extends WPUMSL_Integration {

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->social_id = 'instagram';
		$this->client_id = wpum_get_option( 'instagram_clientid' );
		$this->secret_id = wpum_get_option( 'instagram_secret' );
		parent::__construct();
	}

	/**
	 * The function responsible to contact the social network and grab profiles information.
	 *
	 * @return void
	 */
	public function run_integration() {

		$provider = new \League\OAuth2\Client\Provider\Instagram(
			[
				'clientId'     => $this->client_id,
				'clientSecret' => $this->secret_id,
				'redirectUri'  => $this->redirect_uri,
			]
		);

		if ( ! isset( $_GET['code'] ) ) {

			$authUrl                 = $provider->getAuthorizationUrl();
			$_SESSION['oauth2state'] = $provider->getState();
			wp_redirect( $authUrl );
			exit;

		} elseif ( empty( $_GET['state'] ) || ( $_GET['state'] !== $_SESSION['oauth2state'] ) ) {

			unset( $_SESSION['oauth2state'] );
			exit( 'Invalid state' );

		} else {

			$token = $provider->getAccessToken(
				'authorization_code', [
					'code' => $_GET['code'],
				]
			);

			try {

				$owner_details = $provider->getResourceOwner( $token );
				$this->process_no_email_account( $owner_details );

			} catch ( Exception $e ) {

				wp_die( 'Something went wrong: ' . $e->getMessage() );

			}// End try().
		}// End if().

	}

}

new WPUMSL_Instagram;
