tinymce.PluginManager.add('cryout_short_tabs', function(editor, url) {
    editor.addButton('cryout_short_tabs', {
        tooltip: 'Tabs',
        icon: 'cryout-tabs',
        onclick: function() {
            tinymce.activeEditor.windowManager.open({
                title: 'Tabs',
                url: url + '/tabs.html',
                width: 720,
                height: 480
            });
        }
    });
});
