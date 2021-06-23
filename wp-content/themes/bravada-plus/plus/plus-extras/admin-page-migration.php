<?php
/*
 * Plus admin page - migration subsection
 *
 * @package Cryout Plus
 */
global $cryout_migrate_options; 
 
?>

<p><?php printf( __('%1$s Plus is capable of migrating compatible options from the base %1$s theme.', 'cryout'), _CRYOUT_THEME_LABEL ) ?></p>

<?php if ($cryout_migrate_options->migrateable_options()) { ?>
	<form method="POST" action="">
	<p><?php printf( __('It appears %s was used before on this site. Click the button below to migrate the options:', 'cryout'), _CRYOUT_THEME_LABEL ) ?></p>
	<?php wp_nonce_field( 'perform_migration', '_cryout_migrate_nonce' ); ?>
	<button type="submit" class="button export-button" onclick="return confirm('<?php printf( __('Migration will overwrite any existing %s Plus options. Are you sure?', 'cryout'), _CRYOUT_THEME_LABEL ) ?>')"><?php _e('Migrate options now','cryout') ?></button>
	<!-- href="<?php echo admin_url( _CRYOUT_THEME_PAGE_URL . '&migrate=1#migrate' ) ?>" -->
	<p><em><?php printf( __('CAUTION: If you have started configuring %s Plus, this procedure will completely overwrite any configured options!', 'cryout'), _CRYOUT_THEME_LABEL ) ?></em></p>
	</form>
<?php } else { ?>
	<p><?php printf( __('%s does not appear to have been installed before on this site. There are no options to migrate.', 'cryout'), _CRYOUT_THEME_LABEL ) ?></p>
<?php } ?>