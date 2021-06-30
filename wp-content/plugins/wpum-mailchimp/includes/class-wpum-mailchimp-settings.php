<?php
/**
 * Register a new options panel for the MailChimp addon.
 *
 * @package     wpum-mailchimp
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use \DrewM\MailChimp\MailChimp;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Mailchimp_Settings {

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'carbon_fields_register_fields', [ $this, 'register_mailchimp_settings_panel' ], 1 );
		add_action( 'admin_footer', [ $this, 'style' ] );
		add_action( 'admin_head', [ $this, 'admin_notices' ] );
		add_action( 'carbon_fields_theme_options_container_saved', [ $this, 'activate_api' ] );
		add_action( 'in_admin_footer', [ $this, 'custom_admin_page' ] );
	}

	/**
	 * Register the new options panel.
	 *
	 * @return void
	 */
	public function register_mailchimp_settings_panel() {
		Container::make( 'theme_options', esc_html__( 'WP User Manager Mailchimp Settings', 'wpum-mailchimp' ) )
			->set_page_parent( 'users.php' )
			->set_page_menu_title( esc_html__( 'Mailchimp', 'wpum-mailchimp' ) )
			->set_page_file( 'wpum-mailchimp' )
			->add_fields(
				$this->get_settings()
			);
	}

	/**
	 * Add some styling to the options panel.
	 *
	 * @return void
	 */
	public function style() {

		$screen = get_current_screen();

		if ( $screen->base !== 'users_page_wpum-mailchimp' ) {
			return;
		}

		?>
		<style>
			#carbon_fields_container_wp_user_manager_mailchimp_settings .carbon-separator {
				background: #f6f6f6;
			}
			#carbon_fields_container_wp_user_manager_mailchimp_settings .carbon-separator h3 {
				font-size: 16px !important;
				font-weight: 600;
			}

			#carbon_fields_container_wp_user_manager_mailchimp_settings .notice p {
				margin: .5em 0;
				padding: 2px;
			}

		</style>
		<?php

	}

	/**
	 * Define settings for the options panel.
	 *
	 * @return array
	 */
	public function get_settings() {
		$lists      = $this->get_saved_lists();
		$merge_tags = $this->get_merge_fields();

		$merge_fields = array(
			Field::make( 'select', 'list', esc_html__( 'Mailchimp list', 'wpum-mailchimp' ) )
			     ->set_classes( 'wpum-lists' )
			     ->add_options( $lists ),
			Field::make( 'text', 'list_description', esc_html__( 'Custom list description', 'wpum-mailchimp' ) )
			     ->set_help_text( esc_html__( 'Use a custom description if you wish to display this within the forms.', 'wpum-mailchimp' ) ),
		);

		if ( class_exists( 'WPUM_Custom_Fields' ) ) {
			$merge_sub_fields = array(
				Field::make( 'select', 'custom_field', esc_html__( 'Custom field', 'wpum-mailchimp' ) )
				     ->add_options( $this->get_fields() ),
			);
			foreach ( $lists as $list_id => $list ) {
				$mt_fields          = isset( $merge_tags[ $list_id ] ) ? $merge_tags[ $list_id ] : array();
				$merge_sub_fields[] = Field::make( 'select', $list_id . '_merge_field', esc_html__( 'Merge tag', 'wpum-mailchimp' ) )
				                           ->set_classes( 'wpum-merge-tag wpum-merge-tag-' . $list_id )
				                           ->add_options( $mt_fields );
			}

			$merge_fields_labels = array(
				'plural_name' => 'Fields',
				'singular_name' => 'Add Field',
			);

			$merge_fields[] = Field::make( 'complex', 'mailchimp_custom_fields', esc_html__( 'Mailchimp custom fields' ) )
			                       ->setup_labels($merge_fields_labels)
			                       ->set_classes('wpum-custom-fields')
			                       ->add_fields( $merge_sub_fields );

		} else {
			$message    = sprintf( __( 'Purchase the %s if you wish to send user custom fields as merge fields to your Mailchimp lists.', 'wpum-mailchimp' ), '<a href="https://wpusermanager.com/addons/custom-fields/?utm_source=WP%20User%20Manager&utm_medium=insideplugin&utm_campaign=WPUM%20Mailchimp&utm_content=settings" target="_blank">' . __( 'custom fields addon', 'wpum-mailchimp' ) . '</a>' );
			$merge_fields[] = Field::make( 'html', 'crb_information_text' )
			                   ->set_html( '<div class="notice notice-info notice-alt inline"><p>' . $message . '</p></div>' );
		}

		$lists_labels = array(
			'plural_name' => 'Lists',
			'singular_name' => 'Add List',
		);

		$settings = [
			Field::make( 'separator', 'main_settings', esc_html__( 'Mailchimp Main Settings', 'wpum-mailchimp' ) ),

			Field::make( 'text', 'mailchimp_api_key', esc_html__( 'Mailchimp API Key', 'wpum-mailchimp' ) )
				->set_help_text( esc_html__( 'Enter your Mailchimp API key here.', 'wpum-mailchimp' ) ),

			Field::make( 'radio', 'mailchimp_optin_method', esc_html__( 'Optin method', 'wpum-mailchimp' ) )
				->set_help_text( esc_html__( 'Select which method you wish to use to add users to your mailchimp lists.', 'wpum-mailchimp' ) )
				->add_options(
					array(
						'auto'   => esc_html__( 'Automatically add users to a Mailchimp list when they register.', 'wpum-mailchimp' ),
						'manual' => esc_html__( 'Allow users to opt-in to subcribing to a Mailchimp list when they register.', 'wpum-mailchimp' ),
					)
				),

			Field::make( 'radio', 'mailchimp_user_selection', esc_html__( 'How many lists', 'wpum-mailchimp' ) )
				->set_help_text( esc_html__( 'Select whether you wish to allow users to select one or more lists.', 'wpum-mailchimp' ) )
				->add_options(
					array(
						'single'   => esc_html__( 'Allow users to select a single list', 'wpum-mailchimp' ),
						'multiple' => esc_html__( 'Allow users to select multiple lists', 'wpum-mailchimp' ),
					)
				),

			Field::make( 'checkbox', 'mailchimp_edit_account', esc_html__( 'Account subscription', 'wpum-mailchimp' ) )
				->set_help_text( esc_html__( 'Allow users to subscribe/unsubcribe from their account page.', 'wpum-mailchimp' ) ),
		];

		if ( function_exists( 'wpumuv_get_verification_method' ) && wpumuv_get_verification_method() ) {
			$settings[] = Field::make( 'checkbox', 'mailchimp_after_verification', esc_html__( 'After user verification', 'wpum-mailchimp' ) )
			                   ->set_help_text( esc_html__( 'Only subscribe users to lists after they have been approved or verified.', 'wpum-mailchimp' ) );
		}

		$settings[] = Field::make( 'separator', 'list_settings', esc_html__( 'Lists settings', 'wpum-mailchimp' ) );

		$settings[] = Field::make( 'complex', 'selected_mailchimp_lists', esc_html__( 'Enabled Mailchimp lists', 'wpum-mailchimp' ) )
		                   ->set_max( $this->get_max_lists() )
		                   ->set_classes( 'wpum-mailchimp' )
		                   ->setup_labels( $lists_labels )
		                   ->add_fields( $merge_fields );

		return $settings;
	}

	/**
	 * Display a notice if the api key is wrong.
	 *
	 * @return void
	 */
	public function admin_notices() {

		$screen = get_current_screen();

		if ( $screen->base !== 'users_page_wpum-mailchimp' ) {
			return;
		}

		if ( get_transient( 'wpum_mailchimp_api_key_error' ) ) {
			WPUM()->notices->register_notice( 'wpum_mailchimp_error', 'error', esc_html__( 'Invalid Mailchimp API key.', 'wpum-mailchimp' ), [ 'dismissible' => false ] );
		}

	}

	/**
	 * Activate the api when a new key is given.
	 *
	 * @return void
	 */
	public function activate_api() {

		if ( empty( $_POST['_mailchimp_api_key'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$api_key = esc_html( $_POST['_mailchimp_api_key'] );

		if ( ! empty( $api_key ) ) {
			self::update_mailchimp_api_data( $api_key );
		}

		delete_option( 'wpum_mailchimp_upgrade_message' );
	}

	public static function update_mailchimp_api_data( $api_key ) {
		try {
			$mailchimp = new MailChimp( $api_key );
			$per_page  = 100;
			$page      = 0;
			$all_lists = array();
			do {
				$result = $mailchimp->get( 'lists', array( 'count' => $per_page, 'offset' => $page ) );

				if ( empty( $result ) || ! isset( $result['lists'] ) || empty( $result['lists'] ) ) {
					break;
				}

				$all_lists = array_merge( $all_lists, $result['lists'] );
				$page      = $page + $per_page;

			} while ( $per_page );

			$lists        = [];
			$merge_fields = [];

			if ( ! empty( $all_lists ) ) {
				foreach ( $all_lists as $list ) {
					$lists[ $list['id'] ] = $list['name'];
					$merge_fields_query   = $mailchimp->get( '/lists/' . $list['id'] . '/merge-fields' );
					if ( ! empty( $merge_fields_query ) && is_array( $merge_fields_query ) && isset( $merge_fields_query['merge_fields'] ) ) {
						foreach ( $merge_fields_query['merge_fields'] as $merge_field ) {
							$merge_fields[ $list['id'] ][ $merge_field['merge_id'] ] = [
								'tag'      => $merge_field['tag'],
								'name'     => $merge_field['name'],
								'list_id'  => $merge_field['list_id'],
								'required' => $merge_field['required'],
							];
						}
					}
				}
				update_option( 'wpum_mailchimp_lists', $lists );
				update_option( 'wpum_mailchimp_lists_merge_fields', $merge_fields );
			}
		} catch ( Exception $e ) {
			delete_option( '_mailchimp_api_key' );
			delete_option( 'wpum_mailchimp_lists' );
			delete_option( 'wpum_mailchimp_lists_merge_fields' );
			set_transient( 'wpum_mailchimp_api_key_error', true, 1 );
		}
	}

	/**
	 * Retrieve lists saved into the db.
	 *
	 * @return mixed
	 */
	public function get_saved_lists() {
		$lists = get_option( 'wpum_mailchimp_lists' );
		if ( empty( $lists ) ) {
			return [];
		}

		return $lists;
	}

	/**
	 * Determine how many lists the user can add to the forms.
	 *
	 * @return int
	 */
	private function get_max_lists() {

		$max = -1;

		if ( get_option( '_mailchimp_user_selection' ) == 'single' ) {
			$max = 1;
		}

		return $max;

	}

	/**
	 * Retrieve all custom fields from WPUM.
	 *
	 * @return array
	 */
	private function get_fields() {

		$fields = [];

		$available_fields = WPUM()->fields->get_fields(
			[
				'order'   => 'ASC',
				'orderby' => 'field_order',
			]
		);

		$non_allowed = [
			'user_password',
		];

		if ( ! empty( $available_fields ) && is_array( $available_fields ) ) {
			foreach ( $available_fields as $field ) {
				if ( in_array( $field->get_primary_id(), $non_allowed ) ) {
					continue;
				}
				$fields[ $field->get_ID() ] = $field->get_name();
			}
		}

		return $fields;

	}

	/**
	 * Retrieve merge fields from MailChimp
	 *
	 * @return array
	 */
	private function get_merge_fields() {
		$merge_fields = get_option( 'wpum_mailchimp_lists_merge_fields' );
		$all_fields       = [];

		if ( ! empty( $merge_fields ) && is_array( $merge_fields ) ) {
			foreach ( $merge_fields as $list => $fields ) {
				foreach ( $fields as $field_id => $field ) {
					if ( isset( $field['tag'] ) && in_array( $field['tag'], array( 'FNAME', 'LNAME' ) ) ) {
						continue;
					}
					if ( ! isset( $field['name'] ) ) {
						continue;
					}
					$all_fields[ $list ][ $field_id ] = $field['name'];
				}
			}
		}

		return $all_fields;
	}

	public function custom_admin_page () {
		$screen = get_current_screen();
		if ( 'users_page_wpum-mailchimp' == $screen->base ) {
			?>
			<script type='text/javascript'>
				function init() {
					jQuery('.wpum-mailchimp').find('.carbon-groups-holder .carbon-group-row').each(function(){
						var list = jQuery( this ).find('.wpum-lists select').val();
						if ( ! list ) {
							return;
						}

						jQuery( this ).find( '.wpum-merge-tag' ).hide();
						jQuery( this ).find( '.wpum-merge-tag-' + list ).show();
					});
				}

				jQuery( document ).ready( function() {
					init();
				} );

				jQuery( document ).on( 'change', '.wpum-lists select', function() {
					let list = jQuery( this ).val();
					jQuery( this ).parents('.carbon-field').find( '.wpum-merge-tag' ).hide();
					jQuery( this ).parents('.carbon-field').find( '.wpum-merge-tag-' + list ).show();
				} );

				jQuery( document ).on( 'click', '.wpum-custom-fields .button', function() {
					setTimeout(function() {
						init();
					}, 200);
				} );

				jQuery( document ).on( 'click', '.wpum-mailchimp .button', function() {
					setTimeout(function() {
						init();
					}, 200);
				} );

			</script>
			<?php
		}
	}

}

new WPUM_Mailchimp_Settings;
