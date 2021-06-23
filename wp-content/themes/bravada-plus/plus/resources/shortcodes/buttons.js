(function() {
    tinymce.PluginManager.add('cryout_short_buttons', function(editor, url) {
        editor.addButton('cryout_short_buttons', {
            tooltip: 'Buttons',
            icon: 'cryout-buttons',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Button',
					width: 720,
					height: 360,
					resizable: true,
                    body: [{
						type: 'listbox',
                        name: 'size',
						'values': [
							{ text: 'Mini', value: 'xs' },
							{ text: 'Small', value: 'sm' },
							{ text: 'Normal', value: 'md' },
							{ text: 'Large', value: 'lg' },
							{ text: 'Block', value: 'block' }
						],
						value: 'md',
                        label: 'Size'
					},{
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
							{ text: 'Link', value: 'link' },
							// { text: 'Custom', value: 'custom' }
						],
						value: 'primary',
                        label: 'Type'
                    },{
                        type: 'textbox',
                        name: 'text',
                        value: current_text,
						placeholder: "Button Text",
                        label: 'Text'
                    },{
                        type: 'textbox',
                        name: 'link',
                        value: "#",
                        label: 'Link'
                    },{
						type: 'listbox',
                        name: 'target',
						'values': [
							{ text: '_self', value: '_self' },
							{ text: '_blank', value: '_blank' }
						],
						value: '_self',
                        label: 'Target'
					}],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
						editor.insertContent('['+prefix+'button size="'+e.data.size+'" type="'+e.data.type+'" href="'+e.data.link+'" target="'+e.data.target+'"]'+e.data.text+'[/'+prefix+'button]');
                    }
                });
            }
        });
    });
})();
