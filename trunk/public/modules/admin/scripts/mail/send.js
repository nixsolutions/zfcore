dojo.addOnLoad(function() {
    onChange = function(value) {
        if (value == 'custom email') {
            dijit.byId('filterInput').required = true;


            dojo.fadeIn({node:dojo.byId('filterInput-label')}).play();
            dojo.fadeIn({node:dijit.byId('filterInput').domNode}).play();
            dijit.byId('filterInput').value = '';
        } else {
            dijit.byId('filterInput').required = false;
            dojo.fadeOut({node:dojo.byId('filterInput-label')}).play();
            dojo.fadeOut({node:dijit.byId('filterInput').domNode}).play();
            dijit.byId('filterInput').value = 1;
        }
    }
    
    onChange(dijit.byId('filter').value);
    
    dijit.byId('filter').onChange = onChange;
});