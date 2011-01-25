// ������� ��� ������ Feedback_MessageController

// ��������� action ����� �� submit
function _setFormAction(/*String */value) {
	dojo.attr("feedbackForm", "action", "/feedback/management/" + value);
}

// ���������� �� ������ "reply"
function onButtonReply() {
	dojo.query("#reply").connect("onclick", function (/*Event */e) {
		_setFormAction(this.name.toLowerCase());
	});
}

// ���������� �� ������ "edit"
function onButtonEdit() {
	dojo.query("#edit").connect("onclick", function (/*Event */e) {
		_setFormAction(this.name.toLowerCase());
	});
}

// ���������� �� ������ "delete"
function onButtonDelete() {
	dojo.query("#delete").connect("onclick", function (/*Event */e) {
		if (!confirm("Are you sure you want to delete this is message?")) {
			dojo.stopEvent(e);
		} else {
			_setFormAction(this.name.toLowerCase());
		}
	});
}
	
// ���������� �� ������ "reset"
function onButtonReset() {
	dojo.query("#reset").connect("onclick", function (/*Event */e) {
		dojo.byId("message-Editor_iframe").contentWindow.document.body.innerHTML = "";
	});
}

// ���������� �� ������ "submit"

function onButtonSubmit() {
	// �������� ����� ��������� �����
	dojo.connect(dojo.byId("grid-submit"), "onclick", function (e) {
		dojo.byId
                var grid = dijit.byId("enhancedGrid");
		var selected = grid.selection.getSelected();
		if (selected.length != 1) {	
			dojo.stopEvent(e);
		} else {
			// ���������� �������� �����������
			var index = grid.getItemIndex(selected[0]);
			//alert('index: '+index);
			var id = grid.store.getValue(grid.getItem(index), "id");
			//alert('id: '+id);
			var radio = dojo.query("div#enhancedGrid input[aria-pressed='true']")[0];	
			//alert('radio: '+radio);		
			dojo.attr(radio, "name", "id");
			dojo.attr(radio, "value", id);
			dojo.attr(radio, "checked", 'checked');
			// ��������� action �����
			_setFormAction(dojo.attr("grid-status", "value").toLowerCase());
		}
	});	
}

//���������� �� ���. ����. "template"
function onSelectTemplate() {
	dojo.query('#template').connect("onblur", function (/*Event */e) {
		dojo.xhrPost({
            url: "/feedback/management/get-mail-template/format/html", 
            handleAs: "text",
            content: {description : dojo.attr("template", "value")},
            load: function(response, ioArgs) {
            	dojo.byId("message-Editor_iframe").contentWindow.document.body.innerHTML = response;
                return response;
            },
            error: function(response, ioArgs) {
                console.error("HTTP status code: ", ioArgs.xhr.status);
                return response;
            }
        });
	});
}

//���������� ������� ������ �����
function onInputFile() {
	dojo.query("#inputFile").connect("onchange", function (/*Event */e) {
		dojo.byId("message-Editor_iframe").contentWindow.document.body.innerHTML += " %image%";
	});
}

dojo.addOnLoad(function()
{

    if (dijit.byId('datagrid') != null) {
        dijit.byId('datagrid').onRowDblClick = function(event) {

            var grid = dijit.byId('datagrid');

            console.log(this.storeUrl);

            var ind = event.rowIndex;
            var id = grid.store._items[ind].i.id;

            //_setFormAction('reply/id/' + id);
            //dojo.byId('feedbackForm').submit();

            //location.href = '/feedback/management/read/id/' + id;
            dojo.attr('id', 'value', id);
            var action = dojo.attr("feedbackForm", "action");
            dojo.attr("feedbackForm", "action", action + "/read");
            dojo.byId("feedbackForm").submit();
        }
    }


    if (dojo.byId('formReply') != null) {
        dojo.connect(dijit.byId("button-reply"), "onClick", function(e){
                dojo.stopEvent(e);
                var panel = dojo.byId('formReply');
                var height = 700;
                if (dojo.style(panel, 'display') == 'none') {
                    dojo.style(panel, 'display', 'block');
                    dojo.anim(panel, {height:height}, 1000).play();
                } else {
                    dojo.anim(panel, {height:0}, 1000,  function(){
                        dojo.style(panel, 'display', 'none');
                    }).play();
                }
        });

        dojo.style(dojo.byId('formReply'), 'display', 'none');
    }

});
