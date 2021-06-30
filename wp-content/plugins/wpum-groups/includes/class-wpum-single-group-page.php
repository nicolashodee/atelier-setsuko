<?php
/**
 * Handles the WPUM new group page
 *
 * @package     wpum-groups
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Single_Group_Page {

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'run') );

		add_action( 'wp', function () {
			if ( is_singular( 'wpum_group' ) ) {
				add_filter( 'twentynineteen_can_show_post_thumbnail', '__return_false' );
			}
		} );
	}

	public function run() {
		if ( ! $this->is_page() ) {
			return;
		}

		$this->init();
	}

	protected function is_page() {
		global $post;

		if ( ! isset( $post ) || ! isset( $post->post_type ) ) {
			return false;
		}

		if ( ! is_singular( 'wpum_group' ) ) {
			return false;
		}

		$group_id = apply_filters( 'wpum_group_id', $post->ID );

		$privacy_method = get_post_meta( $group_id, '_group_privacy_method', true );
		if ( 'hidden' === $privacy_method ) {
			$user_id = get_current_user_id();

			if ( ! $user_id ) {
				wp_safe_redirect( home_url() );
				exit;
			}

			if ( ! wpumgrp_is_user_group_member( $group_id, $user_id ) ) {
				wp_safe_redirect( home_url() );
				exit;
			}
		}

		$tab = wpum_get_active_group_tab( $post->ID );

		if ( 'moderation' === $tab ) {
			$db      = new WPUMG_DB_Group_Users;
			$user_id = get_current_user_id();

			if ( ! $user_id || ! array_intersect( $db->get_user_roles( $group_id, $user_id ), array(
					'wpum_group_moderator',
					'wpum_group_admin',
				) ) ) {
				wp_redirect( get_permalink( $post->ID ) );
				exit;
			}
		}

		$template = locate_template( array( 'single-group.php' ) );
		if ( $template ) {
			return false;
		}

		return true;
	}


	protected function init() {
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_excerpt', 'wpautop' );
		add_filter( 'the_content', array( $this, 'the_content' ), 9001 );
		add_filter( 'get_the_excerpt', array( $this, 'the_excerpt' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_filter( 'post_thumbnail_html', array( $this, 'post_thumbnail_html' ) );
	}

	/**
	 * @return string
	 */
	public function the_content( $text ) {
		if ( doing_filter( 'get_the_excerpt' ) ) {
			return $text;
		}
		wpum_get_group_page_content();
	}

	public function the_excerpt() {
		global $post;

		return $post->post_content;
	}

	/**
	 * @return string
	 */
	function template_include() {
		return locate_template( array( 'page.php', 'single.php', 'index.php' ) );
	}

	public function post_thumbnail_html() {
		return '';
	}
}
