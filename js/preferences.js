$(function() {

$('#frmPreferences button').click(function() { confirmPref(); });
if (($('#company-name').val() == '') && ($('#signatory').val() == '') && ($('#grace-period').val() == '')) $('#frmPreferences').validator();

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
	
	$.ajax({
	data: 'post',
	url: 'employees-ajax.php?p=typeahead_employees_fullname',
	success: function(data, status) {

				var kwords = eval(data);		
				
				$('#signatory').typeahead({			
				  hint: true,
				  highlight: true,
				  minLength: 1
				},
				{
				  name: 'kwords',
				  displayKey: 'value',
				  source: substringMatcher(kwords)
				}).on('typeahead:selected', function() {

				});
			 }
	});

$('#company-logo').change(function() {

var $bar = $('.upload-progress');
$bar.width(0);
$bar.text(0 + '%');

var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

var lf = $('#company-logo').val();

if (is_chrome) {
	lf = lf.substr(12);
}

var ext_lf = lf.substr(lf.length - 3);

if ((ext_lf == 'jpg') || (ext_lf == 'png') || (ext_lf == 'gif')) {

	$('#logo-alert').addClass('tog-alert');
	$('#logo_upload').submit();
	
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
		
	};

} else {
	
	$('#company-logo').val('');
	$('#logo-alert').removeClass('tog-alert');
	
}	

});

/* $('#logo_upload').submit(function(event) {

	event.preventDefault();
	document.getElementById('upload_target').onload = function() { alert("Done"); };

}); */

$('#logo-alert').addClass('tog-alert');

});

function confirmPref() {

var f = function() { updatePref(); };	
confirmation('Update system preferences?',f,null);

}

function updatePref() {

var coname = $('#company-name').val();
// company-logo
var sign = $('#signatory').val();
var gse = ($('#gs-enabled').prop('checked')) ? 1 : 0;
var ggp = $('#grace-period').val();
var ggpe = ($('#ggp-enabled').prop('checked')) ? 1 : 0;

$.ajax({
	url: 'preferences-ajax.php?p=update',
	type: 'post',
	data: {pconame: coname, psign: sign, pgse: gse, pggp: ggp, pggpe: ggpe},
	success: function(data, status) {
		notification(data,null);
	}
});

}

