<?php
/**
 * @package     wpum-content-restriction
 * @copyright   Copyright (c) 2021, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUMCR_Post_Types {

	protected $content_restriction_post_types;
	protected $content_restriction_type;
	protected $content_restriction_behaviour;
	protected $content_restriction_redirect_page;
	protected $content_restriction_roles;
	protected $content_restriction_message_type;
	protected $content_restriction_redirect_custom_url;
	protected $content_restriction_everywhere;

	protected function get( $key, $default = false ) {
		if ( $this->{$key} ) {
			return $this->{$key};
		}

		$this->{$key} = wpum_get_option( $key, $default );

		return $this->{$key};
	}

	public function init() {
		add_filter( 'wpumcr_pre_post_restriction', array( $this, 'is_restriction_enabled' ), 10, 2 );
		add_filter( 'wpumcr_post_restriction_action', array( $this, 'restriction_action' ), 10, 2 );
		add_filter( 'wpumcr_post_restriction_redirect', array( $this, 'restriction_redirect' ), 10, 2 );
		add_filter( 'wpumcr_post_restriction_message_type', array( $this, 'restriction_message_type' ), 10, 2 );
		add_filter( 'wpumcr_post_restriction_custom_message', array( $this, 'restriction_custom_message' ), 10, 2 );
		add_filter( 'wpumcr_post_restriction_redirect_custom_url', array( $this, 'restriction_redirect_custom_url' ), 10, 2 );
		add_filter( 'wpumcr_post_restriction_restrict_everywhere', array( $this, 'restriction_everywhere' ), 10, 2 );
	}

	public function is_post_restricted( $post = null ) {
		if ( empty( $post ) ) {
			return false;
		}

		$pre = apply_filters( "wpumcr_pre_post_type_{$post->post_type}_restriction", null, $post );
		if ( ! is_null( $pre ) ) {
			return (bool) $pre;
		}

		$restricted_post_types = $this->get( 'content_restriction_post_types', array() );

		if ( empty( $restricted_post_types ) || ! in_array( $post->post_type, $restricted_post_types ) ) {
			return false;
		}

		$content_restriction_type = $this->get( 'content_restriction_type', 'in' );

		if ( $content_restriction_type === 'out' ) {
			return is_user_logged_in();
		}

		if ( $content_restriction_type === 'in' && ! is_user_logged_in() ) {
			return true;
		}

		$restricted_roles = $this->get( 'content_restriction_roles', array() );

		if ( ! empty( $restricted_roles ) && ! WPUMCR_Post::user_can( get_current_user_id(), $restricted_roles ) ) {
			return true;
		}

		return false;
	}

	public function is_restriction_enabled( $restricted, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			return true;
		}

		return $restricted;
	}

	public function restriction_action ( $action, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			return $this->get( 'content_restriction_behaviour', 'message' );
		}

		return $action;
	}

	public function restriction_redirect ( $action, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			$redirect = $this->get( 'content_restriction_redirect_page', array( 'hp' ) );

			return $redirect[0];
		}

		return $action;
	}

	public function restriction_message_type ( $action, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			return $this->get( 'content_restriction_message_type', 'global' );
		}

		return $action;
	}

	public function restriction_custom_message ( $action, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			return $this->get( 'content_restriction_message', '' );
		}

		return $action;
	}

	public function restriction_redirect_custom_url ( $action, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			return $this->get( 'content_restriction_redirect_custom_url', '' );
		}

		return $action;
	}

	public function restriction_everywhere( $restricted, $post ) {
		if ( $this->is_post_restricted( $post ) ) {
			return $this->get( 'content_restriction_everywhere', false );
		}

		return $restricted;
	}
}
