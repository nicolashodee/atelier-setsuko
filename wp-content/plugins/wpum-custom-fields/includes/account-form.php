<?php
/**
 * Add custom fields within the account page.
 *
 * @package     wpum-custom-fields
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tell WPUM to load the form for this addon from within this plugin's path.
 *
 * @param string $path
 * @param string $form_name
 *
 * @return string
 */
function wpumcf_register_form_path( $path, $form_name ) {
	if ( $form_name == 'custom-group' ) {
		$path = WPUMCF_PLUGIN_DIR . 'includes/class-wpum-form-custom-group.php';
	}
	return $path;
}
add_filter( 'wpum_load_form_path', 'wpumcf_register_form_path', 20, 2 );

/**
 * Add a new path to WPUM's template loader.
 */
function wpumcf_set_template_loader_path( $file_paths ) {
	$file_paths[] = trailingslashit( WPUMCF_PLUGIN_DIR . 'templates' );
	return $file_paths;
}
add_filter( 'wpum_template_paths', 'wpumcf_set_template_loader_path' );

/**
 * Dynamically load custom account page tabs and content.
 *
 * @return void
 */
function wpumcf_account_integration() {
	$fields_groups = WPUM()->fields_groups->get_groups();

	global $wpum_custom_fields_groups;
	$wpum_custom_fields_groups = [];

	if ( ! empty( $fields_groups ) && is_array( $fields_groups ) ) {
		foreach ( $fields_groups as $group ) {

			if ( $group->is_primary() || $group->get_ID() === '1' ) {
				add_filter(
					'wpum_get_account_page_tabs',
					function( $tabs ) use ( $group ) {
						$tabs[ 'settings' ]['priority'] = absint( $group->get_group_order() );
						return $tabs;
					}
				);
				continue;
			}

			$group_nicename                                = str_replace( ' ', '-', strtolower( $group->get_name() ) );
			$wpum_custom_fields_groups[ $group->get_ID() ] = $group;

			/**
			 * Load custom tabs.
			 */
			add_filter(
				'wpum_get_account_page_tabs',
				function( $tabs ) use ( $group, $group_nicename ) {

					$tabs[ $group_nicename ] = [
						'name'     => apply_filters( 'wpum_get_field_group_name', $group->get_name(), $group->get_ID() ),
						'priority' => absint( $group->get_group_order() ),
						'group_id' => $group->get_ID(),
					];

					return $tabs;

				}
			);

			/**
			 * Now load the content for each custom fields group tab content.
			 */
			add_action(
				'wpum_account_page_content_' . $group_nicename,
				function() use ( $group ) {
					echo WPUM()->forms->get_form(
						'custom-group',
						[
							'group'    => $group,
							'group_id' => $group->get_ID(),
						]
					);
				}
			);

		}
	}

}
add_action( 'after_wpum_init', 'wpumcf_account_integration' );

/**
 * Update custom user metas values when updating a profile.
 *
 * @param object $form
 * @param array  $values
 * @param string $user_id
 * @return void
 */
function wpumcf_update_user_profile( $form, $values, $user_id ) {

	if ( ! $user_id ) {
		return;
	}

	if ( empty( $values ) || ! is_array( $values ) ) {
		return;
	}

	$registered_fields = $form->get_fields( 'account' );

	foreach ( $values['account'] as $key => $value ) {

		$field_type = isset( $registered_fields[ $key ]['template'] ) ? $registered_fields[ $key ]['template'] : false;

		if ( $field_type === 'file' ) {

			$currently_uploaded_file = isset( $_POST[ 'current_' . $key ] ) && ! empty( $_POST[ 'current_' . $key ] ) ? esc_url_raw( $_POST[ 'current_' . $key ] ) : false;
			$filepath                = get_user_meta( $user_id, $key . '_path', true );
			$fileurl                = get_user_meta( $user_id, $key, true );

			if ( is_array( $value ) && array_key_exists( 'url', $value ) && $currently_uploaded_file !== $value['url'] ) {
				if ( $filepath !== $value['path'] ) {
					wp_delete_file( $filepath );
				}
				carbon_set_user_meta( $user_id, $key, $value['url'] );
				update_user_meta( $user_id, $key . '_path', $value['path'] );
			}

			if ( ! $currently_uploaded_file && $filepath ) {
				wp_delete_file( $filepath );
				carbon_set_user_meta( $user_id, $key, false );
				delete_user_meta( $user_id, $key . '_path' );
			}

			if ( ! $currently_uploaded_file && $fileurl && ! $filepath ) {
				carbon_set_user_meta( $user_id, $key, false );
			}

		} elseif ( strpos( $key, 'wpum_' ) === 0 && $field_type !== 'file' ) {
			$original_value = $value;
			if ( $value == '1' ) {
				$value = true;
			}

			$value = apply_filters( 'wpum_custom_fields_account_meta_update', $value, $key, $user_id, $original_value );

			carbon_set_user_meta( $user_id, $key, $value );

		}
	}

}
add_action( 'wpum_after_user_update', 'wpumcf_update_user_profile', 20, 3 );
