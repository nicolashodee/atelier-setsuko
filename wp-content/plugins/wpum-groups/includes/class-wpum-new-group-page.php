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

class WPUM_Form_New_Group_Page {

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'run') );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	public function run() {
		if ( ! $this->is_page() ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			$login_page = get_permalink( wpum_get_core_page_id( 'login' ) );
			$login_page = add_query_arg( [
				'redirect_to' => home_url( '/groups/new/' ),
			], $login_page );
			wp_redirect( $login_page );
			exit;
		}

		$this->init();
	}

	/**
	 * @return bool
	 */
	protected function is_page() {
		if ( is_admin() ) {
			return false;
		}

		global $wp_query;
		if ( empty( $wp_query ) ) {
			return false;
		}

		$new_group = get_query_var( 'new_group' );

		if ( empty( $new_group ) ) {
			return false;
		}

		$new_group = intval( $new_group );

		return ! empty( $new_group );
	}

	protected function init() {
		add_filter( 'the_title', array( $this, 'the_title' ) );
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_excerpt', 'wpautop' );
		add_filter( 'the_content', array( $this, 'the_content' ), 9001 );
		add_filter( 'get_the_excerpt', array( $this, 'the_content' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_filter( 'post_thumbnail_html', array( $this, 'post_thumbnail_html' ) );
	}

	/**
	 * @param $query
	 */
	public function pre_get_posts( $query ) {
		if ( ! $this->is_page() || ! is_user_logged_in() ) {
			return;
		}

		$query->set('post_type' ,'page');
		$query->set( 'posts_per_page', 1 );
		$query->set('is_page', true );
		$query->set('is_singular', true );
		$query->set('is_home', false );
		$query->set('is_archive', false );
		$query->set('is_category', false );
	}

	/**
	 * @param $title
	 *
	 * @return string
	 */
	public function the_title( $title ) {
		if ( ! in_the_loop() ) {
			return $title;
		}

		return sprintf( esc_html__( 'Create a New %s', 'wpum-groups' ), WPUM_Group_Editor::singular() );
	}

	/**
	 * @return string
	 */
	public function the_content() {
		return WPUM()->forms->get_form( 'group' );
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
