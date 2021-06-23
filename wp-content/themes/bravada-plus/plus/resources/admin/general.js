/*
 * Cryout Plus general scripts
 *
 * @package Cryout Plus
 */

jQuery(document).ready(function($){
	if (typeof $.fn.tabs === 'function') {
		$('#cryout-tabs').tabs({
			activate: function(event, ui) {
				event.preventDefault();
				hash = ui.newPanel.attr('id');
				hash = hash.replace( /^#/, '' );
				var node = $( '#' + hash );
				if ( node.length ) {
				  node.attr( 'id', '' );
				}
				window.location.hash = hash;
				if ( node.length ) {
				  node.attr( 'id', hash );
				}
				return false;
			}
		});
 	$('body').on('click','#cryout-migration-request', function (event) {
		var index = $("#tab-migrate").index();
		$('#cryout-tabs').tabs("option", "active", index);
	});
 	$('body').on('click','#cryout-license-request', function (event) {
		var index = $("#tab-license").index();
		$('#cryout-tabs').tabs("option", "active", index);
	});
	}
});
