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
            this.element.delegate('th.header', 'click', function() {
                var data = $(this).data();
                data.direction = data.direction.toUpperCase() == 'ASC' ? 'DESC' : 'ASC';
                that.order(data.column, data.direction);
            });

            /** draw grid */
            this.refresh();
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
        refresh: function() {
            var that = this;
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