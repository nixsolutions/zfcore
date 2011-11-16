;(function($, window, undefined) {

    /** core */
    var core = {};

    /** generate namespace */
    core.namespace = function(namespace) {
        var parts = namespace.split('.')
          , root = window
          , i
          , l;

        for (i = 0, l = parts.length; i < l; i++) {
            root[parts[i]] = root[parts[i]] || {};
            root = root[parts[i]];
        }
        return root;
    };

    window.core = core;
})(jQuery, window);