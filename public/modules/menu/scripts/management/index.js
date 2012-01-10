/**
 *
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2012 NIX Solutions (http://www.nixsolutions.com)
 */
$(function() {
    
    $('.grid-buttons').delegate('a#up-button', 'click', function(e) {
        e.stopPropagation();
        var url = this.href, res = [];
        $('#grid').find("input:checked").each(function() {
              res.push(this.value);
        });
        if(!res.length) {
              alert('No row selected');
        } else if(res.length == 1) {
            $.post(url, { id: res[0] }, function() {
                $('#grid').data('plugin_grid').refresh();
            });
        } else {
            alert('Many row selected');
        }
        return false;
    });
    
    $('.grid-buttons').delegate('a#down-button', 'click', function(e) {
        e.stopPropagation();
        var url = this.href, res = [];
        $('#grid').find("input:checked").each(function() {
              res.push(this.value);
        });
        if(!res.length) {
              alert('No row selected');
        } else if(res.length == 1) {
            $.post(url, { id: res[0] }, function() {
                $('#grid').data('plugin_grid').refresh();
            });
        } else {
            alert('Many row selected');
        }
        return false;
    });
});
