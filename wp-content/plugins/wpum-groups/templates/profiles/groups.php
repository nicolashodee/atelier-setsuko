<?php
/**
 * The Template for displaying the groups on the profile.
 *
 * This template can be overridden by copying it to yourtheme/wpum/profiles/groups.php
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

$own_profile = wpum_is_own_profile();

$privacy = array( 'public', 'private' );
if ( $own_profile ) {
	$privacy[] = 'hidden';
}
$groups = wpumgp_get_user_groups( $data->user->ID, $privacy );
?>
<div id="wpum-profile-groups" class="wpum-profile-groups">
	<?php if ( $own_profile && wpum_can_user_create_group( $data->user->ID ) ) :
		$plural = strtolower( WPUM_Group_Editor::plural() );
		?>
		<a href="<?php echo home_url( '/' . $plural . '/new' ); ?>" class="button wpum-create-group"><?php echo sprintf( __( 'Create %s', 'wpum-groups' ), WPUM_Group_Editor::singular() ); ?></a>
	<?php endif; ?>
	<?php
	if ( ! empty( $groups->posts ) ) : ?>
		<div class="wpum-groups">
			<?php
			$db = new WPUMG_DB_Group_Users;
			foreach ( $groups->posts as $group ) {
				$group = wpumgrp_prepare_group( $group, $data->user->ID, $db );

				WPUM()->templates->set_template_data( $group )->get_template_part( 'directory/single-group' );
			} ?>
		</div>
		<div id="profile-pagination">
			<?php
			echo paginate_links( array(
				'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
				'total'        => $groups->max_num_pages,
				'current'      => max( 1, get_query_var( 'paged' ) ),
				'format'       => '?paged=%#%',
				'show_all'     => false,
				'type'         => 'plain',
				'end_size'     => 2,
				'mid_size'     => 1,
				'prev_next'    => true,
				'prev_text'    => sprintf( '<i></i> %1$s', esc_html__( 'Previous page', 'wp-user-manager' ) ),
				'next_text'    => sprintf( '%1$s <i></i>', esc_html__( 'Next page', 'wp-user-manager' ) ),
				'add_args'     => false,
				'add_fragment' => '',
			) );
			?>
		</div>

	<?php else : ?>
		<?php
		WPUM()->templates->set_template_data( [
			'message' => sprintf( esc_html__( '%s is not a member of any groups yet.', 'wpum-groups' ), $data->user->display_name ),
		] )->get_template_part( 'messages/general', 'warning' );
		?>
	<?php endif; ?>

</div>
