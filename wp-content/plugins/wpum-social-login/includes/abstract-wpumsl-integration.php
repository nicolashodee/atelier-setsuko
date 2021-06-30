<?php
/**
 * Base class for each social network integration.
 *
 * @package     wpum-social-login
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
use Brain\Cortex\Route\RedirectRoute;
use Brain\Cortex\Route\RouteCollectionInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class WPUMSL_Integration {

	/**
	 * Holds the id key of the social network.
	 *
	 * @var string
	 */
	public $social_id = '';

	/**
	 * Holds the client id key.
	 *
	 * @var string
	 */
	public $client_id = '';

	/**
	 * Holds the client secret key.
	 *
	 * @var string
	 */
	public $client_secret = '';

	/**
	 * Holds the redirect url for the specified integration.
	 *
	 * @var string
	 */
	public $redirect_uri = '';

	protected $redirect_path;

	/**
	 * Hook the social network integration.
	 */
	public function __construct() {
		$this->redirect_path = 'wpum/auth/' . $this->social_id;
		$this->redirect_uri = $this->get_redirect_uri();

		add_action( 'init', array( $this, 'trigger' ) );

		add_action( 'cortex.routes', function ( RouteCollectionInterface $routes ) {
			$routes->addRoute( new RedirectRoute( $this->redirect_path, function ( array $matches ) {
				return add_query_arg( $matches, $this->get_redirect_uri() );
			} ) );
		} );
	}

	/**
	 * Retrieve the redirect URI for the given social network.
	 *
	 * @return string
	 */
	protected function get_alt_redirect_uri() {
		return home_url( '/' . $this->redirect_path );
	}

	/**
	 * Retrieve the redirect URI for the given social network.
	 *
	 * @return string
	 */
	protected function get_redirect_uri() {
		return add_query_arg( array( 'wpumsl' => $this->social_id ), rtrim( home_url(), '/' ) . '/' );
	}

	/**
	 * Function responsible of triggering the call to the social network.
	 *
	 * @return void
	 */
	public function trigger() {
		if ( isset( $_GET['wpumsl'] ) && $_GET['wpumsl'] == $this->social_id ) {
			if ( ! session_id() ) {
				session_start();
			}
			$this->run_integration();
		}
	}

	/**
	 * The function responsible to contact the social network and grab profiles information.
	 *
	 * @return void
	 */
	public function run_integration() {}

	/**
	 * Process the account found through the social network.
	 *
	 * @param string $user_email
	 * @param object $owner_details
	 * @return void
	 */
	public function process_account( $user_email, $owner_details ) {

		if ( is_email( $user_email ) ) {

			$redirect_url = false;

			if ( isset( $_SESSION['wpum_redirect_to'] ) ) {
				$redirect_url = esc_url_raw( $_SESSION['wpum_redirect_to'] );
				unset( $_SESSION['wpum_redirect_to'] );
			}

			if ( email_exists( $user_email ) ) {

				$wp_user = get_user_by( 'email', $user_email );

				apply_filters( 'wpum_before_social_login', $wp_user, $owner_details );

				wpum_log_user_in( $wp_user->data->ID );

				apply_filters( 'wpum_after_social_login', $wp_user, $owner_details );

				$redirect = home_url();

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

				$first_name = $owner_details->getFirstName();
				$last_name  = $owner_details->getLastName();
				$pwd        = wp_generate_password();
				$new_user   = wp_create_user( $user_email, $pwd, $user_email );

				if ( ! empty( $first_name ) ) {
					update_user_meta( $new_user, 'first_name', $first_name );
				}
				if ( ! empty( $last_name ) ) {
					update_user_meta( $new_user, 'last_name', $last_name );
				}

				do_action( 'wpum_before_social_login_registration', $new_user );

				// Now send a confirmation email to the user.
				wpum_send_registration_confirmation_email( $new_user, $pwd );

				do_action( 'wpum_after_social_login_registration', $new_user, $owner_details );

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
		}
		exit;

	}

	/**
	 * Process an account from a network that does not expose an email address.
	 *
	 * @param object $owner_details
	 * @return void
	 */
	public function process_no_email_account( $owner_details ) {

		if ( $owner_details ) {

			$first_name = '';
			$last_name  = '';
			$user_data  = $owner_details->toArray();
			$username   = $owner_details->getNickname();

			$redirect_url = false;

			if ( isset( $_SESSION['wpum_redirect_to'] ) ) {
				$redirect_url = esc_url_raw( $_SESSION['wpum_redirect_to'] );
				unset( $_SESSION['wpum_redirect_to'] );
			}

			if ( username_exists( $username ) ) {

				$wp_user = get_user_by( 'login', $username );

				if ( wpumsl_is_account_linked( $wp_user->ID, $this->social_id ) ) {

					wpum_log_user_in( $wp_user->ID );

					$redirect = home_url();

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
					wp_die( esc_html__( 'Something went wrong, please try again later.', 'wpum-social-login' ) );
				}
			} else {

				$pwd        = wp_generate_password();
				$user_email = 'temp@changeme.com';
				$new_user   = wp_create_user( $username, $pwd, $user_email );

				if ( ! empty( $first_name ) ) {
					update_user_meta( $new_user, 'first_name', $first_name );
				}
				if ( ! empty( $last_name ) ) {
					update_user_meta( $new_user, 'last_name', $last_name );
				}

				wpumsl_link_account( $new_user, $this->social_id );

				do_action( 'wpum_after_social_login_registration', $new_user );

				wpum_log_user_in( $new_user );

				$redirect = get_permalink( wpum_get_core_page_id( 'account' ) );
				$redirect = add_query_arg( [
					'social-login' => 'update_email',
				], $redirect );

				wp_safe_redirect( $redirect );
				exit;

			}
		} else {
			wp_die( esc_html__( 'Something went wrong, please try again later.', 'wpum-social-login' ) );
		}

	}

}
