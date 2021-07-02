<?php
/*
 * Template Name: Contact Us
 *
 * @package Cryout Plus
 */

// retrieve specific page metas
for ($i=0;$i<=7;$i++) {
	$cryout_page_meta[$i] = get_post_meta( get_the_ID(), '_cryout_meta_templatefield_'.$i, true );
}
if (!empty($cryout_page_meta[5])) {
	// filter map url only
	if (preg_match('/src="([^"]*)"/i',$cryout_page_meta[5],$parts)) $cryout_page_meta[5] = $parts[1];
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

						<div class="entry-content template-contact" <?php cryout_schema_microdata( 'text' ); ?>>
							<div class="template-map-col">
								<?php if (!empty($cryout_page_meta[5])) { ?> <iframe width="100%" height="400" src="<?php echo $cryout_page_meta[5] ?>"></iframe> <?php } ?>
							</div>
							<div class="template-content">
								<?php the_content(); ?>
							</div>
							<div class="template-middle">
								<div class="template-left-col col-sm-6">
									<?php echo do_shortcode( $cryout_page_meta[6] ) ?>
								</div>
								<div class="template-right-col col-sm-6">
									<div class="template-right-col-inside">
										<?php if (!empty($cryout_page_meta[0])) {
											list($template_logo,) = wp_get_attachment_image_src( absint( $cryout_page_meta[0] ), 'full' ); ?>
											<img src="<?php echo $template_logo ?>">
										<?php } ?>
										<address>
											<?php if (!empty($cryout_page_meta[1])) { echo '<span class="address"><i class="icon-template-location" title="Address"> ' . __('Address:', 'cryout') . '</i><span class="address-block">' . $cryout_page_meta[1] . '</span></span>'; } ?>
											<?php if (!empty($cryout_page_meta[2])) { echo '<span class="phone"><i class="icon-template-phone" title="Phone"> ' . __('Phone:', 'cryout') . '</i>' . $cryout_page_meta[2] . '</span>'; } ?>
											<?php if (!empty($cryout_page_meta[3])) { echo '<span class="mobile"><i class="icon-template-mobile" title="Mobile"> ' . __('Mobile:', 'cryout') . '</i>' . $cryout_page_meta[3] . '</span>'; } ?>
											<?php if (!empty($cryout_page_meta[4])) { echo '<span class="email"><i class="icon-template-mail" title="Email"> ' . __('E-mail:', 'cryout') . '</i>' . $cryout_page_meta[4] . '</span>'; } ?>
											<?php if (!empty($cryout_page_meta[7])) { echo '<span class="opening-hours"><i class="icon-template-opening-hours" title="Opening Hours"> ' . __('Opening Hours:', 'cryout') . '</i>' . $cryout_page_meta[7] . '</span>'; } ?>
										</address>
									</div>
								</div>
							</div>
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
