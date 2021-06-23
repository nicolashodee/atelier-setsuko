tinymce.PluginManager.add('cryout_short_alerts', function(editor, url) {
    editor.addButton('cryout_short_alerts', {
        tooltip: 'Alerts',
        icon: 'cryout-alerts',
        onclick: function() {
            tinymce.activeEditor.windowManager.open({
                title: 'Add an alert',
                url: url + '/alerts.html',
                width: 720,
                height: 480
            });
        }
    });
});
