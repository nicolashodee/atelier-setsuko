tinymce.PluginManager.add('cryout_short_grid', function(editor, url) {
    editor.addButton('cryout_short_grid', {
        type: 'menubutton',
        tooltip: 'Grid',
        icon: 'cryout-grid',
        menu: [
            { text: '12 Columns', onclick: function() {
				prefix = ''; if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
				editor.insertContent('['+prefix+'row class="row"]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-1"]Text[/'+prefix+'col]<br class="nc"/>[/'+prefix+'row]'); }
			},
            { text: '6 Columns',  onclick: function() {
				prefix = ''; if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
				editor.insertContent('['+prefix+'row class="row"]<br class="nc"/>['+prefix+'col class="col-sm-2"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-2"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-2"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-2"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-2"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-2"]Text[/'+prefix+'col]<br class="nc"/>[/'+prefix+'row]'); }
			},
            { text: '4 Columns',  onclick: function() {
				prefix = ''; if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
				editor.insertContent('['+prefix+'row class="row"]<br class="nc"/>['+prefix+'col class="col-sm-3"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-3"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-3"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-3"]Text[/'+prefix+'col]<br class="nc"/>[/'+prefix+'row]'); }
			},
            { text: '3 Columns',  onclick: function() {
				prefix = ''; if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
				editor.insertContent('['+prefix+'row class="row"]<br class="nc"/>['+prefix+'col class="col-sm-4"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-4"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-4"]Text[/'+prefix+'col]<br class="nc"/>[/'+prefix+'row]'); }
			},
            { text: '2 Columns',  onclick: function() {
				prefix = ''; if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
				editor.insertContent('['+prefix+'row class="row"]<br class="nc"/>['+prefix+'col class="col-sm-6"]Text[/'+prefix+'col]<br class="nc"/>['+prefix+'col class="col-sm-6"]Text[/'+prefix+'col]<br class="nc"/>[/'+prefix+'row]'); }
			},
            { text: '1 Columns',  onclick: function() {
				prefix = ''; if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
				editor.insertContent('['+prefix+'row class="row"]<br class="nc"/>['+prefix+'col class="col-sm-12"]Text[/'+prefix+'col]<br class="nc"/>[/'+prefix+'row]'); }
			},
            {
                text: 'Custom Grid',
                onclick: function() {
                    tinymce.activeEditor.windowManager.open({
                        title: 'Custom Grid',
                        url: url + '/grid.html',
                        width: 780,
                        height: 600
                    });
                }
            }
        ]
    });
});
