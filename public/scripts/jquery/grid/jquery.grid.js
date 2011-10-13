;(function($) {
    var pluginName = 'grid'
      , defaults = {};

    function Plugin(element, options) {
        this.element = $(element);
        this.options = $.extend({}, defaults, this.element.data(), options || {}) ;
        this.data = {};
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype = {
        init: function() {
            var that = this;
            if (!this.options.url) {
                $.error('Url is not set');
            }

            /** listen changing page */
            this.element.delegate('.pagination a', 'click', function() {
                var page = $(this).data('page');
                if (!!page) {
                    that.page(page);
                }
                return false;
            });

            /** listen changing ordering */
            this.element.delegate('th.orderable', 'click', function() {
                var data = $(this).data();
                if (!!data.column) {
                    data.direction = data.direction.toUpperCase() == 'ASC' ? 'DESC' : 'ASC';
                    that.order(data.column, data.direction);
                }
            });

            /** draw grid */
            this.refresh();
            this.element.disableSelection();
        },
        page: function(page) {
            this.data.page = page;
            this.refresh();
        },
        order: function(column, direction) {
            this.data.orderColumn = column;
            this.data.orderDirection = direction || 'ASC';
            this.refresh();
        },
        filter: function(column, filter) {
            this.data.filterColumn = column;
            this.data.filterValue = filter;
            this.refresh();
        },
        reset: function() {
            this.data = {};
            this.refresh();
        },
        overlay: function() {
            var styles = {
                "position": "absolute",
                "top": "0",
                "left": "0",
                "background-color": "#fff",
                "z-index": "1000",
                "width": "100%",
                "height": "100%",
                "filter": "progid:DXImageTransform.Microsoft.Alpha(opacity=60)",
                "-moz-opacity": "0.6",
                "-khtml-opacity": "0.6",
                "opacity": "0.6"
            };

            this.element
                .css({ position: "relative" })
                .append($('<div>').css(styles));
        },
        refresh: function() {
            var that = this;
            this.overlay();
            $.post(this.options.url, this.data, function(res) {
                that.element.html(res);
            });
        }
    };

    $.fn[pluginName] = function(options) {
        return this.each(function() {
            var element = $(this);
            if (!element.data('plugin_' + pluginName)) {
                element.data('plugin_' + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery);