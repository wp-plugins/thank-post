function thank_post() {
    return "[thanks]";
}

(function() {

    tinymce.create('tinymce.plugins.thankpost', {

        init : function(ed, url){
            ed.addButton('thankpost', {
				title : 'Insert Thanks',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        thank_post()
                        );
                },
                image: url + "/thank.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'Thank-Post',
                author : 'Nulled_Icode',
                authorurl : 'http://icode.it.tc',
                infourl : '',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('thankpost', tinymce.plugins.thankpost);
    
})();
