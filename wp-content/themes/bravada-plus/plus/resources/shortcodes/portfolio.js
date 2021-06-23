(function() {
    tinymce.PluginManager.add('cryout_short_portfolio', function(editor, url) {
        editor.addButton('cryout_short_portfolio', {
            tooltip: 'Portfolio',
            icon: 'cryout-portfolio',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Portfolio',
					width: 720,
					height: 480,
					resizable: true,
                    body: [{
						type: 'listbox',
                        name: 'include_type',
						'values': cryout_plus_shortcodes_get_portolio_data( 'type' ),
						value: '0',
                        label: 'Type'
					},{
                        type: 'listbox',
                        name: 'include_tag',
						'values': cryout_plus_shortcodes_get_portolio_data( 'tag' ),
						value: '0',
                        label: 'Tag'
                    },{
                        type: 'listbox',
                        name: 'columns',
						'values': [
							{ text: '1', value: 1 },
							{ text: '2', value: 2 },
							{ text: '3', value: 3 },
							{ text: '4', value: 4 },
							{ text: '5', value: 5 },
							{ text: '6', value: 6 },
						],
						value: 2,
                        label: 'Columns'
                    },{
                        type: 'textbox',
                        name: 'showposts',
                        value: "10",
                        label: 'Number of items'
                    },{
						type: 'listbox',
                        name: 'order',
						'values': [
							{ text: 'ASC', value: 'ASC' },
							{ text: 'DESC', value: 'DESC' },
						],
						value: 'ASC',
                        label: 'Sort'
					},{
                        type: 'listbox',
                        name: 'orderby',
						'values': [
							{ text: 'Author', value: 'author' },
							{ text: 'Date', value: 'date' },
							{ text: 'Title', value: 'title' },
							{ text: 'Random', value: 'rand' },
						],
						value: 'date',
                        label: 'Order By'
                    },{
                        type: 'checkbox',
                        name: 'display_types',
                        checked: true,
                        label: 'Display Types'
                    },{
                        type: 'checkbox',
                        name: 'display_tags',
                        checked: true,
                        label: 'Display Tags'
                    },{
                        type: 'checkbox',
                        name: 'display_content',
                        checked: true,
                        label: 'Display Content'
                    },{
                        type: 'checkbox',
                        name: 'display_author',
                        checked: true,
                        label: 'Display Author'
                    }],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
						editor.insertContent('[portfolio display_types="' + e.data.display_types + '" display_tags="' + e.data.display_types + '" display_content="' + e.data.display_content +
                         '" display_author="' + e.data.display_author + '" columns="' + e.data.columns + '" showposts="' + e.data.showposts + '" order="' + e.data.order + '" orderby="' + e.data.orderby +
                         '" include_type="' + e.data.include_type + '" include_tag="' + e.data.include_tag + '"]');
                    }
                });
            }
        });
    });
})();


function cryout_plus_shortcodes_get_portolio_data( what = '' ) {
	output = [ {text: '- Select -', value: '0'} ]

	jQuery.ajax(
			cryout_plus_ajax_backend.ajaxurl,
			{ 'method': 'POST', 'data' : { 'action': 'cryout_plus_portfolio_shortcode_data', 'what': what }, async: false, dataType: 'json',
			'success': function( data ) {
				output = data;
			} }
		).fail(function() {
		output = [ {text: '- No data -', value: '0'} ]
	});
	return output;
}
