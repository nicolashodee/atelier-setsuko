<?php
/**
 * The Template for displaying the social login buttons.
 *
 * This template can be overridden by copying it to yourtheme/wpum/social-buttons.php
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

$width = '32%';

$count = count( $data->socials );

if ( $count > 3 ) {
	$width = '23%';
} else if( $count == 2 ) {
	$width = '49%';
}

$redirect = isset( $data->atts['redirect'] ) ? $data->atts['redirect'] : false;

?>

<style>
.wpum-social-login-buttons {
	display: flex;
	justify-content: space-between;
}

.wpum-social-btn {
	width: <?php echo $width; ?>;
}

.wpum-social-btn a {
	margin: 10px 0;
	padding: 10px 15px;
	display: block;
	text-align: center;
	border-radius: 3px;
	transition: 0.5s;
	background: #ccc;
	color: #fff;
}

.wpum-social-btn a.wpum-facebook {
	background: #3b5998;
}

.wpum-social-btn a.wpum-google {
	background: #dd4b39;
}

.wpum-social-btn a.wpum-google {
	background: #dd4b39;
}

.wpum-social-btn a.wpum-instagram {
	background:#405de6;
}

.wpum-social-btn a.wpum-LinkedIn {
	background:#0077b5;
}

.wpum-social-btn a.wpum-twitter {
	background:#1da1f2;
}

</style>

<div class="wpum-social-login-buttons">

	<?php

	foreach ( $data->socials as $social ) :

		$name = '';

		switch ( $social ) {
			case 'facebook':
				$name = __( 'Login with Facebook', 'wpum-social-login' );
				break;
			case 'google':
				$name = __( 'Login with Google', 'wpum-social-login' );
				break;
			case 'instagram':
				$name = __( 'Login with Instagram', 'wpum-social-login' );
				break;
			case 'LinkedIn':
				$name = __( 'Login with LinkedIn', 'wpum-social-login' );
				break;
			case 'twitter':
				$name = __( 'Login with Twitter', 'wpum-social-login' );
				break;
		}

		$name = apply_filters( 'wpum_social_login_button_text', $name, $social );

	?>

	<div class="wpum-social-btn">
		<a href="<?php echo esc_url( wpumsl_get_social_login_url( strtolower( $social ), $redirect ) ); ?>" class="wpum-<?php echo $social; ?>"><span class="wpumsl-icon-<?php echo strtolower( $social ); ?>"></span> <?php echo esc_html( $name ); ?></a>
	</div>

	<?php endforeach; ?>

</div>
