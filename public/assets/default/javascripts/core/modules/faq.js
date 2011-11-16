;(function(core, $, window, undefined) {

    /** init namespace */
    var base = core.namespace('core.modules.faq');

    /** init module */
    base.init = function() {
        $("#faq-content").toggle(function() {
            $("#faq-content-question").hide();
            $("#faq-content").html('[' + translate['show'] + ']');
        }, function() {
            $("#faq-content-question").show();
            $("#faq-content").html('[' + translate['hide'] + ']');
        });
    };
})(core, jQuery, window);