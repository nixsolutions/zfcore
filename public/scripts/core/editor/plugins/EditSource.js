dojo.provide("core.editor.plugins.EditSource");

dojo.require("dijit._editor._Plugin");

dojo.experimental("core.editor.plugins.EditSource");

dojo.declare("core.editor.plugins.EditSource", 
  dijit._editor._Plugin,
  {
    // Override _Plugin.useDefaultCommand... processing is handled by this plugin, not by dijit.Editor.
    useDefaultCommand: false,

    _initButton: function() {
        this.command = "Edit-html"; //class
        this.iconClassPrefix = '';
    	this.editor.commands[this.command] = "Edit Source";
    	this.inherited("_initButton", arguments);
    	delete this.command;
    	this.source = false;
        this.connect(this.button, "onClick", this._showSource);
    },

    _showSource: function () {
    
      if (!this.source) {
        //todo remove hardcode content-Editor
        //todo fix regexp to replace this only in tags (not in text)
        dijit.byId('content-Editor').setValue( dijit.byId('content-Editor').getValue().replace(/</g, '&lt;').replace(/>/g, '&gt;') );
      }
      else {  
      	dijit.byId('content-Editor').setValue( dijit.byId('content-Editor').getValue().replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/(&quot;|")/g, '') );
      }
      this.source = !this.source;
    }

   }
);

// Register this plugin.
dojo.subscribe(dijit._scopeName + ".Editor.getPlugin", null, function(o) {
  if(o.plugin){ return; }
    switch(o.args.name) {
      case "EditSource":
    	o.plugin = new core.editor.plugins.EditSource({
    		command: o.args.name 
    	});
    }
});
