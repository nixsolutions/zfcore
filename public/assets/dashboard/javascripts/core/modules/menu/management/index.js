;(function(core, $, window, undefined) {

    /** init namespace */
    var index = core.namespace('core.modules.menu.management.index');

    /** init module */
    index.init = function() {

        /** init buttons */
        $('#up-button, #down-button').click(function() {
            var ch = [];

            $("input:checked").each(function() {
                ch.push($(this).val());
            });

            $.post(this.href, {ids:ch}, function() {
                $('#grid').data('plugin_grid').refresh();
            });

            return false;
        });
    };
})(core, jQuery, window);