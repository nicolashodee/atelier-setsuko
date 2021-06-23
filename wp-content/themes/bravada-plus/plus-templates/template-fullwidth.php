<?php
/*
 * Template Name: Full Width
 * 
 * This is a custom page template that does not follow the configured theme widths, 
 * ignores sidebars entirely and always displays content full screen width. 
 *
 * @package Cryout Plus
 */
get_header(); ?>

	<div id="container" class="one-column template-full-width">

		<main id="main" class="main">
			<?php cryout_before_content_hook(); ?>
			
			<?php get_template_part( 'content/content', 'page' ); ?>

			<?php cryout_after_content_hook(); ?>
		</main><!-- #main -->

		<?php //bravada_get_sidebar(); ?>

	</div><!-- #container -->

<?php get_footer();
