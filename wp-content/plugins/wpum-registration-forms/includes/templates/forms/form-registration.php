<?php
/**
 * The Template for displaying the registration forms.
 *
 * This template overrides WPUM registration form template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpum-template wpum-form wpum-registration-form">

	<?php do_action( 'wpum_before_registration_form', $data ); ?>

	<form action="<?php echo esc_url( $data->action ); ?>" method="post" id="wpum-submit-registration-form" enctype="multipart/form-data">

		<?php foreach ( $data->fields as $key => $field ) : ?>

			<?php

			if( $field['type'] == 'html_content' ){

				$field[ 'key' ] = $key;
				$template = isset( $field['template'] ) ? $field['template'] : $field['type'];

				WPUM()->templates
					->set_template_data( $field )
					->get_template_part( 'form-fields/' . $template, 'field' );

				continue;
			}

			?>

			<fieldset class="fieldset-<?php echo esc_attr( $key ); ?>">

				<?php if( $field['type'] == 'checkbox' ) : ?>

					<label for="<?php echo esc_attr( $key ); ?>">
						<span class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
							<?php
								// Add the key to field.
								$field[ 'key' ] = $key;
								$template = isset( $field['template'] ) ? $field['template'] : $field['type'];
								WPUM()->templates
									->set_template_data( $field )
									->get_template_part( 'form-fields/' . $template, 'field' );
							?>
						</span>
						<?php echo esc_html( $field['label'] ); ?>
						<?php if( isset( $field['required'] ) && $field['required'] ) : ?>
							<span class="wpum-required">*</span>
						<?php endif; ?>
					</label>

				<?php else : ?>

					<label for="<?php echo esc_attr( $key ); ?>">
						<?php echo esc_html( $field['label'] ); ?>
						<?php if( isset( $field['required'] ) && $field['required'] ) : ?>
							<span class="wpum-required">*</span>
						<?php endif; ?>
					</label>
					<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
						<?php
							// Add the key to field.
							$field[ 'key' ] = $key;
							$template = isset( $field['template'] ) ? $field['template'] : $field['type'];
							WPUM()->templates
								->set_template_data( $field )
								->get_template_part( 'form-fields/' . $template, 'field' );
						?>
					</div>

				<?php endif; ?>

			</fieldset>
		<?php endforeach; ?>

		<input type="hidden" name="wpum_form" value="<?php echo $data->form; ?>" />
		<input type="hidden" name="step" value="<?php echo esc_attr( $data->step ); ?>" />
		<?php wp_nonce_field( 'verify_registration_form', 'registration_nonce' ); ?>

		<?php do_action( 'wpum_before_submit_button_registration_form', $data ); ?>

		<input type="submit" name="submit_registration" class="button" value="<?php esc_html_e( 'Register', 'wp-user-manager' ); ?>" />

	</form>

	<?php do_action( 'wpum_after_registration_form', $data ); ?>

</div>
