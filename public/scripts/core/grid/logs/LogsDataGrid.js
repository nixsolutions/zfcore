dojo.provide("core.grid.logs.LogsDataGrid");

dojo.require('dojox.grid.DataGrid');
dojo.require('dojox.data.QueryReadStore');
dojo.require('dojo.data.ItemFileWriteStore');

// for future
dojo.declare(
    'core.data.QueryWriteStore',
    [dojo.data.ItemFileWriteStore,dojox.data.QueryReadStore],
    {});

dojo.declare(
    'core.grid.logs.LogsDataGrid',
    dojox.grid.DataGrid,
    {
        storeUrl:null,
//       	delAction:function(rowIndex)
//       	{
//       	    var item = this.getItem(rowIndex);
//       		if (confirm("Are you sure you want to delete this is item?")){
//       			dojo.xhrGet( {url: this.storeUrl+"/delete/id/"+item.i.id});
//       			this.store.deleteItem(item);
//       		}
//       	},
       	viewAction:function(rowIndex)
       	{
       	    var item = this.getItem(rowIndex);
       	    location.href = this.storeUrl+"/view/id/"+item.i.id;
       	}
    });
