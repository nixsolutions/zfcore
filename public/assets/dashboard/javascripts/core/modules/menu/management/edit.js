;(function(core, $, window, undefined) {

    /** init namespace */
    var edit = core.namespace('core.modules.menu.management.edit');

    /** init module */
    edit.init = function() {
        core.modules.menu.management.create.init();
    };
})(core, jQuery, window);