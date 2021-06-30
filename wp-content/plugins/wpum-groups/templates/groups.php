<?php
/**
 * The Template for displaying the directory
 *
 * This template can be overridden by copying it to yourtheme/wpum/groups.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div id="wpumg-groups">

	<?php do_action( 'wpumg_before_group_directory' ); ?>

	<form action="<?php the_permalink(); ?>" method="GET" name="wpumg-group-search-form">
		<?php
			WPUMGP()->templates
				->set_template_data( $data )
				->get_template_part( 'directory/search-form' );
			WPUMGP()->templates
				->set_template_data( $data )
				->get_template_part( 'directory/top-bar' );
		?>
	</form>
	<!-- start directory -->
	<div id="wpumg-group-list">

		<?php if( is_array( $data->results ) && ! empty( $data->results ) ) : ?>

			<?php foreach( $data->results as $group ) : ?>
				<?php

					$group_template = 'group';

					WPUMGP()->templates
						->set_template_data( $group )
						->get_template_part( 'directory/single', $group_template );
				?>
			<?php endforeach; ?>

			<?php wpumgp_group_directory_pagination( $data ); ?>

		<?php else : ?>
			<?php

				WPUMGP()->templates
					->set_template_data( [
						'message' => esc_html__( 'No groups have been found.', 'wpumg-group' ),
					] )
					->get_template_part( 'directory/general', 'warning' );

			?>

		<?php endif; ?>

	</div>
	<!-- end directory -->
	<?php do_action( 'wpumg_after_group_directory' ); ?>

</div>
