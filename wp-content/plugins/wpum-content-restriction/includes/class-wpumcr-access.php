<?php
/**
 * Content Restriction hook-up for posts/pages/post types, menu, comments and media.
 *
 * @package     wpum-content-restriction
 * @copyright   Copyright (c) 2020, WP User Manager
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUMCR_Access {

	/**
	 * If true then we use individual restrict content options
	 * for post
	 *
	 * @var bool
	 */
	protected $singular_page;

	/**
	 * @var \WP_Post
	 */
	protected $current_single_post;


	/**
	 * Access constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			return;
		}

		add_filter( 'the_posts', array( &$this, 'restrict_posts' ), 99, 2 );
		add_filter( 'get_pages', array( &$this, 'restrict_posts' ), 99, 2 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'filter_menu' ));

		add_filter( 'wp_get_attachment_url', array( &$this, 'filter_attachment' ), 99, 2 );
		add_filter( 'has_post_thumbnail', array( &$this, 'filter_post_thumbnail' ), 99, 3 );

		add_filter( 'comments_open', array( $this, 'disable_comments' ), 99, 2 );
		add_filter( 'get_comments_number', array( $this, 'disable_comments_number' ), 99, 2 );
	}

	/**
	 * Protect Post Types in query
	 * Restrict content new logic
	 *
	 * @param           $posts
	 * @param \WP_Query $query
	 *
	 * @return array
	 */
	public function restrict_posts( $posts, $query ) {
		if ( empty( $posts ) ) {
			return $posts;
		}

		if ( is_object( $query ) ) {
			$is_singular = $query->is_singular();
		} else {
			$is_singular = ! empty( $query->is_singular );
		}

		foreach ( $posts as $key => $post ) {
			if ( ! empty( $_GET['wc-ajax'] ) && defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX ) {
				continue;
			}

			$cr_post = new WPUMCR_Post( $post );

			if ( ! $cr_post->is_restricted() ) {
				continue;
			}

			if ( empty( $is_singular ) && $cr_post->restrict_everywhere() ) {
				unset( $posts[ $key ] );

				if ( isset( $query->found_posts ) ) {
					$query->found_posts--;
				}

				continue;
			}

			if ( 'message' === $cr_post->get_restriction_action() ) {
				$message = stripslashes( $cr_post->get_restricted_message() );

				$posts[ $key ]->post_content = $message;
				if ( empty( $is_singular ) ) {
					continue;
				}

				$this->current_single_post   = $posts[ $key ];
				add_filter( 'the_content', array( &$this, 'replace_post_content' ), 9999, 1 );
				add_filter( 'single_template', array( &$this, 'woocommerce_template' ), 9999999, 1 );

				continue;
			}

			if ( 'redirect' === $cr_post->get_restriction_action() ) {
				if ( empty( $is_singular ) ) {
					if ( apply_filters( 'wpumcr_restrict_content_everywhere', true, $cr_post ) ) {
						$posts[ $key ]->post_content = '';
					}
					continue;
				}

				$url = $cr_post->get_restricted_redirect( get_permalink( $post->ID ) );

				exit( wp_redirect( esc_url( $url ) ) );
			}
		}

		return array_values( $posts );
	}


	/**
	 * @param string $single_template
	 *
	 * @return string
	 */
	function woocommerce_template( $single_template ) {
		if ( ! function_exists( 'WC_Template_Loader' ) ) {
			return $single_template;
		}

		if ( is_product() ) {
			remove_filter( 'template_include', array( 'WC_Template_Loader', 'template_loader' ) );
		}

		return $single_template;
	}


	/**
	 * @param $content
	 *
	 * @return string
	 */
	function replace_post_content( $content ) {
		return $this->current_single_post->post_content;
	}

	/**
	 * Disable comments if user has not permission to access this post
	 *
	 * @param mixed $open
	 * @param int   $post_id
	 *
	 * @return bool
	 */
	function disable_comments( $open, $post_id ) {
		static $cache = array();

		if ( isset( $cache[ $post_id ] ) ) {
			return $cache[ $post_id ] ? $open : false;
		}

		$wpum_post = new WPUMCR_Post( $post_id );
		if ( $wpum_post->is_restricted() ) {
			$open = false;
		}

		$cache[ $post_id ] = $open;

		return $open;
	}


	/**
	 * Disable comments if user has not permission to access this post
	 *
	 * @param int $count
	 * @param int $post_id
	 *
	 * @return bool
	 */
	function disable_comments_number( $count, $post_id ) {
		static $cache_number = array();

		if ( isset( $cache_number[ $post_id ] ) ) {
			return $cache_number[ $post_id ];
		}

		$wpum_post = new WPUMCR_Post( $post_id );
		if ( $wpum_post->is_restricted() ) {
			$count = 0;
		}

		$cache_number[ $post_id ] = $count;

		return $count;
	}

	/**
	 * Hide attachment if the post is restricted
	 *
	 * @param string $url
	 * @param int    $attachment_id
	 *
	 * @return boolean|string
	 */
	public function filter_attachment( $url, $attachment_id ) {
		return ( $attachment_id && ( new WPUMCR_Post( $attachment_id ) )->is_restricted() ) ? false : $url;
	}


	/**
	 * Hide attachment if the post is restricted
	 *
	 * @param $has_thumbnail
	 * @param $post
	 * @param $thumbnail_id
	 *
	 * @return bool
	 */
	public function filter_post_thumbnail( $has_thumbnail, $post, $thumbnail_id ) {
		if ( $thumbnail_id ) {
			$post_id = $thumbnail_id;
		} elseif ( ! empty( $post ) ) {
			$post_id = $post;
		} else {
			$post_id = get_the_ID();
		}

		$wpum_post = new WPUMCR_Post( $post_id );
		if ( $wpum_post->is_restricted() ) {
			$has_thumbnail = false;
		}

		$has_thumbnail = apply_filters( 'wpumcr_restrict_post_thumbnail', $has_thumbnail, $post, $thumbnail_id );

		return $has_thumbnail;
	}


	/**
	 * @param $menu_items
	 *
	 * @return array
	 */
	function filter_menu( $menu_items ) {
		if ( empty( $menu_items ) ) {
			return $menu_items;
		}

		$filtered_items = array();

		foreach ( $menu_items as $menu_item ) {
			if ( empty( $menu_item->object_id ) || empty( $menu_item->object ) ) {
				$filtered_items[] = $menu_item;

				continue;
			}

			$cr_post = new WPUMCR_Post( get_post( $menu_item->object_id ) );

			if ( $cr_post->is_restricted() ) {
				continue;
			}

			$filtered_items[] = $menu_item;
		}

		return $filtered_items;
	}
}
