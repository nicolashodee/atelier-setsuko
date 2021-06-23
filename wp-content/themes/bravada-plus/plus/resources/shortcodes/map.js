(function() {
    tinymce.PluginManager.add('cryout_short_map', function(editor, url) {
        editor.addButton('cryout_short_map', {
            tooltip: 'Map',
            icon: 'cryout-map',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Map',
					width: 720,
					height: 360,
					resizable: true,
                    body: [{
                        type: 'textbox',
                        name: 'url',
						size: 200,
						multiline: true,
                        value: '',
                        label: 'URL',
						placeholder: 'Enter the Google Maps embeddable URL'
                    },{
                        type: 'textbox',
                        name: 'width',
                        value: '400',
                        label: 'Width'
                    },{
                        type: 'textbox',
                        name: 'height',
                        value: '300',
                        label: 'Height'
                    }],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
						editor.insertContent('['+prefix+'map width="'+e.data.width+'" height="'+e.data.height+'" url="'+e.data.url+'"] '+current_text);
                    }
                });
            }
        });
    });
})();
