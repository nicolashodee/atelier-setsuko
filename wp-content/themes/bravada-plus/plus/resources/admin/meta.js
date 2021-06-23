/*
 * Cryout Plus Meta backend scripts
 *
 * @package Cryout Plus
 */

jQuery(document).ready(function($){

	if (typeof $.fn.tabs === 'function') {

		/* tabs */
		$('.cryout-tabs').tabs();

		/* color picker */
		if (typeof $.fn.wpColorPicker === 'function') $('.cryout-meta-wp-color-picker').wpColorPicker();

		/* hide color label */
		$('label[for="cryout-media-color"]').hide();

	}

	/* Handle clicking on the 'remove image' link */
	$('body').on('click','.cryout-remove-media', function(e) {
		e.preventDefault();
		var container = jQuery(e.target).closest('.widget-content, .cryout-media-selector');
		$(container).find('.cryout-media-image').val('').trigger('change');
		$(container).find('.cryout-add-media-text').show();
		$(container).find('.cryout-remove-media, .cryout-media-image-url, .cryout-meta-background-image-options').hide();
	});

	/**
	 * The following code deals with the custom media modal frame.
	 */

	/* Prepare the variable that holds our custom media manager. */
	var cryout_media_image_frame;
	/* Prepare the variable that holds the instance of our media selector link */
	var cryout_clicked_media_field;

	$( 'body' ).on( 'click', '.cryout-add-media',
		function( e ) {
			e.preventDefault();

			/* Remember which 'add media' button click triggered the media selector */
			cryout_clicked_media_field = e.target;

			/* If frame exists, open it */
			if ( cryout_media_image_frame ) {
				cryout_media_image_frame.open();
				return;
			}

			/* Create the media frame */
			cryout_media_image_frame = wp.media.frames.cryout_media_image_frame = wp.media(
				{

					className: 'media-frame cryout-custom-background-extended-frame',
					frame: 'select',															/* frame type ('select' or 'post') */
					multiple: false,															/* whether to allow multiple images */
					title: cryout_media_image.title,											/* custom frame title */
					library: { type: 'image' },													/* media type allowed */
					button: {																	/* Custom "insert" button */
						text:  cryout_media_image.button
					}
				}
			);

			/* Process image selection */
			cryout_media_image_frame.on( 'select',

				function() {

					var media_attachment = cryout_media_image_frame.state().get( 'selection' ).first().toJSON();

					/* limit scope to the container of the triggering 'add media' button */
					var parent_container = $(cryout_clicked_media_field).closest('.widget-content, .cryout-media-selector');

					/* send the attachment ID to our custom input field via jQuery */
					$(parent_container).find('.cryout-media-image').val( media_attachment.id ).trigger('change');
					$(parent_container).find('.cryout-add-media-text').parent().children('input').val( media_attachment.id ).trigger('change');
					$(parent_container).find('.cryout-add-media-text').hide();
					$(parent_container).find('.cryout-media-image-url').attr( 'src', media_attachment.url );
					$(parent_container).find('.cryout-media-image-url, .cryout-remove-media, .cryout-meta-background-image-options').show();
				}
			);

			/* Open the frame */
			cryout_media_image_frame.open();
		}
	); /* End click */

});
/* FIN */
