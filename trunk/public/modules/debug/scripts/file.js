dojo.addOnLoad(function(){
    dojo.setObject("page.FileReader.tree", dijit.byId('tree'));
    dojo.setObject("page.FileReader.loadFile", function(id){
        dojo.xhrGet({
            content : {
                id : id
            },
            url: '/debug/file/load',
            handleAs: "json",
            load: function(response){
                dojo.byId("fileContent").innerHTML = "";
                dojo.create("h1", {
                    innerHTML: response['name']
                }, "fileContent", "last");
                dojo.create("h4", {
                    innerHTML: response['path']
                }, "fileContent", "last");
                dojo.create("h3", {
                    innerHTML: response['mime']
                }, "fileContent", "last");
                dojo.create("textarea", {
                    innerHTML: response['content'],
                    class: 'file-textarea'
                }, "fileContent", "last");
                return response;
            },

            error: function(response, ioArgs) {
                console.log("failed xhrGet", response, ioArgs);
                return response;
            }
        });
    });
    dojo.connect(page.FileReader.tree, 'onLoad',function(e){
        page.FileReader.tree.model.query = null;
    });
});