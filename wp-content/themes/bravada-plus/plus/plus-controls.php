<?php
/**
 * Plus extra Customizer controls
 *
 * @package Cryout Plus
 */

function cryout_customizer_extras_plus($wp_customize){

	class Cryout_Customize_Sortable_Control extends WP_Customize_Control {
			public $type = 'cryout-sortable';
			public function __construct($manager, $id, $args = array(), $options = array()) {
				parent::__construct( $manager, $id, $args );
			} // __construct()

			public function render_content() {

				if ( empty( $this->choices ) ) return;

				$name = '_customize-sortable-' . $this->id;

				$merger = array_merge(
					array_flip(explode(',',$this->value())),
					$this->choices
				);

				?>
				<?php if ( ! empty( $this->label ) ) { ?> <span class="customize-control-title"><?php echo esc_attr( $this->label ) ?></span> <?php } ?>
				<div class="sortable cryout-sortable-control">
					<input name="<?php echo $this->id; ?>" type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ) ?>" class="the_sorted" />
					<ul class="sortable-row ui-sortable">
						<?php foreach ( $merger as $id => $label ) :
							$disabled = ( preg_match('/(blocks-|boxes-|text-)/i', $id) ? -1 : 0 );
							if (!empty($this->input_attrs['statuses'])) {
								$statuses = $this->input_attrs['statuses'];
								if ( isset( $statuses[$id] ) && ( intval( $statuses[$id] ) != $disabled ) ) {
									$status = "status-enabled";
									$icon = "eye-on";
								} else {
									$status = "status-disabled";
									$icon = "eye-off";
								}
							}
							if (!empty($this->input_attrs['redirects'])) {
								$redirects = $this->input_attrs['redirects'];
								if (isset($redirects[$id]))
									$redirect = 'data-type="control" data-id="' .
												$redirects[$id] .
												'" class="sortable-edit cryout-customizer-focus"';
								else $redirect = 'class="sortable-edit"';
							}
						?>
						<li id="<?php echo $id ?>" class="<?php echo $status ?> ui-sortable-handle">
							<i class="icon-<?php echo $icon ?>"></i>
							<?php echo $label ?><a href="#" <?php echo $redirect ?>>
							<i class="icon-right-dir" title="<?php esc_attr_e('Manage this section','cryout') ?>"></i></a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div><!-- .sortable -->
				
				<div class="order-controls">
					<button class="refresh-order-button cryout-customizer-button" onclick="cryout_lporder_refresh(event)"><?php _e('Refresh Visibility', 'cryout') ?></button>
					<button class="reset-order-button cryout-customizer-button" onclick="cryout_lporder_reset(event)"><?php _e('Reset Order', 'cryout') ?></button>
				</div>

				<?php if ( ! empty( $this->description ) ) { ?> <span class="description cryout-nomove customize-control-description"><?php echo wp_kses_post( $this->description ) ?></span><?php } ?>
			<?php
			} // render_content()

			public function enqueue() {
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				//wp_enqueue_style( 'cryout-customizer-controls-css', get_template_directory_uri() . '/cryout/css/customizer.css', array('jquery'), _CRYOUT_THEME_VERSION );
				wp_enqueue_script( 'cryout-customizer-controls-js', get_template_directory_uri() . '/cryout/js/customizer-controls.js', array('jquery'), _CRYOUT_THEME_VERSION );
			} // enqueue()

	} // class Cryout_Customize_Sortable_Control

}

add_action( 'customize_register', 'cryout_customizer_extras_plus', 9 );

// FIN
