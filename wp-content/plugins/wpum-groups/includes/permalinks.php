<?php
/**
 * Handles all the routing functionalities of WPUM Group.
 *
 * @package     wpum-groups
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 */

use Brain\Cortex\Route\QueryRoute;
use Brain\Cortex\Route\RouteCollectionInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register rewrite rules for the new group page.
 */
add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ) {
	$path = strtolower( WPUM_Group_Editor::plural() );

	$wp_rewrite->rules = array_merge( [ $path . '/new/?$' => 'index.php?new_group=1' ], $wp_rewrite->rules );
} );

add_filter( 'query_vars', function ( $query_vars ) {
	$query_vars[] = 'new_group';

	return $query_vars;
} );

/**
 * Register rewrite rules for the profile page.
 */
add_action(
	'cortex.routes',
	function( RouteCollectionInterface $routes ) {
		$page_slug = strtolower( WPUM_Group_Editor::singular() );
		$page_slug .= '/';

		$page_slugs = apply_filters( 'wpum_group_page_slugs', array( $page_slug ), $page_slug );

		foreach ( $page_slugs as $page_slug ) {
			$routes->addRoute( new QueryRoute( $page_slug . '{slug}/{tab:[a-zA-Z0-9_.-]+}', function ( array $matches ) {
				return [
					'post_type' => 'wpum_group',
					'name'      => $matches['slug'],
					'tab'       => rawurldecode( $matches['tab'] ),
				];
			} ) );

			$routes->addRoute( new QueryRoute( $page_slug . '{slug}/{tab:[a-zA-Z0-9_.-]+}/page/{paged:[a-zA-Z0-9_.-]+}', function ( array $matches ) {
				return [
					'post_type' => 'wpum_group',
					'name'      => $matches['slug'],
					'tab'       => rawurldecode( $matches['tab'] ),
					'paged'     => $matches['paged'],
				];
			} ) );
		}
	}
);
