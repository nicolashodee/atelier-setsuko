tinymce.PluginManager.add('cryout_short_icons', function(editor, url) {
    editor.addButton('cryout_short_icons', {
        tooltip : 'Icons',
        icon : 'cryout-icons',
        onclick : function() {
            tinymce.activeEditor.windowManager.open({
                title : 'Icons',
                url : url + '/icons.php',
                width : 720,
                height : 480
            });
        }
    });
});
