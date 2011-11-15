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