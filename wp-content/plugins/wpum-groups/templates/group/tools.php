<?php
/**
 * The Template for displaying the group members tab.
 *
 * This template can be overridden by copying it to yourtheme/wpum/group/moderation.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 *
 * Export member
 */

if ( empty( wpumgrp_group_user_has_role( apply_filters( 'wpum_group_id', get_the_ID() ), get_current_user_id(), 'wpum_group_admin' ) ) ){
	exit;
}
?>
<div id="wpum-group-tools">
	<div id="wpum-directory-tools">
    	<p>Export members to CSV</p>
		<a class="wpum-export-members-group button" href="<?php echo esc_url( wp_nonce_url( get_permalink( get_the_ID() ) . 'tools?wpum-action=export_group_user&group_id=' . get_the_ID(), 'export_group_user' ) ); ?>">Export</a>
	</div>
</div>
