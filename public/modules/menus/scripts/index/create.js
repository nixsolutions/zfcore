/**
 * 
 */
var mvc = "Route";
var uri = "Link";
var idSelectedRoute = null;
function changedLinkType(el){
	var result = dojo.attr(el, 'value');
	var typeRoute = dojo.byId('type-route');
	var typeUri = dojo.byId("type-uri");
	
	if (result == uri) {
		dojo.style(typeUri, "display", "block");
		dojo.style(typeRoute, "display", "none");
		if (idSelectedRoute) {
			var selectedRoute = dojo.byId(idSelectedRoute);
			dojo.style(selectedRoute, "display", "none");
		}
		dojo.query(".routes input").attr({
	         disabled: "disabled"
	     });
	} else if (result == mvc) {
		dojo.style(typeUri, "display", "none");
		dojo.style(typeRoute, "display", "block");
		changedRoute(dojo.byId('route'));
		//getRoutes();
	}
	//console.log(result);
	//alert(result);
}
function changedRoute(el){
	var result = dojo.attr(el, 'value');
	if (idSelectedRoute) {
		var selectedRoute = dojo.byId(idSelectedRoute);
		dojo.style(selectedRoute, "display", "none");
	}
	idSelectedRoute = 'route-' + result;
	
	 dojo.query(".routes input").attr({
         disabled: "disabled"
     });
	 
	dojo.query(".routes #" + idSelectedRoute + " input").removeAttr("disabled");
	
	//console.log(result);
	//console.log(idSelectedRoute);
	var selectedRoute = dojo.byId(idSelectedRoute);
	
	//console.log(selectedRoute);
	dojo.style(selectedRoute, "display", "block");
	//alert(result);
}

function addParam() {
	//console.log(dojo.byId("newparam"));
	var nameNewParam = dojo.query(".routes #" + idSelectedRoute + " .newparam input").attr("value");
	console.log(nameNewParam);
	//var nameNewParam = dojo.attr(dojo.byId("newparam"), 'value');
	//dojo.attr(dojo.byId("newparam"), 'value', '');
	dojo.query(".routes #" + idSelectedRoute + " .newparam input").attr("value", '');
	var html = "";
	console.log(nameNewParam);
	console.log(nameNewParam.length);
	if (nameNewParam[0].length > 0) {
		html = "<label>" + nameNewParam + "</label><br />";
		html += '<div class="dijit dijitReset dijitInline dijitLeft dijitTextBox">';
		html += '    <input class="dijitReset dijitInputInner" type="text" name="params[' + nameNewParam + ']" value="">';
		html += "</div><br />";
		console.log(html);
		console.log(dojo.query(".routes #" + idSelectedRoute + " .new-parameters"));
		//dojo.query(".routes #" + idSelectedRoute + " .new-parameters").innerHTML =  html;
		dojo.query(".routes #" + idSelectedRoute + " .new-parameters").forEach(function(node, index, array){
		    // append content to each h2 as a direct child of a <div>
		    node.innerHTML += html;
		});
		//dojo.byId("new-parameters").innerHTML = dojo.byId("new-parameters").innerHTML + html;
	}

    
	console.log('click!'); 
}
dojo.ready(function(){
	changedLinkType(document.getElementById('linkType'));
	//var buttonAddparam = dojo.byId('addparam');
	
	 
	
	
	/*var obj = dojo.byId('linkType');
	
	console.log(obj);
	
	dojo.connect(obj, "onchange", function(){
		console.log('onchange!');
	}); 
	dojo.connect(obj, "onselect", null, function(){
		console.log('onchange');
	}); 
	
	dojo.connect(obj, "onfocus", function(){
		console.log('onfocus!');
	}); 
	dojo.connect(obj, "onblur", function(){
		console.log('onblur!');
	}); */
    //alert("Dojo version " + dojo.version + " is loaded");
});
// вешаем обработчик
//var link = dojo.connect(element, "onclick", null, "update");
// или
    
// или используя анонимную функцию
    //dojo.connect(obj, "onclick", function update() { console.log('click!'); });
 
// убираем наш обработчик
//dojo.disconnect(link);
/*(function($) {
    $(document).ready(function () {
    	$("[name='type']").click(function(){
    		  alert('Вы нажали на элемент "foo"');
    		  //name="type"
    			  
    	});
    	console.log($("[name='type']").val());
    	$("[name='type']").change(function(){
    		  alert('Элемент foo был изменен.');
    		});
    	$("#type").focus(function(){
    		  alert('Элемент foo получил фокус.');
    		});
    	//alert('Вы');
    });
})(jQuery);*/