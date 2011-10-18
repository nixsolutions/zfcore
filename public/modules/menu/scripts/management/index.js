(function($){
    $(function(){
        /**
         * move selected rows up
         */
        $('#up-button').click(function(){
            var url = this.href;
            $("input[type=checkbox]:checked").each(function(){
                $.ajax({
                  type: 'POST',
                  url: url,
                  data: {id: /[\d]+/.exec( $(this).attr('name'))}
                });
            });
            $('#grid').data('plugin_grid').refresh();
            return false;
        });

        /**
         * move selected items down
         */
        $('#down-button').click(function(){
            var url = this.href;
            $("input[type=checkbox]:checked").each(function(){
                $.ajax({
                  type: 'POST',
                  url: url,
                  data: {id: /[\d]+/.exec( $(this).attr('name'))}
                });
            });
            $('#grid').data('plugin_grid').refresh();
            return false;
        });

    });
})(jQuery);
