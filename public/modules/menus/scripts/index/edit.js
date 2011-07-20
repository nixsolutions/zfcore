/**
 * 
 */
var mvc = "Route";
var uri = "Link";
var idSelectedRoute = null;

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

dojo.ready(function(){
    changedLinkType(document.getElementById('linkType'));
});
