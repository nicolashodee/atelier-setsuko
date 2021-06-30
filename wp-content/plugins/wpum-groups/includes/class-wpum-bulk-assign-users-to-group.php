<?php
/**
 * Handles the Bulk assign users to Groups
 *
 * @package     wpum-groups
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WPUM_Bulk_Assign_Users_To_Group', false ) ) {
	class WPUM_Bulk_Assign_Users_To_Group {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'load-users.php', array( $this, 'wpum_group_add' ) );
			// Add custom bulk fields.
			add_action( 'restrict_manage_users', array( $this, 'wpum_bulk_fields' ), 5 );
			add_action( 'admin_init', array( $this, 'admin_init_func' ) );
			// Hook to add value to group column to user list table
			add_filter( 'manage_users_columns', array( $this, 'manage_users_columns_func' ) );
			// Hook to add group column to user list table
			add_action( 'manage_users_custom_column', array( $this, 'manage_users_custom_column_func' ), 10, 3 );
		}

		/**
		 * Add group column value to WP user list table.
		 *
		 * @param $value
		 * @param $column_name
		 * @param $user_id
		 *
		 * @return string
		 */
		public function manage_users_custom_column_func( $value, $column_name, $user_id ) {
			if ( 'wpum_group' != $column_name ) {
				return $value;
			}
			$data                = array();
			$total_count         = 0;
			$total_group_display = apply_filters( 'wpum_groups_total_group_displayed', 5 );
			//make sure total group display is greater than zero
			if ( 0 >= absint( $total_group_display ) ) {
				$total_group_display = 5;
			}
			$wpumgrp_get_user_group_ids = wpumgrp_get_user_group_ids( $user_id );
			if ( is_array( $wpumgrp_get_user_group_ids ) ) {
				foreach ( $wpumgrp_get_user_group_ids as $group_id ) {
					$group_name = get_the_title( $group_id );
					$data[]     = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( $group_id ) ), $group_name );
					if ( $total_group_display <= count( $data ) ) {
						break;
					}
				}
			}
			if ( is_array( $data ) && ! empty( $data ) ) {
				if ( $total_group_display < count( $wpumgrp_get_user_group_ids ) ) {
					$group_count     = 2;
					$total_count     = count( $wpumgrp_get_user_group_ids ) - $total_group_display;
					$data            = array_slice( $data, 0, $total_group_display, true );
					$comma_separated = implode( ', ', $data );
				} else {
					$comma_separated = implode( ', ', $data );
					$group_count     = 1;
				}
				$groups = sprintf( _n( '%s', '%s & %s more', $group_count, 'wpum-groups' ), $comma_separated, $total_count );

				return $groups;
			} else {
				return '—';
			}
		}

		/**
		 * Add group column to WP user list table.
		 *
		 * @param array $column_headers
		 *
		 * @return array
		 */
		public function manage_users_columns_func( $column_headers ) {
			$column_headers['wpum_group'] = WPUM_Group_Editor::singular();

			return $column_headers;
		}

		/**
		 * Adds a group to the user.
		 *
		 * @return void
		 * @since  1.0.9
		 * @access public
		 */
		public function wpum_group_add() {
			// Return if any user not selected.
			if ( empty( $_REQUEST['users'] ) ) {
				return;
			}
			// Check if we have a group id selected.
			if ( ! empty( $_REQUEST['wpum-assign-group-top'] ) && ! empty( $_REQUEST['wpum-assign-group-submit-top'] ) ) {
				$group_id = sanitize_text_field( $_REQUEST['wpum-assign-group-top'] );
			} elseif ( ! empty( $_REQUEST['wpum-assign-group-bottom'] ) && ! empty( $_REQUEST['wpum-assign-group-submit-bottom'] ) ) {
				$group_id = sanitize_text_field( $_REQUEST['wpum-assign-group-bottom'] );
			}
			// Check if group id is empty then return.
			if ( empty( $group_id ) ) {
				return;
			}
			// Validate nonce.
			check_admin_referer( 'wpum-bulk-users-group', 'wpum-bulk-users-group-nonce' );

			// If the current user cannot promote users.
			if ( ! current_user_can( $this->get_user_cabpability() ) ) {
				return;
			}
			$all_user_id = wpum_clean( wp_unslash( $_REQUEST['users'] ) );
			$user_count  = 0;
			// Loop through selected users.
			foreach ( $all_user_id as $user_id ) {
				$user_id = absint( $user_id );
				// Check that the current user can promote this specific user.
				if ( ! current_user_can( $this->get_user_cabpability(), $user_id ) ) {
					continue;
				}
				// check if user id and group id not null.
				if ( ! empty( $group_id ) && ! empty( $user_id ) ) {
					$already_member = wpumgrp_is_user_group_member( $group_id, $user_id );
					if ( ! empty( $already_member ) ) {
						continue;
					}
					do_action( 'wpumgp_user_join_group', $group_id, $user_id, 'approved' );
					//Check if user is successfully added to the group then count the user
					$user_count += 1;
				}
			}

			if ( $user_count > 0 ) {
				// Redirect to the users screen.
				wp_redirect( add_query_arg( array(
						'update'  => 'group-added',
						'message' => $this->get_success_message( $user_count, $group_id ),
					), 'users.php' ) );
				exit;
			}
		}

		/**
		 * Group added success message.
		 *
		 * @param $user_count
		 *
		 * @return string
		 * @since  1.0.9
		 * @access public
		 */
		public function get_success_message( $user_count, $group_id ) {
			$group_name = get_the_title( $group_id );
			$message    = sprintf( _n( '%s user added to the %s group', '%s users added to the %s group', $user_count, 'wpum-groups' ), $user_count, $group_name );

			return apply_filters( 'wpum_groups_add_user_to_group_message', $message );
		}

		/**
		 * Set notice variable.
		 *
		 * @return void
		 * @since  1.0.9
		 * @access public
		 */
		public function admin_init_func() {
			if ( isset( $_GET['update'] ) && isset( $_GET['message'] ) ) {
				$action  = sanitize_key( $_GET['update'] );
				$message = sanitize_text_field( $_GET['message'] );
				if ( 'group-added' === $action ) {
					WPUM()->notices->register_notice( 'group_added', 'success', esc_html__( $message, 'wpum-groups' ), [ 'dismissible' => false ] );
				}
			}
		}

		/**
		 * Returns the user capability.
		 *
		 * @return string
		 * @since  1.0.9
		 * @access public
		 */
		public function get_user_cabpability() {
			return apply_filters( 'wpum_groups_add_user_to_group_cap', 'promote_users' );
		}

		/**
		 * Outputs "add to group" dropdown select fields.
		 *
		 * @param string $which
		 *
		 * @return void
		 * @since  1.0.9
		 * @access public
		 */
		public function wpum_bulk_fields( $which ) {
			// If the current user cannot promote users.
			if ( ! current_user_can( $this->get_user_cabpability() ) ) {
				return;
			}
			// Create nonce.
			wp_nonce_field( 'wpum-bulk-users-group', 'wpum-bulk-users-group-nonce' ); ?>

			<?php
			// Get groups query object.
			$get_group_array = wpumgp_get_groups();
			$posts           = $get_group_array->posts;
			?>
			<?php if ( is_array( $posts ) || is_object( $posts ) ) { ?>
				<select name="<?php echo esc_attr( "wpum-assign-group-{$which}" ); ?>"
				        id="<?php echo esc_attr( "wpum-assign-group-{$which}" ); ?>"
				        style="display: inline-block; float: none;">
					<option value=""><?php esc_html_e( 'Add to Group&hellip;', 'wpum-groups' ); ?></option>
					<?php
					// Loop through all groups.
					foreach ( $posts as $post_data ) { ?>
						<option
							value="<?php echo $post_data->ID; ?>"><?php esc_html_e( $post_data->post_title, 'wpum-groups' ); ?></option>
						<?php
					}
					?>
				</select>
			<?php } ?>
			<?php submit_button( esc_html__( 'Apply', 'wpum-groups' ), 'secondary', esc_attr( "wpum-assign-group-submit-{$which}" ), false );
		}
	}
}
