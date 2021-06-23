<?php
/**
 * The Template for displaying single Jetpack potfolios.
 *
 * @package Cryout Plus
 */

get_header();?>

<div id="container" class="<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_layout_class' ); ?>">
	<main id="main" class="main">
		<?php cryout_before_content_hook(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); cryout_schema_microdata( 'article' );?>>
				<div class="schema-image">
					<?php cryout_featured_hook(); ?>
				</div>
				<div class="portfolio-featured-single">
                	<?php the_post_thumbnail('full'); ?>
					<div class="entry-meta-container">
						<div class="entry-meta">
							<span class="entry-date"><?php _e('Date: ', 'cryout'); the_date('F Y') ?></span>
							<?php cryout_single_taxonomy_terms( get_the_ID(), 'jetpack-portfolio-type', 'portfolio-projects', __('Project Types: ', 'cryout') ); ?>
							<?php cryout_single_taxonomy_terms( get_the_ID(), 'jetpack-portfolio-tag', 'portfolio-tags', __('Tags: ', 'cryout') ); ?>
						</div><!-- .entry-meta -->
					</div>
				</div>

                <div class="article-inner">
					<header>
						<?php cryout_post_title_hook(); ?>

						<?php the_title( '<h1 class="entry-title singular-title" ' . cryout_schema_microdata( 'entry-title', 0 ) . '>', '</h1>' ); ?>

						<?php edit_post_link( __( 'Edit', 'cryout' ), '<span class="edit-link"><i class="icon-edit"></i> ', '</span>' ); ?>
					</header>

					<?php cryout_singular_before_inner_hook();  ?>

					<div class="entry-content" <?php cryout_schema_microdata('entry-content'); ?>>
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'cryout' ), 'after' => '</span></div>' ) ); ?>
					</div><!-- .entry-content -->

					<?php /* if ( get_the_author_meta( 'description' ) ) {
							// If a user has filled out their description, show a bio on their entries
							get_template_part( 'content/user-bio' );
					} */ ?>


			<?php	/*	<nav id="nav-below" class="navigation" role="navigation">
						<div class="nav-previous"><?php previous_post_link( '%link', '<i class="icon-angle-left"></i> <span>%title</span>' ); ?></div>
						<div class="nav-next"><?php next_post_link( '%link', '<span>%title</span> <i class="icon-angle-right"></i>' ); ?></div>
					</nav><!-- #nav-below -->

					<?php cryout_singular_before_comments_hook();  ?>

					<?php comments_template( '', true ); ?>
					<?php cryout_singular_after_inner_hook();  ?>

                */ ?>
				</div><!-- .article-inner -->
			</article><!-- #post-## -->

		<?php endwhile; // end of the loop. ?>

		<?php cryout_after_content_hook(); ?>
	</main><!-- #main -->

	<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_sidebar' ); ?>
</div><!-- #container -->

<?php get_footer();
