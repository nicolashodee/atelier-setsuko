(function() {
    tinymce.PluginManager.add('cryout_short_wells', function(editor, url) {
        editor.addButton('cryout_short_wells', {
            tooltip: 'Well',
            icon: 'cryout-wells',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Well',
					width: 720,
					height: 360,
					resizable: true,
                    body: [{
                        type: 'listbox',
                        name: 'size',
						'values': [
							{ text: 'Small', value: 'sm' },
							{ text: 'Medium', value: 'md' },
							{ text: 'Large', value: 'lg' }
						],
                        label: 'Size'
                    },{
                        type: 'textbox',
                        name: 'text',
						size: 200,
						multiline: true,
                        value: current_text,
                        label: 'Text'
                    }],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
						editor.insertContent('['+prefix+'well size="'+e.data.size+'"]'+e.data.text+'[/'+prefix+'well]');
                    }
                });
            }
        });
    });
})();
