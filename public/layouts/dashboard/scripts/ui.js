var animateNext;

function showSubMenu(jNode)
{
    animateNext = null;
    if (!$("ul.navigation > li ul").is(':animated')) {
        jNode.children('ul').slideDown();
    } else {
        animateNext = jNode;
    }
}

function hideSubMenu(jNode) {
    animateNext = null;
    jNode.children('ul').slideUp();
}

setInterval(function() { if (animateNext) showSubMenu(animateNext); }, 500);

$(function(){
	$('.button, input:submit, input:reset').button();
	$('input:text, select, textarea, input:password').addClass('ui-state-default ui-corner-all');
	
	$('.delete').click(function(e){
        if (!confirm(this.title)) {
            e.preventDefault();
        }
    });
	
	// hide all sub menu
    $("ul.navigation > li ul").hide();

 // show active sub menu
    $("ul.navigation > li.active ul").show();

    // fly back on mouse out
    $("ul.navigation > li").hover(function(e){
        showSubMenu($(this));
    }, function(){
        hideSubMenu($(this));
    });
});

;(function($) {
    $(function() {
        $('#messages').slideDown(function() {
            setTimeout(function() {
                $('#messages').slideUp();
            }, 3200);
        });
    });
})(jQuery);