cur_page = 1;
total_page = 0;

$(function() {
	
	popDept('fdept');
	$('#filter button').click(function() { filterEmployee(); });
	content(0);	

	var kwords;
	$.ajax({
		data: 'post',
		async: false,		
		url: 'employees-ajax.php?p=typeahead_employees',
		success: function(data, status) { kwords = eval(data); }
	});	
	populateTypehead('#fename',kwords);	

	$.ajax({
	data: 'post',
	async: false,	
	url: 'employees-ajax.php?p=typeahead_fid',
	success: function(data, status) { kwords = eval(data); }
	});
	populateTypehead('#feid',kwords);	
	
});

function popDept(e) {

	$.ajax({	
		url: 'employees-ajax.php?p=select_department',
		type: 'get',
		success: function(data, status) {		
			$('#' + e).html(data);
		}
	});

}

function content() {

var loading  = '<div style="text-align: center;">';
	loading += '<img src="image/progress.gif">';
	loading	+= '</div>';

$('#employees-page').html(loading);

var args = content.arguments;
var dir = args[0];
var par = '';
if (args.length > 1) par = args[1];

switch (dir) {

case 0: // first page
cur_page = 1;
break;

case 2: // current page
break;

case 3: // last page
cur_page = total_page;
break;

default: // previous next -1/1
cur_page = (cur_page) + parseInt(dir);

}

var page = '&cp=' + cur_page + '&d=' + dir + par;

$.ajax({	
	url: 'employees-ajax.php?p=contents' + page,
	type: 'get',
	success: function(data, status) {		
		var sdata = data.split('|');
		$('#employees-page').html(sdata[0]);
		total_page = parseInt(sdata[1]);
		$('[data-toggle=tooltip]').tooltip();
	}
});
	
}

function popAge() {

var b = $('#employee_dob').val();

if (b == '') return;

var by = b.substring(b.lastIndexOf("/")+1,b.lastIndexOf("/")+5);

var tday = new Date();
var dy = tday.getFullYear();

var age = dy - by;

$('#employee_age').val(age);

}

function addEmployee() {

var t = 'Add New Employee';
var c = 'form/employee.php';
var show = function() {
	$('#frmEmployees button').unbind('click');	
	$('#frmEmployees button').click(function() { employeeForm(1,0); pModalHide(); });
	$('#frmEmployees').validator();
	$('#employee_age').click(function() { popAge(); });
	employee_property.department('#employee_dept');
	employee_property.scheme('#employee_scheme');
};
var hide = function() {

}
pModal(t,c,show,hide);

}

function editEmployee() {

if ( (count_checks('frmContent') == 0) || (count_checks('frmContent') > 1) ) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var t = 'Update Employee Info';
var c = 'form/employee.php';
var show = function() {
	
	employee_property.department('#employee_dept');
	employee_property.scheme('#employee_scheme');
	$('#frmEmployees button').unbind('click');	
	
	$.ajax({
		url: 'employees-ajax.php?p=edit&eid=' + id,
		type: 'get',
		dataType: 'json',
		success: function(data,status) {
			$.each(data, function(key, value) {					
					setTimeout(function() { if ($('#' + key)[0]) $('#'+key).val(value); },100);
			});			
		}
	});		
	
	$('#frmEmployees button').click(function() { employeeForm(2,id); pModalHide(); });
	$('#frmEmployees').validator();
	$('#employee_age').click(function() { popAge(); });	
};
var hide = function() {
	uncheckMulti('frmContent');
}
pModal(t,c,show,hide);

}

function employeeForm(src,id) {

switch (src) {

case 1:
var obj = json_obj.get_obj('input, select','frmEmployees');
$.ajax({
	url: 'employees-ajax.php?p=add',
	type: 'post',
	data: obj,
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});
break;

case 2:
var employee_json = json_obj.get_json('input, select','frmEmployees','update');
var pk = '{ "pk": [{"employee_id":'+id+'}] }';
pk = JSON.parse(pk);
$.ajax({
	url: 'employees-ajax.php?p=update',
	type: 'post',
	data: {employee_id: pk, employee_update: employee_json},
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});
break;

}

}

function delEmployee() {

if (count_checks('frmContent') == 0) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var f = function() { pdelEmployee(id); };	
confirmation('Are you sure you want to delete this employee(s)?',f,function() { uncheckMulti('frmContent'); });

}

function pdelEmployee(id) {

var pk = '{ "pk": [{"employee_id":"'+id+'"}] }';
pk = JSON.parse(pk);

$.ajax({
	url: 'employees-ajax.php?p=delete',
	type: 'post',
	data: {employee_del: pk},
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});

}

function filterEmployee() {

var feid = $('#feid').val();
var fename = $('#fename').val();
var fdept = $('#fdept').val();

var par = '&feid=' + feid + '&fename=' + fename + '&fdept=' + fdept;

content(0,par);

}

function rfilterEmployee() {

var feid = $('#feid').val();
var fename = $('#fename').val();
var fdept = $('#fdept').val();

var par = '&feid=' + feid + '&fename=' + fename + '&fdept=' + fdept;

return par;

}

function extract201file(id) {

window.open('extract-201file.php?eid='+id);
	
}