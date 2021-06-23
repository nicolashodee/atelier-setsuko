(function() {
    tinymce.PluginManager.add('cryout_short_labels', function(editor, url) {
        editor.addButton('cryout_short_labels', {
            tooltip: 'Labels',
            icon: 'cryout-labels',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Label',
					width: 720,
					height: 360,
					resizable: true,
                    body: [{
                        type: 'listbox',
                        name: 'type',
						'values': [
							// { text: 'Default', value: 'default' },
							{ text: 'Primary', value: 'primary' },
							{ text: 'Secondary', value: 'secondary' },
							{ text: 'Light', value: 'light' },
							{ text: 'Dark', value: 'dark' },
							{ text: 'Success', value: 'success' },
							{ text: 'Info', value: 'info' },
							{ text: 'Warning', value: 'warning' },
							{ text: 'Danger', value: 'danger' },
							{ text: 'Link', value: 'link' }
						],
						value: 'primary',
                        label: 'Type'
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
						editor.insertContent('['+prefix+'label type="'+e.data.type+'"]'+e.data.text+'[/'+prefix+'label]');
                    }
                });
            }
        });
    });
})();
