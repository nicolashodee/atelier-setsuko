tinymce.PluginManager.add('cryout_short_divider', function(editor, url) {
    editor.addButton('cryout_short_divider', {
        tooltip: 'Divider',
        icon: 'cryout-divider',
        onclick: function() {
			prefix = '';
			if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
            //editor.insertContent('['+prefix+'divider]');

            // Open window
            editor.windowManager.open({
                title: 'Divider',
                width: 640,
                height: 360,
                resizable: true,
                body: [{
                    type: 'listbox',
                    name: 'height',
                    'values': [
                        { text: '1px', value: '1' },
                        { text: '2px', value: '2' },
                        { text: '3px', value: '3' },
                        { text: '5px', value: '5' },
                        { text: '10px', value: '10' },
                        { text: '15px', value: '15' }
                    ],
                    value: '1',
                    label: 'Divider Height'
                },{
                    type: 'listbox',
                    name: 'margin_top',
                    'values': [
                        { text: '0px', value: '0' },
                        { text: '10px', value: '10' },
                        { text: '20px', value: '20' },
                        { text: '30px', value: '30' },
                        { text: '40px', value: '40' },
                        { text: '50px', value: '50' }
                    ],
                    value: '20',
                    label: 'Margin Top'
                },{
                    type: 'listbox',
                    name: 'margin_bottom',
                    'values': [
                        { text: '0px', value: '0' },
                        { text: '10px', value: '10' },
                        { text: '20px', value: '20' },
                        { text: '30px', value: '30' },
                        { text: '40px', value: '40' },
                        { text: '50px', value: '50' }
                    ],
                    value: '20',
                    label: 'Margin Bottom'
                }],
                buttons: [{
                    text: 'Insert',
                    subtype: 'primary',
                    onclick: 'submit'
                }],
                onsubmit: function(e) {
                    // Insert content when the window form is submitted
                    editor.insertContent('['+prefix+'divider height="'+e.data.height+'" margin_top="'+e.data.margin_top+'" margin_bottom="'+e.data.margin_bottom+'"]');
                }
            });
        }
    });
});
