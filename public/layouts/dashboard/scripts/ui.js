var animateNext;

function showSubMenu(jNode)
{
    jNode.addClass('active');
    animateNext = null;
    if (!$("ul.navigation > li ul").is(':animated')) {
        jNode.children('ul').slideDown();
    } else {
        animateNext = jNode;
    }
}

function hideSubMenu(jNode) {
    animateNext = null;
    jNode.removeClass('active');
    jNode.children('ul').slideUp();
}

setInterval(function(){
        if (animateNext) {
            hideSubMenu(animateNext);
            showSubMenu(animateNext);
        }
    }, 500
);

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

    $("ul.navigation > li").click(function(e){
        if ($(this).hasClass('active')) {
            hideSubMenu($(this));
            return ;
        }
        showSubMenu($(this));
    });

    // move text on hover
    $("ul.navigation > li > a, ul.navigation > li > span")
    .mouseover(function(){
        $(this).animate({
            paddingRight : '+=15'
        },300);
    }).mouseout(function(){
        $(this).animate({
            paddingRight : '-=15'
        },300);
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