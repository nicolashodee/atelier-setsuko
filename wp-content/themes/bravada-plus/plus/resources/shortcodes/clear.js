tinymce.PluginManager.add('cryout_short_clear', function(editor, url) {
    editor.addButton('cryout_short_clear', {
        tooltip: 'Clear',
        icon: 'cryout-clear',
        onclick: function() {
			prefix = '';
			if ( typeof cryout_shortcodes_prefix != 'undefined' ) prefix = cryout_shortcodes_prefix;
            editor.insertContent('['+prefix+'clear]');
        }
    });
});
