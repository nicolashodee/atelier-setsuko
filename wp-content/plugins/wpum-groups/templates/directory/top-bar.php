<?php
/**
 * The Template for displaying the directory top bar.
 *
 * This template can be overridden by copying it to yourtheme/wpum/directory/top-bar.php
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

if ( ! is_user_logged_in() ) {
	return;
}
?>

<div id="wpum-directory-top-bar">

	<div class="wpum-row">

		<div class="wpum-col-xs-3">
			<input type="submit" id="show-all-groups-number" name="show-all-groups" value="<?php echo esc_html( sprintf( __( 'All %s', 'wpumg-group' ), $data->plural ) ); ?> | <?php echo intval($data->total); ?>" <?php echo ( isset( $_GET['show-all-groups'] ) ? 'disabled="disabled"' : false ); ?> />
		</div>

		<?php if ( is_user_logged_in() ) : ?>
		<div class="wpum-col-xs-3">
			<input type="submit" id="show-my-groups-number" name="show-my-groups" value="<?php echo esc_html( sprintf( __( 'My %s', 'wpumg-group' ), $data->plural  ) ); ?> | <?php echo intval($data->count_user_groups); ?>" <?php echo ( isset( $_GET['show-my-groups'] ) ? 'disabled="disabled"' : false ); ?> />
		</div>
		<?php endif; ?>

		<?php if ( $data->create_new_form ) : ?>
			<div class="wpum-col-xs-4">
				<a href="<?php echo home_url( '/groups/new' ); ?>"><?php echo esc_html( sprintf( __( 'Create a %s', 'wpum-groups' ), $data->singular  ) ); ?></a>
			</div>
		<?php endif; ?>

	</div>

</div>
