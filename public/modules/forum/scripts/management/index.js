(function($){
    $(function(){
        $('#delete-all-button').click(function(){
            var url = this.href;
            if (confirm('Are you sure you want to delete this?')) {
                $("input[type=checkbox]:checked").each(function(){
                    $.ajax({
                      type: 'POST',
                      url: url,
                      data: {id: /[\d]+/.exec( $(this).attr('name'))}
                    });
                });
                $('#grid').data('plugin_grid').refresh();
            }
            return false;
        });
    });
})(jQuery);
