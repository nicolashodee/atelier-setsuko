<?php
/**
 * Page/post appearance metas
 *
 * @package Cryout Plus
 */

final class CryoutPlus_Appearance_Meta_Backend {

	public function __construct(){

		add_action( 'admin_enqueue_scripts', array( $this, 'meta_scripts' ) );
		// styling is part of the theme's main meta.css

		// Hook meta boxes into 'add_meta_boxes'
		add_action( 'add_meta_boxes', array( $this, 'meta_add' ) );

		// Extend layout meta option to posts and jetpack-portfolio
		add_action( 'publish_post', _CRYOUT_THEME_SLUG . '_save_custom_post_metadata' );
		add_action( 'draft_post',   _CRYOUT_THEME_SLUG . '_save_custom_post_metadata' );
		add_action( 'future_post',  _CRYOUT_THEME_SLUG . '_save_custom_post_metadata' );
		add_action( 'publish_jetpack-portfolio', _CRYOUT_THEME_SLUG . '_save_custom_post_metadata' );
		add_action( 'draft_jetpack-portfolio',   _CRYOUT_THEME_SLUG . '_save_custom_post_metadata' );
		add_action( 'future_jetpack-portfolio',  _CRYOUT_THEME_SLUG . '_save_custom_post_metadata' );

		// Add new appearance meta options to pages & posts
		add_action( 'publish_page', array( $this, 'meta_save'), 10, 2 );
		add_action( 'draft_page',   array( $this, 'meta_save'), 10, 2 );
		add_action( 'future_page',  array( $this, 'meta_save'), 10, 2 );
		add_action( 'publish_post', array( $this, 'meta_save'), 10, 2 );
		add_action( 'draft_post',   array( $this, 'meta_save'), 10, 2 );
		add_action( 'future_post',  array( $this, 'meta_save'), 10, 2 );
		
		// Filter header image URL on the frontend
		add_filter( _CRYOUT_THEME_SLUG . '_header_image_url', array( $this, 'header_image_url' ) );
		
	} // __construct()

	// Appearance meta init
	function meta_add( $post ) {
		global $wp_meta_boxes;

		// add appearance meta to posts & pages
		add_meta_box(
			'cryout_appearance_meta',
			__( 'Cryout Theme Meta', 'cryout' ),
			array( $this, 'meta_form' ),
			array( 'page', 'post' ),
			apply_filters( _CRYOUT_THEME_SLUG . '_appearance_meta_box_context', 'normal' ), // 'normal', 'side', 'advanced'
			apply_filters( _CRYOUT_THEME_SLUG . '_appearance_meta_box_priority', 'default' ) // 'high', 'core', 'low', 'default'
		);

		// extend layouts to posts and jetpack portfolio
		add_meta_box(
			_CRYOUT_THEME_SLUG . '_layout',
			__( 'Layout', 'cryout' ),
			_CRYOUT_THEME_SLUG . '_layout_meta_box',
			array( 'post', 'jetpack-portfolio' ),
			apply_filters( _CRYOUT_THEME_SLUG . '_layout_meta_box_context', 'side' ), // 'normal', 'side', 'advanced'
			apply_filters( _CRYOUT_THEME_SLUG . '_layout_meta_box_priority', 'default' ) // 'high', 'core', 'low', 'default'
		);

	} // meta_add()

	// Appearance meta HTML
	function meta_form( $post ) {

		// get the background color
		$color = trim( get_post_meta( $post->ID, '_cryout_meta_bgcolor', true ), '#' );
		if (empty($color)) $color = trim( get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgcolor', true ), '#' ); // backwards compatibility

		// get the background image attachment ID
		$attachment_id = get_post_meta( $post->ID, '_cryout_meta_bgimageid', true );
		if (empty($attachment_id)) $attachment_id = get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgimageid', true ); // backwards compatibility

		// if an attachment ID was found, get the image source
		if ( !empty( $attachment_id ) )
			$image = wp_get_attachment_image_src( absint( $attachment_id ), 'large' );

		// get the image URL
		$url = !empty( $image ) && isset( $image[0] ) ? $image[0] : '';

		// get the background image settings
		$repeat     = get_post_meta( $post->ID, '_cryout_meta_bgrepeat', true );
		$position_x = get_post_meta( $post->ID, '_cryout_meta_bgpositionx', true );
		$position_y = get_post_meta( $post->ID, '_cryout_meta_bgpositiony', true );
		$attachment = get_post_meta( $post->ID, '_cryout_meta_bgattachment', true );
		$size 		= get_post_meta( $post->ID, '_cryout_meta_bgsize', true );
		
		// custom header
		$custom_header = get_post_meta( $post->ID, '_cryout_meta_custheader', true );
		
		// backwards compatibility
		if (empty($repeat))     $repeat 		= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgrepeat', true );
		if (empty($position_x)) $position_x 	= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgpositionx', true );
		if (empty($position_y))	$position_y		= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgpositiony', true );
		if (empty($attachment)) $attachment		= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgattachment', true );
		if (empty($size)) 		$size			= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_bgsize', true );
		// end backwards compatibility

		// get theme mods
		$mod_repeat     = get_theme_mod( 'background_repeat',     'repeat' );
		$mod_position_x = get_theme_mod( 'background_position_x', 'left'   );
		$mod_position_y = get_theme_mod( 'background_position_y', 'top'    );
		$mod_attachment = get_theme_mod( 'background_attachment', 'scroll' );
		$mod_size 	    = get_theme_mod( 'background_size', 	  'auto' );

		// make sure values are set for the image options
		$repeat     = !empty( $repeat )     ? $repeat     : $mod_repeat;
		$position_x = !empty( $position_x ) ? $position_x : $mod_position_x;
		$position_y = !empty( $position_y ) ? $position_y : $mod_position_y;
		$attachment = !empty( $attachment ) ? $attachment : $mod_attachment;

		// set up an array of allowed values for the options
		$repeat_options = array(
			'no-repeat' => __( 'No Repeat',           'cryout' ),
			'repeat'    => __( 'Repeat',              'cryout' ),
			'repeat-x'  => __( 'Repeat Horizontally', 'cryout' ),
			'repeat-y'  => __( 'Repeat Vertically',   'cryout' ),
		);
		$position_x_options = array(
			'left'   => __( 'Left',   'cryout' ),
			'right'  => __( 'Right',  'cryout' ),
			'center' => __( 'Center', 'cryout' ),
		);
		$position_y_options = array(
			'top'    => __( 'Top',    'cryout' ),
			'bottom' => __( 'Bottom', 'cryout' ),
			'center' => __( 'Center', 'cryout' ),
		);
		$attachment_options = array(
			'scroll' => __( 'Scroll', 'cryout' ),
			'fixed'  => __( 'Fixed',  'cryout' ),
		);
		$size_options = array(
			'auto'     => __( 'Original', 'cryout' ),
			'contain'  => __( 'Fit to Screen',  'cryout' ),
			'cover'    => __( 'Fill Screen',  'cryout' ),
		);
		
		// custom header
		$custom_header_options = array(
			0	=> __( 'Default header', 'cryout' ),
			1	=> __( 'Force featured image',  'cryout' ),
			2	=> __( 'Hide header image',  'cryout' ),
		);

		// hide elements
		$hide_mainmenu = 	get_post_meta( $post->ID, '_cryout_meta_hide_mainmenu', true );
		$hide_headerimg = 	get_post_meta( $post->ID, '_cryout_meta_hide_headerimg', true );
		$hide_breadcrumbs = get_post_meta( $post->ID, '_cryout_meta_hide_breadcrumbs', true );
		$hide_title = 		get_post_meta( $post->ID, '_cryout_meta_hide_title', true );
		$hide_colophon = 	get_post_meta( $post->ID, '_cryout_meta_hide_colophon', true );
		$hide_footer =		get_post_meta( $post->ID, '_cryout_meta_hide_footer', true );
		// backwards compatibility 
		if (empty($hide_mainmenu)) 		$hide_mainmenu 		= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_mainmenu', true );
		if (empty($hide_headerimg)) 	$hide_headerimg 	= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_headerimg', true );
		if (empty($hide_breadcrumbs)) 	$hide_breadcrumbs 	= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_breadcrumbs', true );
		if (empty($hide_title)) 		$hide_title			= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_title', true );
		if (empty($hide_colophon)) 		$hide_colophon		= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_colophon', true );
		if (empty($hide_footer))		$hide_footer 		= get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_hide_footer', true );
		// end backwards compatibility 

		// template fields
		for ( $i=0; $i<=7; $i++ ) {
			${'template_field'.$i} = get_post_meta( $post->ID, '_cryout_meta_templatefield_' . $i, true );
			if (empty(${'template_field'.$i})) ${'template_field'.$i} = get_post_meta( $post->ID, '_' . _CRYOUT_THEME_SLUG . '_meta_templatefield_' . $i, true ); // backwards compatibility 
		}
		// field0 is a dedicated image field, retrieve img url based on id
		$template_field0_img = '';
		if ( !empty( $template_field0 ) ) {
			list($template_field0_img,) = wp_get_attachment_image_src( absint( $template_field0 ), 'large' );
		};

		// figure out page template
		if (!empty($post)) {
			$page_template = get_post_meta($post->ID, '_wp_page_template', true);
		} else {
			$page_template = '';
		}

		?>

		<div id="cryout-meta-tabs" class="cryout-tabs">
		  <ul>
			<li><a href="#custom-background"><?php _e('Custom Background', 'cryout') ?></a></li>
			<li><a href="#custom-header"><?php _e('Custom Header', 'cryout') ?></a></li>
			<li><a href="#extra"><?php _e('Hide Elements', 'cryout') ?></a></li>
			<li><a href="#template"><?php _e('Template Options', 'cryout') ?></a></li>
		  </ul>
		  <div id="custom-background">


			<p>
				<label for="cryout-meta-background-color"><?php _e( 'Background Color', 'cryout' ); ?></label>
				<input type="text" name="cryout-meta-background-color" class="cryout-meta-wp-color-picker" value="#<?php echo esc_attr( $color ); ?>" />
			</p>

			<div class="cryout-media-selector">
				<p>
					<input type="hidden" name="cryout-meta-background-image" class="cryout-media-image" value="<?php echo esc_attr( $attachment_id ); ?>" />
					<label><?php _e( 'Background Image', 'cryout' ); ?></label>
					<a href="#" class="cryout-add-media cryout-add-media-img"><img class="cryout-media-image-url" src="<?php echo esc_url( $url ); ?>" <?php if (empty($url)) echo 'style="display: none;"'; ?> /></a>
					<a href="#" class="button cryout-add-media cryout-add-media-text"><?php _e( 'Select Image', 'cryout' ); ?></a>
					<a href="#" class="button cryout-remove-media" <?php if (empty($url)) echo 'style="display: none;"'; ?>><?php _e( 'Remove Image', 'cryout' ); ?></a>
				</p>

				<div class="cryout-meta-background-image-options">

					<p>
						<label for="cryout-meta-background-repeat"><?php _e( 'Repeat', 'cryout' ); ?></label>
						<select class="widefat" name="cryout-meta-background-repeat">
						<?php foreach( $repeat_options as $option => $label ) { ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $repeat, $option ); ?> /><?php echo esc_html( $label ); ?></option>
						<?php } ?>
						</select>
					</p>

					<p>
						<label for="cryout-meta-background-position-x"><?php _e( 'Horizontal Position', 'cryout' ); ?></label>
						<select class="widefat" name="cryout-meta-background-position-x">
						<?php foreach( $position_x_options as $option => $label ) { ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $position_x, $option ); ?> /><?php echo esc_html( $label ); ?></option>
						<?php } ?>
						</select>
					</p>

					<p>
						<label for="cryout-meta-background-position-y"><?php _e( 'Vertical Position', 'cryout' ); ?></label>
						<select class="widefat" name="cryout-meta-background-position-y">
						<?php foreach( $position_y_options as $option => $label ) { ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $position_y, $option ); ?> /><?php echo esc_html( $label ); ?></option>
						<?php } ?>
						</select>
					</p>

					<p>
						<label for="cryout-meta-background-attachment"><?php _e( 'Attachment', 'cryout' ); ?></label>
						<select class="widefat" name="cryout-meta-background-attachment">
						<?php foreach( $attachment_options as $option => $label ) { ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $attachment, $option ); ?> /><?php echo esc_html( $label ); ?></option>
						<?php } ?>
						</select>
					</p>

					<p>
						<label for="cryout-meta-background-size"><?php _e( 'Size', 'cryout' ); ?></label>
						<select class="widefat" name="cryout-meta-background-size">
						<?php foreach( $size_options as $option => $label ) { ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $size, $option ); ?> /><?php echo esc_html( $label ); ?></option>
						<?php } ?>
						</select>
					</p>

				</div>
			</div><!--cryout-media-selector-->

		  </div><!--custom background-->
		  
		  <div id="custom-header">
		  
			<p>
				<label for="cryout-meta-header-image"><?php _e( 'Header Image Behaviour', 'cryout' ); ?></label>
				<select class="widefat" name="cryout-meta-header-image">
				<?php foreach( $custom_header_options as $option => $label ) { ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $custom_header, $option ); ?> /><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
			</p>		  
		  
		  </div><!--custom header-->
		  <div id="extra">
			<p>
				<input type="checkbox" name="cryout-meta-hide-mainmenu" id="cryout-meta-hide-mainmenu" <?php checked( $hide_mainmenu, 1 ) ?>>
				<label for="cryout-meta-hide-mainmenu"><?php _e( 'Main Menu', 'cryout' ); ?></label>
			</p>
			<p>
				<input type="checkbox" name="cryout-meta-hide-headerimg" id="cryout-meta-hide-headerimg" <?php checked( $hide_headerimg, 1 ) ?>>
				<label for="cryout-meta-hide-headerimg"><?php _e( 'Header Image', 'cryout' ); ?></label>
			</p>
			<p>
				<input type="checkbox" name="cryout-meta-hide-breadcrumbs" id="cryout-meta-hide-breadcrumbs" <?php checked( $hide_breadcrumbs, 1 ) ?>>
				<label for="cryout-meta-hide-breadcrumbs"><?php _e( 'Breadcrumbs', 'cryout' ); ?></label>
			</p>
			<p>
				<input type="checkbox" name="cryout-meta-hide-title" id="cryout-meta-hide-title" <?php checked( $hide_title, 1 ) ?>>
				<label for="cryout-meta-hide-title"><?php _e( 'Page/Post Title', 'cryout' ); ?></label>
			</p>
			<p>
				<input type="checkbox" name="cryout-meta-hide-colophon" id="cryout-meta-hide-colophon" <?php checked( $hide_colophon, 1 ) ?>>
				<label for="cryout-meta-hide-colophon"><?php _e( 'Footer Widgets', 'cryout' ); ?></label>
			</p>
			<p>
				<input type="checkbox" name="cryout-meta-hide-footer" id="cryout-meta-hide-footer" <?php checked( $hide_footer, 1 ) ?>>
				<label for="cryout-meta-hide-footer"><?php _e( 'Footer', 'cryout' ); ?></label>
			</p>
		  </div><!--extra-->
		  <div id="template"> 
			<?php if ( preg_match( '/plus-templates\/template-contact/i', $page_template ) ) { ?>
				<div class="cryout-media-selector">
					<p>
						<input type="hidden" name="cryout-meta-templatefield0" class="cryout-media-image" value="<?php echo esc_attr( $template_field0 ); ?>" />
						<label><?php _e( 'Logo / Banner Image', 'cryout' ); ?></label>
						<a href="#" class="cryout-add-media cryout-add-media-img"><img class="cryout-media-image-url" src="<?php echo esc_url( $template_field0_img ); ?>" <?php if (empty($template_field0_img)) echo 'style="display: none;"'; ?> /></a>
						<a href="#" class="button cryout-add-media cryout-add-media-text"><?php _e( 'Select Image', 'cryout' ); ?></a>
						<a href="#" class="button cryout-remove-media" <?php if (empty($template_field0_img)) echo 'style="display: none;"'; ?>><?php _e( 'Remove Image', 'cryout' ); ?></a>
					</p>
				</div>
				<p>
					<label for="cryout-meta-templatefield1"><?php _e( 'Address', 'cryout' ); ?></label>
					<textarea name="cryout-meta-templatefield1"><?php echo esc_attr( $template_field1 ); ?></textarea>
				</p>
				<p>
					<label for="cryout-meta-templatefield2"><?php _e( 'Phone', 'cryout' ); ?></label>
					<input type="text" name="cryout-meta-templatefield2" value="<?php echo esc_attr( $template_field2 ); ?>" class="regular-text" />
				</p>
				<p>
					<label for="cryout-meta-templatefield3"><?php _e( 'Mobile', 'cryout' ); ?></label>
					<input type="text" name="cryout-meta-templatefield3" value="<?php echo esc_attr( $template_field3 ); ?>" class="regular-text" />
				</p>
				<p>
					<label for="cryout-meta-templatefield4"><?php _e( 'E-mail', 'cryout' ); ?></label>
					<input type="text" name="cryout-meta-templatefield4" value="<?php echo esc_attr( $template_field4 ); ?>" class="regular-text" />
				</p>
				<p>
					<label for="cryout-meta-templatefield7"><?php _e( 'Opening Hours', 'cryout' ); ?></label>
					<input type="text" name="cryout-meta-templatefield7" value="<?php echo esc_attr( $template_field7 ); ?>" class="regular-text" />
				</p>
				<p>
					<label for="cryout-meta-templatefield5"><?php _e( 'Google Map URL', 'cryout' ); ?></label>
					<textarea name="cryout-meta-templatefield5"><?php echo esc_attr( $template_field5 ); ?></textarea>
				</p>
				<p>&nbsp;</p>
				<p>
					<label for="cryout-meta-templatefield6"><?php _e( 'Contact Form Shortcode', 'cryout' ); ?></label>
					<input type="text" name="cryout-meta-templatefield6" value="<?php echo esc_attr( $template_field6 ); ?>" class="regular-text" />
				</p>

			<?php } elseif ( $page_template == 'plus-templates/template-about.php' ) { ?>
				<div class="cryout-media-selector">
					<p>
						<input type="hidden" name="cryout-meta-templatefield0" class="cryout-media-image" value="<?php echo esc_attr( $template_field0 ); ?>" />
						<label><?php _e( 'Logo / Banner Image', 'cryout' ); ?></label>
						<a href="#" class="cryout-add-media cryout-add-media-img"><img class="cryout-media-image-url" src="<?php echo esc_url( $template_field0_img ); ?>" <?php if (empty($template_field0_img)) echo 'style="display: none;"'; ?> /></a>
						<a href="#" class="button cryout-add-media cryout-add-media-text"><?php _e( 'Select Image', 'cryout' ); ?></a>
						<a href="#" class="button cryout-remove-media" <?php if (empty($template_field0_img)) echo 'style="display: none;"'; ?>><?php _e( 'Remove Image', 'cryout' ); ?></a>
					</p>
				</div>
				<p>
					<label for="cryout-meta-templatefield1"><?php _e( 'Team Shortcode', 'cryout' ); ?></label>
					<input type="text" name="cryout-meta-templatefield1" value="<?php echo esc_attr( $template_field1 ); ?>" class="regular-text" />
				</p>

			<?php } elseif ( !empty($page_template) && ($page_template != 'default') ) { ?>
			<p><?php _e('This page template has no additional options.', 'cryout' ); ?></p>
			<?php } else {  ?>
			<p><?php _e('Select a page template and save draft or publish the page to activate template specific options here. Note that not all templates have specific options.', 'cryout' ); ?></p>
			<?php } ?>
		  </div><!--template-->

		  <?php /* this hidden input is used in the save check to separate between normal saves and quick saves */ ?>
		  <input type="hidden" name="cryout-meta-data-set" value="true">

		</div>
		<?php

	} // meta_form()


	// validate, sanitize, and save post metadata
	function meta_save( $post_id, $post ) {

		// don't break on quick edit
		if ( ! isset( $post ) || ! is_object( $post ) ) {
			return;
		}

		// get the post type object
		$post_type = get_post_type_object( $post->post_type );

		// check if the current user has permission to edit the post
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		// don't save if the post is only a revision
		if ( 'revision' == $post->post_type )
			return;

		// exit if our special meta identifier is not present in the posted data
		if ( empty( $_POST['cryout-meta-data-set'] ) ) return;

		// sanitize color
		$color = (isset($_POST['cryout-meta-background-color']) ? preg_replace( '/[^0-9a-fA-F]/', '', $_POST['cryout-meta-background-color'] ) : '' );

		// make sure the background image attachment id is an absolute integer
		$image_id = (isset($_POST['cryout-meta-background-image']) ? absint( $_POST['cryout-meta-background-image'] ) : 0 );

		if ( 0 >= $image_id ) {
			// if there's not an image ID, set background image options to an empty string
			$repeat = $position_x = $position_y = $attachment = $size = '';
		} else {
			// if there is an image ID, validate the background image options
			if ( !empty( $image_id ) ) {

				$is_custom_header = get_post_meta( $image_id, '_wp_attachment_is_custom_background', true );

				if ( $is_custom_header !== get_stylesheet() )
					update_post_meta( $image_id, '_wp_attachment_is_custom_background', get_stylesheet() );
			}

			// make sure the values have been white-listed
			$allowed_repeat     = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
			$allowed_position_x = array( 'left', 'right', 'center' );
			$allowed_position_y = array( 'top', 'bottom', 'center' );
			$allowed_attachment = array( 'scroll', 'fixed' );
			$allowed_size 		= array( 'auto', 'contain', 'cover' );

			$repeat     = in_array( $_POST['cryout-meta-background-repeat'],     $allowed_repeat )     ? $_POST['cryout-meta-background-repeat']     : '';
			$position_x = in_array( $_POST['cryout-meta-background-position-x'], $allowed_position_x ) ? $_POST['cryout-meta-background-position-x'] : '';
			$position_y = in_array( $_POST['cryout-meta-background-position-y'], $allowed_position_y ) ? $_POST['cryout-meta-background-position-y'] : '';
			$attachment = in_array( $_POST['cryout-meta-background-attachment'], $allowed_attachment ) ? $_POST['cryout-meta-background-attachment'] : '';
			$size 		= in_array( $_POST['cryout-meta-background-size'], 		 $allowed_size ) 	   ? $_POST['cryout-meta-background-size']       : '';
		}
		
		$allowed_custheader = array( 0, 1, 2 );
		
		// custom header
		$custom_header 	= in_array( intval( $_POST['cryout-meta-header-image'] ),$allowed_custheader ) ? intval( $_POST['cryout-meta-header-image'] ) : 0;

		// hide elements
		$hide_mainmenu = 	intval( isset( $_POST['cryout-meta-hide-mainmenu'] ) );
		$hide_headerimg = 	intval( isset( $_POST['cryout-meta-hide-headerimg'] ) );
		$hide_breadcrumbs = intval( isset( $_POST['cryout-meta-hide-breadcrumbs'] ) );
		$hide_title =	 	intval( isset( $_POST['cryout-meta-hide-title'] ) );
		$hide_colophon = 	intval( isset( $_POST['cryout-meta-hide-colophon'] ) );
		$hide_footer =		intval( isset( $_POST['cryout-meta-hide-footer'] ) );

		// set up an array of meta keys and values
		$meta = array(
			'_cryout_meta_bgcolor'			=> $color,
			'_cryout_meta_bgimageid'		=> $image_id,
			'_cryout_meta_bgrepeat'			=> $repeat,
			'_cryout_meta_bgpositionx'		=> $position_x,
			'_cryout_meta_bgpositiony'		=> $position_y,
			'_cryout_meta_bgattachment'		=> $attachment,
			'_cryout_meta_bgsize'			=> $size,
			'_cryout_meta_custheader'		=> $custom_header,
			'_cryout_meta_hide_mainmenu' 	=> $hide_mainmenu,
			'_cryout_meta_hide_headerimg' 	=> $hide_headerimg,
			'_cryout_meta_hide_breadcrumbs' => $hide_breadcrumbs,
			'_cryout_meta_hide_title' 		=> $hide_title,
			'_cryout_meta_hide_colophon' 	=> $hide_colophon,
			'_cryout_meta_hide_footer' 		=> $hide_footer,
		);

		// template metas
		$allowed_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'iframe' => array(
				'src' => array()
			),
			'span' => array('class', 'id'),
			'div' => array('class', 'id'),
		);
		for ($i=0;$i<=7;$i++) {
			${'cryout-meta-templatefield'.$i} = ( isset( $_POST['cryout-meta-templatefield'.$i] ) ? wp_kses( $_POST['cryout-meta-templatefield'.$i], $allowed_tags ) : '' );
			$meta['_cryout_meta_templatefield_'.$i] = ${'cryout-meta-templatefield' . $i};
		}

		// loop through the meta array and add, update, or delete the post metadata
		foreach ( $meta as $meta_key => $new_meta_value ) {

			// get the meta value of the custom field key
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			// if a new meta value was added and there was no previous value, add it
			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			// if the new meta value does not match the old value, update it
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			// if there is no new meta value but an old value exists, delete it
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $meta_key, $meta_value );
					
			// clean up backwards compatibility metas
			delete_post_meta( $post_id, str_replace( '_cryout', '_' . _CRYOUT_THEME_SLUG, $meta_key ) );
		}

	} // meta_save()


	// Necessary scripts for the appearance meta
	function meta_scripts( $hook_suffix ) {

		// make sure we're on the edit post screen before enqueueing scripts
		if ( !in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) )
			return;


		wp_enqueue_script( _CRYOUT_THEME_SLUG . '-meta-js', get_template_directory_uri() . '/plus/resources/admin/meta.js', array( 'wp-color-picker', 'media-views' ), _CRYOUT_THEME_VERSION, true );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-tabs');

		wp_localize_script(
			_CRYOUT_THEME_SLUG . '-meta-js',
			'cryout_media_image',
			array(
				'title'  => __( 'Set Background Image', 'cryout' ),
				'button' => __( 'Set background image', 'cryout' )
			)
		);

		wp_enqueue_style( _CRYOUT_THEME_SLUG . '-meta-css', get_template_directory_uri() . '/plus/resources/admin/meta.css', _CRYOUT_THEME_VERSION, true );
		wp_enqueue_style( 'wp-color-picker' );

	} // meta_scripts()
	
	public function header_image_url( $url ) {
		
		$custom_header = get_post_meta( get_the_ID(), '_cryout_meta_custheader', true );

		switch ($custom_header) {
			case 2:
				return false;
			break;
			case 1:
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), _CRYOUT_THEME_SLUG . '-header' );
				if (!empty($image[0])) return $image[0];
			break;
			case 0:
			default:
				// do nothing
			break;
		}
		
		// failsafe
		return $url;
		
	} // header_image_url()

} // class CryoutPlus_Appearance_Meta_Backend

new CryoutPlus_Appearance_Meta_Backend;


/*
 * Frontend appearance meta functionality
 */
class CryoutPlus_Appearance_Meta_Frontend {

	public $color = '';
	public $image = '';
	public $repeat = 'repeat';
	public $position_y = 'top';
	public $position_x = 'left';
	public $attachment = 'scroll';
	public $size = 'auto';

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup_hides' ), 95 );
	} // __construct()

	public function setup_background() {

		if ( !is_singular() )
			return;

		// get the post variables
		$post    = get_queried_object();
		$post_id = get_queried_object_id();

		// get the custom metas
		$this->color = get_post_meta( $post_id, '_cryout_meta_bgcolor', true );
		if (empty($this->color)) $this->color = get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgcolor', true ); // backwards compatibility

		$attachment_id = get_post_meta( $post_id, '_cryout_meta_bgimageid', true );
		if (empty($attachment_id)) $attachment_id = get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgimageid', true ); // backwards compatibility
		
		if ( !empty( $attachment_id ) ) {
			$image = wp_get_attachment_image_src( $attachment_id, 'full' );
			$this->image = !empty( $image ) && isset( $image[0] ) ? esc_url( $image[0] ) : '';
		}

		// filter background color and image theme mods
		add_filter( 'theme_mod_background_color', array( $this, 'background_color' ), 25 );
		add_filter( 'theme_mod_background_image', array( $this, 'background_image' ), 25 );

		// if image exists, filter image-related theme mods
		if ( !empty( $this->image ) ) {

			$this->repeat     = get_post_meta( $post_id, '_cryout_meta_bgrepeat', true );
			$this->position_x = get_post_meta( $post_id, '_cryout_meta_bgpositionx', true );
			$this->position_y = get_post_meta( $post_id, '_cryout_meta_bgpositiony', true );
			$this->attachment = get_post_meta( $post_id, '_cryout_meta_bgattachment', true );
			$this->size 	  = get_post_meta( $post_id, '_cryout_meta_bgsize', true );

			// backwards compatibility
			if (empty($this->repeat)) 	  $this->repeat     = get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgrepeat', true );
			if (empty($this->position_x)) $this->position_x = get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgpositionx', true );
			if (empty($this->position_y)) $this->position_y = get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgpositiony', true );
			if (empty($this->attachment)) $this->attachment = get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgattachment', true );
			if (empty($this->size)) 	  $this->size 	  	= get_post_meta( $post_id, '_' . _CRYOUT_THEME_SLUG . '_meta_bgsize', true );
			// end backwards compatibility

			add_filter( 'theme_mod_background_repeat',     array( $this, 'background_repeat'     ), 25 );
			add_filter( 'theme_mod_background_position_x', array( $this, 'background_position_x' ), 25 );
			add_filter( 'theme_mod_background_position_y', array( $this, 'background_position_y' ), 25 );
			add_filter( 'theme_mod_background_attachment', array( $this, 'background_attachment' ), 25 );
			add_filter( 'theme_mod_background_size', 	   array( $this, 'background_size'       ), 25 );
		}

	} // setup_background()

	public function background_color( $color ) {
		return !empty( $this->color ) ? preg_replace( '/[^0-9a-fA-F]/', '', $this->color ) : $color;
	} // background_color()

	public function background_image( $image ) {

		if ( !empty( $this->image ) ) {
			$image = $this->image;
		} elseif ( !empty( $this->color ) ) {
			$image = '';
		}

		return $image;
	} // background_image()

	public function background_repeat( $repeat ) {
		return !empty( $this->repeat ) ? $this->repeat : $repeat;
	} // background_repeat()

	public function background_position_x( $position_x ) {
		return !empty( $this->position_x ) ? $this->position_x : $position_x;
	} // background_position_x()

	public function background_position_y( $position_y ) {
		return !empty( $this->position_y ) ? $this->position_y : $position_y;
	} // background_position_y()

	public function background_attachment( $attachment ) {
		return !empty( $this->attachment ) ? $this->attachment : $attachment;
	} // background_attachment()

	public function background_size( $size ) {
		return !empty( $this->size ) ? $this->size : $size;
	} // background_size()

	public function setup_hides() {
		// custom hides are processed in filters.php as body classes
		add_action( 'template_redirect', array( $this, 'setup_background' ) );
	} // setup_hides()

} // class CryoutPlus_Appearance_Meta_Frontend

new CryoutPlus_Appearance_Meta_Frontend;


// FIN
