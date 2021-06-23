<div class="cryout_message <?php esc_attr_e( $class ); ?>">
	<?php foreach ( $this->notices[ $type ] as $notice ) : ?>
		<p><?php echo wp_kses( $notice, wp_kses_allowed_html( 'post' ) ); ?></p>
	<?php endforeach; ?>
	<button class="notice-dismiss" type="button">
		<span class="screen-reader-text"><?php _e('Dismiss this notice.', 'cryout-featured-content' ) ?></span>
	</button>
</div>