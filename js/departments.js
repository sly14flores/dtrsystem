cur_page = 1;
total_page = 0;

$(function() {

	$('#dept-filter + button').click(function() { filterDept(); });
	content(0);		
	
	var kwords;
	$.ajax({
		data: 'post',
		async: false,
		url: 'departments-ajax.php?p=typeahead_dept',
		success: function(data, status) { kwords = eval(data); }
	});
	populateTypehead('#fdeptname',kwords);
	
});

function content() {

var loading  = '<div style="text-align: center;">';
	loading += '<img src="image/progress.gif">';
	loading	+= '</div>';

$('#departments-page').html(loading);

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
	url: 'departments-ajax.php?p=contents' + page,
	type: 'get',
	success: function(data, status) {		
		var sdata = data.split('|');
		$('#departments-page').html(sdata[0]);
		total_page = parseInt(sdata[1]);	
	}
});

}

function addDept() {

var t = 'Add New Department';
var c = 'form/department.php';
var show = function() {
	$('#frmDepartments button').unbind('click');
	$('#frmDepartments button').click(function() { deptForm(1,0); pModalHide(); });
	$('#frmDepartments').validator();

	var kwords;			
	$.ajax({
		data: 'post',
		async: false,
		url: 'employees-ajax.php?p=typeahead_employees_fullname',
		success: function(data, status) { kwords = eval(data); }
	});
	populateTypehead('#dept_head',kwords);	
	
};
var hide = function() {

}
pModal(t,c,show,hide);

}

function editDept() {

if ( (count_checks('frmContent') == 0) || (count_checks('frmContent') > 1) ) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var t = 'Update Department Info';
var c = 'form/department.php';
var show = function() {	
	$('#frmDepartments button').unbind('click');
	$('#frmDepartments button').click(function() { deptForm(2,id); pModalHide(); });
	$('#frmDepartments').validator();
	
	var kwords;			
	$.ajax({
		data: 'post',
		async: false,
		url: 'employees-ajax.php?p=typeahead_employees_fullname',
		success: function(data, status) { kwords = eval(data); }
	});
	populateTypehead('#dept_head',kwords);
	
	$.ajax({
		url: 'departments-ajax.php?p=edit&deptid=' + id,
		type: 'get',
		dataType: 'json',
		success: function(data,status) {
			$.each(data, function(key, value) {
				setTimeout(function() { if ($('#' + key)[0]) $('#'+key).val(value); },100);
			});			
		}
	});
	
};
var hide = function() {
	uncheckMulti('frmContent');
}
pModal(t,c,show,hide);

}

function deptForm(src,id) {


switch (src) {

case 1:
var obj = json_obj.get_obj('input','frmDepartments');
$.ajax({
	url: 'departments-ajax.php?p=add',
	type: 'post',
	data: obj,
	success: function(data, status) {
		notification(data,function() { content(0); });
		// console.log(data);
	}
});
break;

case 2:
var dept_json = json_obj.get_json('input','frmDepartments','update');
var pk = '{ "pk": [{"dept_id":'+id+'}] }';
pk = JSON.parse(pk);
$.ajax({
	url: 'departments-ajax.php?p=update',
	type: 'post',
	data: {dept_id: pk, dept_update: dept_json},
	success: function(data, status) {
		notification(data,function() { content(0); });
		// console.log(data);		
	}
});
break;

}

}

function delDept() {

if (count_checks('frmContent') == 0) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var f = function() { pdelDept(id); };	
confirmation('Are you sure you want to delete this department(s)?',f,function() { uncheckMulti('frmContent'); });

}

function pdelDept(id) {

var pk = '{ "pk": [{"dept_id":"'+id+'"}] }';
pk = JSON.parse(pk);

$.ajax({
	url: 'departments-ajax.php?p=delete',
	type: 'post',
	data: {dept_del: pk},
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});

}

function filterDept() {

var fdeptname = $.trim($('#fdeptname').val());

var par = '&fdeptname=' + fdeptname;

content(0,par);

}

function rfilterDept() {

var fdeptname = $.trim($('#fdeptname').val());

var par = '&fdeptname=' + fdeptname;

return par;

}