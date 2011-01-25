dojo.provide("core.grid.EnhancedGrid");

dojo.require("dojox.grid.cells.dijit");

dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
dojo.require("dojox.data.CsvStore");

dojo.declare(
    'core.grid.EnhancedGrid',
    dojox.grid.EnhancedGrid,
    {
        storeUrl:null,
       	delAction:function(rowIndex)
       	{
       	    var item = this.getItem(rowIndex);
       		if (confirm("Are you sure you want to delete this is item?")){
       			dojo.xhrGet( {url: this.storeUrl+"/delete/id/"+item.i.id});
       			this.store.deleteItem(item);
       		}
       	},
       	editAction:function(rowIndex)
       	{
       	    var item = this.getItem(rowIndex);
       	    location.href = this.storeUrl+"/edit/id/"+item.i.id;
       	}
    });
