/**
 * 
 */
var mvc = "Route";
var uri = "Link";
var idSelectedRoute = null;
var idSelectedModule = null;
var idSelectedController = null;
var countParametrs = 0;
function changedLinkType() {
    
    var linkType = $('#linkType').val();

    if (linkType == 'uri') {
        $(".type-uri").css("display", "block");
        $(".type-route").css("display", "none");
        if (idSelectedRoute) {
            $("#" + idSelectedRoute).css("display", "none");
        }
        $(".routes input").attr("disabled", "disabled");

    } else if (linkType == 'mvc') {
        $(".type-uri").css("display", "none");
        $(".type-route").css("display", "block");
        changedRoute();
    }
}

function changedRoute() {
    var routeName = $("#route").val();
    if (idSelectedRoute) {
        $("#" + idSelectedRoute).css("display", "none");
    }
    
    idSelectedRoute = 'route-' + routeName;
    $(".routes input").attr("disabled", "disabled");
     
    $(".routes #" + idSelectedRoute + " input").removeAttr("disabled");
    $("#" + idSelectedRoute).css("display", "block");
}

function addParam() {
    
    var nameNewParam = $(".routes #" + idSelectedRoute + " .newparam").val();
    $(".routes #" + idSelectedRoute + " .newparam").val('');
    var html = "";
    if (nameNewParam.length > 0) {
        countParametrs++;
        html = '<div id="param_' + countParametrs + '"><label>' + _escape(nameNewParam) + '</label><br />';
        html += '<div>';
        html += '    <input class="ui-state-default ui-corner-all" type="text" name="params[' + nameNewParam + ']" value="">';
        html += '    <input class="ui-button ui-widget ui-state-default ui-corner-all" onclick="removeParam(' + countParametrs + ');" alt="Remove parameter" type="button" name="remove" value="-">';
        html += "</div></div>";
        $(".routes #" + idSelectedRoute + " .new-parameters").append(html);
    }
}
function removeParam(id) {
    $('#param_' + id).remove();
}
function changeModule() {
    
    var moduleName = $('#mvcModule').val();
    $('.controller').removeAttr("disabled");

    if (idSelectedModule) {
        $('#' + idSelectedModule).css("display", "none");
    }
    if (idSelectedController && $('#' + idSelectedController + 's')) {
        $('#' + idSelectedController + 's').css("display", "none");
    }
    
    if (moduleName != '---') {

        idSelectedModule = 'module-' + moduleName;
        $(".modules input").attr("disabled", "disabled");
         
        $(".modules #" + idSelectedModule + " input").removeAttr("disabled");

        $("#" + idSelectedModule).css("display", "block");

    }
}

function getActions(moduleName) {
    var controllerName = $('#' + moduleName + '-Controller').val();
    $('.controller').attr("disabled", "disabled");
    $('#' + moduleName + '-Controller').removeAttr("disabled");

    if (controllerName != '---') {
           
        if (idSelectedController) {
            $('#' + idSelectedController).css("display", "none");
            $('#' + idSelectedController + 's').css("display", "none");
        }
        idSelectedController = moduleName + '-' + controllerName + "-action";

        $.get(
            "/menu/management/get-actions/m/" + moduleName + "/c/" + controllerName,
            {},
            function(data) {
                var html = '<select class="ui-state-default ui-corner-all" name="params[action]">';
                for (var action in data) {
                    html += '<option label="' + data[action] + '" value="' + data[action] + '">' + data[action] + '</option>';
                }
                html += '</select>';
                $('#' + moduleName + '-' + controllerName + '-action').html(html);

            }
        );

        $('#' + idSelectedController).css("display", "block");

        if ($('#' + idSelectedController + 's')) {
            $('#' + idSelectedController + 's').css("display", "block");
        }
    }
    
}

function _escape(str) {
    return str.replace(">","&gt;").replace("<","&lt;");
}