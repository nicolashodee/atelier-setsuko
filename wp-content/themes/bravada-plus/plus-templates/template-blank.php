<?php
/*
 * Template Name: Blank Page
 *
 * @package Cryout Plus
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php cryout_meta_hook(); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php
	cryout_header_hook();
	wp_head();
?>
<style type="text/css">
	html, body {
		height: 100%;
		margin-top: 0 !important;
	}
	.page-template-template-blank #container {
	    display: table;
		height: 100%;
		width: 100%;
		max-width: 1920px;
	}
	.page-template-template-blank #main {
		display: table-cell;
		vertical-align: middle;
		max-width: 100%;
	}
	
</style>
</head>
<body <?php body_class(); cryout_schema_microdata( 'body' );?>>

	<div id="container" class="one-column">

		<main id="main" class="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="article-inner">
						<header>
							<?php // the_title( '<h1 class="entry-title" ' . cryout_schema_microdata( 'entry-title', 0 ) . '>', '</h1>' ); ?>
							<span class="entry-meta">
								<?php edit_post_link( __( 'Edit', 'cryout' ), '<span class="edit-link"><i class="icon-edit"></i> ', '</span>' ); ?>
							</span>
						</header>

						<div class="entry-content" <?php cryout_schema_microdata( 'text' ); ?>>
							<?php the_content(); ?>
							<?php // wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'cryout' ), 'after' => '</div>' ) ); ?>
						</div><!-- .entry-content -->

						<?php // comments_template( '', true ); ?>
					</div><!-- .article-inner -->
				</article><!-- #post-## -->

			<?php endwhile; ?>

		</main><!-- #main -->

	</div><!-- #container -->

	<?php wp_footer(); ?>
</body>
</html>
