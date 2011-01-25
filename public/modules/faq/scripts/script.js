$(function (){  
    $("#faq-content").toggle(function(){
        $("#faq-content-question").hide();
        $("#faq-content").html('[' + translate['show'] + ']');
  	}, function(){
        $("#faq-content-question").show();
        $("#faq-content").html('[' + translate['hide'] + ']');
  	});
});