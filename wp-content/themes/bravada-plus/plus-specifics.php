<?php
/**
 * Bravada Plus specifics:
 * 1. Defaults, options and structure arrays
 * 2. Footer links handling
 * 3. Custom styles
 *
 * @package Cryout Plus
 */

/**
 * individual theme options init functions
 */

// Get the theme options and make sure defaults are used if no values are set
function bravada_plus_get_theme_options() {
	$options = wp_parse_args( get_option( _CRYOUT_THEME_NAME . '_settings', array() ),
		bravada_get_option_defaults() );
	$options = apply_filters( 'bravada_theme_options_array', $options );
	return $options;
} // bravada_get_theme_options()

function bravada_plus_get_theme_structure() {
	global $bravada_big;
	return apply_filters( 'bravada_theme_structure_array', $bravada_big );
} // bravada_get_theme_structure()

/**
 * footer links handling
 */

// footer link hooks
function bravada_footer_actions() {
	remove_action( 'cryout_master_footerbottom_hook', _CRYOUT_THEME_SLUG . '_master_footer' );
	remove_action( 'cryout_master_footer_hook', _CRYOUT_THEME_SLUG . '_copyright' );
	add_action( 'cryout_master_footerbottom_hook', _CRYOUT_THEME_SLUG . '_footer_text_siteinfo' );
	add_action( 'cryout_master_footer_hook', _CRYOUT_THEME_SLUG . '_footer_text_sitecopy' );
} // bravada_footer_actions()

// footer credit link output
function bravada_footer_text_siteinfo() {
	$options = cryout_get_option();
	do_action( 'cryout_footer_hook' ); ?>
	<div style="display:block;margin: 0.5em auto;">
	<?php if ($options[_CRYOUT_THEME_PREFIX . '_bywordpress']): _e('Powered by','cryout')?> <a href="<?php echo esc_url('http://wordpress.org/' ); ?>" title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'cryout'); ?>"> <?php printf(' %s', 'WordPress' ); ?></a>.<?php endif; ?>
	<?php if ($options[_CRYOUT_THEME_PREFIX . '_byself']): ?><?php echo $options[_CRYOUT_THEME_PREFIX . '_byself_text']; ?> <?php endif; ?>
	</div><?php
	do_action( 'cryout_after_footer_hook' );
} // bravada_footer_text_siteinfo()

// footer link output
function bravada_footer_text_sitecopy() {
	$options = cryout_get_option(); ?>
	<div id="site-copyright"><?php echo $options[_CRYOUT_THEME_PREFIX . '_copyright'] ?></div> <?php
} // bravada_footer_text_sitecopy()

/**
 * Theme defaults, structure and options
 */

function cryout_plus_theme_specifics() {

 	$sample_pages = cryoutplus_get_default_pages(10);

	$defaults = array(

		_CRYOUT_THEME_PREFIX . '_db'					=> 0.9,

		/* layout */
		_CRYOUT_THEME_PREFIX . '_archivelayout'			=> '2cSr', // same values as general layout
		_CRYOUT_THEME_PREFIX . '_searchlayout'			=> '2cSr', // same values as general layout
		_CRYOUT_THEME_PREFIX . '_woolayout'				=> '2cSl',
		_CRYOUT_THEME_PREFIX . '_woosinglelayout'		=> '1c',
		_CRYOUT_THEME_PREFIX . '_portlayout'			=> '2cSr',
		_CRYOUT_THEME_PREFIX . '_portsinglelayout'		=> '2cSr',
		_CRYOUT_THEME_PREFIX . '_responsivelimit'		=> 800,

		/* landing page */
		// general
		_CRYOUT_THEME_PREFIX . '_lppostslayout'			=> 3,
		_CRYOUT_THEME_PREFIX . '_lppostscount'			=> 9,
		_CRYOUT_THEME_PREFIX . '_lppostscat'			=> '',

		// blocks
		_CRYOUT_THEME_PREFIX . '_lpblockfive1'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblockfiveicon1'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblocksix1'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblocksixicon1'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockseven1'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblocksevenicon1'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockeight1'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblockeighticon1'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblocknine1'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblocknineicon1'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockmainttitle2'		=> '',
		_CRYOUT_THEME_PREFIX . '_lpblockmaindesc2'		=> '',
		_CRYOUT_THEME_PREFIX . '_lpblockone2'			=> $sample_pages[1],
		_CRYOUT_THEME_PREFIX . '_lpblockoneicon2'		=> 'screen-desktop',
		_CRYOUT_THEME_PREFIX . '_lpblocktwo2'			=> $sample_pages[2],
		_CRYOUT_THEME_PREFIX . '_lpblocktwoicon2'		=> 'layers',
		_CRYOUT_THEME_PREFIX . '_lpblockthree2'			=> $sample_pages[3],
		_CRYOUT_THEME_PREFIX . '_lpblockthreeicon2'		=> 'folder',
		_CRYOUT_THEME_PREFIX . '_lpblockfour2'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblockfouricon2'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockfive2'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblockfiveicon2'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblocksix2'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblocksixicon2'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockseven2'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblocksevenicon2'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockeight2'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblockeighticon2'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblocknine2'			=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblocknineicon2'		=> 'megaphone',
		_CRYOUT_THEME_PREFIX . '_lpblockscontent2'		=> 1, // 0=disabled, 1=excerpt, 2=full
		_CRYOUT_THEME_PREFIX . '_lpblocksclick2'		=> 0,
		_CRYOUT_THEME_PREFIX . '_lpblockperrow1'		=> 3,
		_CRYOUT_THEME_PREFIX . '_lpblockperrow2'		=> 3,
		// boxes
		_CRYOUT_THEME_PREFIX . '_lpboxmainttitle3'		=> '',
		_CRYOUT_THEME_PREFIX . '_lpboxmaindesc3'		=> '',
		_CRYOUT_THEME_PREFIX . '_lpboxcat3'				=> "0", // (string)"0"=disabled
		_CRYOUT_THEME_PREFIX . '_lpboxcount3'			=> 8,
		_CRYOUT_THEME_PREFIX . '_lpboxrow3'				=> 4, // 1-4
		_CRYOUT_THEME_PREFIX . '_lpboxheight3'			=> 300, // pixels
		_CRYOUT_THEME_PREFIX . '_lpboxlayout3'			=> 2, // 1=full width, 2=boxed
		_CRYOUT_THEME_PREFIX . '_lpboxmargins3'			=> 1, // 1=no margins, 2=margins
		_CRYOUT_THEME_PREFIX . '_lpboxanimation3'		=> 3, // 1=animated, 2=static, 3=animated2, 4=static2
		_CRYOUT_THEME_PREFIX . '_lpboxreadmore3'		=> 'Read More',
		_CRYOUT_THEME_PREFIX . '_lpboxlength3'			=> 25,
		// _CRYOUT_THEME_PREFIX . '_lpboxanimation1'		=> 2,
		// _CRYOUT_THEME_PREFIX . '_lpboxanimation2'		=> 1,
		// texts
		_CRYOUT_THEME_PREFIX . '_lptextzero'			=> -1,
		_CRYOUT_THEME_PREFIX . '_lptextfive'			=> $sample_pages[5],
		_CRYOUT_THEME_PREFIX . '_lptextsix'				=> -1,
		// portfolio
		_CRYOUT_THEME_PREFIX . '_lpport'  				=> 1,
		_CRYOUT_THEME_PREFIX . '_lpporttitle'  			=> '',
		_CRYOUT_THEME_PREFIX . '_lpportdesc'  			=> '',
		_CRYOUT_THEME_PREFIX . '_lpportcols'			=> 4,
		_CRYOUT_THEME_PREFIX . '_lpportcount'			=> 8,
		_CRYOUT_THEME_PREFIX . '_lpportorderby'			=> 'date',
		_CRYOUT_THEME_PREFIX . '_lpportsort'			=> 'desc',
		_CRYOUT_THEME_PREFIX . '_lpportreadmore'		=> 'All portfolios',
		_CRYOUT_THEME_PREFIX . '_lpportreadlink'		=> '',
		// testimonials
		_CRYOUT_THEME_PREFIX . '_lptt'  				=> 1,
		_CRYOUT_THEME_PREFIX . '_lptttitle'  			=> '',
		_CRYOUT_THEME_PREFIX . '_lpttdesc'  			=> '',
		_CRYOUT_THEME_PREFIX . '_lpttcols'				=> 3,
		_CRYOUT_THEME_PREFIX . '_lpttcount'				=> 3,
		_CRYOUT_THEME_PREFIX . '_lpttimage'				=> 1,
		_CRYOUT_THEME_PREFIX . '_lpttorderby'			=> 'date',
		_CRYOUT_THEME_PREFIX . '_lpttsort'				=> 'desc',
		// order
		_CRYOUT_THEME_PREFIX . '_lporder'				=> 	'slider,text-zero,blocks-1,text-one,boxes-1,text-two,boxes-2,text-three,boxes-3,portfolio,text-four,testimonials,blocks-2,text-five,index,text-six',

		/* colors */
		_CRYOUT_THEME_PREFIX . '_lpblocksbg'			=> '', // unset values
		_CRYOUT_THEME_PREFIX . '_lpboxesbg'				=> '', // for options
		_CRYOUT_THEME_PREFIX . '_lptextsbg'				=> '', // unavailable in plus
		_CRYOUT_THEME_PREFIX . '_lpcolorblocks1'		=> '#F9F7F5',
		_CRYOUT_THEME_PREFIX . '_lpcolorblocks2'		=> '#191716',
		_CRYOUT_THEME_PREFIX . '_lpcolorboxes1'			=> '#F2EFEC',
		_CRYOUT_THEME_PREFIX . '_lpcolorboxes2'			=> '#F8F8F8',
		_CRYOUT_THEME_PREFIX . '_lpcolorboxes3'			=> '#F6F3F6',
		_CRYOUT_THEME_PREFIX . '_lpcolortextzero'		=> '#F9F7F5',
		_CRYOUT_THEME_PREFIX . '_lpcolortextone'		=> '#F9F7F5',
		_CRYOUT_THEME_PREFIX . '_lpcolortexttwo'		=> '#F6F3F6',
		_CRYOUT_THEME_PREFIX . '_lpcolortextthree'		=> '#EEEBE9',
		_CRYOUT_THEME_PREFIX . '_lpcolortextfour'		=> '#EEEBE9',
		_CRYOUT_THEME_PREFIX . '_lpcolortextfive'		=> '#EEEEEE',
		_CRYOUT_THEME_PREFIX . '_lpcolortextsix'		=> '#EEEEEE',
		_CRYOUT_THEME_PREFIX . '_lpcolorportfolio'		=> '#EEEBE9',
		_CRYOUT_THEME_PREFIX . '_lpcolortestimonial'	=> '#EEEBE9',

		/* schemes */
		_CRYOUT_THEME_PREFIX . '_scheme'				=> 'default',


		/* post info */
		_CRYOUT_THEME_PREFIX . '_related_posts'			=> 1,
		_CRYOUT_THEME_PREFIX . '_related_title' 		=> 'Related Posts',

		/* featured images */
		_CRYOUT_THEME_PREFIX . '_fplaceholder'			=> 1,

		/* misc */
		_CRYOUT_THEME_PREFIX . '_pageexcerpts'			=> 1,
		_CRYOUT_THEME_PREFIX . '_headerjs'				=> '',
		_CRYOUT_THEME_PREFIX . '_bodyjs'				=> '',
		_CRYOUT_THEME_PREFIX . '_footerjs'				=> '',
		_CRYOUT_THEME_PREFIX . '_shortcodes'			=> 1,
		_CRYOUT_THEME_PREFIX . '_shortcodesprefix'		=> '',

		/* footer link */
		/* _CRYOUT_THEME_PREFIX . '_bycss' 				=> 'display: block;float: right;clear: right;', */
		_CRYOUT_THEME_PREFIX . '_bywordpress' 			=> 0, // 0,1
		_CRYOUT_THEME_PREFIX . '_byself' 				=> 0, // 0,1
		_CRYOUT_THEME_PREFIX . '_byself_text' 			=> 'Type in your own "Powered by" text. <em>HTML is supported</em>.',
	); // $defaults

	$structure = array(

		'info_sections' => array(
			'cryoutspecial-about-theme' => array(
				'title' => __( 'About', 'cryout' ) . ' ' . cryout_sanitize_tnl(_CRYOUT_THEME_NAME),
				'desc' => '<img src="' . get_template_directory_uri() . '/admin/images/logo-about-header.png" ><span class="plus">PLUS</span><br>' . __( 'Got a question? Need help?', 'cryout' ),
				'button' => TRUE,
				'button_label' => __( 'Need help?', 'cryout' ),
			),
		), // info_sections

		'info_settings' => array(
			'plus_link' => NULL,
			'support_link_faqs' => array(
				'label' => __('Theme Support', 'cryout'),
				'default' => sprintf( '<a href="https://www.cryoutcreations.eu/wordpress-themes/' . _CRYOUT_THEME_SLUG . '" target="_blank">%s</a>', __( 'Read the Docs', 'cryout' ) ),
				'desc' =>  '',
				'section' => 'cryoutspecial-about-theme',
			),
			'support_link_forum' => NULL,
			'premium_support_link' => array(
				'label' => '',
				'default' => sprintf( '<a href="https://www.cryoutcreations.eu/priority-support" target="_blank">%s</a>', __( 'Priority Support', 'cryout' ) ),
				'desc' => '',
				'section' => 'cryoutspecial-about-theme',
			),
			'rating_url' => NULL,
			'management' => array(
				'label' => __('Theme Options and Addons', 'cryout') ,
				'default' => sprintf( '<a href="themes.php?page=' . _CRYOUT_THEME_SLUG . '-plus-theme">%s</a>', __('Manage Theme Options and Addons', 'cryout') ),
				'desc' => __('Theme options can be exported, imported, or reset from the theme\'s about page.', 'cryout'),
				'section' => 'cryoutspecial-about-theme',
			),
		), // info_settings

		'sections' => array(
			array( 'id'=>_CRYOUT_THEME_PREFIX . '_footerlink', 'title'=>__('Footer Link','cryout'), 'callback'=>'', 'sid'=>'', 'priority'=>90 ),
			array( 'id'=>_CRYOUT_THEME_PREFIX . '_sectionlayout', 'title'=>__('Sections Layout','cryout'), 'callback'=>'', 'sid'=>_CRYOUT_THEME_PREFIX . '_layout_section', 'priority'=>52 ),
            array( 'id'=>_CRYOUT_THEME_PREFIX . '_lpblocks2', 'title'=>__('Featured Icon Blocks 2','cryout'), 'callback'=>'', 'sid'=>_CRYOUT_THEME_PREFIX . '_landingpage', 'priority' => 31 ),
            array( 'id'=>_CRYOUT_THEME_PREFIX . '_lpboxes3', 'title'=>__('Featured Boxes 3','cryout'), 'callback'=>'', 'sid'=>_CRYOUT_THEME_PREFIX . '_landingpage', 'priority' => 51),
            array( 'id'=>_CRYOUT_THEME_PREFIX . '_lpportfolio', 'title'=>__('Jetpack Portfolio','cryout'), 'callback'=>'', 'sid'=>_CRYOUT_THEME_PREFIX . '_landingpage', 'priority' => 95 ),
            array( 'id'=>_CRYOUT_THEME_PREFIX . '_lptestimonials', 'title'=>__('Jetpack Testimonials','cryout'), 'callback'=>'', 'sid'=>_CRYOUT_THEME_PREFIX . '_landingpage', 'priority' => 96 ),
            array( 'id'=>_CRYOUT_THEME_PREFIX . '_lporder', 'title'=>__('Display Order','cryout'), 'callback'=>'', 'sid'=>_CRYOUT_THEME_PREFIX . '_landingpage', 'priority' => 99 ),
			array( 'id'=>_CRYOUT_THEME_PREFIX . '_colors_lp', 'title'=>__('Landing Page','cryout'), 'callback'=>'', 'sid'=> _CRYOUT_THEME_PREFIX . '_colors_section'),
			array( 'id'=>_CRYOUT_THEME_PREFIX . '_related', 'title'=>__('Related Posts','cryout'), 'callback'=>'', 'sid'=> _CRYOUT_THEME_PREFIX . '_post_section', 'priority' => 60 ),
			array( 'id'=>_CRYOUT_THEME_PREFIX . '_schemes', 'title'=>__('Personalities','cryout'), 'callback'=>'', 'sid'=> _CRYOUT_THEME_PREFIX . '_colors_section', 'priority' => 5 ),
		), // sections

        'clones' => array (
            _CRYOUT_THEME_PREFIX . '_lpblocks' => 2,
        	_CRYOUT_THEME_PREFIX . '_lpboxes' => 3,
        ), // clones

		'options' => array(
			//////////////////////////////////////////////////// Landing Page ////////////////////////////////////////////////////
			/* general lp */
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lppostslayout',
				'type' => 'radioimage',
				'label' => __('Posts Layout','cryout'),
				'choices' => array(
					'1' => array(
						'label' => __("One column",'cryout'),
						'url'   => '%s/admin/images/magazine-1col.png'
					),
					'2' => array(
						'label' => __("Two columns",'cryout'),
						'url'   => '%s/admin/images/magazine-2col.png'
					),
					'3' => array(
						'label' => __("Three columns",'cryout'),
						'url'   => '%s/admin/images/magazine-3col.png'
					),
				),
				'desc' => __("This layout applies to posts list on Landing Page.",'cryout'),
				'active_callback' => _CRYOUT_THEME_SLUG . '_conditionals',
			'section' => _CRYOUT_THEME_PREFIX . '_lpfcontent' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lppostscount',
				'type' => 'numberslider',
				'min' => 0,
				'max' => 40,
				'step' => 1,
				'label' => __('Posts Number','cryout'),
				'desc' => '',
				'active_callback' => _CRYOUT_THEME_SLUG . '_conditionals',
			'section' => _CRYOUT_THEME_PREFIX . '_lpfcontent' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lppostscat',
				'type' => 'select',
				'label' => __('Posts Category','cryout'),
				'values' => cryout_categories_for_customizer(1, __('All Categories', 'cryout'), __('- Disabled - ', 'cryout') ),
				'labels' => cryout_categories_for_customizer(2, __('All Categories', 'cryout'), __('- Disabled - ', 'cryout') ),
				'desc' => '',
				'active_callback' => _CRYOUT_THEME_SLUG . '_conditionals',
			'section' => _CRYOUT_THEME_PREFIX . '_lpfcontent' ),

			/* blocks */
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockone#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 25,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocktwo#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 27,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockthree#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 29,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockfour#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 31,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockfiveicon#',
				'type' => 'iconselect',
				'label' => sprintf( __('Block %d','cryout'), 5),
				'desc' => '',
				'priority' => 50,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockfive#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 51,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocksixicon#',
				'type' => 'iconselect',
				'label' => sprintf( __('Block %d','cryout'), 6),
				'desc' => '',
				'priority' => 52,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocksix#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 53,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocksevenicon#',
				'type' => 'iconselect',
				'label' => sprintf( __('Block %d','cryout'), 7),
				'desc' => '',
				'priority' => 54,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockseven#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 55,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockeighticon#',
				'type' => 'iconselect',
				'label' => sprintf( __('Block %d','cryout'), 8),
				'desc' => '',
				'priority' => 56,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockeight#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 57,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocknineicon#',
				'type' => 'iconselect',
				'label' => sprintf( __('Block %d','cryout'), 9),
				'desc' => '',
				'priority' => 58,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocknine#',
				'type' => 'optselect',
				'label' => '',
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Blocks','cryout') => cryout_featured_for_customizer(0, 'block', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __("Define the content and icon.",'cryout'),
				'priority' => 59,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblockperrow#',
				'type' => 'select',
				'label' => __('Items per row','cryout'),
				'values' => array(1,2,3,4),
				'desc' => '',
				'priority' => 20,
			'section' => _CRYOUT_THEME_PREFIX . '_lpblocks#' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpboxanimation#',
				'type' => NULL,
			'section' => _CRYOUT_THEME_PREFIX . '_lpboxes#' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpboxanimation#',
				'type' => 'select',
				'label' => __('Box Appearance','cryout'),
				'values' => array( 1, 2, 3, 4 ),
				'labels' => array( __("Animated",'cryout'), __("Static",'cryout'), __("Animated 2",'cryout'), __("Static 2",'cryout') ),
				'desc' => __("Choose how the box content is shown. 'Animated' makes the content appear on hover while 'static' displays content beneath the image.",'cryout'),
				'priority' => 90,
			'section' => _CRYOUT_THEME_PREFIX . '_lpboxes#' ),

			/* boxes */
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpboxcat#',
				'type' => 'optselect',
				'label' => __('Boxes Content','cryout'),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Boxes','cryout') => cryout_featured_cats_for_customizer(0, '', __('All boxes', 'cryout'), '', true, false ),
					__('Post Categories','cryout') => cryout_categories_for_customizer(0, __('All Categories', 'cryout'), '', true, false )
				),
				'desc' => __("Select the category from which to create landing page boxes.",'cryout'),
				'priority' => 30,
			'section' => _CRYOUT_THEME_PREFIX . '_lpboxes#' ),

			/* texts */
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lptextzero',
                'type' => 'optselect',
                'label' => sprintf( __('Text Area %d','cryout'), 0),
                'disabled' => __('- Disabled - ', 'cryout'),
                'choices' => array(
                    __('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
                    __('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
                ),
                'priority' => 39,
            'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptextone',
				'type' => 'optselect',
				'label' => sprintf( __('Text Area %d','cryout'), 1),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => '',
				'priority' => 40,
			'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptexttwo',
				'type' => 'optselect',
				'label' => sprintf( __('Text Area %d','cryout'), 2),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'priority' => 42,
			'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptextthree',
				'type' => 'optselect',
				'label' => sprintf( __('Text Area %d','cryout'), 3),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'priority' => 43,
			'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptextfour',
				'type' => 'optselect',
				'label' => sprintf( __('Text Area %d','cryout'), 4),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'priority' => 44,
			'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptextfive',
				'type' => 'optselect',
				'label' => sprintf( __('Text Area %d','cryout'), 5),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'priority' => 45,
			'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptextsix',
				'type' => 'optselect',
				'label' => sprintf( __('Text Area %d','cryout'), 6),
				'disabled' => __('- Disabled - ', 'cryout'),
				'choices' => array(
					__('Featured Texts','cryout') => cryout_featured_for_customizer(0, 'text', '', false),
					__('Pages','cryout') => cryout_pages_for_customizer(0, '', false)
				),
				'desc' => __('For position information see the theme documentation.', 'cryout'),
				'priority' => 46,
			'section' => _CRYOUT_THEME_PREFIX . '_lptexts' ),

            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpport',
                'type' => 'toggle',
                'label' => __('Portfolio','cryout'),
                'values' => array( 1, 0 ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpport_notice',
				'type' => 'notice',
				'label' => "",
				'input_attrs' => array( 'class' => 'info' ),
				'desc' => __('This uses Jetpacks\' Portfolio functionality. Make sure the feature is enabled in Jetpack\'s options.', 'cryout'),
			'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpporttitle',
                'type' => 'text',
                'label' => __('Section Title','cryout'),
                'desc' => "",
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpportdesc',
                'type' => 'textarea',
                'label' => __( 'Section Description', 'cryout' ),
                'desc' => "",
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpportspacer1',
				'type' => 'spacer',
			'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpportcount',
                'type' => 'numberslider',
				'min' => 0,
				'max' => 40,
				'step' => 1,
                'label' => __('Items Number','cryout'),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' =>  _CRYOUT_THEME_PREFIX . '_lpportcols',
                'type' => 'select',
                'label' => __("Columns","cryout"),
                'values' => array( 2, 3, 4, 5, 6),
                'values' => array( 2, 3, 4, 5, 6),
                'desc' => "",
            'section' =>  _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpportorderby',
                'type' => 'select',
                'label' => __('Order items by','cryout'),
                'values' => array( 'author', 'date', 'title', 'random' ),
                'labels' => array( __("Author",'cryout'), __("Date",'cryout'), __("Title",'cryout'), __("Random",'cryout') ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpportsort',
                'type' => 'select',
                'label' => __('Sort','cryout'),
                'values' => array( 'desc', 'asc' ),
                'labels' => array( __("DESC",'cryout'), __("ASC",'cryout') ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpportspacer2',
				'type' => 'spacer',
			'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpportreadlink',
                'type' => 'text',
                'label' => __('Portfolio Link URL','cryout'),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpportreadmore',
                'type' => 'text',
                'label' => __('Portfolio Link Label','cryout'),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lpportfolio' ),

            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lptt',
                'type' => 'toggle',
                'label' => __('Testimonials','cryout'),
                'values' => array( 1, 0 ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptt_notice',
				'type' => 'notice',
				'label' => "",
				'input_attrs' => array( 'class' => 'info' ),
				'desc' => __('This uses Jetpacks\' Testimonials functionality. Make sure the feature is enabled in Jetpack\'s options.', 'cryout'),
			'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lptttitle',
                'type' => 'text',
                'label' => __('Section Title','cryout'),
                'desc' => "",
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpttdesc',
                'type' => 'textarea',
                'label' => __( 'Section Description', 'cryout' ),
                'desc' => "",
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpttspacer1',
				'type' => 'spacer',
			'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpttcount',
                'type' => 'numberslider',
				'min' => 0,
				'max' => 40,
				'step' => 1,
                'label' => __('Items Number','cryout'),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' =>  _CRYOUT_THEME_PREFIX . '_lpttcols',
                'type' => 'select',
                'label' => __("Columns","cryout"),
                'values' => array( 1, 2, 3, 4),
                'values' => array( 1, 2, 3, 4),
                'desc' => "",
            'section' =>  _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpttorderby',
                'type' => 'select',
                'label' => __('Order items by','cryout'),
                'values' => array( 'author', 'date', 'title', 'random' ),
                'labels' => array( __("Author",'cryout'), __("Date",'cryout'), __("Title",'cryout'), __("Random",'cryout') ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpttsort',
                'type' => 'select',
                'label' => __('Sort','cryout'),
                'values' => array( 'desc', 'asc' ),
                'labels' => array( __("DESC",'cryout'), __("ASC",'cryout') ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_lpttimage',
                'type' => 'toggle',
                'label' => __('Images','cryout'),
                'values' => array( 1, 0 ),
                'desc' => '',
            'section' => _CRYOUT_THEME_PREFIX . '_lptestimonials' ),


            array(
			'id' => _CRYOUT_THEME_PREFIX . '_lporder',
				'type' => 'sortable',
				'label' => '',
				'choices' => array(
					'slider' 		=> __('Slider', 'cryout'),
                    'text-zero' 	=> sprintf( __('Text Area %d', 'cryout'), 0 ),
					'blocks-1' 		=> sprintf( __('Icon Blocks %d', 'cryout'), 1 ),
					'text-one'		=> sprintf( __('Text Area %d', 'cryout'), 1 ),
                    'boxes-1' 		=> sprintf( __('Featured Boxes %d', 'cryout'), 1 ),
					'text-two' 		=> sprintf( __('Text Area %d', 'cryout'), 2 ),
                    'blocks-2' 		=> sprintf( __('Icon Blocks %d', 'cryout'), 2 ),
					'text-three'	=> sprintf( __('Text Area %d', 'cryout'), 3 ),
                    'boxes-2' 		=> sprintf( __('Featured Boxes %d', 'cryout'), 2 ),
					'text-four' 	=> sprintf( __('Text Area %d', 'cryout'), 4 ),
                    'boxes-3' 		=> sprintf( __('Featured Boxes %d', 'cryout'), 3 ),
					'text-five' 	=> sprintf( __('Text Area %d', 'cryout'), 5 ),
					'portfolio' 	=> __('Jetpack Portfolio', 'cryout'),
					'testimonials' 	=> __('Jetpack Testimonials', 'cryout'),
                    'index' 		=> __('Posts / Static Page', 'cryout'),
					'text-six' 		=> sprintf( __('Text Area %d', 'cryout'), 6 ),
				),
				'input_attrs' => array(
					'statuses' => array(
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
					),
					'redirects' => array(
						'slider' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpslider]',
						'text-zero' 	=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptextzero]',
						'blocks-1' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpblockscontent1]',
						'text-one' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptextone]',
						'boxes-1' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpboxcat1]',
						'text-two' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptexttwo]',
						'blocks-2' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpblockscontent2]',
						'text-three'	=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptextthree]',
						'boxes-2' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpboxcat2]',
						'text-four' 	=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptextfour]',
						'boxes-3' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpboxcat3]',
						'text-five' 	=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptextfive]',
						'portfolio'		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpport]',
						'testimonials'	=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptt]',
						'index' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lpposts]',
						'text-six' 		=> _CRYOUT_THEME_SLUG . '-plus_settings[' . _CRYOUT_THEME_PREFIX . '_lptextsix]',
					),
				),
				'desc' => __('Drag and drop to re-arrange the order of the elements.','cryout'),
			'section' => _CRYOUT_THEME_PREFIX . '_lporder' ),

			//////////////////////////////////////////////////// Layout ////////////////////////////////////////////////////
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_archivelayout',
				'type' => 'radioimage',
				'label' => __('Categories/Archives Layout','cryout'),
				'choices' => array(
					'1c' => array(
						'label' => __("One column (no sidebars)","cryout"),
						'url'   => '%s/admin/images/1c.png'
					),
					'2cSr' => array(
						'label' => __("Two columns, sidebar on the right","cryout"),
						'url'   => '%s/admin/images/2cSr.png'
					),
					'2cSl' => array(
						'label' => __("Two columns, sidebar on the left","cryout"),
						'url'   => '%s/admin/images/2cSl.png'
					),
					'3cSr' => array(
						'label' => __("Three columns, sidebars on the right","cryout"),
						'url'   => '%s/admin/images/3cSr.png'
					),
					'3cSl' => array(
						'label' => __("Three columns, sidebars on the left","cryout"),
						'url'   => '%s/admin/images/3cSl.png'
					),
					'3cSs' => array(
						'label' => __("Three columns, one sidebar on each side","cryout"),
						'url'   => '%s/admin/images/3cSs.png'
					),
				),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_sectionlayout' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_searchlayout',
				'type' => 'radioimage',
				'label' => __('Search Results Layout','cryout'),
				'choices' => array(
					'1c' => array(
						'label' => __("One column (no sidebars)","cryout"),
						'url'   => '%s/admin/images/1c.png'
					),
					'2cSr' => array(
						'label' => __("Two columns, sidebar on the right","cryout"),
						'url'   => '%s/admin/images/2cSr.png'
					),
					'2cSl' => array(
						'label' => __("Two columns, sidebar on the left","cryout"),
						'url'   => '%s/admin/images/2cSl.png'
					),
					'3cSr' => array(
						'label' => __("Three columns, sidebars on the right","cryout"),
						'url'   => '%s/admin/images/3cSr.png'
					),
					'3cSl' => array(
						'label' => __("Three columns, sidebars on the left","cryout"),
						'url'   => '%s/admin/images/3cSl.png'
					),
					'3cSs' => array(
						'label' => __("Three columns, one sidebar on each side","cryout"),
						'url'   => '%s/admin/images/3cSs.png'
					),
				),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_sectionlayout' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_portlayout',
				'type' => 'radioimage',
				'label' => __('Jetpack Portfolio Archive','cryout'),
				'choices' => array(
					'1c' => array(
						'label' => __("One column (no sidebars)","cryout"),
						'url'   => '%s/admin/images/1c.png'
					),
					'2cSr' => array(
						'label' => __("Two columns, sidebar on the right","cryout"),
						'url'   => '%s/admin/images/2cSr.png'
					),
					'2cSl' => array(
						'label' => __("Two columns, sidebar on the left","cryout"),
						'url'   => '%s/admin/images/2cSl.png'
					),
					'3cSr' => array(
						'label' => __("Three columns, sidebars on the right","cryout"),
						'url'   => '%s/admin/images/3cSr.png'
					),
					'3cSl' => array(
						'label' => __("Three columns, sidebars on the left","cryout"),
						'url'   => '%s/admin/images/3cSl.png'
					),
					'3cSs' => array(
						'label' => __("Three columns, one sidebar on each side","cryout"),
						'url'   => '%s/admin/images/3cSs.png'
					),
				),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_sectionlayout' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_portsinglelayout',
				'type' => 'radioimage',
				'label' => __('Jetpack Single Portfolio','cryout'),
				'choices' => array(
					'1c' => array(
						'label' => __("One column (no sidebars)","cryout"),
						'url'   => '%s/admin/images/1c.png'
					),
					'2cSr' => array(
						'label' => __("Two columns, sidebar on the right","cryout"),
						'url'   => '%s/admin/images/2cSr.png'
					),
					'2cSl' => array(
						'label' => __("Two columns, sidebar on the left","cryout"),
						'url'   => '%s/admin/images/2cSl.png'
					),
					'3cSr' => array(
						'label' => __("Three columns, sidebars on the right","cryout"),
						'url'   => '%s/admin/images/3cSr.png'
					),
					'3cSl' => array(
						'label' => __("Three columns, sidebars on the left","cryout"),
						'url'   => '%s/admin/images/3cSl.png'
					),
					'3cSs' => array(
						'label' => __("Three columns, one sidebar on each side","cryout"),
						'url'   => '%s/admin/images/3cSs.png'
					),
				),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_sectionlayout' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_woolayout',
				'type' => 'radioimage',
				'label' => __('WooCommerce Layout','cryout'),
				'choices' => array(
					'1c' => array(
						'label' => __("One column (no sidebars)","cryout"),
						'url'   => '%s/admin/images/1c.png'
					),
					'2cSr' => array(
						'label' => __("Two columns, sidebar on the right","cryout"),
						'url'   => '%s/admin/images/2cSr.png'
					),
					'2cSl' => array(
						'label' => __("Two columns, sidebar on the left","cryout"),
						'url'   => '%s/admin/images/2cSl.png'
					),
					'3cSr' => array(
						'label' => __("Three columns, sidebars on the right","cryout"),
						'url'   => '%s/admin/images/3cSr.png'
					),
					'3cSl' => array(
						'label' => __("Three columns, sidebars on the left","cryout"),
						'url'   => '%s/admin/images/3cSl.png'
					),
					'3cSs' => array(
						'label' => __("Three columns, one sidebar on each side","cryout"),
						'url'   => '%s/admin/images/3cSs.png'
					),
				),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_sectionlayout' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_woosinglelayout',
				'type' => 'radioimage',
				'label' => __('WooCommerce Single Product','cryout'),
				'choices' => array(
					'1c' => array(
						'label' => __("One column (no sidebars)","cryout"),
						'url'   => '%s/admin/images/1c.png'
					),
					'2cSr' => array(
						'label' => __("Two columns, sidebar on the right","cryout"),
						'url'   => '%s/admin/images/2cSr.png'
					),
					'2cSl' => array(
						'label' => __("Two columns, sidebar on the left","cryout"),
						'url'   => '%s/admin/images/2cSl.png'
					),
					'3cSr' => array(
						'label' => __("Three columns, sidebars on the right","cryout"),
						'url'   => '%s/admin/images/3cSr.png'
					),
					'3cSl' => array(
						'label' => __("Three columns, sidebars on the left","cryout"),
						'url'   => '%s/admin/images/3cSl.png'
					),
					'3cSs' => array(
						'label' => __("Three columns, one sidebar on each side","cryout"),
						'url'   => '%s/admin/images/3cSs.png'
					),
				),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_sectionlayout' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_layoutspacer1',
				'type' => 'spacer',
			'section' => _CRYOUT_THEME_PREFIX . '_generallayout' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_responsivelimit',
				'type' => 'numberslider',
				'label' => __('Responsiveness Trigger Limit','cryout'),
				'min' => 480,
				'max' => 1920,
				'step' => 20,
				'um' => 'px',
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_generallayout' ),

			//////////////////////////////////////////////////// Colors ////////////////////////////////////////////////////
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpblocksbg',
				'type' => NULL,
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpboxesbg',
				'type' => NULL,
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lptextsbg',
				'type' => NULL,
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolorblocks1',
				'type' => 'color',
				'label' => sprintf( __('Icon Blocks %d','cryout'), 1 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolorblocks2',
				'type' => 'color',
				'label' => sprintf( __('Icon Blocks %d','cryout'), 2 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolorboxes1',
				'type' => 'color',
				'label' =>sprintf(  __('Boxes %d','cryout'), 1 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolorboxes2',
				'type' => 'color',
				'label' => sprintf( __('Boxes %d','cryout'), 2 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolorboxes3',
				'type' => 'color',
				'label' => sprintf( __('Boxes %d','cryout'), 3 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortextzero',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 0 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortextone',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 1 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortexttwo',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 2 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortextthree',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 3 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortextfour',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 4 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortextfive',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 5 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortextsix',
				'type' => 'color',
				'label' => sprintf( __('Text Area %d','cryout'), 6 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolorportfolio',
				'type' => 'color',
				'label' => __('Portfolio','cryout'),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),

			array( // schemes
			'id' =>  _CRYOUT_THEME_PREFIX . '_scheme',
				'type' => 'select',
				'label' => __( 'Color and Typography Presets', 'cryout' ),
				'values' => array( 'default' ),
				'labels' => array( __("Bravada Default", "cryout") ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_schemes' ),

			array( // schemes
			'id' =>  _CRYOUT_THEME_PREFIX . '_scheme_warn',
				'type' => 'notice',
				'label' => "",
				'input_attrs' => array( 'class' => 'warning' ),
				'desc' => __('Publish the changes and reload the customizer screen for this option to take effect.', 'cryout'),
			'section' => _CRYOUT_THEME_PREFIX . '_schemes' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_lpcolortestimonial',
				'type' => 'color',
				'label' => __('Testimonial','cryout'),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_colors_lp' ),

			/////////////////////////////////////////////////// Post Info ///////////////////////////////////////////////////

			array( // related
			'id' =>  _CRYOUT_THEME_PREFIX . '_related_posts',
				'type' => 'select',
				'label' => __( 'Display Related Posts on Single Post', 'cryout' ),
				'values' => array( 0, 1, 2),
				'labels' => array( __("Disabled","cryout"), __("Same Categories","cryout"), __("Same Tags","cryout") ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_related' ),
			array(
			'id' =>  _CRYOUT_THEME_PREFIX . '_related_title',
				'type' => 'text',
				'label' => __( 'Section Title', 'cryout' ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_related' ),

			/////////////////////////////////////////////////// Featured Image ///////////////////////////////////////////////////

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_fplaceholder',
				'type' => 'toggle',
				'label' => __('Placeholders in Cryout widgets and Related Posts','cryout'),
				'values' => array( 1, 0 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_featured' ),

            array(
            'id' => _CRYOUT_THEME_PREFIX . '_articleanimation',
                'type' => NULL,
            'section' => _CRYOUT_THEME_PREFIX . '_contentgraphics' ),
            array(
            'id' => _CRYOUT_THEME_PREFIX . '_articleanimation',
                'type' => 'select',
                'label' => __('Article Animation on Scroll', 'cryout'),
                'values' => array( 'none', 'fade', 'slide', 'grow', 'slideLeft', 'slideRight', 'zoomIn', 'zoomOut', 'blur', 'flipLeft', 'flipRight', 'flipUp', 'flipDown'),
                'labels' => array( __("None","cryout"), __("Fade","cryout"), __("Slide","cryout"), __("Grow","cryout"),
								__("Slide Left","cryout"), __("Slide Right","cryout"),
								 __("ZoomIn","cryout"), __("ZoomOut","cryout"), __("Blur","cryout"),
							 __("Flip Left","cryout"), __("Flip Right","cryout"), __("Flip Up","cryout"), __("Flip Down","cryout"), ),
                'desc' => __('Choose how to animate the articles when they become visible while scrolling the page.', "cryout"),
                'priority' => 90,
            'section' => _CRYOUT_THEME_PREFIX . '_contentgraphics' ),

			/////////////////////////////////////////////////// Miscellaneous ///////////////////////////////////////////////////

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_pageexcerpts',
				'type' => 'toggle',
				'label' => __('Page Excerpts','cryout'),
				'values' => array( 1, 0 ),
				'desc' => '',
				'priority' => 5,
			'section' => _CRYOUT_THEME_PREFIX . '_misc' ),

			array(
			'id' => _CRYOUT_THEME_PREFIX . '_headerjs',
				'type' => 'textarea',
				'label' => __('Header JS (unfiltered)','cryout'),
				'desc' => '',
				'priority' => 8,
			'section' => _CRYOUT_THEME_PREFIX . '_misc' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_bodyjs',
				'type' => 'textarea',
				'label' => __('Body JS (unfiltered)','cryout'),
				'desc' => '',
				'priority' => 8,
			'section' => _CRYOUT_THEME_PREFIX . '_misc' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_footerjs',
				'type' => 'textarea',
				'label' => __('Footer JS (unfiltered)','cryout'),
				'desc' => '',
				'priority' => 8,
			'section' => _CRYOUT_THEME_PREFIX . '_misc' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_shortcodes',
				'type' => 'toggle',
				'label' => __('Plus Shortcodes','cryout'),
				'values' => array( 1, 0 ),
				'desc' => '',
				'priority' => 10,
			'section' => _CRYOUT_THEME_PREFIX . '_misc' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_shortcodesprefix',
				'type' => 'text',
				'label' => __('Plus Shortcodes Prefix','cryout'),
				'desc' => '',
				'priority' => 10,
			'section' => _CRYOUT_THEME_PREFIX . '_misc' ),

			//////////////////////////////////////////////////// Footer Link ////////////////////////////////////////////////////
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_bywordpress',
				'type' => 'toggle',
				'label' => __('WordPress Link','cryout'),
				'values' => array( 0, 1 ),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_footerlink' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_byself',
				'type' => 'toggle',
				'label' => __('Custom Link','cryout'),
				'values' => array( 0, 1 ),
				'desc' => __('Show or hide your own custom powered by link/text (defined below).','cryout'),
			'section' => _CRYOUT_THEME_PREFIX . '_footerlink' ),
			array(
			'id' => _CRYOUT_THEME_PREFIX . '_byself_text',
				'type' => 'textarea',
				'label' => __('Custom Link Text','cryout'),
				'desc' => '',
			'section' => _CRYOUT_THEME_PREFIX . '_footerlink' ),
			/* array(
			'id' => _CRYOUT_THEME_PREFIX . '_bycss',
				'type' => 'text',
				'label' => __('Container CSS','cryout'),
				'desc' => __('Custom link wrapper div styling. Only modify if you are familiar with CSS.','cryout'),
			'section' => _CRYOUT_THEME_PREFIX . '_footerlink' ), */

		), // options

		'widget-areas' => array(

			'absolute-top' => array(
				'name' => __('Inner Top', 'cryout' ),
				'description' => __('Displayed between the header and content/sidebar(s).','cryout' ),
				'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			),
			'absolute-bottom' => array(
				'name' => __('Inner Bottom', 'cryout' ),
				'description' => __('Displayed between the content/sidebar(s) and footer.','cryout' ),
				'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			),
			'empty-page-area' => array(
				'name' => __('404 / No Results', 'cryout' ),
				'description' => __('Displayed on the 404 and on empty search results pages.','cryout' ),
				'before_widget' => '<section id="%1$s" class="widget-container %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			),

		),

	); // $structure

	return array( 'defaults' => $defaults, 'structure' => $structure );

}; // cryout_plus_theme_specifics()


/* Get sample pages for options defaults */
function cryoutplus_get_default_pages( $number = 4 ) {
	$ids = array();
	for ($i=0;$i<=$number;$i++) $ids[] = 0;

	$default_pages = get_posts(
		array(
			'order' => 'DESC',
			'orderby' => 'date',
			'posts_per_page' => $number,
			'post_type'	=> 'page',
		)
	);
	foreach ( $default_pages as $key => $page ) {
		if ( ! empty ( $page->ID ) ) {
			$ids[$key+1] = $page->ID;
		}
		else {
			$ids[$key+1] = 0;
		}
	}
	return $ids;
} //cryoutplus_get_default_pages()

/**
 * 2. CUSTOM STYLES
 */

/**
 * options-based frontend styling
 */

function cryout_plus_custom_styling() {
	$options = cryout_get_option();

	ob_start(); ?>

/*========== Plus style ========*/

/********** Landing Page **********/

.lp-blocks1 { background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolorblocks1'] ); ?>; }
.lp-blocks2 { background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolorblocks2'] ); ?>; }
.lp-boxes-1 { background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolorboxes1'] ); ?>; }
.lp-boxes-2 { background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolorboxes2'] ); ?>; }
.lp-boxes-3 { background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolorboxes3'] ); ?>; }
#lp-text-zero { background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortextzero'] ); ?>; }
#lp-text-one {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortextone'] ); ?>; }
#lp-text-two {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortexttwo'] ); ?>; }
#lp-text-three {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortextthree'] ); ?>; }
#lp-text-four {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortextfour'] ); ?>; }
#lp-text-five {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortextfive'] ); ?>; }
#lp-text-six {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortextsix'] ); ?>; }
.lp-portfolio {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolorportfolio'] ); ?>; }
.lp-testimonials {  background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_lpcolortestimonial'] ); ?>; }

.lp-blocks2 .lp-block-title {
	font-family: <?php cryout_font_select( $options[_CRYOUT_THEME_PREFIX . '_fheadings'], $options[_CRYOUT_THEME_PREFIX . '_fheadingsgoogle'], true ) ?>;
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

.lp-blocks2 .lp-block-icon {
	background: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
}

.lp-blocks2 .lp-block i::before {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
}

.lp-blocks2 .lp-block-readmore {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.lp-blocks2 .lp-block-readmore:hover {
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

.lp-boxes-static2 .lp-box {
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
}

.lp-boxes-static.lp-boxes-static2 .lp-box-image:hover .lp-box-imagelink {
	border-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.lp-boxes-animated.lp-boxes-animated2 .lp-box-title {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_headingstext'] ) ?>;
}

.lp-boxes-3 .lp-box .lp-box-image {
	height: <?php echo intval ( (int) $options[_CRYOUT_THEME_PREFIX . '_lpboxheight3'] ) ?>px;
}

.bravada-landing-page .lp-portfolio-inside,
.lp-testimonials-inside {
	max-width: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_sitewidth'] ) ?>px;
}

.lp-portfolio .lp-port-title a,
#portfolio-masonry .portfolio-entry .portfolio-entry-title a {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

.lp-portfolio .lp-port:hover .lp-port-title a,
#portfolio-masonry .portfolio-entry:hover .portfolio-entry-title a {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

#portfolio-masonry .portfolio-entry:hover .portfolio-entry-title a::before,
.lp-text .lp-text-overlay + .lp-text-inside {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_sitetext'] ) ?>;
}

.lp-portfolio .lp-port-title,
#portfolio-masonry .portfolio-entry .portfolio-entry-title a {
	font-family: <?php cryout_font_select( $options[_CRYOUT_THEME_PREFIX . '_fheadings'], $options[_CRYOUT_THEME_PREFIX . '_fheadingsgoogle'], true  ) ?>;
}

.lp-text.style-reverse .lp-text-inside {
	color: <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_sitetext'], -127 ) )?>;
}

/********** Shortcodes **********/

.panel-title {
	font-family: <?php cryout_font_select(  $options[_CRYOUT_THEME_PREFIX . '_fgeneral'], $options[_CRYOUT_THEME_PREFIX . '_fgeneralgoogle'], true  ) ?>;
}

.panel-default > .panel-heading > .panel-title > a:hover {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}
<?php /*
.btn {
	font-family: <?php cryout_font_select( $options[_CRYOUT_THEME_PREFIX . '_fheadings'], $options[_CRYOUT_THEME_PREFIX . '_fheadingsgoogle'], true  ) ?>;
} */ ?>

.btn-primary,
.label.label-primary {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.btn-secondary,
.label.label-secondary {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary.focus,
.btn-primary:active,
.btn-primary.active {
	color: <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_accent1'], 34 ) ) ?>;
}

.btn-secondary:hover,
.btn-secondary:focus,
.btn-secondary.focus,
.btn-secondary:active,
.btn-secondary.active {
	color: <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_accent2'], 34 ) ) ?>;
}

.fontfamily-titles-font {
	font-family: <?php cryout_font_select(  $options[_CRYOUT_THEME_PREFIX . '_ftitles'], $options[_CRYOUT_THEME_PREFIX . '_ftitlesgoogle'], true ) ?>;
}

.fontfamily-headings-font {
	font-family: <?php cryout_font_select( $options[_CRYOUT_THEME_PREFIX . '_fheadings'], $options[_CRYOUT_THEME_PREFIX . '_fheadingsgoogle'], true ) ?>;
}

/********** Portfolio **********/

.single article.jetpack-portfolio .entry-meta > span a {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.jetpack-portfolio-shortcode .portfolio-entry .portfolio-entry-meta > div:last-child {
	border-color: <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'], 17 ) ) ?>;
}
#portfolio-filter > a, #portfolio-filter > a::after {
	color: <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_sitetext'], 51 ) ) ?>;
}

#portfolio-filter > a.active {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.portfolio-entry-meta span {
	color: <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_sitetext'], -51 ) ) ?>;
}

.jetpack-portfolio-shortcode .portfolio-entry-title a {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

.lp-portfolio .lp-port-readmore {
	border-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

.lp-portfolio .lp-port-readmore::before {
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

/********** Testimonials **********/

.lp-tt-text-inside {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.lp-tt-meta img {
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
}

.main .lp-tt-title {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent2'] ) ?>;
}

/********** Widgets **********/

.widget-area .cryout-wtabs-nav {
	border-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ); ?>;
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

.cryout-wtab .tab-item-thumbnail::after,
.cryout-wtab .tab-item-avatar::after,
.cryout-wposts .post-item-thumbnail::after {
	background-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] )?>;
	background: -webkit-linear-gradient(to bottom, transparent 40%, <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>);
	background: linear-gradient(to bottom, transparent 40%, <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>);
}

.widget_cryout_contact address > span i {
	color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?>;
}

/******** Team Members ********/

.tmm .tmm_container .tmm_member .tmm_photo {
	border-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'] ) ?>;
}

.cryout .tmm .tmm_container .tmm_member {
	border: 1px solid <?php echo esc_html( cryout_hexdiff( $options[_CRYOUT_THEME_PREFIX . '_contentbackground'], 17 ) ) ?> !important;
}

.cryout .tmm .tmm_container .tmm_member:hover {
	border-color: <?php echo esc_html( $options[_CRYOUT_THEME_PREFIX . '_accent1'] ) ?> !important;
}


@media (min-width: 800px) and (max-width: <?php echo intval( $options[_CRYOUT_THEME_PREFIX . '_responsivelimit'] ) ?>px) {
	.cryout #access { display: none; }
	.cryout #nav-toggle { display: block; }
	.cryout #sheader-container > * { margin-left: 0; margin-right: 2em; }
}
/* end Plus style */
<?php
	return preg_replace( '/((background-)?color:\s*?)[;}]/i', '', ob_get_clean() );
} // cryout_plus_custom_styling()


/**
 * customizer theme-specific styling
 */

function cryout_customizer_styles( $color1 = '#E9B44C', $color2 = '#222' ) {
	ob_start(); ?>

#customize-controls [id*="cryout"][id*="_layout"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_generallayout"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_sectionlayout"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_landingpage"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_general_section"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_colors_section"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_misc"] > h3.accordion-section-title::before,
#customize-controls [id*="cryout"][id*="_footerlink"] > h3.accordion-section-title::before,
/* #customize-controls li.accordion-section[id*="cryout"][id*="_lpgeneral"] > h3.accordion-section-title::before, */
#customize-controls li.accordion-section[id*="cryout"][id*="_lptexts"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_lporder"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_lpblocks"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_lpboxes"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_lpportfolio"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_lptestimonials"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_lpfcontent"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_contentgraphics"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_schemes"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_colors_lp"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_featured"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_post_section"] > h3.accordion-section-title::before,
#customize-controls li.accordion-section[id*="cryout"][id*="_related"] > h3.accordion-section-title::before
{
	color: #fff;
	background: <?php echo esc_html( $color1 ) ?>;
	border-color: <?php echo esc_html( $color1 ) ?>;
}

/* individual options */
#customize-controls ul[id*='cryout'][id*='_generallayout'] li.customize-control[id*='_responsivelimit'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_sectionlayout'] li.customize-control[id*='_archivelayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_sectionlayout'] li.customize-control[id*='_searchlayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_sectionlayout'] li.customize-control[id*='_portlayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_sectionlayout'] li.customize-control[id*='_portsinglelayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_sectionlayout'] li.customize-control[id*='_woolayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_sectionlayout'] li.customize-control[id*='_woosinglelayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpfcontent'] li.customize-control[id*='_lppostslayout'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpfcontent'] li.customize-control[id*='_lppostscount'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpfcontent'] li.customize-control[id*='_lppostscat'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_contentgraphics'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks1'] li.customize-control[id*='_lpblockfiveicon1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks1'] li.customize-control[id*='_lpblocksixicon1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks1'] li.customize-control[id*='_lpblocksevenicon1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks1'] li.customize-control[id*='_lpblockeighticon1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks1'] li.customize-control[id*='_lpblocknineicon1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks1'] li.customize-control[id*='_lpblockperrow1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpblocks2'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpboxes1'] li.customize-control[id*='_lpboxcat1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpboxes1'] li.customize-control[id*='_lpboxanimation1'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpboxes2'] li.customize-control[id*='_lpboxcat2'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpboxes2'] li.customize-control[id*='_lpboxanimation2'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lptexts'] li.customize-control[id*='_lptextzero'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lptexts'] li.customize-control[id*='_lptextfour'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lptexts'] li.customize-control[id*='_lptextfive'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lptexts'] li.customize-control[id*='_lptextsix'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpportfolio'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lptestimonials'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_lpboxes3'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_schemes'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_colors_lp'] li.customize-control:not([id*="_lppostsbg"]) .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_featured'] li.customize-control[id*='_fplaceholder'] .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_related'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'][id*='_footerlink'] li.customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'] li[id*='_pageexcerpts'].customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'] li[id*='_shortcodes'].customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'] li[id*='_shortcodesprefix'].customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'] li[id*='_footerjs'].customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'] li[id*='_bodyjs'].customize-control .customize-control-title::after,
#customize-controls ul[id*='cryout'] li[id*='_headerjs'].customize-control .customize-control-title::after {
    background: <?php echo esc_html( $color1 ) ?>;
    border: 1px solid <?php echo esc_html( $color1 ) ?>;
    color: #fff;
    content: "Plus";
    font-size: 0.8em;
    margin-left: 0.5em;
    padding: 0 0.2em;
}

<?php
	return ob_get_clean();
} // cryout_customizer_styles()


// FIN
