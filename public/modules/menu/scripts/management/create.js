/**
 *
 */
(function($){

    /**
     * @param string label
     * @param string id
     * @return string
     */
    function getSelect(label, name) {
        return '<dt><label for="'+ name +'s-list">' +label+ '</label></dt><dd>'
            + '<select name="params['+name+']" class="ui-state-default ui-corner-all" id="'+name+'s-list">'
            + '</select></dd>' ;
    }

    $(function(){
        var _wrap;

        /** init */
        (function(){
            $('#route-element').after('<div id="routes-wrapper"/>');
            _wrap = $('#routes-wrapper');
            $.post($('#getModules').val(), function(res){
                var options = '', r = res || [];
                for(var i in r) {
                    options += '<option value="' +r[i]+ '">' +r[i]+ '</option>';
                }
                _wrap.append(getSelect('Modules:', 'module'));
                $('#modules-list').html(options);
            });
            /** hidden by default */
            if('mvc' == $('#linkType').val()) {
                $('#uri-element, #uri-label').hide();
            } else {
                $('#route-element, #route-label, #routes-wrapper').hide();
            }
        })();

        /**  changing type - uri or mvc */
        $('#linkType').change(function(){
            $('#uri-element, #uri-label, #route-element, #route-label, #routes-wrapper').toggle();
        });

        /** select module */
        $('#modules-list').live('change', function(){
            $.post($('#getControllers').val(), {m:$(this).val()}, function(res) {
                var options = '', r = res || [];
                for(var i in r) {
                    options += '<option value="' +r[i]+ '">' +r[i]+ '</option>';
                }
                if (!$('#controllers-list').length) {
                    _wrap.append(getSelect('Controllers:', 'controller'));
                }
                $('#controllers-list').html(options);
            });
        });

        /** select controller */
        $('#controllers-list').live('change', function(){
            $.post($('#getActions').val(), {c:$(this).val(), m:$('#modules-list').val()}, function(res) {
                var options = '', r = res || [];
                for(var i in r) {
                    options += '<option value="' +r[i]+ '">' +r[i]+ '</option>';
                }
                if (!$('#actions-list').length) {
                    _wrap.append(getSelect('Actions:', 'action'));
                }
                $('#actions-list').html(options);
            });
        });

        /** add extra params */
        $('#actions-list').live('change', function(){
            if (!$('#newparam-wrapper').length) {
                var template = '<div id="newparam-wrapper">'
                    + '<input type="text" name="newparam" id="newparam"/>'
                    + '<input type="button" name="submit" value="Add param" id="newparam-button">'
                    + '</div>';
                _wrap.append(template);
            }
        });

        /** add extra param */
        $('#newparam-button').live('click', function(){
            var val = $('#newparam').val();
            if(val) {
                var template = '<dt><label for="newparam'+ val +'">' + val + ':</label></dt><dd>'
                    + '<input name="params['+val+']" class="ui-state-default ui-corner-all" id="'+ val +'"/></dd>' ;
                $('#newparam-wrapper').before(template);
                $('#newparam').val('');
            }
        });


    });
})(jQuery);

//var mvc = "Route";
//var uri = "Link";
//var idSelectedRoute = null;
//var idSelectedModule = null;
//var idSelectedController = null;
//function changedLinkType(el){
//    var result = dojo.attr(el, 'value');
//    var typeRoute = dojo.byId('type-route');
//    var typeUri = dojo.byId("type-uri");
//
//    if (result == uri) {
//        dojo.style(typeUri, "display", "block");
//        dojo.style(typeRoute, "display", "none");
//        if (idSelectedRoute) {
//            var selectedRoute = dojo.byId(idSelectedRoute);
//            dojo.style(selectedRoute, "display", "none");
//        }
//        dojo.query(".routes input").attr({
//             disabled: "disabled"
//         });
//    } else if (result == mvc) {
//        dojo.style(typeUri, "display", "none");
//        dojo.style(typeRoute, "display", "block");
//        changedRoute(dojo.byId('route'));
//    }
//}
//
//function changedRoute(el){
//    var result = dojo.attr(el, 'value');
//    if (idSelectedRoute) {
//        var selectedRoute = dojo.byId(idSelectedRoute);
//        dojo.style(selectedRoute, "display", "none");
//    }
//    idSelectedRoute = 'route-' + result;
//
//     dojo.query(".routes input").attr({
//         disabled: "disabled"
//     });
//
//    dojo.query(".routes #" + idSelectedRoute + " input").removeAttr("disabled");
//
//    var selectedRoute = dojo.byId(idSelectedRoute);
//
//    dojo.style(selectedRoute, "display", "block");
//}
//
//function addParam() {
//    var nameNewParam = dojo.query(".routes #" + idSelectedRoute + " .newparam input").attr("value");
//    dojo.query(".routes #" + idSelectedRoute + " .newparam input").attr("value", '');
//    var html = "";
//    if (nameNewParam[0].length > 0) {
//        html = "<label>" + nameNewParam + "</label><br />";
//        html += '<div class="dijit dijitReset dijitInline dijitLeft dijitTextBox">';
//        html += '    <input class="dijitReset dijitInputInner" type="text" name="params[' + nameNewParam + ']" value="">';
//        html += "</div><br />";
//        dojo.query(".routes #" + idSelectedRoute + " .new-parameters").forEach(function(node, index, array){
//            node.innerHTML += html;
//        });
//    }
//}
//function changeModule(el){
//
//    var moduleName = dojo.attr(el, 'value');
//
//    if (idSelectedModule) {
//        var selectedModule = dojo.byId(idSelectedModule);
//        dojo.style(selectedModule, "display", "none");
//    }
//    if (idSelectedController && dojo.byId(idSelectedController + 's')) {
//        dojo.style(dojo.byId(idSelectedController + 's'), "display", "none");
//    }
//
//    if (moduleName != '---') {
//
//        idSelectedModule = 'module-' + moduleName;
//        dojo.query(".modules input").attr({disabled: "disabled"});
//
//        dojo.query(".modules #" + idSelectedModule + " input").removeAttr("disabled");
//
//        var selectedModule = dojo.byId(idSelectedModule);
//
//        dojo.style(selectedModule, "display", "block");
//    }
//}
//
//function getActions(el,moduleName) {
//
//
//    var controllerName = dojo.attr(el, 'value');
//
//    if (controllerName != '---') {
//
//           if (idSelectedController) {
//            dojo.style(dojo.byId(idSelectedController), "display", "none");
//            dojo.style(dojo.byId(idSelectedController + 's'), "display", "none");
//            dojo.style(dojo.byId('widget_' + idSelectedController), "display", "none");
//        }
//           idSelectedController = moduleName + '-' + controllerName + "-action";
//
//           if (dojo.byId('widget_' + idSelectedController) != true) {
//               var actionsStore = new dojo.data.ItemFileReadStore({
//                url:"/menu/management/get-actions/m/" + moduleName + "/c/" + controllerName
//            });
//               if (actionsStore) {
//                   var filteringSelect = new dijit.form.ComboBox({
//                    id: idSelectedController,
//                    name: "params[action]",
//                    value: "index",
//                    store: actionsStore,
//                    searchAttr: "name"
//                },
//                idSelectedController);
//               } else {
//                   alert('Error loading actions');
//               }
//           }
//        dojo.style(dojo.byId('widget_' + idSelectedController), "display", "block");
//
//        if (dojo.byId(idSelectedController + 's')) {
//            dojo.style(dojo.byId(idSelectedController + 's'), "display", "block");
//        }
//    }
//}
//dojo.ready(function() {
//    changedLinkType(document.getElementById('linkType'));
//});