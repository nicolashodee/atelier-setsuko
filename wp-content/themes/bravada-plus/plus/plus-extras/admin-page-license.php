<?php
/*
 * Plus admin page - license subsection
 *
 * @package Cryout Plus
 */
global $cryout_migrate_options; 

if ( ! is_multisite() ) {
 
?>

<p><?php printf( __('To receive %1$s Plus theme updates and be able to install companion plugins you need to enter your license key in the field below.', 'cryout'), _CRYOUT_THEME_LABEL ) ?></p>
<form method="POST" action="options.php">
	<?php settings_fields( 'cryout_dashboard_settings' ); ?>
	<?php do_settings_sections( 'cryout_dashboard_settings' ); ?>
	<br>
	<label for="cryout_license_key"><?php _e('License Key', 'cryout') ?></label>
	<input name="cryout_license_key" id="cryout_license_key" value="<?php echo esc_attr( get_theme_mod('cryout_license_key') ); ?>" />
	<br><br>
	<p><em><?php printf( __('You can retrieve your license key from your %1$s.', 'cryout'), sprintf( '<a href="https://www.cryoutcreations.eu/my-account" target="_blank">%1$s</a>', __( 'account page', 'cryout' ) ) ) ?></em></p>
	<?php submit_button( 'Save Key', 'button button-primary button-bigger' ); ?>
</form>

<?php } else { ?>

<p><?php _e('Auto-update functionality is not currently available for Multisite installations.', 'cryout') ?></p>

<?php }