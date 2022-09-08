cur_page = 1;
total_page = 0;

$(function() {

	popDept('fdept');
	$('#dtr-filter').click(function() { filterDTR(); });
	content(0);
	
	popEmployee();
	$('#fdept').change(function() { popEmployee(); });
	
	
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

	auto_period();
	
$('#dtr-view').change(function() {

	var filter = '';
	var v = $('#dtr-view').val();
	var today = new Date();
	
	switch (parseInt(v)) {
	
		case 1: // cs form 48
			var m = $('#rrmonth').val();		
			filter += '<div class="form-group">'
				   + '<select id="rcmonth" class="form-control" style="width: 200px">'
				   + '<option value="00">--Month--</option>'
				   + '<option value="01">January</option>'
				   + '<option value="02">February</option>'
				   + '<option value="03">March</option>'
				   + '<option value="04">April</option>'
				   + '<option value="05">May</option>'
				   + '<option value="06">June</option>'
				   + '<option value="07">July</option>'
				   + '<option value="08">August</option>'
				   + '<option value="09">September</option>'
				   + '<option value="10">October</option>'
				   + '<option value="11">November</option>'
				   + '<option value="12">December</option>'
				   + '</select>'
				   + '</div>'
				   + '<div class="form-group">'
				   + '<input type="text" id="rcyear" class="form-control" placeholder="Enter year" style="width: 200px;" value="' + today.getFullYear() + '">'				   
				   + '</div>'
				   + '<div class="form-group">'
				   + '<select id="rcperiod" class="form-control" style="width: 200px;">'
				   + '<option value="first-half">First-half</option>'
				   + '<option value="second-half">Second-half</option>'
				   + '</select>'
				   + '</div>';
		break;
		
		case 2: // raw records
			var m = $('#rcmonth').val();
			filter += '<div class="form-group">'
				   + '<select id="rrmonth" class="form-control" style="width: 200px">'
				   + '<option value="00">--Month--</option>'
				   + '<option value="01">January</option>'
				   + '<option value="02">February</option>'
				   + '<option value="03">March</option>'
				   + '<option value="04">April</option>'
				   + '<option value="05">May</option>'
				   + '<option value="06">June</option>'
				   + '<option value="07">July</option>'
				   + '<option value="08">August</option>'
				   + '<option value="09">September</option>'
				   + '<option value="10">October</option>'
				   + '<option value="11">November</option>'
				   + '<option value="12">December</option>'
				   + '</select>'
				   + '</div>'
				   + '<div class="form-group">'
				   + '<input type="text" class="form-control" id="rrdate" data-provide="datepicker" placeholder="mm/dd/yyy (Date)" style="width: 200px;" value="">'
				   + '</div>'
				   + '<div class="form-group">'
				   + '<input type="text" class="form-control" id="rryear" placeholder="Enter year" style="width: 200px;" value="' + today.getFullYear() + '">'
				   + '</div>';
				   
		break;
	
	}
	
	$('#view-filter').html(filter);
	auto_period();
	
	switch (parseInt(v)) {
		
		case 1:
		$('#rcmonth').val(m);
		break;
		
		case 2:
		$('#rrmonth').val(m);
		break;

	}
	
});
	
});

function popDept(e) {

	$.ajax({	
		url: 'dtr-ajax.php?p=populate_department',
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

$('#dtr-page').html(loading);

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

var p = 'contents_csform48';
var dv = $('#dtr-view').val();
$('#dtr-title').html('CS Form 48');
if (parseInt(dv) == 2) {
p = 'contents_raw_records';
$('#dtr-title').html('Raw Records');
}

$.ajax({	
	url: 'dtr-ajax.php?p=' + p + page,
	type: 'get',
	success: function(data, status) {		
		var sdata = data.split('|');
		$('#dtr-page').html(sdata[0]);
		total_page = parseInt(sdata[1]);
		
		var dv = $('#dtr-view').val();
		if (parseInt(dv) == 1) {
		var hfid = $('#hfid').val();

		$('#frmCSForm48 table tbody').children('tr').each(function() {
			
			var dd = this.id.split("_");
			
			// manual time morning arrival
			$('#mora_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});
			// note morning arrival
			$('#ma_note_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});

			// manual time morning departure
			$('#morad_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});
			// note morning departure
			$('#md_note_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});			

			// manual time afternoon arrival
			$('#afta_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});
			// note afternoon arrival
			$('#aa_note_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});			

			// manual time afternoon arrival
			$('#aftd_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});
			// note afternoon arrival
			$('#ad_note_' + dd[1]).editable({
				params: function(params) {
					params.fid = hfid;
					params.ld = dd[1];
					return params;
				}
			});			
			
		});
		
	$('.refresh-dtr').click(function() { filterDTR(); });
	
	}
	
	$('[data-toggle=tooltip]').tooltip();
	$('.table-fixed-header').fixedHeader();	
	
	}
});

}

function uploadDTR() {

var t = 'Upload DTR';
var c = 'form/upload-dtr.php';
var show = function() {

$('#recent-dtr').change(function() {
	var chkd = $('#recent-dtr').prop('checked');
	if (chkd) {
		$('#dtr-file').prop('disabled',true);
		$('#frmUploadDTR button').prop('disabled',false);
	} else {
		$('#dtr-file').prop('disabled',false);
		$('#frmUploadDTR button').prop('disabled',true);	
	}
});

$('#dtr-file').change(function() {

var $bar = $('.upload-progress');
$bar.width(0);
$bar.text(0 + '%');

var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

var df = $('#dtr-file').val();

if (is_chrome) {
	df = df.substr(12);
}

if (df == 'AGL_0001.TXT') {

	$('#dtr-alert').addClass('tog-alert');	
	
	$('#frmUploadDTR').submit();
	
	document.getElementById('upload_target').onload = function() {	
	
		var progress = setInterval(function() {
		var $bar = $('.upload-progress');
		
		if ($bar.width()>=550) {
			clearInterval(progress);
			$('.progress').removeClass('active');
		} else {
			$bar.width((Math.round($bar.width())+55)+1);
		}
		$bar.text((Math.round($bar.width())/550)*100 + "%");
		}, 500);

		$('#frmUploadDTR button').prop('disabled',false);
		
	};

} else {
	
	$('#dtr-logo').val('');
	$('#dtr-alert').removeClass('tog-alert');
	
}	

});

$('#frmUploadDTR button').click(function() { dtrForm(1,0); pModalHide(); });

$('#dtr-alert').addClass('tog-alert');
	
};
var hide = function() {

}
pModal(t,c,show,hide);

}

function printDTR() {

var chkf = $('#frmCSForm48')[0];

if (!chkf) {
	notification('To generate DTR report please search by Employee ID or Employee Last Name and First Name, and enter start/end dates then press Go!',function() { });
	return;
}

$('#frmDTR').trigger('submit');

}

function dtrForm() {

var dm = $('#dtr-month').val();
var dy = $('#dtr-year').val();

var chkd = $('#recent-dtr').prop('checked');
var recent = (chkd) ? 1 : 0;

showProgress();
$.ajax({
	url: 'dtr-ajax.php?p=upload_dtr',
	type: 'post',
	data: {pdm: dm, pdy: dy, userecent: recent},
	success: function(data, status) {
		hideProgress();
		notification(data,function() { });		
	}
});

}

function filterDTR() {

var par = '';
var feid = $('#feid').val();
var fename = $('#fename').val();
var fdept = $('#fdept').val();
var fsename = $('#fsename').val();

var dv = $('#dtr-view').val();

if (parseInt(dv) == 1) {

var rcmonth = $('#rcmonth').val();
var rcyear = $('#rcyear').val();
var rcperiod = $('#rcperiod').val();
par = '&feid=' + feid + '&fename=' + fename + '&fsename=' + fsename + '&rcmonth=' + rcmonth + '&rcyear=' + rcyear + '&rcperiod=' + rcperiod;

} else {

var rrmonth = $('#rrmonth').val();
var rrdate = $('#rrdate').val();
var rryear = $('#rryear').val();
par = '&feid=' + feid + '&fename=' + fename + '&fdept=' + fdept + '&fsename=' + fsename + '&rrmonth=' + rrmonth + '&rrdate=' + rrdate + '&rryear=' + rryear;

}

content(0,par);

}

function rfilterDTR() {

var par = '';
var feid = $('#feid').val();
var fename = $('#fename').val();
var fdept = $('#fdept').val();
var fsename = $('#fsename').val();

var dv = $('#dtr-view').val();

if (parseInt(dv) == 1) {

var sday = $('#sday').val();
var eday = $('#eday').val();
par = '&feid=' + feid + '&fename=' + fename + '&fdept=' + fdept + '&fsename=' + fsename + '&sday=' + sday + '&eday=' + eday;

} else {

var rrmonth = $('#rrmonth').val();
var rrdate = $('#rrdate').val();
var rryear = $('#rryear').val();
par = '&feid=' + feid + '&fename=' + fename + '&fdept=' + fdept + '&fsename=' + fsename + '&rrmonth=' + rrmonth + '&rrdate=' + rrdate + '&rryear=' + rryear;

}

return par;

}

function dateCon(id,d,st) {

var t = 'Manage Time Logs/Notes/Date Option';
var c = 'form/date-con.php?fid=' + id + '&ld=' + d + '&st=' + st;
var show = function() {

};
var hide = function() {
	var chkk = $('#leave').prop('checked');
	if (chkk) leaveNote();
	filterDTR();
}
pModal(t,c,show,hide);

}

function ignoreLog(id,chk) {

var chkk = (chk) ? 1 : 0;

	$.ajax({
		url: 'dtr-ajax.php?p=ignore_log',
		type: 'post',
		data: {pchk: chkk, did: id},
		success: function(data, status) {

		}
	});
}

function enableNote(id,chk) {

var chkk = (chk) ? 1 : 0;

	$.ajax({
		url: 'dtr-ajax.php?p=enable_note',
		type: 'post',
		data: {pchk: chkk, nid: id},
		success: function(data, status) {

		}
	});
}

function popEmployee() {

var fdid = $('#fdept').val();

$.ajax({
data: 'post',
url: 'employees-ajax.php?p=select_employees&fsel_dept=' + fdid,
success: function(data, status) {
		$('#fsename').html(data);
	}
});

}

function onDuty(fid,d,chk) {

var chkk = (chk) ? 1 : 0;

	$.ajax({
		url: 'dtr-ajax.php?p=on_duty',
		type: 'post',
		data: {pchk: chkk, pfid: fid, pod: d},
		success: function(data, status) {

		}
	});

}

function enableHalfday(fid,d,chk) {

var chkk = (chk) ? 1 : 0;

	$.ajax({
		url: 'dtr-ajax.php?p=enable_halfday',
		type: 'post',
		data: {pchk: chkk, pfid: fid, pod: d},
		success: function(data, status) {

		}
	});

}

function enableLeave(fid,d,chk) {

var chkk = (chk) ? 1 : 0;
var ln = $('#leave-note').val();

	$.ajax({
		url: 'dtr-ajax.php?p=enable_leave',
		type: 'post',
		data: {pchk: chkk, pfid: fid, pod: d},
		success: function(data, status) {

		}
	});

}

function leaveNote() {

var ln = $('#leave-note').val();
var fid = $('#hfid').val();
var d = $('#hld').val();

	$.ajax({
		url: 'dtr-ajax.php?p=leave_note',
		type: 'post',
		data: {pln: ln, pfid: fid, pod: d},
		success: function(data, status) {

		}
	});

}

function showLN(fid,d,chk) {

var chkk = (chk) ? 1 : 0;

	$.ajax({
		url: 'dtr-ajax.php?p=show_leave_note',
		type: 'post',
		data: {pchk: chkk, pfid: fid, pod: d},
		success: function(data, status) {

		}
	});

}

function tranLog(id,n) {

var to = $('#log-t' + n).val();

	$.ajax({
		url: 'dtr-ajax.php?p=transitional_log',
		type: 'post',
		data: {tlid: id, tlo: to},
		success: function(data, status) {

		}
	});

}

function tranNote(id,n) {

var tn = $('#note-t' + n).val();

	$.ajax({
		url: 'dtr-ajax.php?p=transitional_note',
		type: 'post',
		data: {tnid: id, tno: tn},
		success: function(data, status) {

		}
	});

}

function auto_period() {

var today = new Date();
var date_today = today.getDate();
var period_month = today.getMonth();

if (parseInt(date_today) > 15) {
	$('#rcperiod').val('first-half');
	period_month = parseInt(period_month)+1;
} else {
	$('#rcperiod').val('second-half');
}

if (period_month < 10) period_month = '0'+period_month.toString();
else period_month = period_month.toString();

$('#rcmonth').val(period_month);
		
}

function explicitLog(id,n) {

var to = $('#log-t' + n).val();

	$.ajax({
		url: 'dtr-ajax.php?p=explicit_log',
		type: 'post',
		data: {tlid: id, tlo: to},
		success: function(data, status) {

		}
	});	
	
}

function showSatSun(chk) {
	
	chk = (chk) ? 1 : 0;
	$('#show_sat_sun').val(chk);
	
}

function mergedNote(id,sel) {

	$.ajax({
		url: 'dtr-ajax.php?p=merge_note',
		type: 'post',
		data: {nid: id, psel: sel},
		success: function(data, status) {

		}
	});
	
}