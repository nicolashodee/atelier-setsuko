<?php
/**
 * @package     wpum-content-restriction
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUMCR_Post {

	/**
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * WPUMCR_Post constructor.
	 *
	 * @param WP_Post|int $post
	 */
	public function __construct( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		$this->post = $post;
	}

	protected function is_core_page( $post ) {
		$pages = array(
			'login',
			'register',
			'account',
			'logout_redirect',
			'password',
		);

		foreach ( $pages as $page ) {
			if ( $post->ID === wpum_get_core_page_id( $page ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param int   $user_id
	 * @param array $roles
	 *
	 * @return bool
	 */
	public static function user_can( $user_id, $roles ) {
		$user_can = false;

		if ( ! empty( $roles ) ) {
			foreach ( $roles as $value ) {
				if ( ! empty( $value ) && user_can( $user_id, $value ) ) {
					return true;
				}
			}
		}

		return $user_can;
	}

	/**
	 * @return bool
	 */
	public function is_restricted() {
		if ( empty( $this->post->post_type ) ) {
			return false;
		}

		if ( 'page' === $this->post->post_type && $this->is_core_page( $this->post ) ) {
			return false;
		}

		$pre = apply_filters( 'wpumcr_pre_post_restriction', null, $this->post );
		if ( ! is_null( $pre ) ) {
			return (bool) $pre;
		}

		$user_logged_in = is_user_logged_in();
		if ( $user_logged_in && current_user_can( 'administrator' ) ) {
			return false;
		}

		$restriction_setting = get_post_meta( $this->post->ID, '_wpumcr_restrict_access_post', true );

		if ( empty( $restriction_setting ) || 'yes' !== $restriction_setting ) {
			return false;
		}

		$authenticated_type = get_post_meta( $this->post->ID, '_wpumcr_accessible', true );

		if ( 'out' === $authenticated_type ) {
			return $user_logged_in;
		}

		if ( 'in' === $authenticated_type && ! $user_logged_in ) {
			return true;
		}

		$restricted_by_role = $this->is_restricted_by_role();
		if ( $restricted_by_role ) {
			return true;
		}

		return apply_filters( 'wpumcr_post_restriction', false, $this->post->ID, $this->post );
	}

	protected function is_restricted_by_role() {
		$allowed_roles = carbon_get_post_meta( $this->post->ID, 'wpumcr_assigned_roles', 'carbon_fields_container_wpum_content_restriction' );

		if ( empty( $allowed_roles ) ) {
			return false;
		}

		return ! self::user_can( get_current_user_id(), (array) $allowed_roles );
	}

	public function get_restriction_action() {
		return apply_filters( 'wpumcr_post_restriction_action', get_post_meta( $this->post->ID, '_wpumcr_restriction_behaviour', true ), $this->post );
	}

	public function get_restricted_message() {
		$message_type = apply_filters( 'wpumcr_post_restriction_message_type', get_post_meta( $this->post->ID, '_wpumcr_restriction_message_type', true ), $this->post );

		if ( 'global' === $message_type ) {
			$login_page = get_permalink( wpum_get_core_page_id( 'login' ) );
			$login_page = add_query_arg( [
				'redirect_to' => get_permalink( $this->post->ID ),
			], $login_page );

			$restricted_global_message = sprintf( __( 'This content is available to members only. Please <a href="%1$s">login</a> or <a href="%2$s">register</a> to view this area.', 'wpum-content-restriction' ) , $login_page, get_permalink( wpum_get_core_page_id( 'register' ) ) );

			return apply_filters( 'wpumcr_global_restriction_message', $restricted_global_message, $this->post->ID );
		}
		$message = apply_filters( 'wpumcr_post_restriction_custom_message', get_post_meta( $this->post->ID, '_wpumcr_restriction_custom_message', true ), $this->post );

		return empty( $message ) ? '' : $message;
	}

	public function get_restricted_redirect( $redirect_to = null ) {
		$redirect = apply_filters( 'wpumcr_post_restriction_redirect', get_post_meta( $this->post->ID, '_wpumcr_access_redirect', true ), $this->post );

		if ( 'wpumcustomredirect' === $redirect ) {
			return apply_filters( 'wpumcr_post_restriction_redirect_custom_url', get_post_meta( $this->post->ID, '_wpumcr_restriction_custom_redirect', true ), $this->post );
		}

		if ( 'hp' === $redirect ) {
			$url = home_url();
		} else {
			$url = get_permalink( $redirect );
		}

		if ( $redirect_to ) {
			$url = add_query_arg( 'redirect_to', $redirect_to, $url );
		}

		return $url;
	}

	public function restrict_everywhere() {
		return (bool) apply_filters( 'wpumcr_post_restriction_restrict_everywhere', get_post_meta( $this->post->ID, '_wpumcr_restrict_everywhere', true ), $this->post );
	}

}
