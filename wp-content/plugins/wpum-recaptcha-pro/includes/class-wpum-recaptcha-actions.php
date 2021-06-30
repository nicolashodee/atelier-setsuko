<?php
/**
 * Hook into WP User Manager to validate recaptcha.
 *
 * @package     wpum-recaptcha
 * @copyright   Copyright (c) 2018, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPUM_Recaptcha_Actions {

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $v2_type;

	/**
	 * @var string
	 */
	protected $settings_suffix;

	/**
	 * WPUM_Recaptcha_Actions constructor.
	 */
	public function __construct() {
		$this->version         = wpum_get_option( 'recaptcha_version', 'v2' );
		$this->v2_type         = wpum_get_option( 'recaptcha_v2_type', 'checkbox' );
		$this->settings_suffix = $this->version === 'v3' ? '_' . $this->version : '';
	}

	public function init() {
		$this->maybe_add_to_forms();
		add_filter( 'submit_wpum_form_validate_fields', array( $this, 'validate' ), 10, 5 );
	}

	protected function get_site_key() {
		return wpum_get_option( 'recaptcha_site_key' . $this->settings_suffix );
	}

	protected function get_secret_key() {
		return wpum_get_option( 'recaptcha_secret_key' . $this->settings_suffix );
	}

	protected function maybe_add_to_forms() {
		$recaptcha_key    = $this->get_site_key();
		$recaptcha_secret = $this->get_secret_key();

		if ( empty( $recaptcha_key ) || empty( $recaptcha_secret ) ) {
			return;
		}

		add_action( 'wpum_before_submit_button_login_form', array( $this, 'add_recaptcha_login_field' ) );
		add_action( 'wpum_before_submit_button_two_factor_login_form', array( $this, 'add_recaptcha_login_field' ) );
		add_action( 'wpum_before_submit_button_registration_form', array( $this, 'add_recaptcha_registration_field' ) );
		add_action( 'wpum_before_submit_button_password_recovery_form', array( $this, 'add_recaptcha_password_recovery_field' ) );
	}

	protected function is_enabled( $form ) {
		$pre = apply_filters( 'wpum_' . $form . '_recaptcha_enabled', true );
		if ( ! $pre ) {
			return false;
		}

		return 'v2' === $this->version ? $this->v2_type : true;
	}

	protected function add_recaptcha_field_to_form( $form ) {
		$type = $this->is_enabled( $form );
		if ( ! $type ) {
			return;
		}

		$type   = ( $type === true || $type === '1' ) ? '' : $type . '_';
		$method = 'add_recaptcha_' . $this->version . '_' . $type . 'field';
		$this->{$method}();
	}

	public function add_recaptcha_login_field() {
		$this->add_recaptcha_field_to_form( 'login' );
	}

	public function add_recaptcha_registration_field() {
		$this->add_recaptcha_field_to_form( 'registration' );
	}

	public function add_recaptcha_password_recovery_field() {
		$this->add_recaptcha_field_to_form( 'password-recovery' );
	}

	/**
	 * Add the recaptcha v2 checkbox field to the form.
	 *
	 * @return void
	 */
	public function add_recaptcha_v2_checkbox_field() {
		$site_key = $this->get_site_key();
		$lang     = apply_filters( 'wpum_recaptcha_language', wpum_get_option( 'recaptcha_language' ) );
		?>
		<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>" style="margin-bottom:20px;"></div>
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo esc_attr( $lang ); ?>" async defer></script>
		<?php
	}

	/**
	 * Add the recaptcha v2 checkbox field to the form.
	 *
	 * @return void
	 */
	public function add_recaptcha_v2_invisible_field() {
		$site_key       = $this->get_site_key();
		$lang           = apply_filters( 'wpum_recaptcha_language', wpum_get_option( 'recaptcha_language' ) );
		$badge_location = apply_filters( 'wpum_recaptcha_badge_location', wpum_get_option( 'recaptcha_badge_location' ) );
		?>
		<div class="wpum-recaptcha-holder"></div>
		<input type="hidden" name="submit_login" value="1" />
		<?php if ( $badge_location === 'inline' ) : ?>
			<style type="text/css">
				.grecaptcha-badge[data-style="inline"] {
					margin-bottom: 20px;
				}
			</style>
		<?php endif; ?>
		<script type="text/javascript">
			var renderGoogleInvisibleRecaptcha = function() {
				for ( var i = 0; i < document.forms.length; ++i ) {
					var form = document.forms[ i ];
					var holder = form.querySelector( '.wpum-recaptcha-holder' );
					if ( null === holder ) {
						continue;
					}

					(function( frm ) {
						var holderId = grecaptcha.render( holder, {
							'sitekey': '<?php echo $site_key; ?>',
							'size': 'invisible',
							'badge': '<?php echo $badge_location; ?>',
							'callback': function( recaptchaToken ) {
								HTMLFormElement.prototype.submit.call( frm );
							}
						} );

						frm.onsubmit = function( evt ) {
							evt.preventDefault();
							grecaptcha.execute( holderId );
						};

					})( form );
				}
			};
		</script>
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo esc_attr( $lang ); ?>&onload=renderGoogleInvisibleRecaptcha&render=explicit" async defer></script>
		<?php
	}

	/**
	 * Add the recaptcha v3 field to the form.
	 *
	 * @return void
	 */
	public function add_recaptcha_v3_field() {
		$site_key = $this->get_site_key();
		$lang     = apply_filters( 'wpum_recaptcha_language', wpum_get_option( 'recaptcha_language' ) );
		?>
		<div id="wpum_recaptcha" data-sitekey="<?php echo $site_key; ?>">
			<input type="hidden" id="wpum_recaptcha_token" name="g-recaptcha-response" />
		</div>
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo esc_attr( $lang ); ?>&render=<?php echo $site_key; ?>"></script>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				var $button = $( '#wpum_recaptcha' ).parent().find( 'input[type="submit"]' );
				$button.prop( 'disabled', true );

				grecaptcha.ready( function() {
					grecaptcha.execute( $( '#wpum_recaptcha' ).data( 'sitekey' ), {
						action: 'register'
					} ).then( function( token ) {
						$( 'input[name="g-recaptcha-response"]' ).val( token );
						$button.prop( 'disabled', false );
					} );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Hook into the forms validation system for the login and registration form
	 * and then validate the recaptcha field.
	 *
	 * @param bool       $pass
	 * @param array      $fields
	 * @param array      $values
	 * @param string     $form_name
	 * @param WPUM_Form  $form
	 *
	 * @return bool|WP_Error
	 */
	public function validate( $pass, $fields, $values, $form_name, $form ) {
		$process = false;

		$recaptcha_key    = $this->get_site_key();
		$recaptcha_secret = $this->get_secret_key();

		if ( empty( $recaptcha_key ) || empty( $recaptcha_secret ) ) {
			return $pass;
		}

		if ( in_array( $form_name, array( 'login', 'password-recovery' ) ) ) {
			$process = $this->is_enabled( $form_name );
		}

		if ( in_array( $form_name, array( 'registration', 'registration-multi' ) ) ) {
			$process = $this->is_enabled( 'registration' );
		}

		if ( ! $process ) {
			return $pass;
		}

		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			return new WP_Error( 'recaptcha-error', esc_html__( apply_filters( 'wpum_recaptcha_failed_message', 'Recaptcha validation failed.' ), 'wpum-recaptcha' ) );
		}

		$recaptcha              = new \ReCaptcha\ReCaptcha( $recaptcha_secret, new \ReCaptcha\RequestMethod\WPPost() );
		$recaptcha_response_key = esc_html( $_POST['g-recaptcha-response'] );
		$resp                   = $recaptcha->verify( $recaptcha_response_key, $_SERVER['REMOTE_ADDR'] );

		$score_threshold_met = true;
		if ( 'v3' === $this->version ) {
			$score_threshold     = apply_filters( 'wpum_recaptcha_score_threshold', 0.5 );
			$score               = $resp->getScore();
			$score               = ! empty( $score ) ? $score : 0;
			$score_threshold_met = $score >= $score_threshold;
		}

		if ( ! $resp->isSuccess() || ! $score_threshold_met ) {
			return new WP_Error( 'recaptcha-error', esc_html__( apply_filters( 'wpum_recaptcha_failed_message', 'Recaptcha validation failed.' ), 'wpum-recaptcha' ) );
		}

		return $pass;
	}
}
