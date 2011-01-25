function showSize(name)
{
	dojo.query('#li'+name+' span').style('display', 'block');
}
	
function hideSize(name)
{
	dojo.query('#li'+name+' span').style('display', 'none');
}


function selectImage(filename, thumbname,alt, width, height) 
{
    if (window.parent.dojo.byId('f_url')) {
    	window.parent.dojo.byId('f_url').value   = filename;
    	window.parent.dojo.byId('f_alt').value   = alt;
    	window.parent.dojo.byId('f_url_thumb').value = thumbname;
    	window.parent.dijit.byId('button-ok').setAttribute('disabled', false);
    }
}
	
function deleteFile(name) 
{
    if (confirm("Delete file?")) {  
	    var path = dojo.byId('path').value;
	    document.location = DELETE_URL+'?path='+path+'&name='+name;
	}
}
	
function updateDir(newDir)
{
    window.parent.dojo.byId('dirPath').value = newDir;
	document.location.search = '?dir='+newDir;
}