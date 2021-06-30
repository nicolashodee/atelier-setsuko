<?php
/**
 * The Template for displaying the group cover.
 *
 * This template can be overridden by copying it to yourtheme/wpum/group/cover.php
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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cover_image = get_the_post_thumbnail_url( $data->group->ID, 'large' );

$display_cover_image = apply_filters( 'wpum_group_display_cover_image', true );
?>
<?php if ( $display_cover_image && $cover_image ) : ?>
	<div id="header-cover-image" style="background-image: url(<?php echo esc_url( $cover_image ); ?>);">
	</div>
<?php endif; ?>
