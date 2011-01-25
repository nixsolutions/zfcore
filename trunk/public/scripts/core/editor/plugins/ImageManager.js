dojo.provide("core.editor.plugins.ImageManager");

dojo.require("dijit._editor._Plugin");
dojo.require("dijit.Dialog");

dojo.experimental("core.editor.plugins.ImageManager");

dojo.declare("core.editor.plugins.ImageManager", 
  dijit._editor._Plugin,
  {
    // Override _Plugin.useDefaultCommand... processing is handled by this plugin, not by dijit.Editor.
    useDefaultCommand: false,

    _initButton: function() {
	    // Override _Plugin._initButton() to setup listener on button click
		// make style for button image
	    this.command = "insertImage";
		this.editor.commands[this.command] = "Image Manager";
		this.inherited("_initButton", arguments);
		delete this.command;
	
        this.connect(this.button, "onClick", this._initDialog);
    },


    _initDialog: function () {
    
      if (!this.dialog) {
      	if (!document.getElementById('imageForm')) {     	
      		var container1 = document.createElement('div'); 
			container1.id = "screen-wrapper";
			
			dojo.byId('main-content').appendChild(container1);	
		
			var container2 = document.createElement('div'); 
				container2.id = "image-manager";
			
			var frame = document.createElement('iframe'); 
				frame.src = IMAGES_URL; //images url
				frame.id = "imageForm";
				frame.className = "form_manager";
				
			container2.appendChild(frame);
		
			dojo.byId('main-content').appendChild(container2);
		}

        dojo.byId("screen-wrapper").style.display="block";
        var node = dojo.byId("image-manager");
        node.style.display = "block";

        if (dojo.isIE != undefined) {
            dojo.style(dojo.byId("screen-wrapper"), 'width', '0px');
        }

        //vertical positioning
        var box = dojo.window.getBox();

        if (typeof(window.pageYOffset) == 'number') {
            offset = window.pageYOffset;
        } else {
            offset = document.body.scrollTop;
        }

        diff_height = (box.h - dojo.position(node).h)/2;
        if ( diff_height > 0 ) {
            offset += diff_height;
        }

        node.style.top = offset+'px';
      }
    },

    _replaceValue: function () {
      
    },
    _replaceValueAndHide: function () {
      
    }
  }
);

// Register this plugin.
/**
 * @todo How we set class set?
 */
dojo.subscribe(dijit._scopeName + ".Editor.getPlugin", null, function(o) {
  if(o.plugin){ return; }
    switch(o.args.name) {
      case "ImageManager":
		o.plugin = new core.editor.plugins.ImageManager(
		{
			command: o.args.name, 
			dir:o.args.dir,
			cls:o.args.cls,
			thumbs:o.args.thumbs,
			name:o.args.divname,
			hash:o.args.hash
		});	
    }
});
