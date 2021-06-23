
<table id="cryout_featured_content_metas" class="form-table">
	<tbody>
		<tr>
			<th>
				<label for="cryout_blob_type"><?php _e('Type', 'cryout-featured-content' )?>:</label>
			</th>
			<td>
				<select id="cryout_blob_type" name="cryout_blob_type" class="regular-text">
					<option value="" <?php selected( $cryout_blob_type, '' ) ?> disabled>- <?php _e( 'Select', 'cryout-featured-content' ) ?> -</option>
					<option value="block" <?php selected( $cryout_blob_type, 'block' ) ?>><?php _e( 'Icon Block', 'cryout-featured-content' ) ?></option>
					<option value="box" <?php selected( $cryout_blob_type, 'box' ) ?>><?php _e( 'Featured Box', 'cryout-featured-content' ) ?></option>
					<option value="text" <?php selected( $cryout_blob_type, 'text' ) ?>><?php _e( 'Text Area', 'cryout-featured-content' ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="cryout_blob_link"><?php _e( 'Link', 'cryout-featured-content' ) ?>:</label>
			</th>
			<td>
				<input id="cryout_blob_link" name="cryout_blob_link" type="text" class="regular-text" value="<?php echo esc_url( $cryout_blob_link ) ?>" >
				<input id="cryout_blob_target" name="cryout_blob_target" type="checkbox" <?php checked( $cryout_blob_target ) ?>> <label for="cryout_blob_target"><?php _e( 'Open in New Tab', 'cryout-featured-content' ) ?></label>
			</td>
		</tr>
		<tr>
			<th>
				<label for="cryout_blob_style"><?php _e( 'Style', 'cryout-featured-content' ) ?>:</label>
			</th>
			<td>
				<select id="cryout_blob_style" name="cryout_blob_style" class="regular-text">
					<option value="default" <?php selected( $cryout_blob_style, 'default' ) ?>><?php _e( 'Default', 'cryout-featured-content' ) ?></option>
					<option value="reverse" <?php selected( $cryout_blob_style, 'reverse' ) ?>><?php _e( 'Reverse', 'cryout-featured-content' ) ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<label for="cryout_blob_hidetitle"><?php _e( 'Hide Title', 'cryout-featured-content' ) ?>:</label>
			</th>
			<td>
				<input id="cryout_blob_hidetitle" name="cryout_blob_hidetitle" type="checkbox" <?php checked( $cryout_blob_hidetitle ) ?>>
			</td>
		</tr>		

	</tbody>
</table>
