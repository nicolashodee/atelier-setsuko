<?php
/**
 * Content Restriction settings manager.
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

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class WPUMCR_Restriction_Manager {

	/**
	 * Get things started.
	 */
	public function __construct() {
		add_action( 'carbon_fields_register_fields', array( $this, 'content_restriction_settings' ), 0 );
		add_action( 'admin_head', array( $this, 'script' ) );
	}

	/**
	 * Return an array containing user roles.
	 *
	 * @return array
	 */
	private function get_roles() {
		$roles = [];

		foreach ( wpum_get_roles( true, true ) as $role ) {
			$roles[ $role['value'] ] = $role['label'];
		}

		return $roles;
	}


	/**
	 * Return an array containing pages.
	 *
	 * @return array
	 */
	private function get_pages() {
		$pages = [];

		foreach ( wpum_get_redirect_pages() as $page ) {
			$pages[ $page['value'] ] = $page['label'];
		}

		return $pages;
	}

	/**
	 * Get post type for page request (in admin)
	 *
	 * @return string
	 */
	public function get_post_type() {
		global $pagenow;

		if ( isset( $_GET['post_type'] ) && 'post-new.php' === $pagenow ) {
			return $_GET['post_type'];
		}

		if ( ! isset( $_GET['post'] ) ) {
			return '';
		}

		$post_id = filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );

		$post = get_post( $post_id );

		return $post->post_type;
	}

	/**
	 * Content Restriction Settings for post/pages/post types.
	 *
	 * @return void
	 */
	public function content_restriction_settings() {
		$current_post_type = $this->get_post_type();

		$pages = $this->get_pages();
		$pages['wpumcustomredirect'] = esc_html__( 'Custom URL', 'wpum-content-restriction' ) ;


		$fields     = array(
			Field::make( 'checkbox', 'wpumcr_restrict_access_post', esc_html__( 'Restrict access to this ' . $current_post_type, 'wpum-content-restriction' ) )
			     ->set_option_value( 'yes' )
			     ->set_classes( 'wpumcr-conditional wpumcr-conditional_restrict' )
			     ->set_help_text( esc_html__( 'Enable this option to apply content restriction settings', 'wpum-content-restriction' ) ),

			Field::make( 'select', 'wpumcr_accessible', esc_html__( 'Who can access this ' . $current_post_type, 'wpum-content-restriction' ) )
			     ->add_options( array(
					     'in'  => esc_html__( 'Logged in users', 'wpum-content-restriction' ) ,
					     'out' => esc_html__( 'Logged out users', 'wpum-content-restriction' ) ,
				     ) )
			     ->set_classes( 'wpumcr-condition-restrict wpumcr-match_yes wpumcr-hide wpumcr-conditional wpumcr-conditional_type' )
			     ->set_help_text( esc_html__( 'Set the visibility of this restricted content.', 'wpum-content-restriction' ) ),

			Field::make( 'multiselect', 'wpumcr_assigned_roles', esc_html__( 'Restriction by role', 'wpum-content-restriction' ) )
			     ->set_help_text( esc_html__( 'Choose user roles for restricted content access.', 'wpum-content-restriction' ) )
			     ->add_options( $this->get_roles() )
			     ->set_classes( 'wpumcr-condition-type wpumcr-match_in wpumcr-hide' ),

			Field::make( 'select', 'wpumcr_restriction_behaviour', esc_html__( 'Restriction behaviour:', 'wpum-content-restriction' ) )
			     ->add_options( array(
					     'message'  => esc_html__( 'Show message', 'wpum-content-restriction' ) ,
					     'redirect' => esc_html__( 'Redirect', 'wpum-content-restriction' ) ,
				     ) )
			     ->set_classes( 'wpumcr-condition-restrict wpumcr-match_yes wpumcr-hide wpumcr-conditional wpumcr-conditional_behaviour' ),

			Field::make( 'select', 'wpumcr_restriction_message_type', esc_html__( 'Message type:', 'wpum-content-restriction' ) )
			     ->add_options( array(
				     'global'  => esc_html__( 'Global default message', 'wpum-content-restriction' ) ,
				     'custom' => esc_html__( 'Custom message', 'wpum-content-restriction' ) ,
			     ) )
			     ->set_classes( 'wpumcr-condition-behaviour wpumcr-match_message wpumcr-hide wpumcr-conditional wpumcr-conditional_message' ),

			Field::make( 'rich_text', 'wpumcr_restriction_custom_message', esc_html__( 'Custom message:', 'wpum-content-restriction' ) )
				->set_classes( 'wpumcr-condition-message wpumcr-match_custom wpumcr-hide' ),


			Field::make( 'select', 'wpumcr_access_redirect', esc_html__( 'Redirection page:', 'wpum-content-restriction' ) )
			     ->add_options( $pages )
			     ->set_classes( 'wpumcr-condition-behaviour wpumcr-match_redirect wpumcr-hide wpumcr-conditional wpumcr-conditional_redirect' )
			     ->set_help_text( esc_html__( 'Select the page where you want to redirect visitor after they visit this post/page.', 'wpum-content-restriction' ) ),

			Field::make( 'text', 'wpumcr_restriction_custom_redirect', esc_html__( 'Custom URL:', 'wpum-content-restriction' ) )
			     ->set_classes( 'wpumcr-condition-redirect wpumcr-match_wpumcustomredirect wpumcr-hide' ),

			Field::make( 'checkbox', 'wpumcr_restrict_everywhere', esc_html__( 'Restrict Everywhere', 'wpum-content-restriction' ) )
			     ->set_classes( 'wpumcr-condition-restrict wpumcr-match_yes wpumcr-hide' )
			     ->set_help_text( esc_html__( 'Hide from archives, RSS feeds and other places for users who do not have permission to view this content', 'wp-user-manager'  ) ),
		);

		Container::make( 'post_meta', 'wpum_content_restriction', 'WP User Manager - Content Restriction' )
		         ->where( 'post_type', 'NOT IN', apply_filters( 'wpumcr_restrict_meta_exclude_post_types', array( 'wpum_directory', 'acf-field-group', 'acf-field', 'shop_order' ) ) )
		         ->add_fields( apply_filters( 'wpumcr_restriction_meta_fields', $fields ) );
	}

	/**
	 * Adjust styling of the menu settings.
	 *
	 * @return void
	 */
	public function script() {
		?>
		<style type="text/css">
			.wpumcr-hide {
				display: none;
			}
		</style>
		<script type="application/javascript">
			(function( $ ) {

				function getConditional( $element ) {
					var elClasses = $element.attr( 'class' ).split( ' ' );
					for ( var index in elClasses ) {
						if ( elClasses[ index ].match( /^wpumcr-conditional_(.*)$/ ) ) {
							return elClasses[ index ].split( '_' )[ 1 ];
						}
					}

					return false;
				}

				function getMatch( $element ) {
					var elClasses = $element.attr( 'class' ).split( ' ' );

					for ( var index in elClasses ) {
						if ( elClasses[ index ].match( /^wpumcr-match_(.*)$/ ) ) {
							return elClasses[ index ].split( '_' )[ 1 ];
						}
					}

					return false;
				}

				$.fn.conditional = function(options) {
					if ( !this.length ) {
						return this;
					}
					var opts = $.extend(true, {}, $.conditional.defaults, options);
					this.each(function() {
						var el = $(this),
							conditional = getConditional( $(this).parents('.wpumcr-conditional') );

						el.on(opts.eventName, function() {
							var value = 'no';
							if ( el.is(':checkbox') ) {
								if ( el.is(':checked') ) {
									value = el.val();
								}
							} else {
								value = el.val();
							}
							var	elements = $('.wpumcr-condition-'+ conditional );
							// Hide all
							opts.onDeactivate(elements, opts, function() {
								// Show the one(s) that match
								elements.each(function() {
									var element = $(this);
									var childConditional = getConditional( element );

									// Get parent
									var match = getMatch( $(this) );
									if ( match == value ) {
										opts.onActivate(element, opts);
										$('.wpumcr-condition-'+ childConditional ).addClass(opts.className);
										$(this).find( 'select, input').trigger(opts.eventName);
									} else {
										$(this).find( 'select, input').trigger(opts.eventName);
										$('.wpumcr-condition-'+ childConditional ).addClass(opts.className);
									}
								});

							});
						});
						el.trigger(opts.eventName);
					});
					return this;
				};
				$.conditional = {
					defaults: {
						className: 'wpumcr-hide',
						eventName: 'change',
						onActivate: function(element, opts) {
							element.removeClass(opts.className);
						},
						onDeactivate: function(elements, opts, callback) {
							elements.addClass(opts.className);
							callback.call();
						},
						autoBind: true
					}
				};

				function toggle_restriction_settings( $element ) {
					if ( ! $element.prop( 'checked' ) ) {
						$( '.carbon-container-carbon_fields_container_wpum_content_restriction .carbon-field' ).nextAll().addClass('wpumcr-hide');
					}
				}

				$( document ).ready( function() {
					if ( $.conditional.defaults.autoBind ) {
						$( '.wpumcr-conditional select' ).conditional();
						$( '.wpumcr-conditional input' ).conditional();

						$( '.wpumcr-conditional_restrict input' ).change( function() {
							toggle_restriction_settings( $(this) );
						} );

						toggle_restriction_settings( $( '.wpumcr-conditional_restrict input' ) );


					}
				});

			})( jQuery );
		</script>
		<?php
	}


}
