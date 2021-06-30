<?php
/**
 * The Template for displaying the profile intro details.
 *
 * This template can be overridden by copying it to yourtheme/wpum/profiles/intro.php
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

<div id="header-profile-details">
	<div id="header-name-container" class="wpum-row">
		<div class="wpum-col-xs-8">
			<h2>
				<?php echo esc_html( $data->group->post_title ); ?>
				<?php
				if ( in_array( 'wpum_group_admin', $data->roles ) ) : ?>
					<a href="<?php echo esc_url( get_permalink() . 'edit' ); ?>"><small><?php echo esc_html( sprintf( __( '( Edit %s )', 'wpum-groups' ), strtolower( WPUM_Group_Editor::singular() ) ) ); ?></small></a>
				<?php endif; ?>
				<br>
				<?php if ( $data->group->privacy_method === 'private' && 'approved' !== $data->group->user_status ) : ?>
					<small style="text-decoration: none; font-weight: normal;"><?php echo esc_html( apply_filters( 'wpum_group_private_group_heading_text', sprintf( __( 'Private %s, requires approval', 'wpum-groups' ), strtolower( WPUM_Group_Editor::singular() ) ), $data->group->singular ) ); ?></small>
				<?php endif; ?>
			</h2>

		</div>

		<div class="wpum-col-xs-4 wpum-group-actions">
			<?php
			wpumgp_group_member_actions( $data->group->ID, $data->current_user_id, $data->group->is_member, $data->group->hide_buttons );
			?>
		</div>
	</div>
	<div id="profile-navigation">
		<?php
			WPUM()->templates->set_template_data( [
					'group'           => $data->group,
					'current_user_id' => $data->current_user_id,
					'tabs'            => wpum_get_registered_group_tabs( $data->group->ID )
				] )
				->get_template_part( 'group/navigation' );
		?>
	</div>
</div>
