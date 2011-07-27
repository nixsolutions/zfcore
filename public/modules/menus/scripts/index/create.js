/**
 * 
 */
var mvc = "Route";
var uri = "Link";
var idSelectedRoute = null;
var idSelectedModule = null;
var idSelectedController = null;
function changedLinkType(el){
    var result = dojo.attr(el, 'value');
    var typeRoute = dojo.byId('type-route');
    var typeUri = dojo.byId("type-uri");
    
    if (result == uri) {
        dojo.style(typeUri, "display", "block");
        dojo.style(typeRoute, "display", "none");
        if (idSelectedRoute) {
            var selectedRoute = dojo.byId(idSelectedRoute);
            dojo.style(selectedRoute, "display", "none");
        }
        dojo.query(".routes input").attr({
             disabled: "disabled"
         });
    } else if (result == mvc) {
        dojo.style(typeUri, "display", "none");
        dojo.style(typeRoute, "display", "block");
        changedRoute(dojo.byId('route'));
    }
}

function changedRoute(el){
    var result = dojo.attr(el, 'value');
    if (idSelectedRoute) {
        var selectedRoute = dojo.byId(idSelectedRoute);
        dojo.style(selectedRoute, "display", "none");
    }
    idSelectedRoute = 'route-' + result;
    
     dojo.query(".routes input").attr({
         disabled: "disabled"
     });
     
    dojo.query(".routes #" + idSelectedRoute + " input").removeAttr("disabled");

    var selectedRoute = dojo.byId(idSelectedRoute);

    dojo.style(selectedRoute, "display", "block");
}

function addParam() {
    var nameNewParam = dojo.query(".routes #" + idSelectedRoute + " .newparam input").attr("value");
    dojo.query(".routes #" + idSelectedRoute + " .newparam input").attr("value", '');
    var html = "";
    if (nameNewParam[0].length > 0) {
        html = "<label>" + nameNewParam + "</label><br />";
        html += '<div class="dijit dijitReset dijitInline dijitLeft dijitTextBox">';
        html += '    <input class="dijitReset dijitInputInner" type="text" name="params[' + nameNewParam + ']" value="">';
        html += "</div><br />";
        dojo.query(".routes #" + idSelectedRoute + " .new-parameters").forEach(function(node, index, array){
            node.innerHTML += html;
        });
    }
}
function changeModule(el){
    
    var moduleName = dojo.attr(el, 'value');

    if (idSelectedModule) {
        var selectedModule = dojo.byId(idSelectedModule);
        dojo.style(selectedModule, "display", "none");
    }
    if (idSelectedController && dojo.byId(idSelectedController + 's')) {
        dojo.style(dojo.byId(idSelectedController + 's'), "display", "none");
    }
    
    if (moduleName != '---') {

        idSelectedModule = 'module-' + moduleName;
        dojo.query(".modules input").attr({disabled: "disabled"});
         
        dojo.query(".modules #" + idSelectedModule + " input").removeAttr("disabled");

        var selectedModule = dojo.byId(idSelectedModule);

        dojo.style(selectedModule, "display", "block");
    }
}

function getActions(el,moduleName) {
    
    
    var controllerName = dojo.attr(el, 'value');

    if (controllerName != '---') {
           
           if (idSelectedController) {
            dojo.style(dojo.byId(idSelectedController), "display", "none");
            dojo.style(dojo.byId(idSelectedController + 's'), "display", "none");
            dojo.style(dojo.byId('widget_' + idSelectedController), "display", "none");
        }
           idSelectedController = moduleName + '-' + controllerName + "-action";

           if (dojo.byId('widget_' + idSelectedController) != true) {
               var actionsStore = new dojo.data.ItemFileReadStore({
                url:"/menus/index/get-actions/m/" + moduleName + "/c/" + controllerName
            });
               if (actionsStore) {
                   var filteringSelect = new dijit.form.ComboBox({
                    id: idSelectedController,
                    name: "params[action]",
                    value: "index",
                    store: actionsStore,
                    searchAttr: "name"
                },
                idSelectedController);
               } else {
                   alert('Error loading actions');
               }
           }
        dojo.style(dojo.byId('widget_' + idSelectedController), "display", "block");

        if (dojo.byId(idSelectedController + 's')) {
            dojo.style(dojo.byId(idSelectedController + 's'), "display", "block");
        }
    }
}
dojo.ready(function() {
    changedLinkType(document.getElementById('linkType'));
});