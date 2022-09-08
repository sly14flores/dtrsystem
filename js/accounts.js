cur_page = 1;
total_page = 0;

$(function() {

	$('#account-filter button').click(function() { filterAccount(); });
	$('#ua-deleted').click(function() { showDeleteAccount(); });
	content(0);

});

function content() {

var loading  = '<div style="text-align: center;">';
	loading += '<img src="image/progress.gif">';
	loading	+= '</div>';

$('#accounts-page').html(loading);

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
	url: 'accounts-ajax.php?p=contents' + page,
	type: 'get',
	success: function(data, status) {		
		var sdata = data.split('|');
		$('#accounts-page').html(sdata[0]);
		total_page = parseInt(sdata[1]);	
	}
});

}

function addAccount() {

var t = 'Add New User Account';
var c = 'form/user-account.php';
var show = function() {
	$('#frmUserAccounts button').unbind('click');	
	$('#frmUserAccounts button').click(function() {
		var uname_exists = $('#username-exists').val();
		if (parseInt(uname_exists) == 1) return;
		userAccountForm(1,0); pModalHide();
	});
	$('#frmUserAccounts').validator();
	$('#user_account_password').showPassword();
	$('#user_account_username').change(function() { verUname(); });
};
var hide = function() {

}
pModal(t,c,show,hide);

}

function editAccount() {

if ( (count_checks('frmContent') == 0) || (count_checks('frmContent') > 1) ) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var t = 'Update User Account Info';
var c = 'form/user-account.php';
var show = function() {
	$('#frmUserAccounts button').unbind('click');
	$('#frmUserAccounts button').click(function() { userAccountForm(2,id); pModalHide(); });
	$('#frmUserAccounts').validator();
	
	$.ajax({
		url: 'accounts-ajax.php?p=edit&uaid=' + id,
		type: 'get',
		dataType: 'json',
		success: function(data,status) {
			$.each(data, function(key, value) {
				if ($('#' + key)[0]) $('#'+key).val(value);
				if (key == 'user_account_deleted') accountDeleted(value);
			});			
		}
	});
	
	$('#user_account_password').showPassword();
	popPword(id);	
	
};
var hide = function() {
	uncheckMulti('frmContent');
}
pModal(t,c,show,hide);

}

function userAccountForm(src,id) {

switch (src) {

case 1:
var obj = json_obj.get_obj('input[type=text], input[type=email], input[type=password], select','frmUserAccounts');
$.ajax({
	url: 'accounts-ajax.php?p=add',
	type: 'post',
	data: obj,
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});
break;

case 2:
var ua_json = json_obj.get_json('input[type=text], input[type=email], input[type=password], select','frmUserAccounts','update');
if ($('#ua-undelete')[0]) {
var cuua = $('#ua-undelete').prop('checked'); 
var uua = (cuua) ? 0 : 1;
ua_json.update[0]['user_account_deleted'] = uua;
// console.log(ua_json.update[0]);
}
var pk = '{ "pk": [{"user_account_id":'+id+'}] }';
pk = JSON.parse(pk);
$.ajax({
	url: 'accounts-ajax.php?p=update',
	type: 'post',
	data: {user_account_id: pk, ua_update: ua_json},
	success: function(data, status) {
		notification(data,function() { content(0,rfilterAccount()); });		
	}
});
break;

}

}

function delAccount() {

if (count_checks('frmContent') == 0) {
	var f = function() { uncheckMulti('frmContent'); };
	notification('Please select one.',f);
	return;
}

id = getCheckedId('frmContent');

var f = function() { pdelAccount(id); };	
confirmation('Are you sure you want to delete this user account(s)?',f,function() { uncheckMulti('frmContent'); });

}

function pdelAccount(id) {

var ua_json = '{ "update": [{"user_account_deleted": 1}] }';
ua_json = JSON.parse(ua_json);
var pk = '{ "pk": [{"user_account_id":"'+id+'"}] }';
pk = JSON.parse(pk);

$.ajax({
	url: 'accounts-ajax.php?p=delete',
	type: 'post',
	data: {user_account_id: pk, ua_update: ua_json},
	success: function(data, status) {
		notification(data,function() { content(0); });		
	}
});

}

function filterAccount() {

var faccountname = $.trim($('#faccountname').val());
var showd = $('#ua-deleted').prop('checked');
var dua = (showd) ? 1 : 0;

var par = '&faccountname=' + faccountname + '&dua=' + dua;

content(0,par);

}

function rfilterAccount() {

var faccountname = $.trim($('#faccountname').val());
var showd = $('#ua-deleted').prop('checked');
var dua = (showd) ? 1 : 0;

var par = '&faccountname=' + faccountname + '&dua=' + dua;

return par;

}

function showDeleteAccount() {

filterAccount();

}

function popPword(id) {

$.ajax({
	url: 'accounts-ajax.php?p=pop_pword',
	type: 'post',
	data: {puaid: id},
	success: function(data, status) {
		$('#ua-pword').val(data);
	}
});

}

function verUname() {

var uname = $('#user_account_username').val();
if (uname == '') return;

$.ajax({
	url: 'accounts-ajax.php?p=verify_uname',
	type: 'post',
	data: {puname: uname},
	success: function(data, status) {
		if (parseInt(data) == 1) {
			$('#user_account_username + span').html('<span style="color: #A94442;">Username is already taken.</span>');
			$('#user_account_username').css('border-color','rgb(169, 68, 66)');
			$('#username-exists').val(1);
		} else {
			$('#user_account_username + span').html('');
			$('#user_account_username').css('border-color','');
			$('#username-exists').val(0);
		}
	}
});

}

function accountDeleted(is_deleted) {
	
	var markup = '<div class="form-group">';
	markup += '<div class="checkbox">';
	markup += '<label><input id="ua-undelete" type="checkbox"> Undelete User Account</label>';
	markup += '</div>';
	markup += '</div>';
	
	if (parseInt(is_deleted) == 1) $(markup).insertAfter('#username-exists');
	
}