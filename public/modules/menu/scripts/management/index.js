(function($) {
    /** move selected rows */
    function move(url) {
        var ch = [];
        $("input:checked").each(function() {
            ch.push($(this).val());
        });
        $.post(url, {ids:ch}, function() {
            $('#grid').data('plugin_grid').refresh();
        });
    }
    $(function() {
        /** move selected rows up */
        $('#up-button').click(function() {
            alert('s');
            move(this.url);
            return false;
        });
        /** move selected items down */
        $('#down-button').click(function() {
            alert('s');
            move(this.url);
            return false;
        });
    });
})(jQuery);
