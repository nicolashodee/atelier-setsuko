<?php
/**
 * Plus core functions
 *
 * @package Cryout Plus
 */

/**
 * Landing page posts list override for Plus
 */
if ( ! function_exists( 'cryout_lpindexplus' ) ):
function cryout_lpindexplus() {

	$options = cryout_get_option( array(
		//_CRYOUT_THEME_PREFIX . '_landingpage',
		_CRYOUT_THEME_PREFIX . '_lpposts',
		_CRYOUT_THEME_PREFIX . '_lppostscount',
		_CRYOUT_THEME_PREFIX . '_lppostscat',
		_CRYOUT_THEME_PREFIX . '_lppostslayout',
	) );

	switch ($options[_CRYOUT_THEME_PREFIX . '_lpposts']) {

		case 2: // static page

			if ( have_posts() ) :
					?><section id="lp-page"> <div class="lp-page-inside"><?php
					get_template_part( 'content/content', 'page' );
					?></div> </section><!-- #lp-posts --><?php
			endif;

		break;

		case 1: // posts

			if ( get_query_var('paged') ) $paged = get_query_var('paged');
			elseif ( get_query_var('page') ) $paged = get_query_var('page');
			else $paged = 1;

			if ( ! empty( $options[_CRYOUT_THEME_PREFIX . '_lppostscat'] ) ) $cat = $options[_CRYOUT_THEME_PREFIX . '_lppostscat']; else $cat = '';

			$args = apply_filters( _CRYOUT_THEME_PREFIX . '_lpindex_query_args', array(
				'posts_per_page' => $options[_CRYOUT_THEME_PREFIX . '_lppostscount'],
				'cat' => cryout_localize_cat( $cat ),
				'paged' => intval($paged),
				'lang' => cryout_localize_code(),
			) );

			cryout_lpindexplus_output( $args );

		break;

		case 0: // disabled
		default: break;
	}

} // cryout_lpindexplus()
endif;

/**
 * Landing page posts/page index output for Plus
 */
if ( ! function_exists('cryout_lpindexplus_output') ):
function cryout_lpindexplus_output( $args = array() ) {

	$custom_query = new WP_query();
	$custom_query->query( $args );

	if ( $custom_query->have_posts() ) :  ?>
		<section id="lp-posts"> <div class="lp-posts-inside">
		<div id="content-masonry" class="content-masonry" <?php cryout_schema_microdata( 'blog' ); ?>> <?php
			while ( $custom_query->have_posts() ) : $custom_query->the_post();
				get_template_part( 'content/content', get_post_format() );
			endwhile; ?>
		</div> <!-- content-masonry -->
		</div> </section><!-- #lp-posts -->
		<?php call_user_func( _CRYOUT_THEME_SLUG . '_pagination' );
		wp_reset_postdata();
	else :
		get_template_part( 'content/content', 'notfound' );
	endif;

} // cryout_lpindexplus_output()
endif;

/**
 * Landing page portfolio items for Plus
 */
if ( ! function_exists( 'cryout_lpportfolioplus' ) ):
function cryout_lpportfolioplus() {
	$options = cryout_get_option(
				array(
					 _CRYOUT_THEME_PREFIX . '_lpport',
					 _CRYOUT_THEME_PREFIX . '_lpportcount',
					 _CRYOUT_THEME_PREFIX . '_lpportcols',
					 _CRYOUT_THEME_PREFIX . '_lpportorderby',
					 _CRYOUT_THEME_PREFIX . '_lpportsort',
					 _CRYOUT_THEME_PREFIX . '_lpporttitle',
					 _CRYOUT_THEME_PREFIX . '_lpportdesc',
					 _CRYOUT_THEME_PREFIX . '_lpportreadmore',
					 _CRYOUT_THEME_PREFIX . '_lpportreadlink',
				 )
			 );

	if ( ( $options[_CRYOUT_THEME_PREFIX . '_lpportcount'] <= 0 ) || ( $options[_CRYOUT_THEME_PREFIX . '_lpport'] == 0 ) ) return;

	$args = array(
		'post_type' => 'jetpack-portfolio',
		'tax_query' => array(),
		'posts_per_page' => $options[_CRYOUT_THEME_PREFIX . '_lpportcount'],
		'order' => $options[_CRYOUT_THEME_PREFIX . '_lpportsort'],
		'orderby' => $options[_CRYOUT_THEME_PREFIX . '_lpportorderby'],
		'lang' => cryout_localize_code(),
	);

	$type = apply_filters( _CRYOUT_THEME_SLUG . '_portfolio_query_type', '' );
	// if type selected, add tax query args
	if (!empty($type)) array_push( $args['tax_query'], array(
			'taxonomy' => 'jetpack-portfolio-type',
			'field'    => 'slug',
			'terms'    => $type
			) );

	$tag = apply_filters( _CRYOUT_THEME_SLUG . '_portfolio_query_tag', '' );
	// if tag selected, add tax query args
	if (!empty($tag)) array_push( $args['tax_query'], array(
			'taxonomy' => 'jetpack-portfolio-tag',
			'field'    => 'slug',
			'terms'    => $tag
			) );

	cryout_lpportfolioplus_output( apply_filters( _CRYOUT_THEME_SLUG . '_portfolio_query_args', $args ), $options );
	wp_reset_postdata();

} //  cryout_lpportfolioplus()
endif;


/**
 * Landing page portfolio container output for Plus
 */
if ( ! function_exists( 'cryout_lpportfolioplus_output' ) ):
function cryout_lpportfolioplus_output( $args=array(), $options=NULL, $titles = true, $columns = 0 ) {

	// check that the post type is available before trying to use it
	if ( ! post_type_exists( 'jetpack-portfolio' ) ) {
		?><section id="lp-portfolio"><div class="lp-portfolio-inside"><?php
		_e('Jetpack\'s Portfolio post type does not appear to be enabled on your site. Check its status or disable this section from the theme\'s options.', 'cryout');
		?></div></section><?php
		return false;
	}

 	$item_counter = 1;

	if ( empty($columns) ) $columns = absint( $options[_CRYOUT_THEME_PREFIX . '_lpportcols'] );
	if ( empty($options) ) $options = cryout_get_option();

	if ( in_array( $columns, array( 1,2,3 ) ) ) {
		$image_size = _CRYOUT_THEME_SLUG . '-featured-square-large';
	} else {
		$image_size = _CRYOUT_THEME_SLUG . '-featured-square';
	}

	$readmore_label = do_shortcode( $options[_CRYOUT_THEME_PREFIX . '_lpportreadmore'] );
	$readmore_link = do_shortcode( $options[_CRYOUT_THEME_PREFIX . '_lpportreadlink'] );
	$readmore_target = ( apply_filters( _CRYOUT_THEME_SLUG . '_portfolio_target', false ) ? 'target="_blank"' : '' );

	$custom_query = new WP_Query( $args );
    if ( $custom_query->have_posts() ) : ?>
		<section id="lp-portfolio" class="lp-portfolio lp-portfolio-rows-<?php echo $columns; ?>">
			<?php if ( $titles && ( $options[_CRYOUT_THEME_PREFIX . '_lpporttitle'] || $options[_CRYOUT_THEME_PREFIX . '_lpportdesc'] ) ) { ?>
				<header class="lp-section-header">
					<?php if ( ! empty( $options[_CRYOUT_THEME_PREFIX . '_lpporttitle'] ) ) { ?> <h3 class="lp-section-title"> <?php  echo do_shortcode( $options[_CRYOUT_THEME_PREFIX . '_lpporttitle'] ); ?></h3><?php } ?>
					<?php if ( ! empty( $options[_CRYOUT_THEME_PREFIX . '_lpportdesc'] ) ) { ?><div class="lp-section-desc"> <?php echo do_shortcode( $options[_CRYOUT_THEME_PREFIX . '_lpportdesc'] ); ?></div><?php } ?>
				</header>
			<?php } ?>

			<div class="lp-portfolio-inside">

				<?php $project_types = get_terms ( array (
					'taxonomy' => 'jetpack-portfolio-type',
					'orderby' => 'name',
					'order' => 'ASC',
					)
				);

				if ( ! empty ( $project_types ) && ! is_wp_error( $project_types ) ) : ?>

				<nav id="portfolio-filter">
					<a href="#" class="active" data-slug="all"> <?php _e('All', 'cryout') ?></a>
					<?php foreach ($project_types as $project) { ?>
						<a href="<?php echo get_term_link($project); ?>" data-slug="<?php echo $project->slug ?>"> <?php echo $project->name ?></a>
					<?php } ?>
				</nav>

				<?php endif; ?>

				<div id="portfolio-masonry">
					<div class="jetpack-portfolio-shortcode">
			    		<?php while ( $custom_query->have_posts() ) :
				            $custom_query->the_post();
							if ( has_excerpt() ) {
								$excerpt = get_the_excerpt();
							} else {
								$excerpt = get_the_content();
							};
				            $item = array();
				            $item['colno'] = $item_counter++;
				            $item['counter'] = $options[_CRYOUT_THEME_PREFIX . '_lpportcount'];
				            $item['title'] = get_the_title();
				            $item['content'] = ''; //$excerpt;
				            list( $item['image'], ) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $image_size );
							$item['link'] = apply_filters( _CRYOUT_THEME_SLUG . '_portfolio_url', get_permalink(), get_the_ID() );

							$project_types = wp_get_object_terms( get_the_ID(), 'jetpack-portfolio-type', array( 'fields' => 'slugs' ) );
							$item['class'] = array();
							$item['types'] = array();

							// add a type- class for each project type
							foreach ( $project_types as $project_type ) {
								$item['class'][] = 'type-' . esc_html( $project_type );
								$item['types'][] = esc_html( $project_type );
							}

							cryout_lpportfolioplus_single_output( $item );

						endwhile; ?>
					</div>
				</div><!-- portfolio-masonry -->
			</div><!-- portfolio-inside -->
			<?php if( $titles && ! empty( $readmore_link ) ) { ?>
				<a class="lp-port-readmore" href="<?php echo esc_url( $readmore_link ) ?>" <?php echo $readmore_target ?>> <?php echo esc_html( $readmore_label ); ?> <i class="icon-angle-right"></i></a>
			<?php } ?>
		</section><!-- .lp-portfolio -->
	<?php endif;
	wp_reset_postdata();
} // cryout_lpportfolioplus_output()
endif;

/**
 * Landing page portfolio single item output
 */
if ( ! function_exists( 'cryout_lpportfolioplus_single_output' ) ):
function cryout_lpportfolioplus_single_output( $data ) {
	extract($data); ?>
			<div class="lp-port lp-port-<?php echo absint( $colno ); ?> portfolio-entry <?php echo implode( ' ', $class ); ?>">
				<a href="<?php if( ! empty( $link ) ) { echo esc_url( $link ); } ?>" title="<?php echo esc_attr( $title ); ?>">
					<div class="lp-port-image">
						<?php if( ! empty( $image ) ) { ?><img alt="<?php echo esc_attr( $title ); ?>" src="<?php echo esc_url( $image ); ?>" /> <?php } ?>
					</div>
					<div class="lp-port-content">
						<?php if ( ! empty( $title ) ) { ?><h4 class="lp-port-title"><?php echo do_shortcode( $title ); ?></h4><?php } ?>
						<div class="lp-port-text">
							<ul class="lp-port-tax">
								<?php foreach( $types as $type ) { ?>
									<li><?php echo $type ?></li>
								<?php } ?>
							</ul>
							<?php if ( ! empty( $content ) ) { ?>
								<div class="lp-box-text-inside"> <?php echo do_shortcode( $content ); ?> </div>
							<?php } ?>
						</div>
					</div>
				</a>
			</div><!-- lp-port -->
	<?php
} // cryout_lpportfolioplus_single_output()
endif;


/**
 * Landing page testimonials items for Plus
 */
if ( ! function_exists( 'cryout_lptestimonialsplus' ) ):
function cryout_lptestimonialsplus() {
	$options = cryout_get_option(
				array(
					 _CRYOUT_THEME_PREFIX . '_lptt',
					 _CRYOUT_THEME_PREFIX . '_lpttcount',
					 _CRYOUT_THEME_PREFIX . '_lpttcols',
					 _CRYOUT_THEME_PREFIX . '_lpttorderby',
					 _CRYOUT_THEME_PREFIX . '_lpttsort',
					 _CRYOUT_THEME_PREFIX . '_lptttitle',
					 _CRYOUT_THEME_PREFIX . '_lpttdesc',
					 _CRYOUT_THEME_PREFIX . '_lpttimage',
				 )
			 );

	if ( ( $options[_CRYOUT_THEME_PREFIX . '_lpttcount'] <= 0 ) || ( $options[_CRYOUT_THEME_PREFIX . '_lptt'] == 0 ) ) return;

	$args = array(
		'post_type' => 'jetpack-testimonial',
		'tax_query' => array(),
		'posts_per_page' => $options[_CRYOUT_THEME_PREFIX . '_lpttcount'],
		'order' => $options[_CRYOUT_THEME_PREFIX . '_lpttsort'],
		'orderby' => $options[_CRYOUT_THEME_PREFIX . '_lpttorderby'],
		'lang' => cryout_localize_code(),
	);

	cryout_lptestimonialsplus_output( apply_filters( _CRYOUT_THEME_SLUG . '_testimonials_query_args', $args ), $options );
	wp_reset_postdata();

} //  cryout_lptestimonialsplus()
endif;


/**
 * Landing page testimonials container output for Plus
 */
if ( ! function_exists( 'cryout_lptestimonialsplus_output' ) ):
function cryout_lptestimonialsplus_output( $args=array(), $options=NULL ) {

	// check that the post type is available before trying to use it
	if ( ! post_type_exists( 'jetpack-testimonial' ) ) {
		?><section id="lp-testimonials"><div class="lp-testimonials-inside"><?php
		_e('Jetpack\'s Testimonials post type does not appear to be enabled on your site. Check its status or disable this section from the theme\'s options.', 'cryout');
		?></div></section><?php
		return false;
	}

 	$item_counter = 1;

	$image_size = _CRYOUT_THEME_SLUG . '-featured-square'; // '-featured-square-large' ???

	$custom_query = new WP_Query( $args );
    if ( $custom_query->have_posts() ) : ?>
		<section id="lp-testimonials" class="lp-testimonials lp-testimonials-rows-<?php echo absint( $options[_CRYOUT_THEME_PREFIX . '_lpttcols'] ); ?>">
			<?php if( $options[_CRYOUT_THEME_PREFIX . '_lptttitle'] || $options[_CRYOUT_THEME_PREFIX . '_lpttdesc'] ) { ?>
				<header class="lp-section-header">
					<?php if ( ! empty( $options[_CRYOUT_THEME_PREFIX . '_lptttitle'] ) ) { ?> <h2 class="lp-section-title"> <?php  echo do_shortcode( $options[_CRYOUT_THEME_PREFIX . '_lptttitle'] ); ?></h2><?php } ?>
					<?php if ( ! empty( $options[_CRYOUT_THEME_PREFIX . '_lpttdesc'] ) ) { ?><div class="lp-section-desc"> <?php echo do_shortcode( $options[_CRYOUT_THEME_PREFIX . '_lpttdesc'] ); ?></div><?php } ?>
				</header>
			<?php } ?>
			<div class="lp-testimonials-inside">
    		<?php while ( $custom_query->have_posts() ) :
	            $custom_query->the_post();
				if ( has_excerpt() ) {
					$text = get_the_excerpt();
				} else {
					$text = get_the_content();
				};
	            $item = array();
	            $item['colno'] = $item_counter++;
	            $item['counter'] = $options[_CRYOUT_THEME_PREFIX . '_lpttcount'];
	            $item['title'] = get_the_title();
	            $item['content'] = $text;
	            list( $item['image'], ) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $image_size );

				cryout_lptestimonialsplus_single_output( $item );

			endwhile; ?>
			</div>
		</section><!-- .lp-testimonials -->
	<?php endif;
	wp_reset_postdata();
} // cryout_lptestimonialsplus_output()
endif;

/**
 * Landing page testimonial single item output
 */
if ( ! function_exists( 'cryout_lptestimonialsplus_single_output' ) ):
function cryout_lptestimonialsplus_single_output( $data ) {
	extract($data); ?>
			<div class="lp-tt lp-tt-<?php echo absint( $colno ); ?> ">
					<div class="lp-tt-content">
						<div class="lp-tt-text">
							<div class="lp-tt-text-inside">
								<?php if ( ! empty( $content ) ) { echo do_shortcode( $content ); } ?>
							</div>
						</div>
						<div class="lp-tt-meta">
							<?php if( ! empty( $image ) ) { ?><div class="lp-tt-image"><img alt="<?php echo esc_attr( $title ); ?>" src="<?php echo esc_url( $image ); ?>" /></div> <?php } ?>
							<?php if ( ! empty( $title ) ) { ?><h3 class="lp-tt-title"><?php echo do_shortcode( $title ); ?></h3><?php } ?>
						</div>
					</div>
			</div><!-- lp-tt -->
	<?php
} // cryout_lptestimonialsplus_single_output()
endif;


/*
 * Retrieves related posts based on theme options
 * hooked in cryout_singular_before_comments_hook()
 */
if ( ! function_exists( 'cryout_related_posts' ) ):
function cryout_related_posts() {
	wp_reset_postdata();
	global $post;

	// Define shared post arguments
	$args = array(
		'no_found_rows'				=> true,
		'update_post_meta_cache'	=> false,
		'update_post_term_cache'	=> false,
		//'ignore_sticky_posts'		=> 1,
		'orderby'					=> 'rand',
		'post__not_in'				=> array($post->ID),
		'posts_per_page'			=> 3
	);
	// Related by categories
	if ( cryout_get_option( _CRYOUT_THEME_PREFIX . '_related_posts' ) == 1 ) {

		$cats = get_post_meta($post->ID, 'related-cat', true);

		if ( !$cats ) {
			$cats = wp_get_post_categories($post->ID, array('fields'=>'ids'));
			$args['category__in'] = $cats;
		} else {
			$args['cat'] = $cats;
		}
		if ( !$cats ) { $any = true; }
	}
	// Related by tags
	if ( cryout_get_option( _CRYOUT_THEME_PREFIX . '_related_posts' ) == 2 ) {

		$tags = get_post_meta($post->ID, 'related-tag', true);

		if ( !$tags ) {
			$tags = wp_get_post_tags($post->ID, array('fields'=>'ids'));
			$args['tag__in'] = $tags;
 		} else {
			$args['tag_slug__in'] = explode(',', $tags);
		}
		if ( !$tags ) { $any = true; }
	}

	$related_query = !isset($any) ? new WP_Query($args) : new WP_Query;

	if ( $related_query->have_posts() ):
	?>

	<aside class="related-posts">

		<h2 class="related-main-title"><span><?php esc_html_e( cryout_get_option( _CRYOUT_THEME_PREFIX . '_related_title' ) ) ?></span></h2>
		<ul>

			<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
			<li class="related-item">
				<article>

					<div class="related-thumbnail">
						<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
							<?php if ( has_post_thumbnail() ):
								the_post_thumbnail( _CRYOUT_THEME_SLUG . '-featured-third');
							elseif ( 1 == cryout_get_option( _CRYOUT_THEME_PREFIX . '_fplaceholder') ): ?>
								<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/resources/images/fallback/fallback-medium.gif" alt="<?php the_title_attribute(); ?>" />
							<?php endif; ?>
						</a>
						<?php if ( comments_open() ): ?>
							<a class="related-comments" href="<?php comments_link(); ?>"><span><i class="icon-comments"></i><?php comments_number( '0', '1', '%' ); ?></span></a>
						<?php endif; ?>
					</div><!--related-thumbnail-->

					<div class="related-inside">

						<h3 class="related-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						</h3><!--related-title-->

						<div class="related-meta">
							<p class="related-date"><?php the_date(); ?></p>
						</div><!--related-meta-->

					</div><!--related-inside-->

				</article>
			</li><!--related-item-->
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>

		</ul>
	</aside><!--related-posts-->
	<?php

	endif;
	wp_reset_postdata();

} // cryout_related_posts()
endif;

/*
 * Outputs custom taxonomy terms lists for single CPT
 */
function cryout_single_taxonomy_terms( $id, $taxonomy, $class = "taxonomy-container", $label = '' ) {

    $terms = wp_get_post_terms( $id, $taxonomy );
    $links = array();

    foreach ( $terms as $term ) {
        $link = get_term_link( $term );
        if ( is_wp_error( $link ) ) continue;
        $links[] = '<a href="' . $link . '">' . $term->name . '</a>';
    }

	if (!empty($links)) echo '<span class="' . $class . '">' . $label . ' ' . implode( apply_filters( _CRYOUT_THEME_SLUG . '_taxonomy_terms_list_delim', ', ' ), $links) . '</span>';

} // cryout_single_taxonomy_terms()

/**
* Master Plus hook to force customizer options to take effect
*/
if ( ! function_exists( 'cryout_master_hook_plus' ) ) :
function cryout_master_hook_plus() {
	$interim_options = cryout_get_option( array(
		_CRYOUT_THEME_PREFIX . '_related_posts',
		)
	);

	if ( FALSE != $interim_options[_CRYOUT_THEME_PREFIX . '_related_posts'] ) {
		add_action( 'cryout_singular_before_comments_hook', 'cryout_related_posts' );
	}

};
endif;
add_action( 'wp', 'cryout_master_hook_plus' );

/**
* Master Plus hook to force customizer options to take effect
*/
if ( ! function_exists( 'cryout_master_customize_hook_plus' ) ) :
function cryout_master_customize_hook_plus() {
	global $cryout_theme_settings;
	foreach ($cryout_theme_settings['options'] as $data => &$item) {
		if ($item['id'] == _CRYOUT_THEME_PREFIX . '_lporder') {
			$item['input_attrs']['statuses'] = array(
				// this array is defined in multiple locations:
				// 	- plus.php / retrieve_lporder_sortable_data()
				// 	- plus-specifics.php / master options array
				//  - plus-functions.php / cryout_master_customize_hook_plus()
				'slider' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpslider'),
				'text-zero' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextzero'),
				'blocks-1' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpblockscontent1'),
				'text-one' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextone'),
				'boxes-1' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpboxcat1'),
				'text-two' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptexttwo'),
				'blocks-2' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpblockscontent2'),
				'text-three'	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextthree'),
				'boxes-2' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpboxcat2'),
				'text-four' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextfour'),
				'boxes-3' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpboxcat3'),
				'text-five' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextfive'),
				'portfolio' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpport'),
				'testimonials' 	=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptt'),
				'index' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lpposts'),
				'text-six' 		=> cryout_get_option(_CRYOUT_THEME_PREFIX . '_lptextsix'),
			);
		}
	}

};
endif;
add_action( 'customize_register', 'cryout_master_customize_hook_plus', 9 );

// FIN
