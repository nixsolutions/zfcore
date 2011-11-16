;(function(core, $, window, undefined) {

    /** init namespace */
    var base = core.namespace('core.modules.base');

    /** init module */
    base.init = function() {

        core.widgets.sidebar.enable();

        $('.button, input:submit, input:reset').button();
        $('input:text, select, textarea, input:password').addClass('ui-state-default ui-corner-all');

        $('.delete').click(function(e) {
            return confirm(this.title);
        });

        /** showing flash messages */
        core.util.bindFinalized(function() {
            $('#messages').slideDown(function() {
                setTimeout(function() {
                    $('#messages').slideUp();
                }, 3200);
            });
        });
    };
})(core, jQuery, window);