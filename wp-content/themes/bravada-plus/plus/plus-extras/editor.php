<?php
/* 
 * Plus visual editor for categories and tags
 *
 * @package Cryout Plus
 */

class CryoutTaxVisualEditor {

	public $taxonomies;

	public function __construct( array $taxonomies ) {
		$this->taxonomies = $taxonomies;
		$this->register();
	} // __construct()

	// actions and filters
	public function register() {

		if ( current_user_can( 'publish_posts' ) ) {

			// remove filters which disallow HTML in term descriptions
			remove_filter( 'pre_term_description', 'wp_filter_kses' );
			remove_filter( 'term_description', 'wp_kses_data' );

			// add filters to disallow unsafe HTML tags
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				add_filter( 'pre_term_description', 'wp_kses_post' );
				add_filter( 'term_description', 'wp_kses_post' );
			}
		}

		// apply `the_content` filters to term description
		if ( isset( $GLOBALS['wp_embed'] ) ) {
			add_filter( 'term_description', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
			add_filter( 'term_description', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
		}

		add_filter( 'term_description', 'wptexturize' );
		add_filter( 'term_description', 'convert_smilies' );
		add_filter( 'term_description', 'convert_chars' );
		add_filter( 'term_description', 'wpautop' );

		if ( ! is_admin() ) {
			add_filter( 'term_description', 'shortcode_unautop' );
			add_filter( 'term_description', 'do_shortcode', 11 );
		}

		// loop through the taxonomies, add actions
		foreach ( $this->taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'render_edit' ), 1, 2 );
			add_action( $taxonomy . '_add_form_fields', array( $this, 'render_add' ), 1, 1 );
		}
	} // register()

	// html for edit tag screen
	public function render_edit( $tag, $taxonomy ) {

		$settings = array(
			'textarea_name' => 'description',
			'textarea_rows' => 10,
			'editor_class'  => 'i18n-multilingual',
		);

		?>
		<tr class="form-field term-description-wrap">
			<th scope="row"><label for="description"><?php _e( 'Description' ); ?></label></th>
			<td>
				<?php

				wp_editor( htmlspecialchars_decode( $tag->description ), 'html-tag-description', $settings );

				?>
				<p class="description"><?php _e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>
			</td>
			<script type="text/javascript">
				// Remove the non-html field
				jQuery('textarea#description').closest('.form-field').remove();
			</script>
		</tr>
		<?php
	} // render_edit()

	// html for new tag screen
	public function render_add( $taxonomy ) {

		$settings = array(
			'textarea_name' => 'description',
			'textarea_rows' => 7,
			'editor_class'  => 'i18n-multilingual',
		);

		?>
		<div class="form-field term-description-wrap">
			<label for="tag-description"><?php _e( 'Description' ); ?></label>
			<?php

			wp_editor( '', 'html-tag-description', $settings );

			?>
			<p><?php _e( 'The description is not prominent by default; however, some themes may show it.' ); ?></p>

			<script type="text/javascript">
				// Remove the non-html field
				jQuery('textarea#tag-description').closest('.form-field').remove();

				jQuery(function () {
					// Trigger save
					jQuery('#addtag').on('mousedown', '#submit', function () {
						tinyMCE.triggerSave();
					});
				});

			</script>
		</div>
		<?php
	} // render_add()
	
} // class CryoutTaxVisualEditor

function cryout_init_visualeditor_for_taxonomies() {
	new CryoutTaxVisualEditor( 
		apply_filters('cryout_visual_editor_taxonomies', 
			array( 
				'category', 
				'post_tag'
			) 
		) 
	);
};
add_action('init', 'cryout_init_visualeditor_for_taxonomies');

// FIN