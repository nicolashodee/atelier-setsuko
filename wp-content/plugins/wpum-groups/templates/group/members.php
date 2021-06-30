<?php
/**
 * The Template for displaying the group members tab.
 *
 * This template can be overridden by copying it to yourtheme/wpum/group/members.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Get all members of group
$members = wpumgp_get_group_members( $data->group->ID, 'approved' );
?>

<div id="wpum-group-members">

	<div id="wpum-directory-users-list">
		<?php foreach( $members as $user ) :
			$user = get_user_by( 'id', $user->user_id );
			if ( wpum_group_display_user( $data->group->ID, $user->ID ) ) {
				WPUM()->templates
				->set_template_data( $user )
				->get_template_part( 'group/single', 'member' );
			}
			?>
		<?php endforeach; ?>
	</div>
</div>