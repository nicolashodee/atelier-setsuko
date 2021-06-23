<?php
/**
 * Plus Shortcodes
 *
 * @package Cryout Plus
 */

class CryoutShortcodes {

    public $shortcodes = array(
        'grid',
        'tabs',
        'collapse',
        'alerts',
        'buttons',
        'labels',
        'wells',
        'lead',
        'pullquote',
        'tooltip',
        'clear',
        'divider',
        'map',
        'icons',
		//'portfolio', // depends on Jetpack, so it's added later
    );

	public $prefix = 'cryout_';

    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    function init() {
		// init shortcodes when option is enabled
		if ( 1 != cryout_get_option( _CRYOUT_THEME_PREFIX . '_shortcodes') ) return;

		// init the prefix
		$this->prefix = cryout_get_option( _CRYOUT_THEME_PREFIX . '_shortcodesprefix' );

        if( is_admin() ) {
			if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
				return;
			}
			wp_enqueue_style( _CRYOUT_THEME_SLUG . '-admin-shortcodes-css', get_template_directory_uri() . '/plus/resources/admin/shortcodes.css', _CRYOUT_THEME_VERSION );

			// placeholder script for wp_localize_script call below
			wp_enqueue_script( _CRYOUT_THEME_SLUG . '-admin-placeholder-js', get_template_directory_uri() . '/plus/resources/admin/placeholder.js', NULL, _CRYOUT_THEME_VERSION );

			// ajaxurl is also used in main Plus class on customizer js
			wp_localize_script( _CRYOUT_THEME_SLUG . '-admin-placeholder-js', 'cryout_plus_ajax_backend', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

			// portfolio shortcode backend for authenticated users
			add_action( 'wp_ajax_cryout_plus_portfolio_shortcode_data', array( $this, 'get_portfolio_data' ) );
		} else {
            wp_enqueue_style( _CRYOUT_THEME_SLUG . 'plus-shortcodes', get_template_directory_uri() . '/plus/resources/shortcodes.css', NULL, _CRYOUT_THEME_VERSION );
            wp_enqueue_script( _CRYOUT_THEME_SLUG . 'plus-shortcodes', get_template_directory_uri() . '/plus/resources/shortcodes.js', array( 'jquery' ), _CRYOUT_THEME_VERSION );

			// register shortcodes
			add_shortcode( $this->prefix . 'row', array( $this, 'short_row' ) );
			add_shortcode( $this->prefix . 'col', array( $this, 'short_span' ) );
			add_shortcode( $this->prefix . 'tabs', array( $this, 'short_tabs' ) );
			add_shortcode( $this->prefix . 'thead', array( $this, 'short_thead' ) );
			add_shortcode( $this->prefix . 'tab', array( $this, 'short_tab' ) );
			add_shortcode( $this->prefix . 'dropdown', array( $this, 'short_dropdown' ) );
			add_shortcode( $this->prefix . 'tabgroup', array( $this, 'short_tabgroup' ) );
			add_shortcode( $this->prefix . 'tabinner', array( $this, 'short_tabinner' ) );
			add_shortcode( $this->prefix . 'collapse', array( $this, 'short_collapse' ) );
			add_shortcode( $this->prefix . 'citem', array( $this, 'short_citem' ) );
			add_shortcode( $this->prefix . 'notification', array( $this, 'short_alert' ) );
			add_shortcode( $this->prefix . 'well', array( $this, 'short_well' ) );
			add_shortcode( $this->prefix . 'button', array( $this, 'short_button' ) );
			add_shortcode( $this->prefix . 'label', array( $this, 'short_label' ) );
			add_shortcode( $this->prefix . 'icon', array( $this, 'short_icon' ) );
			add_shortcode( $this->prefix . 'lead', array( $this, 'short_lead' ) );
			add_shortcode( $this->prefix . 'tooltip', array( $this, 'short_tooltip' ) );
			add_shortcode( $this->prefix . 'pullquote', array( $this, 'short_pullquote' ) );
			add_shortcode( $this->prefix . 'clear', array( $this, 'short_clear' ) );
			add_shortcode( $this->prefix . 'divider', array( $this, 'short_divider' ) );
			add_shortcode( $this->prefix . 'map', array( $this, 'short_map' ) );
        }

        if ( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( 'mce_external_plugins', array( $this, 'init_shortcodes' ) );
            add_filter( 'mce_buttons_3', array( $this, 'register_buttons' ) ); // add mce buttons on the third row
			foreach ( array('post.php','post-new.php') as $hook ) add_action( "admin_head-$hook", array( $this, 'tinymce_vars' ) );
        }
    } // init()

	// mce buttons callback
    function register_buttons( $buttons ) {
        foreach ( $this->shortcodes as $shortcode ) {
            array_push( $buttons, 'cryout_short_' . $shortcode );
        }
        return $buttons;
    } // register_buttons()

	// mce plugins callback
    function init_shortcodes( $shorts ) {

		// conditionally add the portfolio widget only when jetpack's cpt is available
		if ( post_type_exists( 'jetpack-portfolio' ) && !in_array( 'portfolio', $this->shortcodes ) ) $this->shortcodes[] = 'portfolio';

        foreach ( $this->shortcodes as $shortcode ) {
            $shorts[ 'cryout_short_' . $shortcode ] = get_template_directory_uri() . '/plus/resources/shortcodes/' . $shortcode . '.js';
        }
        return $shorts;
    } // init_shortcodes()

	// localizes the shortcode prefix variable for js
	function tinymce_vars( $settings ) {
		?>
		<!-- cryout tinymce vars -->
		<script type='text/javascript'>
		var cryout_shortcodes_prefix = '<?php echo $this->prefix ?>';
		</script>
		<?php
	} // tinymce_vars()

	// returns json portfolio data to the shortcode creator button
	public function get_portfolio_data() {
		if (in_array( $_POST['what'], array( 'type', 'tag' )) ) $what = esc_attr($_POST['what']);
		$data = $this->get_portfolio($what);
		echo json_encode( $data );
		wp_die();
	} // get_portfolio_data()

	// returns portofolio types/tags list
	function get_portfolio( $term = 'type' ) {
		$data = get_terms( 'jetpack-portfolio-'.$term, array( 'hide_empty' => false ) );

		$results[] = array( 'text'=>'All', 'value'=>0 );

		$items = array();
		foreach ($data as $items) {
			$results[] = array( 'text'=>$items->name, 'value'=>$items->slug );
		}

		if (count($results)<1) $results = array( array('text' => 'No data', 'value' => 0) );

		return $results;
	} // get_portfolio()

	/**
	 * the shortcodes
	 */

	// grid //
	function short_row( $params, $content=null ) {
		extract( shortcode_atts( array(
				'class' => 'row'
			), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<div class="' . $class . '">';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_row()

	function short_span( $params, $content=null ) {
		extract( shortcode_atts( array(
				'class' => 'col-sm-1'
			), $params ) );
		$result = '<div class="' . $class . '">';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_span()

	// tabs //
	/*--------------
	[tabs]
		[thead]
			[tab href="#link" title="title"]
			[dropdown title="title"]
				[tab href="#link" title="title"]
			[/dropdown]
		[/thead]
		[tabgroup]
			[tabinner id="link"]
			[/tabinner]
		[/tabgroup]
	[/tabs]
	---------------*/

	function short_tabs( $params, $content=null ) {
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<div class="tab_wrap">';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_tabs()

	function short_thead( $params, $content=null) {
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<ul class="nav nav-tabs">';
		$result .= do_shortcode( $content );
		$result .= '</ul>';
		return force_balance_tags( $result );
	} // short_thead()

	function short_tab( $params, $content=null ) {
		extract( shortcode_atts( array(
			'href' => '#',
			'title' => '',
			'class' => ''
			), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );

		$result = '<li class="' . $class . '">';
		$result .= '<a data-toggle="tab" href="' . $href . '">' . $title . '</a>';
		$result .= '</li>';
		return force_balance_tags( $result );
	} // short_tab()

	function short_dropdown( $params, $content=null ) {
		global $bs_timestamp;
		extract( shortcode_atts( array(
			'title' => '',
			'id' => '',
			'class' => '',
			), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<li class="dropdown">';
		$result .= '<a class="' . $class . '" id="' . $id . '" class="dropdown-toggle" data-toggle="dropdown">' . $title . '<b class="caret"></b></a>';
		$result .= '<ul class="dropdown-menu">';
		$result .= do_shortcode( $content );
		$result .= '</ul></li>';
		return force_balance_tags( $result );
	} // short_dropdown()

	function short_tabgroup( $params, $content=null ) {
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<div class="tab-content">';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_tabgroup()

	function short_tabinner( $params, $content=null ) {
		extract(shortcode_atts(array(
			'id' => '',
			'class'=>'',
			), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$class = ($class=='active')? 'active in': '';
		$result = '<div class="tab-pane fade ' . $class . '" id=' . $id . '>';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_tabinner()

	// collapse //
	function short_collapse( $params, $content=null ){
		extract( shortcode_atts( array(
			'id'=>'',
            'scheme' => '',
			 ), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<div class="panel-group scheme-' . $scheme . '" id="' . $id . '">';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_collapse()

	function short_citem( $params, $content=null ){
		extract( shortcode_atts( array(
			'id'=> '',
			'title'=> 'Collapse title',
			'parent' => '',
			'open' => 'false',
			 ), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result =  '<div class="panel panel-default" role="tablist">';
		$result .= '    <div class="panel-heading" role="tab" id="heading_' . $id . '">';
		$result .= '        <h4 class="panel-title">';
		$result .= '<a class="accordion-toggle collapsed" data-toggle="collapse" aria-controls="heading_' . $id . '" data-parent="#' . $parent . '" href="#' . $id . '">';
		$result .= $title;
		$result .= '</a>';
		$result .= '        </h4>';
		$result .= '    </div>';
		$result .= '    <div id="' . $id . '" class="panel-collapse collapse '.($open=='true'? 'in' : '').'" role="tabpanel" aria-labelledby="heading_' . $id . '">';
		$result .= '        <div class="panel-body">';
		$result .= do_shortcode( $content );
		$result .= '        </div>';
		$result .= '    </div>';
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_citem()

	// alert //
	function short_alert( $params, $content=null ) {
		extract( shortcode_atts( array(
				'type' => 'unknown',
				'dismissible' => 'true'
			), $params ) );
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result =  '<div class="alert alert-'.$type.($dismissible=='true'? ' alert-dismissible' : '').'">';
		$result .= $dismissible=='true'? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' : '';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_alert()

	// well //
	function short_well( $params, $content=null ) {
    extract( shortcode_atts( array(
			'size' => 'unknown'
		), $params));

		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result =  '<div class="well well-' . $size . '">';
		$result .= do_shortcode( $content );
		$result .= '</div>';
		return force_balance_tags( $result );
	} // short_well

	// button //
	function short_button( $params, $content=null ) {
		extract(shortcode_atts(array(
			'size' => 'default',
			'type' => 'default',
			'text' => 'button2',
			'target' => '_self',
			'href' => "#"
		), $params ) );

		$content = do_shortcode( preg_replace( '/<br class="nc".\/>/', '', $content ) );
		$result = '<a class="btn btn-' . $size . ' btn-' . $type . '" href="' . $href . '" target="' . $target . '">' . $content . '</a>';
		return force_balance_tags( $result );
	} // short_button()

	// label //
	function short_label( $params, $content=null ) {
		extract( shortcode_atts( array(
			'type' => 'default'
		), $params ) );

		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<span class="label label-' . $type . '">' . $content . '</span>';
		return force_balance_tags( $result );
	} // short_label()

	// icon //
	function short_icon( $params, $content=null ) {
		extract(shortcode_atts(array(
            'size' => 'md',
			'name' => 'default'
		), $params));

		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<i class="' . $name . ' ' . $size . '"></i>';
		return force_balance_tags( $result );
	} // short_icon()

	// lead //
	function short_lead( $params, $content=null ) {

        extract( shortcode_atts( array(
            'fontfamily' => 'default',
            'fontsize' => '100',
        ), $params ) );

		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		$result = '<div class="lead fontfamily-' . $fontfamily . ' fontsize-' . $fontsize . '">';
		$result .= do_shortcode( $content );
		$result .= '</div>';

		return force_balance_tags( $result );
	} // short_lead()

	// tooltip //
	function short_tooltip( $params, $content=null ) {
		extract( shortcode_atts( array(
			'placement' => 'top',
			'trigger' => 'hover',
			'title' => 'This is a tooltip',
			'href' => "#"
		), $params ) );

		$placement = ( in_array( $placement, array( 'top', 'right', 'bottom', 'left' ) ) ) ? $placement : 'top';
		$content = preg_replace( '/<br class="nc".\/>/', '', $content );
		//$title = explode( '\n', wordwrap( $content, 20, '\n' ) );
		$title = esc_attr( $title );
		$result = '<a href="#" class="tooltip-anchor" data-toggle="tooltip" title="' . $title . '" data-placement="' . esc_attr( $placement ) . '" data-trigger="' . esc_attr( $trigger ) . '">' . do_shortcode( $content ) . '</a>';
		return force_balance_tags( $result );
	} // short_tooltip()

    // Pullquote //
    function short_pullquote( $params, $content=null ){

        extract( shortcode_atts( array(
            'align' => '',
            'size' => '',
            'fontfamily' => 'default',
            'fontsize' => '110',
        ), $params ) );

        $content = preg_replace( '/<br class="nc".\/>/', '', $content );
        $result = '<div class="pullquote ' . $align . ' ' .  $size . ' fontfamily-' . $fontfamily . ' fontsize-' . $fontsize .'">';
        $result .= do_shortcode( $content );
        $result .= '</div>';

        return force_balance_tags( $result );
    } // short_pullquote()

    // Clear //
    function short_clear( $params, $content=null ){
        $result = '<div class="clearfix">&nbsp;</div>';
        return force_balance_tags( $result );
    } // short_clear()

    // Divider //
    function short_divider( $params, $content=null ){

        extract( shortcode_atts( array(
            'height'   => '1',
            'margin_top' => '20',
            'margin_bottom' => '20',
        ), $params ) );

        $result = '<hr class="divider height-' . $height . ' margin-top-' . $margin_top . ' margin-bottom-' . $margin_bottom . '" />';
        return force_balance_tags( $result );
    } // short_divider()

    // Pullquote //
    function short_map( $params, $content=null ){

        extract( shortcode_atts( array(
			'url' => '',
			'width' => '',
			'height' => '',
		), $params ) );

        $result = '<iframe width="' . absint( $width ) . '" height="' . absint( $height ) . '" src="' . esc_url( $url ) . '"></iframe>';
        return force_balance_tags( $result );
    } // short_map()

} // class CryoutShortcodes


new CryoutShortcodes();

// FIN
