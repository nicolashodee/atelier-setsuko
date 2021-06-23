<?php
/*
 * Template Name: About Us
 *
 * @package Cryout Plus
 */

// retrieve specific page metas
for ($i=0;$i<=1;$i++) {
	$cryout_page_meta[$i] = get_post_meta( get_the_ID(), '_cryout_meta_templatefield_'.$i, true );
}

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

						<?php //cryout_singular_before_inner_hook();  ?>

						<div class="entry-content template-about" <?php cryout_schema_microdata( 'text' ); ?>>
							<?php if (!empty($cryout_page_meta[0])) { ?>
								<div class="template-image">
									<?php list($template_img,) = wp_get_attachment_image_src( absint( $cryout_page_meta[0] ), 'full' ); ?>
									<img src="<?php echo $template_img ?>">
								</div>
							<?php } ?>
							<div class="template-content">
								<?php the_content(); ?>
							</div>
							<?php if (!empty($cryout_page_meta[1])) { ?>
								<div class="template-team">
									<?php echo do_shortcode( $cryout_page_meta[1] ) ?>
								</div>
							<?php } ?>
						</div><!-- .entry-content -->

						<?php //comments_template( '', true ); ?>
						<?php //cryout_singular_after_inner_hook();  ?>
					</div><!-- .article-inner -->
				</article><!-- #post-## -->

			<?php endwhile; ?>

			<?php cryout_after_content_hook(); ?>
		</main><!-- #main -->

		<?php call_user_func( _CRYOUT_THEME_SLUG . '_get_sidebar' ); ?>

	</div><!-- #container -->

<?php get_footer();
