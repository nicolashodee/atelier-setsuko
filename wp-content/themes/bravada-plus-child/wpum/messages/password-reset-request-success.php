<?php
/**
 * The template that displays the success message after requesting a new password.
 *
 * This template can be overridden by copying it to yourtheme/wpum/messages/password-reset-request-success.php
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

// Retrieve the curren
$masked_email = wpum_mask_email_address( $data->email );
?>

<div class="wpum-password-reset-request-success wpum-message success">
	<p><?php printf( esc_html__( 'Nous avons envoyé un email à %s avec les instructions pour réinitialiser le mot de passe.', 'wp-user-manager' ), '<strong>' . $masked_email . '</strong>' ); ?></p>
</div>

<p><?php printf( esc_html__( 'Si l\'email ne s\'affiche pas, pensez à vérifier vos spams. Nous l\'avons envoyé depuis l\'adresse %s.', 'wp-user-manager' ), '<strong>' . antispambot( $data->from ) . '</strong>' ); ?></p>
