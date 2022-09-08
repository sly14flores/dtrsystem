cur_page = 1;
total_page = 0;

$(function() {

	$('#schemes-filter + button').click(function() { filterSchemes(); });
	content(0);	
	
});

function content() {

var loading  = '<div style="text-align: center;">';
	loading += '<img src="image/progress.gif">';
	loading	+= '</div>';

$('#schemes-page').html(loading);

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
	url: 'schemes-ajax.php?p=contents' + page,
	type: 'get',
	success: function(data, status) {		
		var sdata = data.split('|');
		$('#schemes-page').html(sdata[0]);
		total_page = parseInt(sdata[1]);	
	}
});

}

function verScheme(i) {

var st = $('#' + i).val();
if ($('#'+i).prop('required') == false) return;

$.ajax({
	url: 'schemes-ajax.php?p=verifiy_scheme',
	type: 'post',
	data: {pst: st},
	success: function(data, status) {
		if (parseInt(data) == 0) {
			$('#' + i + '+ span').html('<span style="color: #A94442;">Invalid time format</span>');
			$('#' + i).css('border-color','rgb(169, 68, 66)');
		} else {
			$('#' + i + '+ span').html('');
			$('#' + i).css('border-color','');		
		}
	}
});

}

function addScheme() {

var t = 'Add New Scheme';
var c = 'form/scheme.php';
var show = function() {
	$('#frmSchemes button').click(function() { schemeForm(1,0); pModalHide(); });
	$('#frmSchemes').validator();

	$('input', $('#frmSchemes')).each(function(ind,obj){		
		if ($(obj).prop('placeholder') == '00:00') {
			$(obj).change(function(){ verScheme(obj.id); });
		}
	});	
	
};
var hide = function() {

}
pModal(t,c,show,hide);

}

function editScheme() {

if ( (count_checks('frmContent') == 0) || (count_checks('frmContent') > 1) ) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var t = 'Update Scheme Info';
var c = 'form/scheme.php';
var show = function() {
	$('#frmSchemes button').click(function() { schemeForm(2,id); pModalHide(); });
	$('#frmSchemes').validator();
	
	$('input', $('#frmSchemes')).each(function(ind,obj){
		$(obj).change(function(){
			if ($(obj).prop('placeholder') == '00:00') verScheme(obj.id);
		});
	});		
	
	var arr_days_t = ["mon_t","tue_t","wed_t","thu_t","fri_t","sat_t","sun_t"];	
	var arr_checkboxes = ["mon_t","tue_t","wed_t","thu_t","fri_t","sat_t","sun_t"];	
	arr_checkboxes.push("mon_off","tue_off","wed_off","thu_off","fri_off","sat_off","sun_off");
	arr_checkboxes.push("mon_am_out_t","tue_am_out_t","wed_am_out_t","thu_am_out_t","fri_am_out_t","sat_am_out_t","sun_am_out_t");
	arr_checkboxes.push("mon_pm_in_t","tue_pm_in_t","wed_pm_in_t","thu_pm_in_t","fri_pm_in_t","sat_pm_in_t","sun_pm_in_t");
	arr_checkboxes.push("mon_pm_out_t","tue_pm_out_t","wed_pm_out_t","thu_pm_out_t","fri_pm_out_t","sat_pm_out_t","sun_pm_out_t");
	$.ajax({
		url: 'schemes-ajax.php?p=edit&scid=' + id,
		type: 'get',		
		dataType: 'json',
		success: function(data,status) {
			$.each(data, function(key, value) {
				if ($('#' + key)[0]) $('#'+key).val(value);
				if (searchInArray(arr_checkboxes,key)) {				
					toggleCheckbox(key,value);
				}
				if (searchInArray(arr_days_t,key)) {
					var day_t = key.split("_");
					transverse($('#'+key).prop('checked'),day_t[0]);
				}
			});			
		}
	});
	
};
var hide = function() {
	uncheckMulti('frmContent');
}
pModal(t,c,show,hide);

}

function schemeForm(src,id) {

switch (src) {

case 1:
var obj = json_obj.get_obj('input, checkbox','frmSchemes');
$.ajax({
	url: 'schemes-ajax.php?p=add',
	type: 'post',
	data: obj,
	success: function(data, status) {
		notification(data,function() { content(0); });
	}
});
break;

case 2:
var scheme_json = json_obj.get_json('input, checkbox','frmSchemes','update');
var pk = '{ "pk": [{"scheme_id":'+id+'}] }';
pk = JSON.parse(pk);
$.ajax({
	url: 'schemes-ajax.php?p=update',
	type: 'post',
	data: {scheme_id: pk, scheme_update: scheme_json},
	success: function(data, status) {
		notification(data,function() { content(0); });
		console.log(data);		
	}
});
break;

}

}

function delScheme() {

if (count_checks('frmContent') == 0) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var f = function() { pdelScheme(id); };	
confirmation('Are you sure you want to delete this scheme(s)?',f,function() { uncheckMulti('frmContent'); });

}

function pdelScheme(id) {

var pk = '{ "pk": [{"scheme_id":"'+id+'"}] }';
pk = JSON.parse(pk);

$.ajax({
	url: 'schemes-ajax.php?p=delete',
	type: 'post',
	data: {scheme_del: pk},
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});

}

function filterSchemes() {

var fscheme = $.trim($('#fscheme').val());

var par = '&fscheme=' + fscheme;

content(0,par);

}

function rfilterScheme() {

var fscheme = $.trim($('#fscheme').val());

var par = '&fscheme=' + fscheme;

return par;

}

function transverse(chk,d) {

switch (d) {

case "mon":
$('#mon_am_out_t').prop('disabled',!chk);
$('#mon_pm_in_t').prop('disabled',!chk);
$('#mon_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#mon_am_out_t').prop('checked',false);
$('#mon_pm_in_t').prop('checked',false);
$('#mon_pm_out_t').prop('checked',false);
}
break;

case "tue":
$('#tue_am_out_t').prop('disabled',!chk);
$('#tue_pm_in_t').prop('disabled',!chk);
$('#tue_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#tue_am_out_t').prop('checked',false);
$('#tue_pm_in_t').prop('checked',false);
$('#tue_pm_out_t').prop('checked',false);
}
break;

case "wed":
$('#wed_am_out_t').prop('disabled',!chk);
$('#wed_pm_in_t').prop('disabled',!chk);
$('#wed_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#wed_am_out_t').prop('checked',false);
$('#wed_pm_in_t').prop('checked',false);
$('#wed_pm_out_t').prop('checked',false);
}	
break;

case "thu":
$('#thu_am_out_t').prop('disabled',!chk);
$('#thu_pm_in_t').prop('disabled',!chk);
$('#thu_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#thu_am_out_t').prop('checked',false);
$('#thu_pm_in_t').prop('checked',false);
$('#thu_pm_out_t').prop('checked',false);
}
break;

case "fri":
$('#fri_am_out_t').prop('disabled',!chk);
$('#fri_pm_in_t').prop('disabled',!chk);
$('#fri_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#fri_am_out_t').prop('checked',false);
$('#fri_pm_in_t').prop('checked',false);
$('#fri_pm_out_t').prop('checked',false);	
}
break;

case "sat":
$('#sat_am_out_t').prop('disabled',!chk);
$('#sat_pm_in_t').prop('disabled',!chk);
$('#sat_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#sat_am_out_t').prop('checked',false);
$('#sat_pm_in_t').prop('checked',false);
$('#sat_pm_out_t').prop('checked',false);
}
break;

case "sun":
$('#sun_am_out_t').prop('disabled',!chk);
$('#sun_pm_in_t').prop('disabled',!chk);
$('#sun_pm_out_t').prop('disabled',!chk);
if (!chk) {
$('#sun_am_out_t').prop('checked',false);
$('#sun_pm_in_t').prop('checked',false);
$('#sun_pm_out_t').prop('checked',false);	
}
break;

}

}

function skipValidation(obj) {

var _off = ["_am_arrival","_am_departure","_pm_arrival","_pm_departure","_rh"];

var s_obj = obj.id.split("_");
var day = s_obj[0];
var chk = obj.checked;

if (chk) {
	
	$.each(_off, function(ind, val) {
		$('#'+day+val).prop('required', false);
	});
	
} else {
	
	$.each(_off, function(ind, val) {
		$('#'+day+val).prop('required', true);			
	});	
	
}
	
}