<?php
/*
 * Template Name: Blog (Posts List)
 *
 * @package Cryout Plus
 */

get_header(); ?>

	<div id="container" class="<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_layout_class' ); ?>">

		<main id="main" class="main">

			<?php cryout_before_content_hook(); ?>

			 <?php while ( have_posts() ) : the_post(); ?>

				<header class="page-header pad-container post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/WebPageElement">
					<?php the_title( '<h1 class="page-title" ' . cryout_schema_microdata( 'entry-title', 0 ) . '>', '</h1>' ); ?>
					<span class="entry-meta" >
						<?php edit_post_link( __( 'Edit', 'cryout' ), '<span class="edit-link"><i class="icon-edit"></i> ', '</span>' ); ?>
					</span>
					<div class="taxonomy-description">
						<?php the_content(); ?>
					</div>
				</header>

			<?php endwhile; ?>

			<?php
			$old_query = $wp_query;

			wp_reset_postdata();

			if ( get_query_var('paged') ) { $paged = get_query_var('paged'); } elseif ( get_query_var('page') ) { $paged = get_query_var('page'); } else { $paged = 1; }

			$wp_query = new WP_Query( array(
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'desc',
				'posts_per_page' => get_option('posts_per_page'),
				'paged' => $paged,
			) );

			if ( have_posts() ) : ?>
				<div id="content-masonry" class="content-masonry" <?php cryout_schema_microdata( 'blog' ); ?>> <?php
					while ( have_posts() ) : the_post();
						global $more; $more=0;
						get_template_part( 'content/content', get_post_format() );
					endwhile; ?>
				</div> <!-- content-masonry -->
				<?php call_user_func( _CRYOUT_THEME_SLUG . '_pagination' );
			else :
				get_template_part( 'content/content', 'notfound' );
			endif;

			wp_reset_postdata();
			$wp_query = $old_query;
			unset($old_query);

			?>

			<?php cryout_after_content_hook(); ?>
		</main><!-- #main -->

		<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_sidebar' ); ?>

	</div><!-- #container -->

<?php get_footer();
