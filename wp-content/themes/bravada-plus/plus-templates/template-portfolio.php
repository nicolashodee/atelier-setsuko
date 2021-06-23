<?php
/*
 * Template Name: Portfolio
 *
 * @package Cryout Plus
 */

get_header(); ?>

	<div id="container" class="<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_layout_class' ); ?>">

		<main id="main" class="main">
			<?php cryout_before_content_hook(); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="schema-image">
						<?php cryout_featured_hook(); ?>
					</div>
					<div class="article-inner">
						<header>
							<?php the_title( '<h1 class="entry-title" ' . cryout_schema_microdata( 'entry-title', 0 ) . '>', '</h1>' ); ?>
							<span class="entry-meta" >
								<?php edit_post_link( __( 'Edit', 'cryout' ), '<span class="edit-link"><i class="icon-edit"></i> ', '</span>' ); ?>
							</span>
						</header>

						<?php cryout_singular_before_inner_hook();  ?>

						<div class="entry-content" <?php cryout_schema_microdata( 'text' ); ?>>
							<?php the_content(); ?>
							<?php // wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'cryout' ), 'after' => '</div>' ) ); ?>
						</div><!-- .entry-content -->


						<?php cryout_lpportfolioplus_output(
							array(
								'post_type' => 'jetpack-portfolio',
								'tax_query' => array(),
								'posts_per_page' => -1,
								'order' => 'ASC',
								'orderby' => 'date'
							),
							NULL,	// options
							false,	// titles
							4		// columns
						); ?>

						<?php cryout_singular_after_inner_hook(); ?>
					</div><!-- .article-inner -->
				</article><!-- #post-## -->

			<?php endwhile; ?>

			<?php cryout_after_content_hook(); ?>
		</main><!-- #main -->

		<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_sidebar' ); ?>

	</div><!-- #container -->

<?php get_footer();
