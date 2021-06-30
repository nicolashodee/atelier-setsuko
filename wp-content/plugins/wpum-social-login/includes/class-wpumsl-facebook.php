<?php
/**
 * Facebook login & registration integration.
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
 * Facebook integration class.
 */
class WPUMSL_Facebook extends WPUMSL_Integration {

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->social_id = 'facebook';
		$this->client_id = wpum_get_option( 'facebook_clientid' );
		$this->secret_id = wpum_get_option( 'facebook_secret' );
		parent::__construct();
	}

	/**
	 * The function responsible to contact the social network and grab profiles information.
	 *
	 * @return void
	 */
	public function run_integration() {

		$provider = new \League\OAuth2\Client\Provider\Facebook(
			[
				'clientId'        => $this->client_id,
				'clientSecret'    => $this->secret_id,
				'redirectUri'     => $this->redirect_uri,
				'graphApiVersion' => 'v2.10',
			]
		);

		if ( ! isset( $_GET['code'] ) ) {
			$authUrl                 = $provider->getAuthorizationUrl();
			$_SESSION['oauth2state'] = $provider->getState();
			if ( isset( $_GET['redirect_to'] ) ) {
				$_SESSION['wpum_redirect_to'] = esc_url_raw( $_GET['redirect_to'] );
			}
			wp_redirect( $authUrl );
			exit;
		} elseif ( empty( $_GET['state'] ) || ( $_GET['state'] !== $_SESSION['oauth2state'] ) ) {
			unset( $_SESSION['oauth2state'] );
			exit( 'Invalid state' );
		} else {
			try {
				$token = $provider->getAccessToken( 'authorization_code', [
					'code' => $_GET['code'],
				] );

				// We got an access token, let's now get the owner details.
				$owner_details = $provider->getResourceOwner( $token );
				$user_email    = $owner_details->getEmail();

				$this->process_account( $user_email, $owner_details );

			} catch ( Exception $e ) {
				wp_die( 'Something went wrong: ' . $e->getMessage() );
			}
		}

	}

}

new WPUMSL_Facebook;
