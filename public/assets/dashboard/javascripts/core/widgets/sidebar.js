;(function(core, $, window, undefined) {

    /** init namespace */
    var sidebar = core.namespace('core.widgets.sidebar')
      , animateNext;

    function showSubMenu(jNode) {
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

    /** enable widget */
    sidebar.enable = function() {

        setInterval(function() {
            if (animateNext) {
                hideSubMenu(animateNext);
                showSubMenu(animateNext);
            }
        }, 500);

        /** hide all sub menu */
        $("ul.navigation > li ul").hide();

        /** show active sub menu */
        $("ul.navigation > li.active ul").show();

        $("ul.navigation > li").click(function(e) {
            if ($(this).hasClass('active')) {
                hideSubMenu($(this));
                return;
            }
            showSubMenu($(this));
        });

        /** move text on hover */
        $("ul.navigation > li > a, ul.navigation > li > span").mouseover(function() {
            $(this).animate({
                paddingRight : '+=15'
            }, 300);
        }).mouseout(function() {
            $(this).animate({
                paddingRight : '-=15'
            }, 300);
        });
    };
})(core, jQuery, window);