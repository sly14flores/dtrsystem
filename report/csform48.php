<?php

$onduty = json_decode($_POST['onduty_arr'],true);
$doff = json_decode($_POST['doff_arr'],true);
$show_sat_sun = $_POST['show_sat_sun'];
$dtr = $_POST['dtr_arr'];
$notes = $_POST['notes_arr'];
$notes_enabled = $_POST['notes_enabled_arr'];
$notes_enabled_arr = json_decode($notes_enabled,true);
$merge_notes = json_decode($_POST['merge_note_arr'],true); // convert json string post data to php array
$dtr_meta = $_POST['dtr_meta'];

$rcmonth = (isset($_GET['rcmonth'])) ? $_GET['rcmonth'] : "00";
$rcyear = (isset($_GET['rcyear'])) ? $_GET['rcyear'] : "";
$rcperiod = (isset($_GET['rcperiod'])) ? $_GET['rcperiod'] : "first-half";

$first_half = "first-half";
$second_half = "second-half";
$period = $rcperiod;

$last_day_of_the_month = date("t",strtotime("$rcyear-$rcmonth-01"));
$sday = ($period == $first_half) ? date("$rcyear-$rcmonth-01") : date("$rcyear-$rcmonth-16"); // first day to 15th day
$eday = ($period == $first_half) ? date("$rcyear-$rcmonth-15") : date("$rcyear-$rcmonth-").$last_day_of_the_month; // 16th day to last day of the month
$sday_cache = $sday;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CS Form 48">
    <meta name="author" content="sly@unlimited">
    <link rel="shortcut icon" href="../favicon.ico">

    <title>CS Form No. 48 - Print | DTR System</title>
	<style type="text/css">
	
	* {
		margin: 0;
		padding: 0;
	}
	
	body {
		font: 100% arial, sans-serif;
	}
	
	.wrapper {
		position: relative;
	}
	
	.left-report {
		width: 48%;
		position: absolute;
		top: 0;
		left: 0;
	}
	
	.right-report {
		width: 48%;
		position: absolute;
		top: 0;
		right: 0;
	}	
	
	.header {
		text-align: center;
	}
	
	.header p {
		font-size: 11px;
		margin-top: 3px;		
	}
	
	.header h4:first-of-type {
		margin-top: 15px;
	}
	
	.header h4 {
		margin-top: 10px;		
	}	
	
	.profile {
		margin-top: 15px;
		font-size: 11px;
	}

	.profile table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 5px;
	}
	
	.profile table td {
		padding: 2px;
	}
	
	.profile .meta-profile {
		text-align: center;
		border-bottom: 1px groove;
	}
	
	.profile > p {
		margin-top: 10px;
	}
	
	.employee_name {
		text-transform: uppercase;
	}
	
	.dtr {
		margin-top: 20px;
	}
	
	.dtr table {
		width: 100%;
		border-collapse: collapse;
		font-size: 12px;
		text-align: center;
	}
	
	.dtr table td {
		padding: 3px;
		border: 1px groove;
	}
	
	.footer {
		margin-top: 15px;
		font-size: 11px;
	}
	
	.footer p:nth-child(1) {
		text-indent: 30px;
		line-height: 15px;
	}
	
	.footer p:nth-child(2), .footer p:nth-child(3) {
		text-align: center;
	}
	
	.footer p:nth-child(2) {
		width: 75%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 40px;
		padding-bottom: 3px;
		margin-bottom: 3px;
		border-bottom: 1px groove;
		font-size: 12px;		
	}
	
	.footer p:nth-child(4) {
		margin-top: 20px;
		border-bottom: 1px groove;
	}
	
	.footer p:nth-child(5) {
		margin-top: 20px;
	}
	
	.footer p:nth-child(6) {
		width: 75%;
		margin-left: auto;
		margin-right: auto;
		margin-top: 50px;
		padding-bottom: 3px;
		margin-bottom: 3px;
		border-bottom: 1px groove;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		text-transform: uppercase;
	}
	
	.footer p:nth-child(7) {
		text-align: center;		
	}
	
	</style>
  </head>

  <body role="document">
  <div class="wrapper">
  <div class="left-report">
	<div class="header">
	<p>Republic of the Philippines</p>
	<p>Province of Ilocos Sur</p>
	<p>Municipality of Sta. Lucia</p>
	<h4>CIVIL SERVICE FORM NO. 48</h4>
	<h4>DAILY TIME RECORD</h4>
	</div>
	<div class="profile">
	<table>
	<tr><td style="width: 15%;">NAME:</td><td class="meta-profile employee_name">&nbsp;</td></tr>
	</table>
	<table>
	<tr><td style="width: 30%;">For the month of:</td><td class="meta-profile dtr_month_year">&nbsp;</td></tr>
	</table>
	<p>OFFICIAL HOURS For Arrival &amp; Departure</p>
	</div>
	<div class="dtr">
	<table>
	<thead>
	<tr><td rowspan="2">&nbsp;</td><td colspan="2">Morning</td><td colspan="2">Afternoon</td></tr>
	<tr><td>Arrival</td><td>Departure</td><td>Arrival</td><td>Departure</td></tr>
	</thead>
	<tbody>
	<?php
	
	$merge_note_index = 0;
	while (strtotime($sday) <= strtotime($eday)) {
		
	$current_date = date("j",strtotime($sday));
	$current_day = date("D",strtotime($sday));

	?>
	
	<?php if (($current_day == "Sat") && ($onduty['onduty'][0][$sday] == 0)) { ?>
	
	<?php if ($show_sat_sun == 1) { ?>
	<tr><td><?php echo $current_date; ?></td><td colspan="4">Saturday</td></tr>
	<?php } else { ?>
	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>
	
	<?php } elseif (($current_day == "Sun") && ($onduty['onduty'][0][$sday] == 0)) { ?>
	
	<?php if ($show_sat_sun == 1) { ?>
	<tr><td><?php echo $current_date; ?></td><td colspan="4">Sunday</td></tr>
	<?php } else { ?>	
	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>	
	
	<?php } else { ?>
	
	<?php
	if ($notes_enabled_arr['notes_enabled'][$merge_note_index]['morning_arrival_'.$current_date] == 1) {
	if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 5) {
	?>
	<tr><td><?php echo $current_date; ?></td><td colspan="4" class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>
	<?php if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 6) { ?>
	<tr><td><?php echo $current_date; ?></td><td colspan="2" class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	<?php } ?>
	<?php if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 7) { ?>
	<tr><td><?php echo $current_date; ?></td><td>&nbsp;</td><td>&nbsp;</td><td colspan="2" class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>
	<?php if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 1) { ?>
	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>
	<?php } ?>
	<?php } else { ?>

	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>

	<?php } ?>
	<?php } ?>
	
	<?php
	
	$sday = date ("Y-m-d", strtotime("+1 day", strtotime($sday)));
	
	$merge_note_index++;
	
	}
	
	?>
	</tbody>
	</table>
	</div>
	<div class="footer">
	<p>I HEREBY CERFITY that on my honor the above is true and correct reports of hours worked performed; records of which was made daily at the time of arrival and departure from office hours.</p>
	<p class="employee_name">&nbsp;</p>
	<p>Printed Name &amp; Signature of Employee</p>
	<p>&nbsp;</p>
	<p>Verified as to prescribed office hours</p>
	<p class="department_signatory">&nbsp;</p>
	<p class="signatory_title">&nbsp;</p>
	</div>
  </div>
<?php $sday = $sday_cache; ?>
  <div class="right-report">
	<div class="header">
	<p>Republic of the Philippines</p>
	<p>Province of Ilocos Sur</p>
	<p>Municipality of Sta. Lucia</p>
	<h4>CIVIL SERVICE FORM NO. 48</h4>
	<h4>DAILY TIME RECORD</h4>
	</div>
	<div class="profile">
	<table>
	<tr><td style="width: 15%;">NAME:</td><td class="meta-profile employee_name">&nbsp;</td></tr>
	</table>
	<table>
	<tr><td style="width: 30%;">For the month of:</td><td class="meta-profile dtr_month_year">&nbsp;</td></tr>
	</table>
	<p>OFFICIAL HOURS For Arrival &amp; Departure</p>
	</div>
	<div class="dtr">
	<table>
	<thead>
	<tr><td rowspan="2">&nbsp;</td><td colspan="2">Morning</td><td colspan="2">Afternoon</td></tr>
	<tr><td>Arrival</td><td>Departure</td><td>Arrival</td><td>Departure</td></tr>
	</thead>
	<tbody>
	<?php
	
	$merge_note_index = 0;
	while (strtotime($sday) <= strtotime($eday)) {
		
	$current_date = date("j",strtotime($sday));
	$current_day = date("D",strtotime($sday));	

	?>
	
	<?php if (($current_day == "Sat") && ($onduty['onduty'][0][$sday] == 0)) { ?>
	
	<?php if ($show_sat_sun == 1) { ?>
	<tr><td><?php echo $current_date; ?></td><td colspan="4">Saturday</td></tr>
	<?php } else { ?>
	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>
	
	<?php } elseif (($current_day == "Sun") && ($onduty['onduty'][0][$sday] == 0)) { ?>
	
	<?php if ($show_sat_sun == 1) { ?>
	<tr><td><?php echo $current_date; ?></td><td colspan="4">Sunday</td></tr>
	<?php } else { ?>	
	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>	
	
	<?php } else { ?>
	
	<?php
	if ($notes_enabled_arr['notes_enabled'][$merge_note_index]['morning_arrival_'.$current_date] == 1) {
	if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 5) {
	?>
	<tr><td><?php echo $current_date; ?></td><td colspan="4" class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>
	<?php if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 6) { ?>
	<tr><td><?php echo $current_date; ?></td><td colspan="2" class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	<?php } ?>
	<?php if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 7) { ?>
	<tr><td><?php echo $current_date; ?></td><td>&nbsp;</td><td>&nbsp;</td><td colspan="2" class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>
	<?php if ($merge_notes['merge_note'][$merge_note_index]['morning_arrival_'.$current_date] == 1) { ?>
	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>	
	<?php } ?>		
	<?php } else { ?>

	<tr><td><?php echo $current_date; ?></td><td class="morning_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="morning_departure_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_arrival_<?php echo $current_date; ?>">&nbsp;</td><td class="afternoon_departure_<?php echo $current_date; ?>">&nbsp;</td></tr>

	<?php } ?>
	<?php } ?>
	
	<?php
	
	$sday = date ("Y-m-d", strtotime("+1 day", strtotime($sday)));
	
	$merge_note_index++;
	
	}
	
	?>
	</tbody>
	</table>
	</div>
	<div class="footer">
	<p>I HEREBY CERFITY that on my honor the above is true and correct reports of hours worked performed; records of which was made daily at the time of arrival and departure from office hours.</p>
	<p class="employee_name">&nbsp;</p>
	<p>Printed Name &amp; Signature of Employee</p>
	<p>&nbsp;</p>
	<p>Verified as to prescribed office hours</p>
	<p class="department_signatory">&nbsp;</p>
	<p class="signatory_title">&nbsp;</p>
	</div>
  </div>  
  </div>
 
  <script src="../bootstrap/dist/js/jquery.min.js"></script>
  <script type="text/javascript">
	
	var obj = '<?php echo $dtr_meta; ?>';
	var obj_json = JSON.parse(obj);
	
	$.each(obj_json.dtr_meta, function(k, val) {
		$.each(val, function(key, value) {
			if ($('.'+key)[0]) $('.'+key).html(value);
		});
	});	
	
	obj = '<?php echo $dtr; ?>';
	obj_json = JSON.parse(obj);	
	
	$.each(obj_json.dtr, function(k, val) {
		$.each(val, function(key, value) {
			if ($('.'+key)[0]) $('.'+key).html(value);
		});
	});

	obj = '<?php echo $notes_enabled; ?>';
	obj_json = JSON.parse(obj);	
	
	obj1 = '<?php echo $notes; ?>';
	obj_json1 = JSON.parse(obj1);	

	$.each(obj_json.notes_enabled, function(k, val) {
		$.each(val, function(key, value) {
			console.log(key);
			if ((value == 1) || (value == '1')) {
				if ($('.'+key)[0]) $('.'+key).html(obj_json1.notes[k][key]);				
			}
		});		
	});	
	
  </script>
  </body>
</html>