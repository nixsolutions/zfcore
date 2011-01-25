// validate upload by file extension
dojo.addOnLoad(function(){
    var filter = function(){
        var extension = this.value.replace(/.*\./,'').toLowerCase();
        var allowed = false;
        for (var i in VALID_FILES) {
            if (VALID_FILES[i] == extension) {
                allowed = true;
                break;
            }
        }
        if (!allowed) {
            _showError(ERROR_FILE_TYPE);
            dijit.byId('upload').reset();
            dijit.byId('upload').fileInput.onchange = filter;
        }
    };
    dijit.byId('upload').fileInput.onchange = filter;
    
});

function onCancel() 
{
    window.top.dojo.byId('screen-wrapper').style.display='none';
	window.top.dojo.byId('image-manager').style.display='none';
}

function onOK() 
{
	// pass data back to the calling window
	var f_url = dojo.byId("f_url").value;
	if (f_url.length < 1) return;
	
	var f_thumb = dojo.byId("f_url_thumb").value;
	
	var f_size = dojo.query('input[name="f_size"]:checked')[0];
	
	var f_alt   = ' alt="'+dojo.byId("f_alt").value+'" ';
	var f_class = ' class="'+dojo.byId("f_class").value+'" ';
	
	var f_align = '';
	
	if (dojo.byId('f_align').value != 'not set') {
	    f_align = ' align="'+dojo.byId("f_align").value+'" ';
	}
	
	if ( f_size.value != '0') {
	   var f_thumb = ' src="'+f_thumb.replace(/\_\d+x\d+\.\w+$/, '')+'_'+f_size.title+'.'+f_url.replace(/^.*?\./, '')+'" ';
	   var xhrArgs = {
            url: THUMB_URL,
            postData: 'size='+f_size.value+'&file='+f_url,
            handleAs: "json",
            load: function(data) {
                if (data.result != RESULT_SUCCESS) {
                    _showError(ERROR_THUMB_CREATION);
                } else {
                    window.top.dojo.byId("screen-wrapper").style.display = 'none';
                    _insertImage();
                }
            }
            };
            dojo.xhrPost(xhrArgs);
	} else {
	    var f_thumb  = ' src="'+f_url+'" ';
	    _insertImage();
	    
	}
	
	function _insertImage() 
	{
	    var f_full = '<img '+f_thumb+f_alt+f_align+f_class+' />';
	
            if (dojo.byId('f_big').checked) {
                f_full = '<a href='+f_url+'>'+f_full+'</a>';
            }
            
            window.top.dijit.byId('content-Editor').execCommand('inserthtml', f_full);
            onCancel();
	}
};

function _showError(message)
{
    if (!dojo.byId("screen-wrapper")) {
        var wrapper = document.createElement('div'); 
            wrapper.id = "screen-wrapper";
        dojo.body().appendChild(wrapper);	
    }
    dojo.byId("screen-wrapper").style.display = 'block';
    
    if (message != undefined) {   
        if (!dojo.byId('image-manager-error')) {
            var error = document.createElement('div'); 
    		    error.id = "image-manager-error";
    		    dojo.body().appendChild(error);
        }
        var node = dojo.byId('image-manager-error');
        node.innerHTML = message;
    	node.style.display = 'block';
    		
        //vertical positioning
    	offset = window.pageYOffset;
    	diff_height = (window.innerHeight - dojo.position(node).h)/2;
    	if ( diff_height > 0 ) {
    	    offset += diff_height;
    	}
    	node.style.top       = offset+'px';
    	
    	//horizontal positioning
    	diff_width = (window.innerWidth - dojo.position(node).w)/2;
    	node.style.left = diff_width+'px';
    }
    setTimeout(function(){
        node.style.display = 'none';
        dojo.byId("screen-wrapper").style.display = 'none';
    }, 3000);
}

function goUpDir()
{
	var dir = dojo.byId('dirPath').value.replace(/\/\w*$/, '');
	dojo.byId('dirPath').value = dir;
	dojo.byId('imgManager').contentWindow.location.search = '?dir='+dir;
}