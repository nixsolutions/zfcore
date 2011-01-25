dojo.provide("core.grid.logs.Logs");
dojo.require("core.grid.logs.LogsDataGrid");
dojo.require("core.grid.EnhancedGrid");
          
dojo.declare(
    'core.grid.logs.Logs', null,
{
    url:null,
    store:null,
    grid:null,
    element:null,
    
    constructor:function(cells, elementId, url, viewButton, deleteButton, customButton, typeGrid){
        
        var _self = this;
        this.url = url;
        this.element = elementId;
        if (viewButton) {
            cells.push({
            	name: "View",
            	_class: "viewActionBtn",
        		title: "View",
        		action: "",
        		field: "id",
        		width:'40px',
        		formatter:this.buttonActionFromatter
        	});
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
        	this.grid  = new core.grid.logs.LogsDataGrid({
                storeUrl        : this.url,
                query			: {},
                jsid			: "datagrid",
                id				: "datagrid",
                store			: this.store,
                autoHeight		: false,
                rowsPerPage		: 5,
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
    },
    cellsEvents:function(rowIndex, cells, rowNode)
    {
        var _self = this;
        dojo.query(".viewActionBtn", rowNode).forEach(function(el){
            dojo.connect(el, "onclick", function(e){
        	    e.preventDefault();
        		_self.grid.viewAction(rowIndex);
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