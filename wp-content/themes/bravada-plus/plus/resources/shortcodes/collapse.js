(function() {
    tinymce.PluginManager.add('cryout_short_collapse', function(editor, url) {
        editor.addButton('cryout_short_collapse', {
            tooltip: 'Accordion',
            icon: 'cryout-collapse',
            onclick: function() {

				prefix = '';
				if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;

                // Open window
                editor.windowManager.open({
                    title: 'Accordion',
                    width: 720,
					height: 360,
					resizable: true,
                    body: [{
                        type: 'textbox',
                        name: 'itemnum',
                        value: '3',
                        label: 'Number of items'
                    },{
                        type: 'checkbox',
                        name: 'isopen',
                        checked: false,
                        label: 'Start open'
                    },{
                        type: 'checkbox',
                        name: 'isindependent',
                        checked: false,
                        label: 'Independent items'
                    },{
                        type: 'listbox',
                        name: 'colorscheme',
						'values': [
                            { text: 'Light 1', value: 'light-1' },
                            { text: 'Light 2', value: 'light-2' },
                            { text: 'Dark 1', value: 'dark-1' },
							{ text: 'Dark 2', value: 'dark-2' }
						],
                        value: 'light-1',
                        label: 'Color Scheme'
                    }],
					buttons: [{
						text: 'Insert',
						subtype: 'primary',
						onclick: 'submit'
					}],
                    onsubmit: function(e) {
                        // Insert content when the window form is submitted
                        var uID = guid();
                        var shortcode = '['+prefix+'collapse id="collapse_' + uID + '" scheme="' + e.data.colorscheme + '"]<br class="nc"/>';
                        var num = e.data.itemnum;
                        for (i = 0; i < num; i++) {
                            var id = guid();
                            var title = 'Collapsible Group Item ' + (i + 1);
                            shortcode += '['+prefix+'citem';
                            shortcode += ' title="' + title + '"';
                            shortcode += ' id="citem_' + id + '"';
                            shortcode += (!e.data.isindependent? ' parent="collapse_' + uID + '"': '');
                            shortcode += (e.data.isopen? ' open="true"': '');
                            shortcode += ']<br class="nc"/>';
                            shortcode += 'Collapse content goes here....<br class="nc"/>';
                            shortcode += '[/'+prefix+'citem]<br class="nc"/>';
                        }

                        shortcode += '[/'+prefix+'collapse]';
                        editor.insertContent(shortcode);
                    }
                });
            }
        });
    });

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }
        return s4() + '-' + s4();
    }
})();
