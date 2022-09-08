$(function() {

$('[data-toggle=tooltip]').tooltip();

});

function settings() {

var src = 0;
var args = settings.arguments;
if (args.length > 0) src = args[0];

var t = 'Settings';
var c = 'form/settings.php';
var show = function() {
	$('#frmSettings button').click(function() { settingsForm(); });
	$('#frmSettings').validator();
	$('#current').keyup(function() { $('.show-verify').html(''); });
	$('#pnew').keyup(function() { $('.show-verify').html(''); });
	$('#rnew').keyup(function() { $('.show-verify').html(''); });
};
var hide = function() {

}
if (src == 1) sModal(t,c,show,hide);
else pModal(t,c,show,hide);
}

function verCurrentP() {

var current = $('#current').val();

var r = false;
$.ajax({
	url: 'settings.php?p=verify_current_password',
	type: 'post',
	async: false,
	data: {pcurrent: current},
	success: function(data, status) {
		if (parseInt(data) == 1) r = true;
	}
});

return r;

}

function verNewP() {

var r = false;
var pnew = $('#pnew').val();

if (pnew.length < 6) {
	r = false;
} else {
	r = true;
}

return r;

}

function verrNewP() {

var r = false;

var rnew = $('#rnew').val();

if (rnew.length < 6) {
	r = false;
} else {
	r = true;
}

return r;

}

function nPmatch() {

var r = false;
var pnew = $('#pnew').val();
var rnew = $('#rnew').val();

if (rnew != pnew) {
	r = false;
} else {
	r = true;
}

return r;

}

function settingsForm() {

$('.show-verify').html('');

if (!verCurrentP()) {
	$('.show-verify').html('Current password is invalid.');
	return;
}

if (!verNewP()) {
	$('.show-verify').html('New password must be at least 6 characters.');
	return;
}

if (!verrNewP()) {
	$('.show-verify').html('Re-type new password must be at least 6 characters.');
	return;
}

if (!nPmatch()) {
	$('.show-verify').html('New password and re-type new password don\'t match.');
	return;
}

var rnew = $('#rnew').val();

$.ajax({
	url: 'settings.php?p=change_password',
	type: 'post',
	data: {prnew: rnew},
	success: function(data, status) {
		pModalHide();
		notification(data,function() { });
	}
});

}

function about() {

var t = 'About';
var c = 'form/about.php';
var show = function() {

};
var hide = function() {

}
pModal(t,c,show,hide);

}

function logout() {

var f = function() { logout_confirmed(); };	
confirmation('Are you sure you want to logout?',f);

}

function logout_confirmed() {

window.location.href = 'signout.php';

}

function notification(c,f) {

$('#modal-notify').modal('show');
$('#modal-notify .modal-body').html(c);
$('#modal-notify').on('hidden.bs.modal', function (e) {
	f();
});

}

function confirmation(c,y,n) {

$('#btnYes').unbind('click');
$('#btnYes').click(function() { y(); $('#modal-confirm').modal('hide'); });
$('#modal-confirm').modal('show');
$('#modal-confirm .modal-body').html(c);
$('#modal-confirm').on('hidden.bs.modal', function (e) {
	n();
});

}

function pModal(t,c,s,h) {

$('#parentModal').modal('show');
$('#parentModal .modal-title').html(t);
$('#parentModal .modal-body').load(c, function() { s(); });
$('#parentModal').on('hidden.bs.modal', function (e) {
	h();
});

}

function pModalHide() {

$('#parentModal').modal('hide');

}

function sModal(t,c,s,h) {

$('#settingsModal').modal('show');
$('#settingsModal .modal-title').html(t);
$('#settingsModal .modal-body').load(c, function() { s(); });
$('#settingsModal').on('hidden.bs.modal', function (e) {
	h();
});

}

function sModalHide() {

$('#settingsModal').modal('hide');

}

function showProgress() {
	$('#modal-progress').modal('show');
}

function hideProgress() {
	$('#modal-progress').modal('hide');
}

function Check_all(theForm, theParentCheck){
	elem = theForm.elements;
		
	for(i=0; i<elem.length; ++i){
		if(elem[i].type == "checkbox"){
			elem[i].checked	= theParentCheck.checked;
		}
	}
}

function Uncheck_Parent(ParentCheckboxName, me){
	var theParentCheckbox = document.getElementById(ParentCheckboxName);
	
	if(!me.checked && theParentCheckbox.checked){
		theParentCheckbox.checked = false;		
	}
}

function uncheckSelected(id) {

	$('#chk_' + id).prop('checked',false);

}

function uncheckMulti(frm) {

	var f = $('#' + frm)[0];
	var e = f.elements;

	for (i=0; i<e.length; ++i) {
		if (e[i].type == "checkbox") {
			if (e[i].checked) e[i].checked = false;
		}
	}

}

function getCheckedId(theFormName){
var theForm		= document.getElementById(theFormName);
var	elem		= theForm.elements;
var tmp_arr, rec_id;

	rec_id	= "";

	for(i=0; i<elem.length; ++i){
		if(elem[i].type == "checkbox"){
			if (elem[i].checked && elem[i].name != 'chk_checkall'){
				tmp_arr	= elem[i].name.split('_');
				rec_id	+= tmp_arr[1] + ',';
			}
		}
	}

	if (rec_id.length > 0){
		rec_id = rec_id.substr(0, rec_id.length-1);
	}
	return rec_id;
}

function count_checks(theFormName){
var theForm		= document.getElementById(theFormName);
var	elem		= theForm.elements;
var int_count	= 0;
		
	for(i=0; i<elem.length; ++i){
		if(elem[i].type == "checkbox"){
			if (elem[i].checked  && elem[i].name != 'chk_checkall') ++int_count;
		}
	}
	
	return int_count;
}

var json_obj = {
		
		json: '',
		
		obj: {},
		
		get_json: function(children,parent,jsonn) {
			json_obj.json = '{ "' + jsonn + '": ['
			
			json_obj.json += '{';			
			$(children, $('#'+parent)).each(function(ind, obj){
				if (obj.id != "") {
					if (obj.type == 'checkbox') {
						var chk = '0';
						if (obj.checked) chk = '1';
						json_obj.json += '"' + obj.id +'":' + chk + ',';
					} else {
						json_obj.json += '"' + obj.id +'":"' + obj.value + '",';
					}
				}
			});
			json_obj.json = json_obj.json.substring(0,json_obj.json.length - 1);			
			json_obj.json += '}';				
			json_obj.json += '] }';
			return JSON.parse(json_obj.json);
		},
		
		get_json_multi: function(children,parent,jsonn) {
			
		},
		
		get_obj: function(children,parent) {
			$(children, $('#'+parent)).each(function(ind, obj) {
				if (obj.type == 'checkbox') {
					json_obj.obj[obj.id] = (obj.checked) ? 1 : 0;
				} else {
					json_obj.obj[obj.id] = obj.value;
				}
			});			
			return json_obj.obj;
		}
	
};

var employee_property = {
	
	department: function(html) {
		$.ajax({
			url: 'employees-ajax.php?p=employee_departments',
			type: 'get',
			success: function(data,status) {
				$(html).html(data);
			}
		});
	},
	
	scheme: function(html) {
		$.ajax({
			url: 'employees-ajax.php?p=employee_schemes',
			type: 'get',
			success: function(data,status) {
				$(html).html(data);
			}
		});				
	}
	
};

function populateTypehead(e,autocomplete) { // #id, json

var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
	var matches, substringRegex;
 
	// an array that will be populated with substring matches
	matches = [];
 
	// regex used to determine if a string contains the substring `q`
	substrRegex = new RegExp(q, 'i');
 
	// iterate through the pool of strings and for any string that
	// contains the substring `q`, add it to the `matches` array
	$.each(strs, function(i, str) {
	  if (substrRegex.test(str)) {
		// the typeahead jQuery plugin expects suggestions to a
		// JavaScript object, refer to typeahead docs for more info
		matches.push({ value: str });
	  }
	});
 
	cb(matches);
  };
};

$(e).typeahead({			
  hint: true,
  highlight: true,
  minLength: 1
},
{
  name: 'kwords',
  displayKey: 'value',
  source: substringMatcher(autocomplete)
}).on('typeahead:selected', function() { });

}

function toggleCheckbox(id,value) {
	$('#'+id).prop('checked',(value == 1) ? true : false);
}

function searchInArray(arr,str) {
	var found = false;
	$.each(arr, function(ind,val) {
		if (val == str) {
			found = true;
			return;
		}
	});
	return found;	
}