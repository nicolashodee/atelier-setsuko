<?php
/**
 * Handles the form where users can update their mailchimp subscriptions preferences.
 *
 * @package     wpum-delete-account
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0.0
 */

use \DrewM\MailChimp\MailChimp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Form_Email_Subscriptions extends WPUM_Form {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'email-subscriptions';

	/**
	 * Stores static instance of class.
	 *
	 * @access protected
	 * @var WPUM_Form_Login The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Returns static instance of class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'process' ) );

		$this->steps = (array) apply_filters(
			'email_subscriptions_steps', array(
				'submit' => array(
					'name'     => __( 'Email subscriptions', 'wpum-mailchimp' ),
					'view'     => array( $this, 'submit' ),
					'handler'  => array( $this, 'submit_handler' ),
					'priority' => 10,
				),
				'confirmation' => array(
					'name'     => __( 'Email subscriptions', 'wpum-mailchimp' ),
					'view'     => array( $this, 'confirmation' ),
					'handler'  => false,
					'priority' => 10,
				),
			)
		);

		uasort( $this->steps, array( $this, 'sort_by_priority' ) );

		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

	}

	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}

		$this->fields = apply_filters(
			'email_subscriptions_form_fields', array(
				'mailchimp_subscriptions' => array(
					'mailchimp' => array(
						'label'       => false,
						'description' => '',
						'type'        => 'multicheckbox',
						'required'    => false,
						'placeholder' => '',
						'value'       => wpumchimp_get_current_users_lists(),
						'options'     => wpumchimp_get_enabled_lists(),
					),
				),
			)
		);

	}

	/**
	 * Show the form.
	 *
	 * @return void
	 */
	public function submit() {

		$this->init_fields();

		$data = [
			'form'      => $this->form_name,
			'action'    => $this->get_action(),
			'fields'    => $this->get_fields( 'mailchimp_subscriptions' ),
			'step'      => $this->get_step(),
			'step_name' => $this->steps[ $this->get_step_key( $this->get_step() ) ]['name'],
		];

		WPUM()->templates
			->set_template_data( $data )
			->get_template_part( 'forms/email-subscriptions-form' );

	}

	/**
	 * Handle submission of the form.
	 *
	 * @return void
	 */
	public function submit_handler() {
		try {

			$this->init_fields();

			$values = $this->get_posted_fields();

			if ( ! wp_verify_nonce( $_POST['account_email_subscriptions_nonce'], 'verify_email_subscriptions_form' ) ) {
				return;
			}
			if ( empty( $_POST['submit_email_subscriptions'] ) ) {
				return;
			}
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}

			$user = wp_get_current_user();

			if ( $user instanceof WP_User && is_array( $values['mailchimp_subscriptions']['mailchimp'] ) && ! empty( $values['mailchimp_subscriptions']['mailchimp'] ) ) {
				foreach ( $values['mailchimp_subscriptions']['mailchimp'] as $list_id ) {
					$api_key = carbon_get_theme_option( 'mailchimp_api_key' );
					if ( ! empty( $api_key ) ) {
						try {
							$mailchimp       = new MailChimp( $api_key );
							$subscriber_hash = $mailchimp->subscriberHash( $user->user_email );
							$result = $mailchimp->post(
								"lists/$list_id/members", [
									'email_address' => $user->user_email,
									'status'        => 'subscribed',
								]
							);
						} catch ( Exception $e ) {
							$this->add_error( $e->getMessage() );
							return;
						}
					}
				}
			}

			// Remove user from any other list non enabled.
			$enabled_lists = wpumchimp_get_enabled_lists();

			if ( is_array( $enabled_lists ) && ! empty( $enabled_lists ) && is_array( $values['mailchimp_subscriptions']['mailchimp'] ) ) {
				foreach ( $enabled_lists as $list_id => $value ) {
					if ( ! in_array( $list_id, $values['mailchimp_subscriptions']['mailchimp'] ) ) {
						$api_key = carbon_get_theme_option( 'mailchimp_api_key' );
						if ( ! empty( $api_key ) ) {
							try {
								$mailchimp       = new MailChimp( $api_key );
								$subscriber_hash = $mailchimp->subscriberHash( $user->user_email );
								$mailchimp->delete( "lists/$list_id/members/$subscriber_hash" );
							} catch ( Exception $e ) {
								$this->add_error( $e->getMessage() );
								return;
							}
						}
					}
				}
			} else {
				foreach ( $enabled_lists as $list_id => $value ) {
					$api_key = carbon_get_theme_option( 'mailchimp_api_key' );
					if ( ! empty( $api_key ) ) {
						try {
							$mailchimp       = new MailChimp( $api_key );
							$subscriber_hash = $mailchimp->subscriberHash( $user->user_email );
							$mailchimp->delete( "lists/$list_id/members/$subscriber_hash" );
						} catch ( Exception $e ) {
							$this->add_error( $e->getMessage() );
							return;
						}
					}
				}
			}

			$this->step ++;

		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Display confirmation message.
	 *
	 * @return void
	 */
	public function confirmation() {

		$message = esc_html__( 'Email preferences successfully updated.', 'wpum-mailchimp' );

		echo '<h2>' . $this->steps[ $this->get_step_key( $this->get_step() ) ]['name'] . '</h2>';

		$data = [
			'message' => apply_filters( 'wpumchimp_success_message', $message ),
		];

		WPUM()->templates
			->set_template_data( $data )
			->get_template_part( 'messages/general', 'success' );

	}

}
