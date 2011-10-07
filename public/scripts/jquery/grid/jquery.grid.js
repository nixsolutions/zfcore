(function($) {
    var defaults = {}
      , methods = {
        page: function(page) {
            var _e = $(this)
              , data = $.extend({}, _e.data('data'), { page: page });

            _e.data('data', data);

            methods.refresh.call(this);
        },
        order: function(column, direction) {
            var _e = $(this)
              , data = $.extend({}, _e.data('data'), {
                orderColumn: column,
                orderDirection: direction || 'ASC'
            });

            _e.data('data', data);

            methods.refresh.call(this);
        },
        refresh: function() {
            var _e = $(this);
            $.post(_e.data('url'), _e.data('data'), function(res) {
                _e.html(res);
            });
        },
        init: function(params) {
            var _e = $(this)
              , that = this
              , options = {};

            /** check if grid is not initialized */
            if (!_e.data('grid')) {

                /** merge options */
                $.extend(options, defaults, _e.data(), params || {});

                /** check if url is set */
                if (!options.url) {
                    $.error('Url is not set');
                }

                /** init plugin */
                _e.data('grid', true)
                  .data('url', options.url)
                  .data('data', {});

                /** listen changing page */
                _e.delegate('.pagination a', 'click', function() {
                    var page = $(this).data('page');
                    if (!!page) {
                        methods.page.call(that, page);
                    }
                    return false;
                });

                /** listen changing ordering */
                _e.delegate('th.header', 'click', function() {
                    var data = $(this).data();
                    data.direction = data.direction.toUpperCase() == 'ASC' ? 'DESC' : 'ASC';
                    methods.order.call(that, data.column, data.direction);
                });

                /** refresh grid */
                methods.refresh.call(this);
            }
        }
    };

    $.fn.grid = function(method) {
        var args = arguments;
        if (!!methods[method]) {
            this.each(function(i, e) {
                methods[method].apply(e, Array.prototype.slice.call(args, 1));
            });
        } else if (typeof method === 'object' || !method) {
            this.each(function(i, e) {
                methods.init.apply(e, args);
            });
        } else {
            $.error('Method "' + method + '" does not exist');
        }

        return this;
    };
})(jQuery);