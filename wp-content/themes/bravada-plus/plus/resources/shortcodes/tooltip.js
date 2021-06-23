(function() {
    tinymce.PluginManager.add('cryout_short_tooltip', function(editor, url) {
        editor.addButton('cryout_short_tooltip', {
            tooltip: 'Tooltip',
            icon: 'cryout-tooltip',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

				current_text = tinymce.activeEditor.selection.getContent({format : 'text'});
				if (current_text.length<1) current_text = '';

                // Open window
                editor.windowManager.open({
                    title: 'Tooltip',
					width: 720,
					height: 360,
					resizable: true,
                    body: [{
                        type: 'textbox',
                        name: 'text',
						multiline: true,
                        value: current_text,
                        label: 'Text'
                    },{
                        type: 'textbox',
                        name: 'title',
                        value: '',
                        label: 'Title'
                    },{
                        type: 'listbox',
                        name: 'placement',
						'values': [
							{ text: 'Top', value: 'top' },
							{ text: 'Right', value: 'right' },
							{ text: 'Bottom', value: 'bottom' },
							{ text: 'Left', value: 'left' }
						],
                        value: 'top',
                        label: 'Placement'
                    },{
                        type: 'listbox',
                        name: 'trigger',
						'values': [
							{ text: 'Hover', value: 'hover' },
							{ text: 'Focus', value: 'focus' },
							{ text: 'Click', value: 'click' }
						],
                        value: 'hover',
                        label: 'Trigger'
                    }],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
						editor.insertContent('['+prefix+'tooltip placement="'+e.data.placement+'" trigger="'+e.data.trigger+'" title="'+e.data.title+'"]'+e.data.text+'[/'+prefix+'tooltip]');
                    }
                });
            }
        });
    });
})();
