;(function($) {
    $(document).ready(function(){
        // show/hide message from system
        $('#messages').slideDown(function() {
            setTimeout(function(){
                jQuery('#messages').slideUp();
            }, 3200);
        });
    });
})(jQuery);