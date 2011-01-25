dojo.addOnLoad(function()
{
    dojo.setObject("page.LogReader.markId", null);
    dojo.setObject("page.LogReader.markVal", null);
    dojo.setObject("page.LogReader.selected", false);
    dojo.setObject("page.LogReader.countBegin", page.LogReader.addNumber);
    dojo.setObject("page.LogReader.countEnd", page.LogReader.addNumber);
    dojo.setObject("page.LogReader.markString", function(){
        var id     = this.id;
        var elm    = dojo.byId(id);
        var oldelm = dojo.byId(page.LogReader.markId);
        if(page.LogReader.markId != null){
            if(page.LogReader.markId == id){
                if(page.LogReader.selected){
                    dojo.attr(elm, "style", {
                        color: '#000000',
                        backgroundColor: '#FFFFFF'
                    });
                    page.LogReader.selected = false;
                } else {
                   dojo.attr(elm, "style", {
                        color: '#FFFFFF',
                        backgroundColor: '#000000'
                    });
                    page.LogReader.selected = true;
                }
            } else {
               dojo.attr(oldelm, "style", {
                    color: '#000000',
                    backgroundColor: '#FFFFFF'
                });
               dojo.attr(elm, "style", {
                    color: '#FFFFFF',
                    backgroundColor: '#000000'
                });
                page.LogReader.selected = true;
            }
         } else {
               dojo.attr(elm, "style", {
                    color: '#FFFFFF',
                    backgroundColor: '#000000'
                });
            page.LogReader.selected = true;
         }
         page.LogReader.markId = id;
         page.LogReader.markVal = elm.innerHTML;
    });

    dojo.connect(dojo.byId("addButtonBegin"), "onclick", function(){
        dojo.xhrGet({
            content : {
                length : page.LogReader.countBegin,
                id : page.LogReader.logId,
                direction: 'begin',
                string: page.LogReader.beginLogString
            },
            url: '/debug/logs/add',
            handleAs: "json",
            load: function(response){
                var node;
                var id;
                for(var i in response){
                    id = i + page.LogReader.countBegin + 'b';
                    node = dojo.create("div", {
                        innerHTML: response[i],
                        id: id
                    }, "contentBegin", "first");
                    dojo.attr(id, "style", {
                        color: "#000000",
                        marginTop: "5px"
                    });
                    if (response[i] == page.LogReader.markVal) {
                        dojo.attr(id, "style", {
                             color: '#FFFFFF',
                             backgroundColor: '#000000'
                         });
                         page.LogReader.markId = id;
                    }
                    dojo.connect(node, 'onclick', page.LogReader.markString);
                }
                page.LogReader.countBegin += page.LogReader.addNumber;
                return response;
            },

            error: function(response, ioArgs) {
                console.log("failed xhrGet", response, ioArgs);
                return response;
            }
        });
    });

    dojo.connect(dojo.byId("addButtonEnd"), "onclick", function(){
        dojo.xhrGet({
            content : {
                length : page.LogReader.countEnd,
                id : page.LogReader.logId,
                direction: 'end',
                string: page.LogReader.beginLogString
            },
            url: '/debug/logs/add',
            handleAs: "json",
            load: function(response){
                var node;
                var id;
                for(var i in response){
                    id = i + page.LogReader.countEnd + 'e';
                    node = dojo.create("div", {
                        innerHTML: response[i],
                        id: id
                    }, "contentEnd", "last");
                    dojo.attr(id, "style", {
                        color: "#000000",
                        marginTop: "5px"
                    });
                    if (response[i] == page.LogReader.markVal) {
                        dojo.attr(id, "style", {
                             color: '#FFFFFF',
                             backgroundColor: '#000000'
                         });
                         page.LogReader.markId = id;
                    }
                    dojo.connect(node, 'onclick', page.LogReader.markString);
                }
                page.LogReader.countEnd += page.LogReader.addNumber;
                return response;
            },

            error: function(response, ioArgs) {
                console.log("failed xhrGet", response, ioArgs);
                return response;
            }
        });
    });

    dojo.query('.logLines').connect("onclick", page.LogReader.markString);
});
