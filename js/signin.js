function forgotPassword() {

var t = 'Forgot Password';
var c = 'form/email.php';
var show = function() {
	$('#frmEmail button').click(function() { emailPassword(); pModalHide(); });
	$('#frmEmail').validator();
};
var hide = function() {

}
pModal(t,c,show,hide);

}

function emailPassword() {

showProgress();
var euname = $('#email-username').val()

$.ajax({
	url: 'signin-ajax.php?p=email_password',
	type: 'post',
	data: {peuname: euname},
	success: function(data, status) {
		hideProgress();
		notification(data,function() {  });		
	}
});

}