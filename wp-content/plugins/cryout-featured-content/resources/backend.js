/**
 * Cryout Featured Content
 * backend JavaScript
 */
 
jQuery(document).ready( function() {
	/*
	 * custom meta box field dependencies
	 */
	jQuery('#cryout_blob_type').on( 'change', function() {
		type = jQuery(this).val();
		jQuery('.cryout-depends-blob-type').hide(0);
		if ('block'==type) jQuery('.cryout-block-icon').show();
	});
	jQuery('#cryout_blob_type').trigger('change');
} )