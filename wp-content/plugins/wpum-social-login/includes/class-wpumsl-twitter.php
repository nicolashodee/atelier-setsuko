<?php
/**
 * Twitter login & registration integration.
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
 * Twitter integration class.
 */
class WPUMSL_Twitter extends WPUMSL_Integration {

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->social_id = 'twitter';
		$this->client_id = wpum_get_option( 'twitter_clientid' );
		$this->secret_id = wpum_get_option( 'twitter_secret' );
		parent::__construct();
		// Use path based url for callback, as twitter doesn't allow query strings
		$this->redirect_uri = $this->get_alt_redirect_uri();
	}

	/**
	 * The function responsible to contact the social network and grab profiles information.
	 *
	 * @return void
	 */
	public function run_integration() {

		$server = new League\OAuth1\Client\Server\Twitter(
			array(
				'identifier'   => $this->client_id,
				'secret'       => $this->secret_id,
				'callback_uri' => $this->redirect_uri,
			)
		);

		if ( isset( $_GET['user'] ) ) {

			if ( ! isset( $_SESSION['token_credentials'] ) ) {
				wp_die( esc_html__( 'No token credentials found.', 'wpum-social-login' ) );
			}

			$token_credentials = unserialize( $_SESSION['token_credentials'] );

			$user         = $server->getUserDetails( $token_credentials );
			$user_email   = isset( $user->email ) ? $user->email : false;
			$username     = $server->getUserScreenName( $token_credentials );
			$redirect_url = '';
			if ( isset( $_SESSION['wpum_redirect_to'] ) ) {
				$redirect_url = esc_url_raw( $_SESSION['wpum_redirect_to'] );
				unset( $_SESSION['wpum_redirect_to'] );
			}
			$redirect     = '';

			if ( is_email( $user_email ) ) {

				if ( email_exists( $user_email ) ) {

					$wp_user = get_user_by( 'email', $user_email );

					do_action( 'wpum_before_social_login', $wp_user );

					wpum_log_user_in( $wp_user->data->ID );

					if ( $redirect_url && ! empty( $redirect_url ) ) {
						$redirect = $redirect_url;
					} elseif ( ! empty( wpum_get_login_redirect() ) ) {
						$redirect = wpum_get_login_redirect();
					} else {
						$redirect = get_permalink( wpum_get_core_page_id( 'login' ) );
					}

					wp_safe_redirect( $redirect );
					exit;

				} else {

					$username = username_exists( $username ) ? $user_email : $username;
					$pwd      = wp_generate_password();
					$new_user = wp_create_user( $username, $pwd, $user_email );

					do_action( 'wpum_before_social_login_registration', $new_user );

					// Now send a confirmation email to the user.
					wpum_send_registration_confirmation_email( $new_user, $pwd );

					do_action( 'wpum_after_social_login_registration', $new_user );

					$auto_login_user = apply_filters( 'wpum_auto_login_user_after_registration', true );
					if ( $auto_login_user ) {
						wpum_log_user_in( $new_user );
					}

					if ( $redirect_url && ! empty( $redirect_url ) ) {
						wp_safe_redirect( $redirect_url );
						exit;
					} else {
						$redirect_page = wpum_get_option( 'registration_redirect' );
						if ( ! empty( $redirect_page ) && is_array( $redirect_page ) ) {
							$redirect_page = $redirect_page[0];
							wp_safe_redirect( get_permalink( $redirect_page ) );
							exit;
						} else {
							$registration_page = get_permalink( wpum_get_core_page_id( 'register' ) );
							$registration_page = add_query_arg(
								[
									'registration' => 'success',
								],
								$registration_page
							);
							wp_safe_redirect( $registration_page );
							exit;
						}
					}
				}
			} else {
				wp_die( esc_html__( 'Something went wrong. Please make sure that your Twitter app has the "Request email address from users" permission setting enabled.', 'wpum-social-login' ) );
			}
		} elseif ( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) && isset( $_SESSION['temporary_credentials'] ) ) {

			$temporary_credentials = unserialize( $_SESSION['temporary_credentials'] );

			$token_credentials = $server->getTokenCredentials( $temporary_credentials, $_GET['oauth_token'], $_GET['oauth_verifier'] );

			unset( $_SESSION['temporary_credentials'] );

			$_SESSION['token_credentials'] = serialize( $token_credentials );

			session_write_close();

			$redirect = add_query_arg(
				[
					'wpumsl' => 'twitter',
					'user'   => 'user',
				],
				home_url()
			);

			wp_safe_redirect( $redirect );
			exit;

		} elseif ( isset( $_GET['denied'] ) ) {

			$url = add_query_arg(
				[
					'wpumsl' => 'twitter',
				],
				home_url()
			);

			wp_die( sprintf( esc_html__( 'We couldn\'t verify your account. Please try again %s', 'wpum-social-login' ), '<a href="' . $url . '">' . $url . '</a>' ) );

		} else {

			$temporary_credentials             = $server->getTemporaryCredentials();
			$_SESSION['temporary_credentials'] = serialize( $temporary_credentials );
			if ( isset( $_GET['redirect_to'] ) ) {
				$_SESSION['wpum_redirect_to'] = esc_url_raw( $_GET['redirect_to'] );
			}
			session_write_close();
			$server->authorize( $temporary_credentials );
			exit;
		}

	}

}

new WPUMSL_Twitter;
