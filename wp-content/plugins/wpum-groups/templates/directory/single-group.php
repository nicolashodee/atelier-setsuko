<?php
/**
 * The Template for displaying the directory single user item loop.
 *
 * This template can be overridden by copying it to yourtheme/wpum/directory/single-group.php
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

$privacy = get_post_meta( $data->ID, '_group_privacy_method', true );
if ( $privacy == 'public' ) {
	$privacy_method = __( 'Public Group', 'wpum-groups' );
} elseif ( $privacy == 'private' ) {
	$privacy_method = __( 'Private Group', 'wpum-groups' );
} elseif ( $privacy == 'hidden' ) {
	$privacy_method = __( 'Hidden Group', 'wpum-groups' );
}

$singular = WPUM_Group_Editor::singular()
?>

<div class="wpum-single-group group<?php echo intval( $data->ID ); ?>">
	<div class="wpum-row wpum-middle-xs">
		<?php if ( has_post_thumbnail( $data->ID ) ) : ?>
		<div class="wpum-col-xs-2" id="directory-avatar">
				<a href="<?php echo get_permalink( $data->ID ); ?>">
					<img src="<?php echo esc_url_raw( get_the_post_thumbnail_url( $data->ID ) ); ?>" width="" height=""
					     alt="<?php echo esc_html( $data->post_title ); ?>"/>
				</a>
		</div>
		<?php endif; ?>
		<div class="<?php echo has_post_thumbnail( $data->ID ) ? 'wpum-col-xs-7' : 'wpum-col-xs-9'; ?>">
			<h3 class="wpumg-name">
				<a href="<?php echo get_permalink( $data->ID ); ?>"><?php echo esc_html($data->post_title); ?></a>
			</h3>
			<p class="wpumg-meta">
				<?php echo esc_html( $privacy_method ); ?>
			</p>
			<p class="wpumg-description">
				<?php echo wp_kses_post( $data->post_content ); ?>
			</p>
		</div>

		<div class="wpum-col-xs-3">
			<?php wpumgp_group_member_actions( $data->ID, $data->user_id, $data->is_member, $data->hide_buttons ); ?>
			<p class="wpumg-meta wpumg-bar"><?php echo sprintf( esc_html( __( 'Total members: %d', 'wpum-groups' ) ), $data->members_count ); ?></p>
		</div>
	</div>
</div>
