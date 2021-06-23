<?php
/**
 * The template for displaying Jetpack Portfolio archives.
 *
 * @package Plus
 *
 */

get_header(); ?>

	<div id="container" class="<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_layout_class' ); ?>">
		<main id="main" class="main">
			<?php cryout_before_content_hook(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="article-inner">
					<?php if ( have_posts() ) : ?>
						<header <?php cryout_schema_microdata( 'element' ); ?>>
							<h1 class="entry-title" <?php echo cryout_schema_microdata('entry-title', 0) ?>><?php single_cat_title(); ?></h1>
							<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
						</header><!-- .page-header -->

						<div class="content-masonry entry-content" <?php cryout_schema_microdata( 'blog' ); ?>>

							<?php
							$term =  get_queried_object();

							if ( $term->taxonomy == 'jetpack-portfolio-type' ) {
								$shortcode_filter = 'include_type="'. $term->slug . '"';
							} else {
								$shortcode_filter = 'include_tag="'. $term->slug . '"';
							}

							?>

							<div id="portfolio-masonry">
								<?php echo do_shortcode( '[portfolio ' . $shortcode_filter . ' display_types="false" display_tags="false" display_author="false" columns="3" display_content="false" ]' ); ?>
							</div>

						</div><!--content-masonry-->
						<?php call_user_func( _CRYOUT_THEME_SLUG . '_pagination' );

						// If no content, include the "No posts found" template.
						else :
							get_template_part( 'content/content', 'notfound' );
						endif;	?>

					</div>
				</article>

		<?php cryout_after_content_hook(); ?>
		</main><!-- #main -->

		<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_sidebar' ); ?>
	</div><!-- #container -->

<?php get_footer(); ?>
