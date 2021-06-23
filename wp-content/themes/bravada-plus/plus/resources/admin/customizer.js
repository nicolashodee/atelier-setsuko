/*
 * Cryout Plus Customizer backend scripting
 *
 * @package Cryout Plus
 */
 
function cryout_lporder_refresh(e) {
	e.preventDefault();
	jQuery('.cryout-sortable-control').fadeOut();
	jQuery.ajax(
			cryout_plus_ajax_backend.ajaxurl,
			{ 'method': 'POST', 'data' : { 'action': 'cryout_plus_lporder_refresh' }, async: false, dataType: 'json',
			'success': function( data ) {
				jQuery('.cryout-sortable-control ul.sortable-row li').each( function() {
					jQuery(this).removeClass('status-enabled');
					jQuery(this).removeClass('status-disabled');
					id = jQuery(this).attr('id');
					if (id.match(/(blocks-|boxes-|text-)/i)) disabled = "-1"; else disabled = "0";
					if (data[id]==disabled) jQuery(this).addClass('status-disabled');
							 else jQuery(this).addClass('status-enabled');
				} );
				jQuery('.cryout-sortable-control').fadeIn(100);
			} }
	).fail(function() {
		jQuery('.cryout-sortable-control').fadeIn(100);
	});
	return false;
}

function cryout_lporder_reset(e) {
	e.preventDefault();
	if ( confirm('Are you sure you want to reset to the default order?') ) {
		jQuery('.cryout-sortable-control').fadeOut();
		jQuery.ajax(
				cryout_plus_ajax_backend.ajaxurl,
				{ 'method': 'POST', 'data' : { 'action': 'cryout_plus_lporder_default' }, async: false, dataType: 'json',
				'success': function( data ) {
					jQuery('.cryout-sortable-control input.the_sorted').val(data);
					var ids = data.split(',');
					var index, len;
					for (index = 0, len = ids.length; index < len-1; ++index) {
						jQuery('#'+ids[index]).after( jQuery('#'+ids[index+1]) );
					}
					jQuery('.cryout-sortable-control input.the_sorted').trigger('change');
					jQuery('.cryout-sortable-control').fadeIn(100);
				} }
		).fail(function() {
			jQuery('.cryout-sortable-control').fadeIn(100);
		});		
	}	
	return false;
}
