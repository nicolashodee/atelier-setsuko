<?php
/**
 * Plus Actions
 *
 * @package Cryout Plus
 */
 
class Plus_Actions {
	
	function __construct() {
		// plus widget areas
		add_action( 'cryout_empty_page_hook',  array( $this, 'empty_page_widget_area' ) );
		add_action( 'cryout_absolute_top_hook',  array( $this, 'top_widget_area' ) );
		add_action( 'cryout_absolute_bottom_hook',  array( $this, 'bottom_widget_area' ) );
		
		// unfiltered js fields
		add_action( 'wp_head', array( $this, 'header_js' ), 50 );
		add_action( 'cryout_body_hook', array( $this, 'body_js' ), 5 );
		add_action( 'wp_footer', array( $this, 'footer_js' ), 50 );
	} // __construct()
	
	// top widget area
	function top_widget_area() {
		if ( is_active_sidebar( 'absolute-top' ) ) { ?>
			<aside class="top-widget-area" <?php cryout_schema_microdata( 'sidebar' );?>>
				<?php dynamic_sidebar( 'absolute-top' ); ?>
			</aside><!--top-widget-area--><?php
		}
	} //top_widget_area()

	// bottom widget area
	function bottom_widget_area() {
		if ( is_active_sidebar( 'absolute-bottom' ) ) { ?>
			<aside class="bottom-widget-area" <?php cryout_schema_microdata( 'sidebar' );?>>
				<?php dynamic_sidebar( 'absolute-bottom' ); ?>
			</aside><!--bottom-widget-area--><?php
		}
	} //bottom_widget_area()
	
	
	// 404 / search results empty page widget area
	function empty_page_widget_area() {
		if ( is_active_sidebar( 'empty-page-area' ) ) { ?>
			<aside class="content-widget content-empty-page" <?php cryout_schema_microdata( 'sidebar' );?>>
				<?php dynamic_sidebar( 'empty-page-area' ); ?>
			</aside><!--content-empty-page--><?php
		}
	} //empty_page_widget_area()

	// unfiltered header javascript field
	function header_js(){ 
		$js = cryout_get_option( _CRYOUT_THEME_PREFIX . '_headerjs' );
		if (strlen(trim($js))>0) {
			if ( strpos($js, '<script')>-1 ) echo preg_replace('/<script/i', '<script data-id="custom-header-js-"', $js);
				else echo PHP_EOL . '<script type="text/javascript" data-id="custom-header-js">' . PHP_EOL . $js . PHP_EOL . '</script>' . PHP_EOL;
		}
	} // header_js()
	
	// unfiltered body javascript field
	function body_js(){ 
		$js = cryout_get_option( _CRYOUT_THEME_PREFIX . '_bodyjs' );
		if (strlen(trim($js))>0) {
			if ( strpos($js, '<script')>-1 ) echo preg_replace('/<script/i', '<script data-id="custom-body-js-"', $js);
				else echo PHP_EOL . '<script type="text/javascript" data-id="custom-body-js">' . PHP_EOL . $js . PHP_EOL . '</script>' . PHP_EOL;
		}
	} // body_js()

	// unfiltered footer javascript field
	function footer_js(){ 
		$js = cryout_get_option( _CRYOUT_THEME_PREFIX . '_footerjs' );
		if (strlen(trim($js))>0) {
			if ( strpos($js, '<script')>-1 ) echo preg_replace('/<script/i', '<script data-id="custom-footer-js-"', $js);
				else echo PHP_EOL . '<script type="text/javascript" data-id="custom-footer-js">' . PHP_EOL . $js . PHP_EOL . '</script>' . PHP_EOL;
		}
	} // footer_js()
	
} // Plus_Actions class

new Plus_Actions;

// FIN