;(function(core, $, window, undefined) {

    /** init namespace */
    var base = core.namespace('core.modules.base');

    /** init module */
    base.init = function() {

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