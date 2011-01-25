dojo.provide("core.grid.crontab.Crontab");
dojo.require("core.grid.crontab.CrontabDataGrid");
dojo.require("core.grid.EnhancedGrid");

dojo.declare(
    'core.grid.crontab.Crontab', null,
{
    url:null,
    store:null,
    grid:null,
    element:null,

    constructor:function(cells, elementId, url, editButton, deleteButton, customButton, typeGrid){

        var _self = this;
        this.url = url;
        this.element = elementId;
        if (editButton) {
            cells.push({
            	name: "Edit",
            	_class: "editActionBtn",
        		title: "Edit",
        		action: "",
        		field: "id",
        		width:'25px',
        		formatter:this.buttonActionFromatter
        	});
        }
        if (deleteButton) {
            cells.push({
        		name: "Del",
        		_class: "delActionBtn",
        		title: "Delete",
        		action: "",
        		field: "id",
        		width:'25px',
        		formatter:this.buttonActionFromatter
        	});
        }
        if (customButton) {
            customButton.formatter = this.buttonActionFromatter;
            customButton.field     = 'id';
            cells.push(customButton);
        }


        this.layout = {onAfterRow:this.cellsEvents, cells:cells};

        this.store = new core.data.QueryWriteStore({url:this.url+"/store/"});

        switch(typeGrid) {
        case "EnhancedGrid":
        	this.grid = new dojox.grid.EnhancedGrid({
                storeUrl         : this.url,
                query			 : {},
                jsid			 : "enhancedGrid",
                id				 : "enhancedGrid",
                store			 : this.store,
                autoHeight		 : false,
                rowsPerPage		 : 15,
                clientSort       : true,
                rowSelector      : '0px',
                structure		 : this.layout,
                plugins: {
                	selectionMode   : "single",
                    indirectSelection : {
                        name     : "Selection",
                        width    : "50px",
                        styles   : "text-align: center;"
                    }
                }
            },
            document.createElement('div'));
        	break;

        case "DataGrid":
        default:
        	this.grid  = new core.grid.crontab.CrontabDataGrid({
                storeUrl        : this.url,
                query			: {},
                jsid			: "datagrid",
                id				: "datagrid",
                store			: this.store,
                autoHeight		: false,
                rowsPerPage		: 15,
                structure		: this.layout
            }, document.createElement("div"));
        	break;
        }

        dojo.byId(elementId).appendChild(this.grid.domNode);

        this.grid.startup();

    },
    postscript:function(){
        var _self = this;
        dojo.connect(dijit.byId("grid-search"), "onClick", function (){
            if (dijit.byId('grid-filter').isValid()) {
                //var field  = dijit.byId("grid-field").store.root.value;
				var field = dijit.byId("grid-field").item;
				if (field == null) field = dijit.byId("grid-field").store.root.value;
				else field = field.value;
                var filter = dojo.byId("grid-filter").value;

                _self.grid.setQuery({field:field,filter:filter});
            }
        });

        dojo.connect(dijit.byId("grid-undo"), "onClick", function (){
                dojo.byId("grid-filter").value = '*';
                _self.grid.setQuery({});
                _self.store.revert();
            });

       	dojo.connect(dijit.byId("grid-del"), "onClick", function(){
       		if (confirm("Are you sure you want to delete this is item(s)?")){

    			var items = _self.grid.selection.getSelected();
    			if (items.length){
    				dojo.forEach(items, function(item){
    				    dojo.xhrGet( {url:_self.url+"/delete/id/"+item.i.id});
    				});
    			}
       		    _self.grid.removeSelectedRows();
       			_self.store.save();
       		}
       	});

       	dojo.connect(dijit.byId("grid-new"), "onClick", function(){
       	    location.href = _self.url+"/create";
       	});
    },
    cellsEvents:function(rowIndex, cells, rowNode)
    {
        var _self = this;
        dojo.query(".editActionBtn", rowNode).forEach(function(el){
            dojo.connect(el, "onclick", function(e){
        	    e.preventDefault();
        		_self.grid.editAction(rowIndex);
        	});
        });

        dojo.query(".delActionBtn", rowNode).forEach(function(el){
            dojo.connect(el, "onclick", function(e){
        	    e.preventDefault();
        		_self.grid.delAction(rowIndex);
        	});
        });
    },
    buttonActionFromatter:function(id, rowIndex, objRow)
    {
        if (id)
            return '<a href="javascript:void(0)" name="'+id+
                   '" title="'+objRow._props.title+
                   '" class="'+objRow._props._class+
                   '" onclick="'+objRow._props.action+
                   '">'+objRow._props.title+'</a>';
    }
});