<?php
/**
 * The Template for displaying the group about tab content.
 *
 * This template can be overridden by copying it to yourtheme/wpum/group/about.php
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

<div id="group-content-about" class="group-content-settings">
	<p><?php echo esc_html( $data->group->post_content ); ?></p>

</div>
