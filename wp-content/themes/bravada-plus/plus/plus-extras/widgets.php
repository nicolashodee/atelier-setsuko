<?php
/*
 * Plus Widgets
 *
 * @package Cryout Plus
 */

/*****************************************
 *          Social Icons Widget
 ****************************************/

class CryoutSocials extends WP_Widget {

	private $defaults = array(
		'title' => 'Follow Us',
	);

	function __construct() {
		parent::__construct(
			false,
			'Cryout Socials',
			array(
				'description' => __( 'Displays the social icons menu in a widget area.', 'cryout' ),
				'classname' => 'widget_cryout_socials'
			)
		);
	} // __construct()

	// backend
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		if ( ! has_nav_menu( 'socials' ) ) { ?>
			<p><em><?php esc_attr_e( 'You did not assign a menu to the theme\'s social menu. Assign one to display it in this widget.', 'cryout' ); ?></em></p>
		<?php } ?>
		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'cryout' ) ?></label><br />
			<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" value="<?php esc_attr_e( $instance['title'] ); ?>" />
		</p>

	<?php
	}

	// save
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = esc_html( $new_instance['title'] );
		return $instance;
	} // update()

	// frontend
	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title = apply_filters( 'widget_title', esc_html( $instance['title'] ) );

		echo $before_widget;

		if( $title ) {
			echo $before_title . esc_html( $title ) . $after_title;
		}
		?>
		<div class="widget-socials">
			<?php call_user_func( _CRYOUT_THEME_SLUG . '_socials_menu', 'widget' ); ?>
		</div>

		<?php echo $after_widget;
	} // widget()

} // class CryoutSocials

// Add Widget
function cryout_widgets_init_socials() {
	register_widget( 'CryoutSocials' );
}
add_action( 'widgets_init', 'cryout_widgets_init_socials' );


/*****************************************
 *          Tabs Widget
 ****************************************/

class CryoutTabs extends WP_Widget {

	private $defaults = array(
			'title' 			=> '',
			'tabs_category' 	=> 1,
			'tabs_date' 		=> 1,
			// recent
			'recent_show' 		=> 1,
			'recent_thumbs' 	=> 1,
			'recent_cat_id' 	=> '0',
			'recent_num' 		=> '5',
			// popular
			'popular_show' 		=> 1,
			'popular_thumbs' 	=> 1,
			'popular_cat_id' 	=> '0',
			'popular_time' 		=> '0',
			'popular_num' 		=> '5',
			// comments
			'comments_show' 	=> 1,
			'comments_avatars' 	=> 1,
			'comments_num' 		=> '5',
			// tags
			'tags_show' 		=> 1,
			// order
			'order_recent' 		=> '1',
			'order_popular' 	=> '2',
			'order_comments' 	=> '3',
			'order_tags' 		=> '4',
		);

	function __construct() {
		parent::__construct(
			false,
			'Cryout Tabs',
			array(
				'description' => __( 'A different way of listing posts, comments and/or tags.', 'cryout' ),
				'classname' => 'widget_cryout_wtabs'
			)
		);
	} // __construct()

	//
	private function _prototype( $tabs, $count ) {
		$titles = array(
			'recent'	=> esc_attr__( 'Recent Posts', 'cryout' ),
			'popular'	=> esc_attr__( 'Popular Posts', 'cryout' ),
			'comments'	=> esc_attr__( 'Recent Comments', 'cryout' ),
			'tags'		=> esc_attr__( 'Tags', 'cryout' )
		);
		$icons = array(
			'recent'   => 'icon icon-widget-time',
			'popular'  => 'icon icon-widget-star',
			'comments' => 'icon icon-widget-comments',
			'tags'     => 'icon icon-widget-tags'
		);
		$output = sprintf( '<ul class="cryout-wtabs-nav group tab-count-%s">', $count );
		foreach ( $tabs as $tab ) {
			$output .= sprintf( '<li class="cryout-wtab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',
				$tab,
				$tab . '-' . $this -> number,
				$icons[$tab],
				$titles[$tab]
			);
		}
		$output .= '</ul>';
		return $output;
	} // _prototype()

	private function _proto_select( $id = '', $name = '', $option = 0, $values = array(), $class = '', $style = 'width:100%' ) { ?>
		<select class="<?php echo $class ?>" style="<?php echo $style ?>" id="<?php echo $id; ?>" name="<?php echo $name ?>">
			<?php foreach ( $values as $value => $label ) { ?>
			<option value="<?php echo $value ?>" <?php selected( $option, $value ) ?>><?php echo $label ?></option>
			<?php } ?>
		</select>
	<?php
	} // _proto_select()

	// backend
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<style>
		.widget .widget-inside .cryout-wtab-options-tabs .postform { width: 100%; }
		.widget .widget-inside .cryout-wtab-options-tabs fieldset { border: 1px solid #eee; padding: 1em; margin: 1em 0; background: #fafafa; }
		.widget .widget-inside .cryout-wtab-options-tabs fieldset legend { font-weight: 700; padding: 0 1em; }
		.widget .widget-inside .cryout-wtab-options-tabs p { margin: 3px 0; }
		.widget .widget-inside .cryout-wtab-options-tabs hr { margin: 20px 0 10px; }
		.widget .widget-inside .cryout-wtab-options-tabs h4 { margin-bottom: 10px; }
		</style>

		<div class="cryout-wtab-options-tabs">
			<p>
				<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title', 'cryout' ) ?>:</label>
				<input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php esc_attr_e( $instance["title"] ); ?>" />
			</p>

			<fieldset>
			<legend><?php esc_attr_e( 'Recent Posts', 'cryout' ) ?></legend>

			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'recent_show' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'recent_show' ) ); ?>" <?php checked( (bool) $instance["recent_show"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'recent_show' ) ); ?>"><?php esc_attr_e( 'Enabled', 'cryout' ) ?></label>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'recent_thumbs' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'recent_thumbs' ) ); ?>" <?php checked( (bool) $instance["recent_thumbs"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'recent_thumbs' ) ); ?>"><?php esc_attr_e( 'Show thumbnails', 'cryout' ) ?></label>
			</p>
			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'recent_num') ); ?>"><?php esc_attr_e( 'Number of items', 'cryout' ) ?></label>
				<input style="width:20%;" id="<?php esc_attr_e( $this->get_field_id( 'recent_num' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'recent_num' ) ); ?>" type="text" value="<?php echo absint( $instance["recent_num"] ); ?>" size='3' />
			</p>
			<p>
				<label style="width: 100%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'recent_cat_id' ) ); ?>"><?php esc_attr_e( 'Category:', 'cryout' ) ?></label>
				<?php wp_dropdown_categories( array( 'name' => $this->get_field_name( 'recent_cat_id' ), 'selected' => $instance["recent_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>
			</p>

			</fieldset>
			<fieldset>
			<legend><?php esc_attr_e( 'Popular Posts', 'cryout' ) ?></legend>

			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'popular_show' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'popular_show' ) ); ?>" <?php checked( (bool) $instance["popular_show"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'popular_show' ) ); ?>"><?php esc_attr_e( 'Enabled', 'cryout' ) ?></label>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'popular_thumbs' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'popular_thumbs' ) ); ?>" <?php checked( (bool) $instance["popular_thumbs"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'popular_thumbs' ) ); ?>"><?php esc_attr_e( 'Show thumbnails', 'cryout' ) ?></label>
			</p>
			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'popular_num' ) ); ?>"><?php esc_attr_e( 'Number of items', 'cryout' ) ?></label>
				<input style="width:20%;" id="<?php esc_attr_e( $this->get_field_id( 'popular_num' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'popular_num' ) ); ?>" type="text" value="<?php echo absint( $instance["popular_num"] ); ?>" size='3' />
			</p>
			<p>
				<label style="width: 100%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'popular_cat_id' ) ); ?>"><?php esc_attr_e( 'Category:', 'cryout' ) ?></label>
				<?php wp_dropdown_categories( array( 'name' => $this->get_field_name( 'popular_cat_id' ), 'selected' => $instance["popular_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>
			</p>
			<p style="padding-top: 0.3em;">
				<label style="width: 100%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'popular_time' ) ); ?>"><?php esc_attr_e( 'Post most commented in last:', 'cryout' ) ?></label>
				<?php $this->_proto_select(
					esc_attr( $this->get_field_id( 'popular_time' ) ),
					esc_attr( $this->get_field_name( 'popular_time' ) ),
					$instance["popular_time"],
					array(
						0 => __( 'All time', 'cryout' ),
						'1 year ago' => __( '1 Year', 'cryout' ),
						'1 month ago' => __( '1 Month', 'cryout' ),
						'1 week ago' => __( '1 Week', 'cryout' ),
						'1 day ago' => __( '1 Day', 'cryout' ),
					)
				); ?>
			</p>

			</fieldset>
			<fieldset>
			<legend><?php esc_attr_e( 'Latest Comments', 'cryout' ) ?></legend>

			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'comments_show' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'comments_show' ) ); ?>" <?php checked( (bool) $instance["comments_show"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'comments_show' ) ); ?>"><?php esc_attr_e( 'Enable', 'cryout' ) ?></label>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'comments_avatars' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'comments_avatars' ) ); ?>" <?php checked( (bool) $instance["comments_avatars"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'comments_avatars' ) ); ?>"><?php esc_attr_e( 'Show avatars', 'cryout' ) ?></label>
			</p>
			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'comments_num' ) ); ?>"><?php esc_attr_e( 'Number of items', 'cryout' ) ?></label>
				<input style="width:20%;" id="<?php esc_attr_e( $this->get_field_id( 'comments_num' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'comments_num' ) ); ?>" type="text" value="<?php echo absint( $instance["comments_num"] ); ?>" size='3' />
			</p>

			</fieldset>
			<fieldset>
			<legend><?php esc_attr_e( 'Tags', 'cryout' ) ?></legend>

			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'tags_show' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'tags_show' ) ); ?>" <?php checked( (bool) $instance["tags_show"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'tags_show' ) ); ?>"><?php esc_attr_e( 'Enable', 'cryout' ) ?></label>
			</p>

			</fieldset>
			<fieldset>
			<legend><?php esc_attr_e( 'Order' ) ?></legend>

			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'order_recent' ) ); ?>"><?php esc_attr_e( 'Recent Posts', 'cryout' ) ?></label>
				<?php $this->_proto_select(
					esc_attr( $this->get_field_id( 'order_recent' ) ),
					esc_attr( $this->get_field_name( 'order_recent' ) ),
					$instance["order_recent"],
					array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ),
					'widefat',
					'width:20%;'
				); ?>
			</p>
			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'order_popular' ) ); ?>"><?php esc_attr_e( 'Popular Posts', 'cryout' ) ?></label>
				<?php $this->_proto_select(
					esc_attr( $this->get_field_id("order_popular") ),
					esc_attr( $this->get_field_name("order_popular") ),
					$instance["order_popular"],
					array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ),
					'widefat',
					'width:20%;'
				); ?>
			</p>
			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'order_comments' ) ); ?>"><?php esc_attr_e( 'Latest Comments', 'cryout' ) ?></label>
				<?php $this->_proto_select(
					esc_attr( $this->get_field_id( "order_comments" ) ),
					esc_attr( $this->get_field_name( "order_comments" ) ),
					$instance["order_comments"],
					array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ),
					'widefat',
					'width:20%;'
				); ?>
			</p>
			<p>
				<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'order_tags' ) ); ?>"><?php esc_attr_e( 'Tags', 'cryout' ) ?></label>
				<?php $this->_proto_select(
					esc_attr( $this->get_field_id( "order_tags" ) ),
					esc_attr( $this->get_field_name( "order_tags" ) ),
					$instance["order_tags"],
					array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ),
					'widefat',
					'width:20%;'
				); ?>
			</p>

			</fieldset>
			<fieldset>
			<legend><?php esc_attr_e( 'Extra', 'cryout' ) ?></legend>

			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'tabs_category' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'tabs_category' ) ); ?>" <?php checked( (bool) $instance["tabs_category"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'tabs_category' ) ); ?>"><?php esc_attr_e( 'Display categories', 'cryout' ) ?></label>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'tabs_date' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'tabs_date' ) ); ?>" <?php checked( (bool) $instance["tabs_date"], true ); ?>>
				<label for="<?php esc_attr_e( $this->get_field_id( 'tabs_date' ) ); ?>"><?php esc_attr_e( 'Display dates', 'cryout' ) ?></label>
			</p>

			</fieldset>

		</div>
		<?php

	} // form()

	// save
	public function update( $new, $old ) {
		$instance = $old;
		$instance['title'] = strip_tags( $new['title'] );
		$instance['tabs_category'] = !empty( $new['tabs_category'] );
		$instance['tabs_date'] = !empty( $new['tabs_date'] );
		// recent
		$instance['recent_show'] = !empty( $new['recent_show'] );
		$instance['recent_thumbs'] = !empty( $new['recent_thumbs'] );
		$instance['recent_cat_id'] = absint( $new['recent_cat_id'] );
		$instance['recent_num'] = absint( $new['recent_num'] );
		// popular
		$instance['popular_show'] = !empty( $new['popular_show'] );
		$instance['popular_thumbs'] = !empty( $new['popular_thumbs'] );
		$instance['popular_cat_id'] = absint( $new['popular_cat_id'] );
		$instance['popular_time'] = strip_tags( $new['popular_time'] );
		$instance['popular_num'] = absint( $new['popular_num'] );
		// comments
		$instance['comments_show'] = !empty( $new['comments_show'] );
		$instance['comments_avatars'] = !empty( $new['comments_avatars'] );
		$instance['comments_num'] = absint( $new['comments_num'] );
		// tags
		$instance['tags_show'] = !empty( $new['tags_show'] );
		// order
		$instance['order_recent'] = absint( $new['order_recent'] );
		$instance['order_popular'] = absint( $new['order_popular'] );
		$instance['order_comments'] = absint( $new['order_comments'] );
		$instance['order_tags'] = absint( $new['order_tags'] );
		return $instance;
	} // update()

	// frontend
	public function widget( $args, $instance ) {

		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$instance['title'] ? NULL : $instance['title'] = '';
		$title = apply_filters( 'widget_title', $instance['title'] );
		$output = $before_widget . PHP_EOL;
		if ( !empty( $title ) )
			$output .= $before_title . $title . $after_title;
		ob_start();

		$tabs = array();
		$count = 0;
		$order = array(
			'recent'	=> $instance['order_recent'],
			'popular'	=> $instance['order_popular'],
			'comments'	=> $instance['order_comments'],
			'tags'		=> $instance['order_tags']
		);
		asort( $order );
		foreach ( $order as $key => $value ) {
			if ( $instance[$key . '_show'] ) {
				$tabs[] = $key;
				$count++;
			}
		}
		if ( $tabs && ( $count > 1 ) ) {
			$output .= $this->_prototype( $tabs, $count );
		}
		?>

		<div class="cryout-wtabs-container">

			<?php
			/* recent posts tab */
			if ( $instance['recent_show'] ) { ?>

				<?php $recent=new WP_Query(); ?>
				<?php $recent->query( array(
						'showposts' => $instance["recent_num"],
						'cat' => $instance["recent_cat_id"],
						'ignore_sticky_posts' => 1,
						)
					); ?>

				<ul id="tab-recent-<?php echo absint( $this->number ) ?>" class="cryout-wtab group <?php if ( $instance['recent_thumbs'] ) { echo 'thumbs-enabled'; } ?>">
					<?php while ( $recent->have_posts() ): $recent->the_post(); ?>
					<li>

						<?php if ( $instance['recent_thumbs'] ) { // Thumbnails enabled? ?>
						<div class="tab-item-thumbnail">
							<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
								<?php if ( has_post_thumbnail() ):
									the_post_thumbnail( _CRYOUT_THEME_SLUG . '-featured-square' );
								else: ?>
									<img src="<?php echo get_template_directory_uri(); ?>/resources/images/fallback/fallback-small.gif" alt="<?php the_title_attribute(); ?>" />
								<?php endif;
								if ( has_post_format( 'video' ) && !is_sticky() ) { ?>
									<span class="thumb-icon small"><i class="fa fa-play"></i></span>
								<?php }
								if ( has_post_format( 'audio' ) && !is_sticky() ) { ?>
									<span class="thumb-icon small"><i class="fa fa-volume-up"></i></span>
								<?php }
								if ( is_sticky() ) { ?>
									<span class="thumb-icon small"><i class="fa fa-star"></i></span>
								<?php } ?>
							</a>
						</div>
						<?php } ?>

						<div class="tab-item-inner group">
							<?php if ( $instance['tabs_category'] ) { ?>
								<p class="tab-item-category"><?php the_category( ' / ' ); ?></p>
							<?php } ?>
							<p class="tab-item-title"><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
							<?php if ( $instance['tabs_date'] ) { ?>
								<p class="tab-item-date"><?php echo get_the_date(); ?></p>
							<?php } ?>
						</div>

					</li>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</ul><!--cryout-wtab-recent-->

			<?php }
			/* popular posts tab */
			if ( $instance['popular_show'] ) { ?>

				<?php
					$popular = new WP_Query( array(
						'post_type'				=> array( 'post' ),
						'showposts'				=> $instance['popular_num'],
						'cat'					=> $instance['popular_cat_id'],
						'ignore_sticky_posts'	=> true,
						'orderby'				=> 'comment_count',
						'order'					=> 'desc',
						'date_query' => array(
							array(
								'after' => $instance['popular_time'],
							),
						),
					) );
				?>
				<ul id="tab-popular-<?php echo absint( $this->number ) ?>" class="cryout-wtab group <?php if( $instance['popular_thumbs'] ) { echo 'thumbs-enabled'; } ?>">

					<?php while ( $popular->have_posts() ): $popular->the_post(); ?>
					<li>

						<?php if ( $instance['popular_thumbs'] ) { ?>
							<div class="tab-item-thumbnail">
								<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
									<?php if ( has_post_thumbnail() ):
										the_post_thumbnail( 'thumbnail' );
									else: ?>
										<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/resources/images/fallback/fallback-small.gif" alt="<?php the_title_attribute(); ?>" />
									<?php endif;
									if ( has_post_format( 'video' ) && !is_sticky() ) { ?>
										<span class="thumb-icon small"><i class="fa fa-play"></i></span>
									<?php }
									if ( has_post_format( 'audio' ) && !is_sticky() ) { ?>
										<span class="thumb-icon small"><i class="fa fa-volume-up"></i></span>
									<?php }
									if ( is_sticky() ) { ?>
										<span class="thumb-icon small"><i class="fa fa-star"></i></span>
									<?php } ?>
								</a>
							</div>
						<?php } ?>

						<div class="tab-item-inner group">
							<?php if ( $instance['tabs_category'] ) { ?>
								<p class="tab-item-category"><?php the_category( ', ' ); ?></p>
							<?php } ?>
							<p class="tab-item-title"><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
							<?php if ( $instance['tabs_date'] ) { ?>
								<p class="tab-item-date"><?php the_time( 'M j, Y' ); ?></p>
							<?php } ?>
						</div>

					</li>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</ul><!--cryout-wtab-popular-->

			<?php }
			/* comments tab */
			if ( $instance['comments_show'] ) { ?>

				<?php $comments = get_comments( array( 'number' => $instance["comments_num"], 'status' => 'approve', 'post_status' => 'publish' ) ); ?>

				<ul id="tab-comments-<?php echo absint( $this->number ) ?>" class="cryout-wtab group <?php if ( $instance['comments_avatars'] ) { echo 'avatars-enabled'; } ?>">
					<?php foreach ( $comments as $comment ): ?>
					<li>

							<?php if ( $instance['comments_avatars'] ) { ?>
								<div class="tab-item-avatar">
									<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
										<?php echo get_avatar( $comment->comment_author_email, $size='96' ); ?>
									</a>
								</div>
							<?php } ?>

							<div class="tab-item-inner group">
								<?php $str = explode( ' ', get_comment_excerpt( $comment->comment_ID ) );
									$comment_excerpt = implode( ' ', array_slice( $str, 0, 11 ) );
									if( count( $str ) > 11 && substr( $comment_excerpt, -1 ) != '.' ) $comment_excerpt .= '...' ?>
								<div class="tab-item-name">
									<?php esc_attr_e( $comment->comment_author ) ?>:
								</div>
								<div class="tab-item-comment">
									<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php esc_attr_e( $comment_excerpt ) ?></a>
								</div>
							</div>

					</li>
					<?php endforeach; ?>
				</ul><!--cryout-wtab-comments-->

			<?php }
			/* tags tab */
			if ( $instance['tags_show'] ) { ?>

				<ul id="tab-tags-<?php echo absint( $this->number ) ?>" class="cryout-wtab group">
					<li class="tagcloud">
						<?php wp_tag_cloud(); ?>
					</li>
				</ul><!--cryout-wtab-tags-->

			<?php } ?>
		</div>

	<?php
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	} // widget()

} // class CryoutTabs

function cryout_widgets_init_tabs() {
	register_widget( 'CryoutTabs' );
}
add_action( 'widgets_init', 'cryout_widgets_init_tabs' );


/*****************************************
 *          Posts Widget
 ****************************************/

class CryoutFeaturedPosts extends WP_Widget {

	private $defaults = array(
			'title' 			=> '',
			// posts
			'posts_thumb' 		=> 1,
			'posts_category'	=> 1,
			'posts_date'		=> 1,
			'posts_num' 		=> '4',
			'posts_cat_id' 		=> '0',
			'posts_orderby' 	=> 'date',
			'posts_time' 		=> '0',
	);

	function __construct() {
		parent::__construct(
			false,
			__( 'Cryout Featured Posts', 'cryout' ),
			array(
				'description' => __( 'Displays posts from a category in an advanced way.', 'cryout' ),
				'classname' => 'widget_cryout_posts'
			)
		);
	} // __construct()

	private function _proto_select( $id = '', $name = '', $option = 0, $values = array(), $class = '', $style = 'width:100%' ) { ?>
		<select class="<?php echo $class ?>" style="<?php echo $style ?>" id="<?php echo $id; ?>" name="<?php echo $name ?>">
			<?php foreach ( $values as $value => $label ) { ?>
			<option value="<?php echo $value ?>" <?php selected( $option, $value ) ?>><?php echo $label ?></option>
			<?php } ?>
		</select>
	<?php
	} // _proto_select()

	// backend
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>

		<style>
		.widget .widget-inside .cryout-posts-options .postform { width: 100%; }
		.widget .widget-inside .cryout-posts-options fieldset { border: 1px solid #eee; padding: 1em; margin: 1em 0; background: #fafafa; }
		.widget .widget-inside .cryout-posts-options fieldset legend { font-weight: 700; padding: 0 1em; }
		.widget .widget-inside .cryout-posts-options p { margin: 3px 0; }
		.widget .widget-inside .cryout-posts-options hr { margin: 20px 0 10px; }
		.widget .widget-inside .cryout-posts-options h4 { margin-bottom: 10px; }
		</style>

		<div class="cryout-posts-options">
			<p>
				<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'cryout' ) ?></label>
				<input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php esc_attr_e( $instance["title"] ); ?>" />
			</p>

			<fieldset>
				<legend><?php esc_attr_e( 'Filters', 'cryout' ) ?></legend>
				<p>
					<label style="width: 55%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'posts_num' ) ); ?>"><?php esc_attr_e( 'Number of items', 'cryout' ) ?></label>
					<input style="width:20%;" id="<?php esc_attr_e( $this->get_field_id( 'posts_num' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'posts_num' ) ); ?>" type="text" value="<?php echo absint( $instance["posts_num"] ); ?>" size='3' />
				</p>
				<p>
					<label style="width: 100%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'posts_cat_id' ) ); ?>"><?php esc_attr_e( 'Category:', 'cryout' ) ?></label>
					<?php wp_dropdown_categories( array( 'name' => $this->get_field_name( 'posts_cat_id' ), 'selected' => $instance["posts_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>
				</p>
				<p style="padding-top: 0.3em;">
					<label style="width: 100%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'posts_orderby' ) ); ?>"><?php esc_attr_e( 'Order by:', 'cryout' ) ?></label>
					<?php $this->_proto_select(
						esc_attr( $this->get_field_id( "posts_orderby" ) ),
						esc_attr( $this->get_field_name( "posts_orderby" ) ),
						$instance["posts_orderby"],
						array(
							'date' => __( 'Most recent', 'cryout' ),
							'comment_count' => __( 'Most commented', 'cryout' ),
							'rand' => __( 'Random', 'cryout' ),
						)
					); ?>
				</p>
				<p style="padding-top: 0.3em;">
					<label style="width: 100%; display: inline-block;" for="<?php esc_attr_e( $this->get_field_id( 'posts_time' ) ); ?>"><?php esc_attr_e( 'Posts from last:', 'cryout' ) ?></label>
					<?php $this->_proto_select(
						esc_attr( $this->get_field_id( "posts_time" ) ),
						esc_attr( $this->get_field_name( "posts_time" ) ),
						$instance["posts_time"],
						array(
							0 => __( 'All time', 'cryout' ),
							'1 year ago' => __( '1 Year', 'cryout' ),
							'1 month ago' => __( '1 Month', 'cryout' ),
							'1 week ago' => __( '1 Week', 'cryout' ),
							'1 day ago' => __( '1 Day', 'cryout' ),
						)
					); ?>
				</p>

			</fieldset>
			<fieldset>
				<legend><?php esc_attr_e( 'Extra', 'cryout' ) ?></legend>

				<p>
					<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'posts_thumb' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'posts_thumb' ) ); ?>" <?php checked( (bool) $instance["posts_thumb"], true ); ?>>
					<label for="<?php esc_attr_e( $this->get_field_id( 'posts_thumb' ) ); ?>"><?php esc_attr_e( 'Display thumbnails', 'cryout' ) ?></label>
				</p>
				<p>
					<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'posts_category' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'posts_category' ) ); ?>" <?php checked( (bool) $instance["posts_category"], true ); ?>>
					<label for="<?php esc_attr_e( $this->get_field_id( 'posts_category' ) ); ?>"><?php esc_attr_e( 'Display categories', 'cryout' ) ?></label>
				</p>
				<p>
					<input type="checkbox" class="checkbox" id="<?php esc_attr_e( $this->get_field_id( 'posts_date' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'posts_date' ) ); ?>" <?php checked( (bool) $instance["posts_date"], true ); ?>>
					<label for="<?php esc_attr_e( $this->get_field_id( 'posts_date' ) ); ?>"><?php esc_attr_e( 'Display dates', 'cryout' ) ?></label>
				</p>

			</fieldset>

		</div>
		<?php

	} // form()

	// save
	public function update( $new, $old ) {
		$instance = $old;
		$instance['title'] = strip_tags( $new['title'] );
		// posts
		$instance['posts_thumb'] = !empty( $new['posts_thumb'] );
		$instance['posts_category'] = !empty( $new['posts_category'] );
		$instance['posts_date'] = !empty( $new['posts_date'] );
		$instance['posts_num'] = strip_tags( $new['posts_num'] );
		$instance['posts_cat_id'] = strip_tags( $new['posts_cat_id'] );
		$instance['posts_orderby'] = strip_tags( $new['posts_orderby'] );
		$instance['posts_time'] = strip_tags( $new['posts_time'] );
		return $instance;
	} // update()

	// frontend
	public function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$instance['title'] ? NULL : $instance['title'] = '' ;
		$title = apply_filters( 'widget_title', $instance['title'] );

		$output = $before_widget . PHP_EOL;

		if( $title )
			$output .= $before_title . $title . $after_title;
		ob_start();

		$posts = new WP_Query( array(
			'post_type'				=> array( 'post' ),
			'showposts'				=> $instance['posts_num'],
			'cat'					=> $instance['posts_cat_id'],
			'ignore_sticky_posts'	=> true,
			'orderby'				=> $instance['posts_orderby'],
			'order'					=> 'desc',
			'date_query' => array(
				array(
					'after' => $instance['posts_time'],
				),
			),
		) );
		?>

		<ul class="cryout-wposts group <?php if( $instance['posts_thumb'] ) { echo 'thumbs-enabled'; } ?>">
			<?php while ( $posts->have_posts() ): $posts->the_post(); ?>
			<li>

				<?php if ( $instance['posts_thumb'] ) { ?>
				<div class="post-item-thumbnail">
					<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
						<?php if ( has_post_thumbnail() ):
							the_post_thumbnail( 'medium' );
						else: ?>
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/resources/images/fallback/fallback-medium.gif" alt="<?php the_title_attribute(); ?>" />
						<?php endif;
						if ( has_post_format( 'video' ) && !is_sticky() ) { ?>
							<span class="thumb-icon small"><i class="fa fa-play"></i></span>
						<?php }
						if ( has_post_format( 'audio' ) && !is_sticky() ) { ?>
							<span class="thumb-icon small"><i class="fa fa-volume-up"></i></span>
						<?php }
						if ( is_sticky() ) { ?>
							<span class="thumb-icon small"><i class="fa fa-star"></i></span>
						<?php } ?>
					</a>
				</div>
				<?php } ?>

				<div class="post-item-inner group">
					<?php if ( $instance['posts_category']) { ?>
						<p class="post-item-category"><?php the_category( ', ' ); ?></p>
					<?php } ?>
					<p class="post-item-title"><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
					<?php if ( $instance['posts_date'] ) { ?>
						<p class="post-item-date"><?php echo get_the_date(); ?></p>
					<?php } ?>
				</div>

			</li>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</ul><!--cryout-wposts-->

		<?php
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	} // widget()

} // class CryoutFeaturedPosts

function cryout_widgets_init_posts() {
	register_widget( 'CryoutFeaturedPosts' );
}
add_action( 'widgets_init', 'cryout_widgets_init_posts' );


/*****************************************
 *          Contact Widget
 ****************************************/

class CryoutContact extends WP_Widget {

	private $defaults = array(
		'title' => 'Contact Info',
		'address' => '',
		'phone' => '',
		'mobile' => '',
		'email' => '',
		'web' => '',
		'opening_hours' => '',
		'mapsrc' => '',
		'mapwidth' => '400',
		'mapheight' => '300',
	);

	function __construct() {
		parent::__construct(
			false,
			'Cryout Contact',
			array(
				'description' => __( 'Displays contact information in a customized layout.', 'cryout'),
				'classname' => 'widget_cryout_contact'
			)
		);
	} // __construct()

	// backend
	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>
		<div class="cryout-wtab-options-tabs">
			<p>
				<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'cryout' ) ?></label><br />
				<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" value="<?php echo wp_kses_post( $instance['title'] ); ?>" />
			</p>
			<fieldset>
				<legend></legend>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'address' ) ); ?>"><?php esc_attr_e( 'Address:', 'cryout' ) ?></label>
					<textarea class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'address' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'address' ) ); ?>"><?php echo wp_kses_post( $instance['address'] ); ?></textarea>
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'phone' ) ); ?>"><?php esc_attr_e( 'Phone:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'phone' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'phone' ) ); ?>" value="<?php esc_attr_e( $instance['phone'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'mobile' ) ); ?>"><?php esc_attr_e( 'Mobile:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'mobile' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'mobile' ) ); ?>" value="<?php esc_attr_e( $instance['mobile'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'email' ) ); ?>"><?php esc_attr_e( 'Email:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'email' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'email' ) ); ?>" value="<?php esc_attr_e( $instance['email'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'web' ) ); ?>"><?php esc_attr_e( 'Website URL:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'web' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'web' ) ); ?>" value="<?php esc_attr_e( $instance['web'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'web' ) ); ?>"><?php esc_attr_e( 'Opening Hours:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'opening_hours' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'opening_hours' ) ); ?>" value="<?php esc_attr_e( $instance['opening_hours'] ); ?>" />
				</p>
			</fieldset>
			<fieldset>
				<legend><?php _e( 'Google Map', 'cryout' ); ?></legend>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'mapsrc' ) ); ?>"><?php esc_attr_e( 'Link:', 'cryout' ) ?> </label>
					<textarea class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'mapsrc' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'mapsrc' ) ); ?>"><?php echo esc_url( $instance['mapsrc'] ); ?></textarea>
					<label for="<?php esc_attr_e( $this->get_field_id( 'mapwidth' ) ); ?>"><?php esc_attr_e( 'Width:', 'cryout' ) ?> </label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'mapwidth' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'mapwidth' ) ); ?>" value="<?php echo absint( $instance['mapwidth'] ); ?>" />
					<label for="<?php esc_attr_e( $this->get_field_id( 'mapheight' ) ); ?>"><?php esc_attr_e( 'Height:', 'cryout' ) ?> </label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'mapheight' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'mapheight' ) ); ?>" value="<?php echo absint( $instance['mapheight'] ); ?>" />
				</p>
			</fieldset>
		</div>
    <?php
	} // form()

	// save
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = wp_kses_post( $new_instance['title'] );
		$instance['address'] = wp_kses_post( $new_instance['address'] );
		$instance['phone'] = esc_html( $new_instance['phone'] );
		$instance['mobile'] = esc_html( $new_instance['mobile'] );
		$instance['email'] = esc_html( $new_instance['email'] );
		$instance['opening_hours'] = esc_html( $new_instance['opening_hours'] );

   		$src = preg_match( '/src="([^"]*)"/i', $new_instance['mapsrc'] ) ;
		if ( $src ) {
			$new_instance['mapsrc'] = $src;
		} else {
			$instance['mapsrc'] = esc_url( $new_instance['mapsrc'] );
		}

		$instance['mapwidth'] = absint( $new_instance['mapwidth'] );
		$instance['mapheight'] = absint( $new_instance['mapheight'] );

		if ( ! empty( $new_instance['web'] ) ) $instance['web'] = esc_url( $new_instance['web'] ); else $instance['web'] = '';

		return $instance;
	} // update()

	// frontend
	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title = apply_filters( 'widget_title', esc_html( $instance['title'] ) );

		echo $before_widget;
		echo $before_title . esc_html( $title ) . $after_title;
		?>

		<address>
			<?php if ( ! empty( $instance['address'] ) ) { ?>
				<span><i class="icon icon-widget-location" title="<?php esc_attr_e( 'Address', 'cryout' ) ?>"></i><span class="cryout-contact-right address-block"> <?php echo preg_replace( '/\\n/','<br>',wp_kses_post( $instance['address'] ) ); ?></span></span>
			<?php }; ?>

			<?php if ( ! empty( $instance['phone'] ) ) { ?>
				<span><i class="icon-widget-phone" title="<?php esc_attr_e( 'Phone', 'cryout' ) ?>"></i><strong><?php esc_attr_e( 'Phone', 'cryout' ) ?>:</strong><span class="cryout-contact-right"> <?php echo esc_html( $instance['phone'] ); ?></span></span>
			<?php }; ?>

			<?php if ( ! empty( $instance['mobile'] ) ) { ?>
				<span><i class="icon-widget-mobile" title="<?php esc_attr_e( 'Mobile', 'cryout' ) ?>"></i><strong><?php esc_attr_e( 'Mobile', 'cryout' ) ?>:</strong> <span class="cryout-contact-right"><?php echo esc_html( $instance['mobile'] ); ?></span></span>
			<?php }; ?>

			<?php if ( ! empty( $instance['email'] ) ) { ?>
				<span><i class="icon-widget-mail" title="<?php esc_attr_e( 'E-mail', 'cryout' ) ?>"></i><strong><?php esc_attr_e( 'E-mail', 'cryout' ) ?>:</strong> <span class="cryout-contact-right"><a href="mailto:<?php echo esc_html( $instance['email'] ); ?>"><?php echo esc_html( $instance['email'] ); ?></a></span></span>
			<?php }; ?>

			<?php if ( ! empty( $instance['web'] ) ) { ?>
				<span><i class="icon-widget-link" title="<?php esc_attr_e( 'Web', 'cryout' ) ?>"></i><strong><?php esc_attr_e( 'Web', 'cryout' ) ?>:</strong> <span class="cryout-contact-right"><a href="<?php echo esc_url( $instance['web'] ); ?>" target="_blank"><?php echo esc_url( $instance['web'] ); ?></a></span></span>
			<?php }; ?>
			<?php if ( ! empty( $instance['opening_hours'] ) ) { ?>
				<span><i class="icon-widget-opening-hours" title="<?php esc_attr_e( 'Opening Hours', 'cryout' ) ?>"></i><strong><?php esc_attr_e( 'Opening Hours', 'cryout' ) ?>:</strong> <span class="cryout-contact-right"><?php echo esc_html( $instance['opening_hours'] ); ?></span></span>
			<?php }; ?>

			<?php if ( ! empty( $instance['mapsrc'] ) ) { ?>
				<span class="map"><iframe width="<?php echo absint( $instance['mapwidth'] ) ?>" height="<?php echo absint( $instance['mapheight'] ) ?>" src="<?php echo esc_url( $instance['mapsrc'] ) ?>"></iframe></span>
				<?php }; ?>

		</address>

		<?php
		echo $after_widget;
	} // widget()

} // class CryoutContact

function cryout_widgets_init_contact() {
	register_widget( 'CryoutContact' );
}
add_action( 'widgets_init', 'cryout_widgets_init_contact' );


/*****************************************
 *          GetInTouch Widget
 ****************************************/

class CryoutGetInTouch extends WP_Widget {

	private $defaults = array(
		'title' => 'Get In Touch',
		'extratext' => '',
		'address' => '',
		'phone' => '',
		'email' => '',
		'opening_hours' => '',
	);

	function __construct() {
		parent::__construct(
			false,
			'Cryout GetInTouch',
			array(
				'description' => __( 'Displays contact information. Suitable for the Top Section area', 'cryout'),
				'classname' => 'widget_cryout_getintouch'
			)
		);
	} // __construct()

	// backend
	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>
		<div class="cryout-wtab-options-tabs">
			<fieldset>
				<legend></legend>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'extratext' ) ); ?>"><?php esc_attr_e( 'Intro Text:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'extratext' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'extratext' ) ); ?>" value="<?php esc_attr_e( $instance['extratext'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'address' ) ); ?>"><?php esc_attr_e( 'Address:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'address' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'address' ) ); ?>" value="<?php echo wp_kses_post( $instance['address'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'phone' ) ); ?>"><?php esc_attr_e( 'Phone:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'phone' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'phone' ) ); ?>" value="<?php esc_attr_e( $instance['phone'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'email' ) ); ?>"><?php esc_attr_e( 'Email:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'email' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'email' ) ); ?>" value="<?php esc_attr_e( $instance['email'] ); ?>" />
				</p>
				<p>
					<label for="<?php esc_attr_e( $this->get_field_id( 'opening_hours' ) ); ?>"><?php esc_attr_e( 'Opening Hours:', 'cryout' ) ?></label>
					<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'opening_hours' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'opening_hours' ) ); ?>" value="<?php esc_attr_e( $instance['opening_hours'] ); ?>" />
				</p>
			</fieldset>
		</div>
    <?php
	} // form()

	// save
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['extratext'] = wp_kses_post( $new_instance['extratext'] );
		$instance['address'] = wp_kses_post( $new_instance['address'] );
		$instance['phone'] = wp_kses_post( $new_instance['phone'] );
		$instance['email'] = esc_html( $new_instance['email'] );
		$instance['opening_hours'] = esc_html( $new_instance['opening_hours'] );

		return $instance;
	} // update()

	// frontend
	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget; ?>

			<?php if ( ! empty( $instance['extratext'] ) ) { ?><div class="cryoutgetintouch-extra cryout-getintouch-right"><?php echo wp_kses_post( $instance['extratext'] ); ?></div>
			<?php }; ?>
			<div class="cryoutgetintouch-items">
				<?php if ( ! empty( $instance['address'] ) ) { ?>
					<span class="cryoutgetintouch-address"><i class="icon icon-widget-location" title="<?php esc_attr_e( 'Address', 'cryout' ) ?>"></i><em class="cryout-getintouch-right address-block"> <?php echo preg_replace( '/\\n/','<br>',wp_kses_post( $instance['address'] ) ); ?></em></span>
				<?php }; ?>

				<?php if ( ! empty( $instance['phone'] ) ) { ?>
					<span class="cryoutgetintouch-phone"><i class="icon icon-widget-phone" title="<?php esc_attr_e( 'Phone', 'cryout' ) ?>"></i><em class="cryout-getintouch-right"> <?php echo esc_html( $instance['phone'] ); ?></em></span>
				<?php }; ?>

				<?php if ( ! empty( $instance['email'] ) ) { ?>
					<span class="cryoutgetintouch-email"><i class="icon icon-widget-mail" title="<?php esc_attr_e( 'E-mail', 'cryout' ) ?>"></i><em class="cryout-getintouch-right">  <a href="mailto:<?php echo esc_html( $instance['email'] ); ?>"><?php echo esc_html( $instance['email'] ); ?></a></em></span>
				<?php }; ?>
				<?php if ( ! empty( $instance['opening_hours'] ) ) { ?>
					<span class="cryoutgetintouch-opening-hours"><i class="icon icon-widget-opening-hours" title="<?php esc_attr_e( 'Opening Hours', 'cryout' ) ?>"></i><em class="cryout-getintouch-right"> <?php echo esc_html( $instance['opening_hours'] ); ?></em></span>
				<?php }; ?>

			</div>

		<?php
		echo $after_widget;
	} // widget()

} // class CryoutGetInTouch

function cryout_widgets_init_getintouch() {
	register_widget( 'CryoutGetInTouch' );
}
add_action( 'widgets_init', 'cryout_widgets_init_getintouch' );


/*****************************************
 *          About Widget
 ****************************************/

class CryoutAbout extends WP_Widget {

	private $defaults = array(
		'title' => 'About Us',
		'text' => '',
		'image_id' => false,
	);

	function __construct() {
		parent::__construct(
			false,
			'Cryout About',
			array(
				'description' => __( 'Displays information about you or the site.', 'cryout' ),
				'classname' => 'widget_cryout_about'
			)
		);
	} // __construct()

	function check_empty($value, $reverse=false){
		if ( ( empty($value) && !$reverse ) || ( !empty($value) && $reverse ) ) {
			return 'style="display:none;"';
		} else {
			return '';
		}
	} // check_empty()

	// backend
	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$url = '';
		/* If an attachment ID was found, get the image source. */
		if ( ! empty( $instance['image_id'] ) )
			list( $url, ) = wp_get_attachment_image_src( absint( $instance['image_id'] ), 'large' );

		$visibility = '';
		if (empty($url))
			$visibility = "style='display: none;'";
		?>

		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'cryout' ) ?></label><br />
			<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" value="<?php echo wp_kses_post( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'text' ) ); ?>"><?php esc_attr_e( 'Text:', 'cryout' ) ?></label>
			<textarea class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'text' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'text' ) ); ?>" rows="6"><?php echo wp_kses_post( $instance['text'] ); ?></textarea>
		</p>
		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'image_id' ) ); ?>"> </label>
			<a href="#" class="cryout-add-media cryout-add-media-img"><img class="cryout-media-image-url" src="<?php echo esc_url( $url ); ?>" <?php echo $this->check_empty($url) ?>/></a>
				<a href="#" class="cryout-add-media cryout-add-media-text button" <?php echo $this->check_empty($url,true) ?>>
					<?php esc_attr_e( 'Select image', 'cryout' ); ?>
				</a>
				<a href="#" class="cryout-remove-media button" <?php echo $this->check_empty($url) ?>>
					<?php esc_attr_e( 'Remove image', 'cryout' ); ?>
				</a>
			<input type="hidden" id="<?php esc_attr_e( $this->get_field_id( 'image_id' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'image_id' ) ); ?>" value="<?php esc_attr_e( $instance['image_id'] ); ?>" class="cryout-media-image"/>

		</p>
		<style>
			img.cryout-media-image-url {
				display: block;
				max-width: 100%;
				max-height: 250px;
				margin-bottom: 1em;
			}
		</style>

    <?php
	} // form()

	// save
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = wp_kses_post( $new_instance['title'] );
		$instance['image_id'] = absint( $new_instance['image_id'] );
		$instance['text'] = wp_kses_post( $new_instance['text'] );

		return $instance;
	} // update()

	// frontend
	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title = apply_filters( 'widget_title', esc_html( $instance['title'] ) );

		echo $before_widget;
		echo $before_title . esc_html( $title ) . $after_title;
		?>

		<?php if ( ! empty( $instance['image_id'] ) ) { ?>
		<span class="cryout-about-image">
			<?php echo wp_get_attachment_image( $instance['image_id'], 'medium' ); ?>
		</span>
		<?php }; ?>

		<?php if ( ! empty( $instance['text'] ) ) { ?>
			<div class="cryout-about-text"><?php echo preg_replace( '/\\n/', '<br>', wp_kses_post( $instance['text'] ) ); ?></div>
		<?php } ?>

		<?php
		echo $after_widget;
	} // widget()

} // class CryoutAbout

function cryout_widgets_init_about() {
	register_widget( 'CryoutAbout' );
}
add_action( 'widgets_init', 'cryout_widgets_init_about' );

function cryout_widgets_init_about_scripts() {
    if( function_exists( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    } else {
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_style( 'thickbox' );
    }
	wp_enqueue_script( _CRYOUT_THEME_SLUG . '-meta-js', get_template_directory_uri() . '/plus/resources/admin/meta.js' );
	wp_localize_script(
			_CRYOUT_THEME_SLUG . '-meta-js',
			'cryout_media_image',
			array(
				'title'  => __( 'Select Image', 'cryout' ),
				'button' => __( 'Select image', 'cryout' )
			)
		);
} // cryout_widgets_init_about_scripts()

add_action( 'admin_print_scripts-widgets.php', 'cryout_widgets_init_about_scripts' );


/*****************************************
 *          Portfolio Widget
 ****************************************/

class CryoutPortfolio extends WP_Widget {

	private $defaults = array(
		'title' => 'Portfolio',
		'number' => 6,
		'columns' => 3,
		'type' => 0,
		'tag' => 0
	);

	private $portfolio_types = array();
	private $portfolio_tags = array();

	function __construct() {
		parent::__construct(
			false,
			'Cryout Portfolio',
			array(
				'description' => __( 'Displays your portfolio items in a neat stack.', 'cryout'),
				'classname' => 'widget_cryout_portfolio'
			)
		);

	} // __construct()

	private function _get_data() {
		$types = get_terms( 'jetpack-portfolio-type', array( 'hide_empty' => false ) );
		$tags = get_terms( 'jetpack-portfolio-tag', array( 'hide_empty' => false ) );

		$this->portfolio_types = $this->portfolio_tags = array( array( 'name'=>__('All','cryout'), 'slug'=>0 ) );

		if (!is_wp_error($types)) {
			foreach ($types as $type) {
				$this->portfolio_types[] = array( 'name'=>$type->name, 'slug'=>$type->slug );
			}
			foreach ($tags as $tag) {
				$this->portfolio_tags[] = array( 'name'=>$tag->name, 'slug'=>$tag->slug );
			}
		}
	} // _get_data()

	private function _proto_select( $id = '', $name = '', $option = 0, $values = array(), $class = '', $style = 'width:100%' ) { ?>
		<select class="<?php echo $class ?>" style="<?php echo $style ?>" id="<?php echo $id; ?>" name="<?php echo $name ?>">
			<?php foreach ( $values as $value => $label ) { ?>
			<option value="<?php echo $value ?>" <?php selected( $option, $value ) ?>><?php echo $label ?></option>
			<?php } ?>
		</select>
	<?php
	} // _proto_select()

	// backend
	function form( $instance ) {

		$this->_get_data();

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$disabled = ( ! cryout_cpt_exists( 'jetpack-portfolio' ) ? 'disabled="disabled"' : '' ); ?>

		<?php if ( $disabled ) { ?>
			<p><em><strong><?php _e( "This widget uses Jetpacks' Portfolio functionality. Make sure the feature is enabled in Jetpack's options.", 'cryout'); ?></strong></em></p>
		<?php } ?>
		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'cryout' ) ?></label><br />
			<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" value="<?php esc_attr_e( $instance['title'] ); ?>" <?php echo $disabled ?>/>
		</p>

		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'type' ) ); ?>">Type:</label><br />
			<select class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'type' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'type' ) ); ?>" <?php echo $disabled ?>>
				<?php foreach ($this->portfolio_types as $item) { ?>
					<option value="<?php echo $item['slug'] ?>" <?php selected( $instance['type'], $item['slug'] ); ?>><?php echo $item['name'] ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'tag' ) ); ?>">Tag:</label><br />
			<select class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'tag' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'tag' ) ); ?>" <?php echo $disabled ?>>
				<?php foreach ($this->portfolio_tags as $item) { ?>
					<option value="<?php echo $item['slug'] ?>" <?php selected( $instance['tag'], $item['slug'] ); ?>><?php echo $item['name'] ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'number' ) ); ?>"><?php esc_attr_e( 'Number:', 'cryout' ) ?></label><br />
			<input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'number' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'number' ) ); ?>" value="<?php esc_attr_e( $instance['number'] ); ?>" <?php echo $disabled ?>/>
		</p>
		<p>
			<label for="<?php esc_attr_e( $this->get_field_id( 'columns' ) ); ?>"><?php esc_attr_e( 'Columns', 'cryout' ) ?></label>
			<?php $this->_proto_select(
				esc_attr( $this->get_field_id( "columns" ) ),
				esc_attr( $this->get_field_name( "columns" ) ),
				$instance["columns"],
				array( 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8 ),
				'widefat',
				'width:20%;'
			); ?>
		</p>
	<?php
	}

	// save
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = esc_html( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
		$instance['columns'] = absint( $new_instance['columns'] );
		$instance['type'] = esc_attr( $new_instance['type'] );
		$instance['tag'] = esc_attr( $new_instance['tag'] );

		return $instance;
	} // update()

	// frontend
	function widget( $args, $instance ) {
		extract( $args );

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = apply_filters( 'widget_title', esc_html( $instance['title'] ) );
		$number = intval( $instance['number'] );
		$columns = intval( $instance['columns'] );
		$type = $instance['type'];
		$tag = $instance['tag'];
		$orderby = 'date';
		$order = 'DESC';

		echo $before_widget;

		if( $title ) {
			echo $before_title . esc_html( $title ) . $after_title;
		}
		?>
		<div class="widget-portfolio portfolio-columns-<?php echo absint( $columns ); ?>">
		<?php
		$args = array(
			'post_type' => 'jetpack-portfolio',
			'tax_query' => array(),
			'posts_per_page' => $number,
			'order' => $order,
			'orderby' => $orderby
		);

		// if type selected, add tax query args
		if (!empty($type)) array_push( $args['tax_query'], array(
				'taxonomy' => 'jetpack-portfolio-type',
				'field'    => 'slug',
				'terms'    => $type
				) );
		// if tag selected, add tax query args
		if (!empty($tag)) array_push( $args['tax_query'], array(
				'taxonomy' => 'jetpack-portfolio-tag',
				'field'    => 'slug',
				'terms'    => $tag
				) );

		$portfolio = new WP_Query( apply_filters( 'cryout_widget_portfolio_query_args', $args ) );

		if ( $portfolio->have_posts() ) {
		?>
		<?php while ( $portfolio->have_posts() ): $portfolio->the_post(); ?>
			<div class="widget-portfolio-item">
				<?php if ( has_post_thumbnail() ) { ?>
					<a href="<?php echo esc_url( get_permalink() ) ?>" title="<?php the_title_attribute(); ?>" class="portfolio-image">
						<?php the_post_thumbnail( _CRYOUT_THEME_SLUG . '-featured-square' ); ?>
						<span class="portfolio-overlay">
							<!-- <i class="icon-plus"></i> -->
							<span class="portfolio-title"><?php wp_kses_post( the_title() ); ?></span>
						</span>

					</a>
				<?php } ?>
		    </div>
		<?php endwhile; } elseif ( current_user_can( 'manage_options' ) ) { ?>
			<p><?php esc_attr_e( 'There are no Jetpack Portfolio posts to display. Add Portfolio items to display them here or disable this widget.', 'cryout' ) ?></p>
		<?php } ?>
		</div>

		<?php echo $after_widget;
	} // widget()

} // class CryoutPortfolio

// Add Widget
function cryout_widgets_init_portfolio() {
	register_widget( 'CryoutPortfolio' );
}
add_action( 'widgets_init', 'cryout_widgets_init_portfolio' );


/* FIN */
