<?php
/**
 * Handles the display of the sync interface.
 *
 * @package     wpum-mailchimp
 * @copyright   Copyright (c) 2016, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

use \DrewM\MailChimp\MailChimp;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 * @package WPUMCHIMP_Batch_Sync
 */
class WPUMCHIMP_Batch_Sync {

	/**
	 * Number of users to sync per batch.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $num;

	/**
	 * Main plugin method for querying data.
	 *
	 * @since 1.0.0
	 * @param int $ppp    The posts_per_page number to use in the query.
	 * @param int $offset The offset to use for querying data.
	 * @return mixed      An array of data to be processed in bulk fashion.
	 */
	public function get_query_data( $ppp, $offset, $role ) {

		error_log( print_r( $role, true ), 0 );

		$user_query = new WP_User_Query(
			array(
				'role'   => $role,
				'number' => 99999,
				'offset' => $offset,
			)
		);

		return $user_query->get_results();

	}

	/**
	 * Loops through the array of data and processes it as necessary.
	 *
	 * @param array $data An array of data to process.
	 * @param       $list_id
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function process_query_data( $data, $list_id ) {
		$api_key   = carbon_get_theme_option( 'mailchimp_api_key' );
		$mailchimp = new MailChimp( $api_key );
		// Loop through each post and add a custom field.
		foreach ( (array) $data as $user ) {
			$subscriber_hash = $mailchimp->subscriberHash( $user->user_email );

			$data   = wpumchimp_get_merge_fields( $list_id, $user );
			$result = $mailchimp->put( "lists/$list_id/members/$subscriber_hash", [
				'email_address' => $user->user_email,
				'status'        => 'subscribed',
				'merge_fields'  => $data,
			] );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log( print_r( $user->user_email, true ), 0 );
				error_log( print_r( $result, true ), 0 );
			}

		}

	}

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_slug = 'wpumchimp-batch-sync';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Plugin menu hook.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $hook = false;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the plugin.
		$this->num = $this->users_per_batch();
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_filter( 'views_users', [ $this, 'register_sync_link' ] );
		add_action( 'wp_ajax_WPUMCHIMP_Batch_Sync', array( $this, 'process_bulk_routine' ) );

	}

	/**
	 * Processes the bulk editing routine experience.
	 *
	 * @since 1.0.0
	 */
	public function process_bulk_routine() {

		// Run a security check first to ensure we initiated this action.
		check_ajax_referer( $this->plugin_slug, 'nonce' );

		// Prepare variables.
		$step    = absint( $_POST['step'] );
		$steps   = absint( $_POST['steps'] );
		$role    = esc_attr( $_POST['role'] );
		$list_id = esc_attr( $_POST['list'] );
		$ppp     = $this->num;
		$offset  = 1 == $step ? 0 : $ppp * ( $step - 1 ) - 1;
		$done    = false;

		// Possibly return early if the offset exceeds the total steps and the $ppp is equal to the difference.
		if ( $offset > ( $steps - ( $this->num * 2 ) ) && $ppp == ( $offset - $steps ) ) {
			die( json_encode( array( 'success' => true ) ) );
		}

		// If our offset is greater than our steps but $ppp is different, set $ppp to the difference.
		if ( $offset > ( $steps - ( $this->num * 2 ) ) ) {
			$ppp  = $offset - $steps;
			$done = true;
		}

		// If we have matched our limit, set done to true.
		if ( ( $step * $ppp ) >= $steps ) {
			$done = true;
		}

		// Grab all of our data.
		$data = $this->get_query_data( $ppp, $offset, $role );

		// If we have no data or it returns false, we are done!
		if ( empty( $data ) || ! $data ) {
			wp_cache_flush();
			die( json_encode( array( 'done' => true ) ) );
		}

		// Process our query data.
		$this->process_query_data( $data, $list_id );

		// Flush the internal cache after every successful step.
		wp_cache_flush();

		// Send back our response to say we need to process more items.
		die( json_encode( array( 'done' => $done ) ) );

	}

	/**
	 * Loads the plugin into WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Hide the menu item.
	 *
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'users.php', 'wpumchimp-batch-sync' );
	}

	/**
	 * Add a new link to the users table views.
	 *
	 * @param array $views
	 * @return void
	 */
	public function register_sync_link( $views ) {

		$sync_url = admin_url( 'users.php?page=wpumchimp-batch-sync' );
		$views['mailchimp-sync'] = '<a href="' . esc_url( $sync_url ) . '">' . esc_html__( 'Import to Mailchimp', 'wpum-mailchimp' ) . '</a>';

		return $views;

	}

	/**
	 * Returns the limit to process for each batch.
	 *
	 * @since 1.0.0
	 * @return string batch limit (int)
	 */
	public function batch_limit() {

		return apply_filters( 'wpumchimp_batch_limit', 1000 );

	}

	/**
	 * Returns the amount of users to sync per batch.
	 *
	 * @since 1.0.0
	 * @return string batch limit (int)
	 */
	private function users_per_batch() {

		return apply_filters( 'wpumchimp_users_per_batch_limit', 10 );

	}

	/**
	 * Loads the admin menu item under the Tools menu.
	 *
	 * @since 1.0.0
	 */
	public function menu() {

		$this->hook = add_users_page( esc_html__( 'MailChimp Sync', 'wpum-mailchimp' ), esc_html__( 'MailChimp Sync', 'wpum-mailchimp' ), 'manage_options', $this->plugin_slug, array( $this, 'menu_cb' ) );

	}

	/**
	 * Outputs the menu view.
	 *
	 * @since 1.0.0
	 */
	public function menu_cb() {

		$processing = isset( $_GET['tgm-batch-updates'] ) || isset( $_GET['tgm-batch-step'] ) ? true : false;
		$step       = isset( $_GET['tgm-batch-step'] ) ? absint( $_GET['tgm-batch-step'] ) : 1;
		$role       = isset( $_GET['sync_roles'] ) ? esc_attr( $_GET['sync_roles'] ) : '';
		$list       = isset( $_GET['sync_list'] ) ? esc_attr( $_GET['sync_list'] ) : '';
		$optin      = isset( $_GET['double_optin'] ) ? true : false;
		$welcome    = isset( $_GET['welcome_email'] ) ? true : false;
		$steps      = isset( $_GET['tgm-batch-limit'] ) ? round( ( absint( $_GET['tgm-batch-limit'] ) / $this->num ), 0 ) : 0;
		$nonce      = wp_create_nonce( $this->plugin_slug );

		// Retrieve some information.
		$api_key = carbon_get_theme_option( 'mailchimp_api_key' );
		$roles   = wpum_get_roles( true );
		$roles_opt = [];

		foreach ( $roles as $rolen ) {
			$roles_opt[ $rolen['value'] ] = $rolen['label'];
		}

		$roles_selection = array(
			'options'          => $roles_opt,
			'label'            => '',
			'desc'             => esc_html__( 'Select a specific role to synchronize.', 'wpum-mailchimp' ),
			'id'               => 'wpumchimp-role-selector',
			'name'             => 'sync_roles',
			'multiple'         => false,
			'show_option_all'  => false,
			'show_option_none' => false,
		);

		$list_selection = array(
			'options'          => wpumchimp_get_enabled_lists(),
			'label'            => '',
			'desc'             => esc_html__( 'Select the list to synchronize your user base with.', 'wpum-mailchimp' ),
			'id'               => 'wpumchimp-list-selector',
			'name'             => 'sync_list',
			'multiple'         => false,
			'show_option_all'  => false,
			'show_option_none' => false,
		);

		?>

		<div class="wrap" id="wpum-settings-panel">

			<h2 class="wpum-page-title"><?php esc_html_e( 'WP User Manager Sync to MailChimp', 'wpum-mailchimp' ); ?> <?php do_action( 'wpum_next_to_settings_title' ); ?></h2>

			<?php if ( empty( $api_key ) ) : ?>
				<p><?php esc_html_e( 'A MailChimp api key is required before you can start syncing your users.', 'wpum-mailchimp' ); ?></p>
			<?php else : ?>

				<?php if ( $processing ) : ?>

					<p><?php esc_html_e( 'The sync routine has started. Please be patient as this may take several minutes to complete.', 'wpum-mailchimp' ); ?> <img class="tgm-batch-loading" src="<?php echo includes_url( 'images/spinner-2x.gif' ); ?>" alt="<?php esc_attr_e( 'Loading...', 'wpum-mailchimp' ); ?>" width="20px" height="20px" style="vertical-align:bottom" /></p>
					<p class="tgm-batch-step"><strong><?php printf( esc_html__( 'Currently on step %1$d of a possible %2$d (steps may be less if your limit exceeds available users ).', 'wpum-mailchimp' ), (int) $step, (int) $steps ); ?></strong></p>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							// Trigger the bulk upgrades to continue to processing.
							$.post( ajaxurl, { action: 'WPUMCHIMP_Batch_Sync', role: '<?php echo $role; ?>', list: '<?php echo $list; ?>', step: '<?php echo $step; ?>', steps: '<?php echo absint( $_GET['tgm-batch-limit'] ); ?>', nonce: '<?php echo $nonce; ?>' }, function(res){
								if ( res && res.success || res && res.done ) {
									$('.tgm-batch-step').after('<?php echo $this->get_success_message(); ?>');
									$('.tgm-batch-loading').remove();
									return;
								} else {
									document.location.href = '<?php echo add_query_arg( array( 'page' => $this->plugin_slug, 'tgm-batch-updates' => 1, 'sync_roles' => $role, 'sync_list' => $list, 'tgm-batch-step' => (int) $step + 1, 'tgm-batch-limit' => absint( $_GET['tgm-batch-limit'] ) ), admin_url( 'users.php' ) ); ?>';
								}
							}, 'json');
						});
					</script>

				<?php else : ?>

					<form id="tgm-batch-updates" method="get" action="<?php echo add_query_arg( 'page', $this->plugin_slug, admin_url( 'users.php' ) ); ?>">

						<table class="form-table">
							<tbody>
								<tr>
								  <th scope="row"><?php esc_html_e( 'Select a role', 'wpum-mailchimp' ); ?>:</th>
								  <td><?php echo WPUM()->elements->select( $roles_selection ); ?></td>
								</tr>
								<tr>
								  <th scope="row"><?php esc_html_e( 'Select a list', 'wpum-mailchimp' ); ?>:</th>
								  <td><?php echo WPUM()->elements->select( $list_selection ); ?></td>
								</tr>
								<?php do_action( 'wpumchimp_sync_interface_fields' ); ?>
							 </tbody>
						</table>

						<input type="hidden" name="page" value="<?php echo $this->plugin_slug; ?>" />
						<input type="hidden" name="tgm-batch-updates" value="1" />
						<input type="hidden" name="tgm-batch-step" value="1" />
						<input type="hidden" name="tgm-batch-limit" value="<?php echo $this->batch_limit(); ?>" />

						<?php submit_button( esc_html__( 'Sync Users', 'wpum-mailchimp' ) ); ?>

					</form>

				<?php endif; ?>

			<?php endif; ?>

		</div>

		<?php

	}

	/**
	 * Returns the batch update completed message.
	 *
	 * @since 1.0.0
	 *
	 * @return string $message The batch update completed message.
	 */
	public function get_success_message() {

		$message  = '<div class="updated"><p>' . esc_html__( 'The sync routine has been completed! Users should appear into your MailChimp lists within the next few minutes.', 'wpum-mailchimp' ) . '</p></div>';
		$message .= '<p><a class="button button-secondary" href="' . add_query_arg( array( 'page' => $this->plugin_slug ), admin_url( 'users.php' ) ) . '" title="' . esc_attr__( 'Start another Sync', 'wpum-mailchimp' ) . '">' . __( 'Start another Sync', 'wpum-mailchimp' ) . '</a></p>';
		return $message;

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The WPUMCHIMP_Batch_Sync object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPUMCHIMP_Batch_Sync ) ) {
			self::$instance = new WPUMCHIMP_Batch_Sync();
		}

		return self::$instance;

	}

}

// Load the main plugin class.
$WPUMCHIMP_Batch_Sync = WPUMCHIMP_Batch_Sync::get_instance();
