<?php
/**
 * The Template for displaying the account forms.
 *
 * This template can be overridden by copying it to yourtheme/wpum/forms/form-group.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpum-template wpum-form wpum-account-form">

	<h2><?php echo esc_html( $data->step_name ); ?></h2>

	<?php do_action( 'wpumg_before_group_form' ); ?>

		<form action="<?php echo esc_url( $data->action ); ?>" method="post" id="wpumg-submit-new-group-form" enctype="multipart/form-data">

			<?php foreach ( $data->fields['group'] as $key => $field ) : ?>
				<fieldset class="fieldset-<?php echo esc_attr( $key ); ?>">

					<?php if( $field['type'] === 'checkbox' ) : ?>

						<label for="<?php echo esc_attr( $key ); ?>">
							<span class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
								<?php
									// Add the key to field.
									$field[ 'key' ] = $key;
									WPUM()->templates
										->set_template_data( $field )
										->get_template_part( 'form-fields/' . $field['template'], 'field' );
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
								WPUM()->templates
									->set_template_data( $field )
									->get_template_part( 'form-fields/' . $field['template'], 'field' );
							?>
						</div>

					<?php endif; ?>

				</fieldset>
			<?php endforeach; ?>
			<?php if ( $data->group_id ) : ?>
				<input type="hidden" name="wpum_group_id" value="<?php echo $data->group_id; ?>"/>
			<?php endif; ?>
			<input type="hidden" name="wpum_form" value="<?php echo $data->form; ?>" />
			<input type="hidden" name="wpumg_user_id" value="<?php echo $data->user_id; ?>" />
			<?php wp_nonce_field( 'verify_group_form', 'group_nonce' ); ?>
			<input type="submit" name="submit_group" class="button" value="<?php echo ( $data->group_id ) ? sprintf( __( 'Update %s', 'wpum-groups' ), WPUM_Group_Editor::singular() ) : sprintf( __( 'Create %s', 'wpum-groups' ), WPUM_Group_Editor::singular() ); ?>" />
			<?php if ( $data->group_id ) : ?>
				<p><a href="<?php echo get_permalink( $data->group_id ); ?>"><?php _e( 'Cancel', 'wpum-groups' ); ?></a></p>
			<?php endif; ?>
		<?php do_action( 'wpumg_after_group_form' ); ?>

	</form>

</div>
