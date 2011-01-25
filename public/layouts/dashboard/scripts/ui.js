dojo.addOnLoad(function(){

    // hide all sub menu
    dojo.query("ul.navigation > li ul").style('display','none');

    dojo.query("ul.navigation > li.active ul").style('display','block');

    // click in main menu
    dojo.query("ul.navigation > li > span").onclick(function(){
        if (this.parentNode.className == 'active' ) {
            return false;
        }
        dojo.query('ul.navigation > li.active').removeClass('active');
        dojo.addClass(this.parentNode, 'active');

        dojo.query("ul.navigation > li ul").forEach(function(node){

            if ( dojo.style(node, 'display') != 'none') {
                dojo.fx.wipeOut({
                    node: node,
                    duration: 500
                }).play();
            }
        });

        dojo.fx.wipeIn({
            node: dojo.query('ul',this.parentNode)[0],
            duration: 500
        }).play();
    });

    // fly left on mouse over
    dojo.query("ul.navigation > li > a, ul.navigation > li > span").onmouseover(function(){
        dojo.animateProperty({ node: this,
                               duration:200,
                               properties: {
                                   paddingRight: { start: 15, end: 25, unit:"px"  }  // ������������
                               }
                            }).play();
        return false;
    });

    // fly back on mouse out
    dojo.query("ul.navigation > li > a, ul.navigation > li > span").onmouseout(function(){

        dojo.animateProperty({ node: this,
                               duration:500,
                               properties: {
                                   paddingRight: { start: 25, end: 15, unit:"px"  }  // ������������
                               }
                            }).play();
        return false;
    });
});