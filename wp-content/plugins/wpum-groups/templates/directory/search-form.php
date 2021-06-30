<?php
/**
 * The Template for displaying the group search form.
 *
 * This template can be overridden by copying it to yourtheme/wpum/directory/search-form.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! $data->has_search_form ) {
	return;
}

$value = isset( $_GET['group-search'] ) && ! empty( $_GET['group-search'] ) ? sanitize_text_field( $_GET['group-search'] ) : false;
?>

<?php if ($data->has_search_form == 'true') : ?>
<div id="wpum-directory-search-form">
	<div class="wpum-row">
		<div class="form-fields wpum-col-xs-9">
			<?php do_action( 'wpumg_group_search_form_top_fields'); ?>
			<input class="input-text" type="text" name="group-search" id="wpumg-group-search" placeholder="<?php echo esc_html( sprintf( __( 'Search %s...', 'wpumg-group'), strtolower( $data->plural ) ) ); ?>" value="<?php echo esc_html( $value ); ?>">
			<?php do_action( 'wpumg_group_search_form_bottom_fields' ); ?>
		</div>
		<div class="form-submit wpum-col-xs-3">
			<?php wp_nonce_field( 'group_search_action', '_wpnonce', false, true ); ?>
			<input type="submit" id="wpumg-submit-user-search" class="button wpum-button" value="<?php esc_html_e( 'Search', 'wpumg-group' ); ?>">
		</div>
	</div>
	<div class="wpum-row" style="margin-top: 20px;">
		<div class="form-fields wpum-col-xs-5">
			<?php echo WPUM()->elements->select( [
				'name'             => 'category',
				'id'               => 'wpumg-group-search-cat',
				'selected'         => isset( $_GET['category'] ) ? esc_attr( $_GET['category'] ) : $data->category_by_default,
				'show_option_all'  => false,
				'show_option_none' => false,
				'options'          => wpumgp_get_group_categories(),
			] ); ?>
		</div>
		<div class="form-fields wpum-col-xs-5">
			<?php echo WPUM()->elements->select( [
				'name'             => 'tag',
				'id'               => 'wpumg-group-search-tag',
				'selected'         => isset( $_GET['tag'] ) ? esc_attr( $_GET['tag'] ) : $data->tag_by_default,
				'show_option_all'  => false,
				'show_option_none' => false,
				'options'          => wpumgp_get_group_tags(),
			] ); ?>
		</div>
		<div class="wpum-col-xs-2">
			<?php
			$parsed = parse_url( home_url() . $_SERVER['REQUEST_URI'] );
			if ( ! empty( $parsed['query'] ) ) : ?>
				<div class="form-reset wpum-col-xs-1">
					<?php
					$clear_url = get_permalink();
					?>
					<a id="wpumg-submit-user-reset" href="<?php echo $clear_url; ?>"
					   class="wpumg-reset"><?php esc_html_e( 'Clear', 'wpumg-group' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>
