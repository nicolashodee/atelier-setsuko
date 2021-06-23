(function() {
    tinymce.PluginManager.add('cryout_short_lead', function(editor, url) {
        editor.addButton('cryout_short_lead', {
            tooltip: 'Lead',
            icon: 'cryout-lead',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Lead',
					width: 720,
					height: 360,
					resizable: true,
                    body: [{
                        type: 'textbox',
                        name: 'text',
						size: 200,
						multiline: true,
                        value: current_text,
                        label: 'Text'
                    },
                    {
                        type: 'listbox',
                        name: 'fontfamily',
                        'values': [
                            // { text: 'Default', value: 'default' },
                            { text: ' - General Font Family -', value: 'default' },
                            { text: ' - Titles Font Family -', value: 'titles-font' },
                            { text: ' - Headings Font Family -', value: 'headings-font' },
                            { text: 'Helvetica Neue', value: 'helvetica' },
                            { text: 'Segoe UI', value: 'segoeui' },
                            { text: 'Verdana', value: 'verdana' },
                            { text: 'Geneva', value: 'geneva' },
                            { text: 'Futura', value: 'futura' },
                            { text: 'Georgia', value: 'georgia' },
                            { text: 'Times New Roman', value: 'timesnewroman' },
                            { text: 'Palatino Linetype', value: 'palatino' },
                            { text: 'Bakersville', value: 'bakersville' },
                            { text: 'Courier New', value: 'courier' },
                            { text: 'Consolas', value: 'consolas' },
                            { text: 'Monaco', value: 'monaco' }
                        ],
                        value: 'default',
                        label: 'Font Family'
                    },
                    {
                        type: 'listbox',
                        name: 'fontsize',
                        'values': [
                            { text: '90%', value: '90' },
                            { text: '100%', value: '100' },
                            { text: '110%', value: '110' },
                            { text: '120%', value: '120' },
                            { text: '125%', value: '125' },
                            { text: '130%', value: '130' },
                            { text: '140%', value: '140' },
                            { text: '150%', value: '150' }
                        ],
                        value: '100',
                        label: 'Font Size'
                    }],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
						editor.insertContent('['+prefix+'lead fontfamily="'+e.data.fontfamily+'" fontsize="'+e.data.fontsize+'"]'+e.data.text+'[/'+prefix+'lead]');
                    }
                });
            }
        });
    });
})();
