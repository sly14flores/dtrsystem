<!DOCTYPE html>
<html lang="en">
<head>
<meta name="description" content="Daily Time Record">
<meta name="author" content="sly@unlimited">
<link rel="shortcut icon" href="../favicon.ico">

<title>CS Form No. 48 - Print | DTR System</title>

<style type="text/css">

* {
	margin: 0;
	padding: 0;
}

body {
	font: 12px Arial;
}

#wrapper {
	width: 100%;
	position: relative;
	margin-left: auto;
	margin-right: auto;
}

#left, #right {
	width: 49%;
}

#left {
	position: absolute;
	top: 0px;
	left: 0px;
}

#right {
	position: absolute;
	top: 0px;
	right: 0px;
}

#header {
	width: 100%;
	text-align: center;
}

.left-italic {
	font-style: italic; text-align: left;
}

.right-italic {
	font-style: italic; text-align: right;
}

#second-header {
	width: 100%;
	border-collapse: collapse;
}

#second-header td {
	padding: 1px;
}

#tab-dtr {
	width: 100%;
	border-collapse: collapse;
	margin-top: 15px;
}

#tab-dtr td {
	border: 1px solid;
	text-align: center;	
}

#tab-dtr tfoot td {
	border: none;
	font-size: 10px;
	text-align: left;
}

</style>
<script type="text/javascript">

function onLoadf() {

// var w = document.getElementById('wrapper').clientWidth;
// alert(w);

}

</script>
</head>
<body onload="onLoadf();">
<?php

$swh = $_GET['swh'];
$su = $_GET['su'];

require '../config.php';
require '../globalf.php';

$feid = (isset($_GET['feid'])) ? $_GET['feid'] : "";
$fename = (isset($_GET['fename'])) ? $_GET['fename'] : "";
$fdept = (isset($_GET['fdept'])) ? $_GET['fdept'] : 0;
$fsename = (isset($_GET['fsename'])) ? $_GET['fsename'] : 0;
$sday = (isset($_GET['sday'])) ? $_GET['sday'] : "";
if ($sday != "") $sday = date("Y-m-d",strtotime($sday));
$ssday = $sday;
$eday = (isset($_GET['eday'])) ? $_GET['eday'] : "";
if ($eday != "") $eday = date("Y-m-d",strtotime($eday));

$join_employee = "LEFT JOIN employees ON dtr.dtr_employee = employees.employee_fid";
$filter = " WHERE dtr_employee != 0";
$c1 = " and dtr_employee = '$feid'";
$c2 = " and concat(employee_lastname, ', ', employee_firstname) like '$fename%'";
$c3 = " and employee_dept = $fdept";

if ($feid == "") $c1 = "";
if ($fename == "") $c2 = "";
if ($fdept == 0) $c3 = "";

if ($fsename != 0) $feid = $fsename;
if ($fsename != 0) {
	$c1 = " and dtr_employee = '$feid'";
	$c2 = "";
	$c3 = "";
}

$filter .= $c1 . $c2 . $c3;

$full_name = "";
$scheme = 0;
$gperiod = 0;

$doff = array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);
$am_arr = array("Mon"=>"08:00:00","Tue"=>"08:00:00","Wed"=>"08:00:00","Thu"=>"08:00:00","Fri"=>"08:00:00","Sat"=>"08:00:00","Sun"=>"08:00:00");
$am_dep = array("Mon"=>"12:00:00","Tue"=>"12:00:00","Wed"=>"12:00:00","Thu"=>"12:00:00","Fri"=>"12:00:00","Sat"=>"12:00:00","Sun"=>"12:00:00");
$pm_arr = array("Mon"=>"13:00:00","Tue"=>"13:00:00","Wed"=>"13:00:00","Thu"=>"13:00:00","Fri"=>"13:00:00","Sat"=>"13:00:00","Sun"=>"13:00:00");
$pm_dep = array("Mon"=>"17:00:00","Tue"=>"17:00:00","Wed"=>"17:00:00","Thu"=>"17:00:00","Fri"=>"17:00:00","Sat"=>"17:00:00","Sun"=>"17:00:00");
$wh = array("Mon"=>8,"Tue"=>8,"Wed"=>8,"Thu"=>8,"Fri"=>8,"Sat"=>8,"Sun"=>8);

$transitional = array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);
$am_out_t = array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);
$pm_in_t = array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);
$pm_out_t = array("Mon"=>0,"Tue"=>0,"Wed"=>0,"Thu"=>0,"Fri"=>0,"Sat"=>0,"Sun"=>0);

$incharge = "";
$sql = "SELECT dtr_employee, (select concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) from employees where employee_fid = dtr_employee) full_name, (select dept_name from departments where dept_id = employee_dept) department, (select concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) from employees where employee_id = (select dept_head from departments where dept_id = employee_dept)) in_charge, (select grace_period from schemes where scheme_id = employee_scheme) gperiod, employee_scheme FROM dtr $join_employee $filter ORDER BY dtr_employee LIMIT 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$full_name = $rec['full_name'];
	$gperiod = $rec['gperiod'];
	$scheme = $rec['employee_scheme'];	
	$incharge = $rec['in_charge'];
}
db_close();

$sql = "SELECT grace_period, (select gperiod_global_enabled from preferences where preference_id = 1) global_gperiod_enabled, (select preference_company_gperiod from preferences where preference_id = 1) global_gperiod FROM schemes WHERE scheme_id = $scheme";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$gperiod = $rec['grace_period'];
	if ($rec['global_gperiod_enabled'] == 1) $gperiod = $rec['global_gperiod'];
}
db_close();

$sql = "SELECT preference_company_signatory, signatory_global_enabled, preference_company_gperiod, gperiod_global_enabled FROM preferences WHERE preference_id = 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	if ($rec['signatory_global_enabled'] == 1) $incharge = $rec['preference_company_signatory'];
}
db_close();

?>
<div id="wrapper">
<div id="left">
<?php

$report_body = '<tbody>';

$total_undertime = 0;
$total_work_hours = 0;
$regular_days = 0;
$saturdays = 0;

$sql = "SELECT mon_off, tue_off, wed_off, thu_off, fri_off, sat_off, sun_off, mon_am_arrival, tue_am_arrival, wed_am_arrival, thu_am_arrival, fri_am_arrival, sat_am_arrival, sun_am_arrival, mon_am_departure, tue_am_departure, wed_am_departure, thu_am_departure, fri_am_departure, sat_am_departure, sun_am_departure, mon_pm_arrival, tue_pm_arrival, wed_pm_arrival, thu_pm_arrival, fri_pm_arrival, sat_pm_arrival, sun_pm_arrival, mon_pm_departure, tue_pm_departure, wed_pm_departure, thu_pm_departure, fri_pm_departure, sat_pm_departure, sun_pm_departure, mon_rh, tue_rh, wed_rh, thu_rh, fri_rh, sat_rh, sun_rh, mon_t, tue_t, wed_t, thu_t, fri_t, sat_t, sun_t, mon_t, tue_t, wed_t, thu_t, fri_t, sat_t, sun_t, mon_am_out_t, mon_pm_in_t, mon_pm_out_t, tue_am_out_t, tue_pm_in_t, tue_pm_out_t, wed_am_out_t, wed_pm_in_t, wed_pm_out_t, thu_am_out_t, thu_pm_in_t, thu_pm_out_t, fri_am_out_t, fri_pm_in_t, fri_pm_out_t, sat_am_out_t, sat_pm_in_t, sat_pm_out_t, sun_am_out_t, sun_pm_in_t, sun_pm_out_t FROM schedules WHERE sched_sid = $scheme";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$doff = array("Mon"=>$rec['mon_off'],"Tue"=>$rec['tue_off'],"Wed"=>$rec['wed_off'],"Thu"=>$rec['thu_off'],"Fri"=>$rec['fri_off'],"Sat"=>$rec['sat_off'],"Sun"=>$rec['sun_off']);
		
	$am_arr = array("Mon"=>$rec['mon_am_arrival'],"Tue"=>$rec['tue_am_arrival'],"Wed"=>$rec['wed_am_arrival'],"Thu"=>$rec['thu_am_arrival'],"Fri"=>$rec['fri_am_arrival'],"Sat"=>$rec['sat_am_arrival'],"Sun"=>$rec['sun_am_arrival']);
	$am_arr = array("Mon"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['mon_am_arrival']))),"Tue"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['tue_am_arrival']))),"Wed"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['wed_am_arrival']))),"Thu"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['thu_am_arrival']))),"Fri"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['fri_am_arrival']))),"Sat"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['sat_am_arrival']))),"Sun"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['sun_am_arrival']))));
	
	$am_dep = array("Mon"=>$rec['mon_am_departure'],"Tue"=>$rec['tue_am_departure'],"Wed"=>$rec['wed_am_departure'],"Thu"=>$rec['thu_am_departure'],"Fri"=>$rec['fri_am_departure'],"Sat"=>$rec['sat_am_departure'],"Sun"=>$rec['sun_am_departure']);

	$pm_arr = array("Mon"=>$rec['mon_pm_arrival'],"Tue"=>$rec['tue_pm_arrival'],"Wed"=>$rec['wed_pm_arrival'],"Thu"=>$rec['thu_pm_arrival'],"Fri"=>$rec['fri_pm_arrival'],"Sat"=>$rec['sat_pm_arrival'],"Sun"=>$rec['sun_pm_arrival']);

	$pm_dep = array("Mon"=>$rec['mon_pm_departure'],"Tue"=>$rec['tue_pm_departure'],"Wed"=>$rec['wed_pm_departure'],"Thu"=>$rec['thu_pm_departure'],"Fri"=>$rec['fri_pm_departure'],"Sat"=>$rec['sat_pm_departure'],"Sun"=>$rec['sun_pm_departure']);

	$wh = array("Mon"=>$rec['mon_rh'],"Tue"=>$rec['tue_rh'],"Wed"=>$rec['wed_rh'],"Thu"=>$rec['thu_rh'],"Fri"=>$rec['fri_rh'],"Sat"=>$rec['sat_rh'],"Sun"=>$rec['sun_rh']);

	$transitional = array("Mon"=>$rec['mon_t'],"Tue"=>$rec['tue_t'],"Wed"=>$rec['wed_t'],"Thu"=>$rec['thu_t'],"Fri"=>$rec['fri_t'],"Sat"=>$rec['sat_t'],"Sun"=>$rec['sun_t']);

	$am_out_t = array("Mon"=>$rec['mon_am_out_t'],"Tue"=>$rec['tue_am_out_t'],"Wed"=>$rec['wed_am_out_t'],"Thu"=>$rec['thu_am_out_t'],"Fri"=>$rec['fri_am_out_t'],"Sat"=>$rec['sat_am_out_t'],"Sun"=>$rec['sun_am_out_t']);
	$pm_in_t = array("Mon"=>$rec['mon_pm_in_t'],"Tue"=>$rec['tue_pm_in_t'],"Wed"=>$rec['wed_pm_in_t'],"Thu"=>$rec['thu_pm_in_t'],"Fri"=>$rec['fri_pm_in_t'],"Sat"=>$rec['sat_pm_in_t'],"Sun"=>$rec['sun_pm_in_t']);
	$pm_out_t = array("Mon"=>$rec['mon_pm_out_t'],"Tue"=>$rec['tue_pm_out_t'],"Wed"=>$rec['wed_pm_out_t'],"Thu"=>$rec['thu_pm_out_t'],"Fri"=>$rec['fri_pm_out_t'],"Sat"=>$rec['sat_pm_out_t'],"Sun"=>$rec['sun_pm_out_t']);
	
}
db_close();

while (strtotime($sday) <= strtotime($eday)) {
$tsday = date ("Y-m-d", strtotime("+1 day", strtotime($sday)));

// date options
$halfd = array("$sday"=>0);
$dleave = array("$sday"=>0);
$leave_note = array("$sday"=>"");
$show_leave_note = array("$sday"=>0);
$sql = "SELECT option_id, option_fid, option_date, halfday, dtr_leave, leave_note, show_leave_note FROM dtr_options WHERE option_fid = '$feid' AND option_date = '$sday'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$halfd[$sday] = $rec['halfday'];
	$dleave[$sday] = $rec['dtr_leave'];
	$leave_note[$sday] = $rec['leave_note'];
	$show_leave_note[$sday] = $rec['show_leave_note'];
}
db_close();

$mor_in = "";
$mor_out = "";
$aft_in = "";
$aft_out = "";

$smor_in = "";
$smor_out = "";
$saft_in = "";
$saft_out = "";

$is_manual_mi = 0;
$is_manual_mo = 0;
$is_manual_ai = 0;
$is_manual_ao = 0;

// hours/minutes computations
$d_am_arr = date("Y-m-d H:i", strtotime("$sday ".$am_arr[date("D",strtotime($sday))]));
$d_am_dep = date("Y-m-d H:i", strtotime("$sday ".$am_dep[date("D",strtotime($sday))]));
$d_pm_arr = date("Y-m-d H:i", strtotime("$sday ".$pm_arr[date("D",strtotime($sday))]));
$d_pm_dep = date("Y-m-d H:i", strtotime("$sday ".$pm_dep[date("D",strtotime($sday))]));

$am_late = 0;
$am_undr = 0;
$pm_late = 0;
$pm_undr = 0;

$work_hour = 0;
$undertime = 0;
$undr_hour = "";
$undr_minute = "";
//

$sql = "SELECT dtr_no, machine_id, dtr_employee, date_time_log, time_is_manual, log_order, ignored, transitional_order FROM dtr $join_employee $filter";
$date_span = " AND substr(date_time_log,1,10) = '$sday'";
if ($transitional[date("D",strtotime($sday))] == 1) {
$date_span = " AND date_time_log >= '$sday 00:00:00' AND date_time_log <= '$tsday 23:59:00'";
}
$sql .= $date_span . " ORDER BY date_time_log";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();

	// regular scheme
	if ($transitional[date("D",strtotime($sday))] == 0) {
		if (($rec['time_is_manual'] == 0) && ($rec['transitional_order'] == 0)) { // from biometrics
			if ($i == 0) {
				if ($rec['ignored'] == 0) {				
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$smor_in = date("h:i A",strtotime($rec['date_time_log']));
				
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;
				}
			}
			if ($i == 1) {
				if ($rec['ignored'] == 0) {				
				$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$smor_out = date("h:i A",strtotime($rec['date_time_log']));
				
				$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
				if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;
				}
			}
			if ($i == 2) {
			if ($rec['ignored'] == 0) {			
				$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$saft_in = date("h:i A",strtotime($rec['date_time_log']));
				
				$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
				if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;				
				}
			}
			if ($i == 3) {
			if ($rec['ignored'] == 0) {			
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$saft_out = date("h:i A",strtotime($rec['date_time_log']));
				
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;
				}
			}
		}

/* 		
		// from biometrics but logs count is not four
		if (($rec['time_is_manual'] == 0) && ($rec['transitional_order'] == 0)) {
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-2 Hours",strtotime($d_am_arr))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+2 Hours",strtotime($d_am_arr))))) ) {
				if ($rec['ignored'] == 0) {
					$smor_in = date("H:i",strtotime($rec['date_time_log']));
					$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				}
			}
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-30 Minutes",strtotime($d_am_dep))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+30 Minutes",strtotime($d_am_dep))))) ) {
				if ($rec['ignored'] == 0) {
					$smor_out = date("H:i",strtotime($rec['date_time_log']));
					$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				}
			}
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-30 Minutes",strtotime($d_pm_arr))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+30 Minutes",strtotime($d_pm_arr))))) ) {
				if ($rec['ignored'] == 0) {
					$saft_in = date("H:i",strtotime($rec['date_time_log']));
					$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				}
			}
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-2 Hours",strtotime($d_pm_dep))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+2 Hours",strtotime($d_pm_dep))))) ) {
				if ($rec['ignored'] == 0) {
					$saft_out = date("H:i",strtotime($rec['date_time_log']));
					$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				}
			}
		}		
		//		
		 */
		
		if (($rec['time_is_manual'] == 1) && ($rec['transitional_order'] == 0)) { // manual time logs
			if ($rec['log_order'] == 1) {
			if ($rec['ignored'] == 0) {
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$smor_in = date("h:i A",strtotime($rec['date_time_log']));
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;
				$is_manual_mi = 1;
				}
			}
			if ($rec['log_order'] == 2) {
			if ($rec['ignored'] == 0) {
				$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$smor_out = date("h:i A",strtotime($rec['date_time_log']));
				$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
				if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;
				$is_manual_mo = 1;	
				}
			}
			if ($rec['log_order'] == 3) {
			if ($rec['ignored'] == 0) {
				$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$saft_in = date("h:i A",strtotime($rec['date_time_log']));
				$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
				if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;
				$is_manual_ai = 1;
				}
			}
			if ($rec['log_order'] == 4) {
			if ($rec['ignored'] == 0) {
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$saft_out = date("h:i A",strtotime($rec['date_time_log']));
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;
				$is_manual_ao = 1;
				}
			}				
		}
	}
	// end regular scheme

	// transitional scheme
	if ($transitional[date("D",strtotime($sday))] == 1) {
		if (($rec['time_is_manual'] == 0) && ($rec['transitional_order'] > 0)) { // transitional schemes from biometrics
			if ( ($rec['transitional_order'] == 1) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) ) {
			if ($rec['ignored'] == 0) {
				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$d_am_arr = date("Y-m-d H:i", strtotime(substr($mor_in,0,10)." ".$am_arr[date("D",strtotime($sday))]));
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;
				}				
			}
			if ($rec['transitional_order'] == 2) {
			if ($am_out_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {			
					if ($rec['ignored'] == 0) {
						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;				
					}
				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {			
					if ($rec['ignored'] == 0) {
						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;				
					}
				}			
			}
			}
			if ($rec['transitional_order'] == 3) {
			if ($pm_in_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {
					if ($rec['ignored'] == 0) {
						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;
					}
				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {
					if ($rec['ignored'] == 0) {
						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;
					}
				}			
			}
			}
			if ($rec['transitional_order'] == 4) {
			if ( ($rec['ignored'] == 0) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) ) {
				$saft_out = date("H:i",strtotime($rec['date_time_log']));
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$d_pm_dep = date("Y-m-d H:i", strtotime(substr($aft_out,0,10)." ".$pm_dep[date("D",strtotime($sday))]));
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;				
				}
			}				
		}
		
		if (($rec['time_is_manual'] == 1) && ($rec['transitional_order'] > 0)) { // transitional manual time logs
			if ( ($rec['transitional_order'] == 1) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) ) {
			if ($rec['ignored'] == 0) {
				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$is_manual_mi = 1;
				$mino = $rec['dtr_no'];
				$d_am_arr = date("Y-m-d H:i", strtotime(substr($mor_in,0,10)." ".$am_arr[date("D",strtotime($sday))]));
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;
				}
			}
			if ($rec['transitional_order'] == 2) {
			if ($am_out_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {			
					if ($rec['ignored'] == 0) {
						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$is_manual_mo = 1;
						$mono = $rec['dtr_no'];
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;
					}
				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {			
					if ($rec['ignored'] == 0) {
						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$is_manual_mo = 1;
						$mono = $rec['dtr_no'];
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;
					}
				}			
			}
			}
			if ($rec['transitional_order'] == 3) {
			if ($pm_in_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {			
					if ($rec['ignored'] == 0) {
						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));				
						$is_manual_ai = 1;
						$aino = $rec['dtr_no'];
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;
					}
				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {			
					if ($rec['ignored'] == 0) {
						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));				
						$is_manual_ai = 1;
						$aino = $rec['dtr_no'];
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;
					}
				}			
			}
			}
			if ( ($rec['transitional_order'] == 4) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) ) {
			if ($rec['ignored'] == 0) {
				$saft_out = date("H:i",strtotime($rec['date_time_log']));
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$is_manual_ao = 1;
				$aono = $rec['dtr_no'];
				$d_pm_dep = date("Y-m-d H:i", strtotime(substr($aft_out,0,10)." ".$pm_dep[date("D",strtotime($sday))]));
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;
				}
			}				
		}
	}
	// end transitional scheme	
}

$work_hour = $wh[date("D",strtotime($sday))];
if ($rc == 1) $work_hour = 0;
if ($halfd[$sday] == 1) $work_hour = $wh[date("D",strtotime($sday))]/2;
$undertime = $am_late + $am_undr + $pm_late + $pm_undr;
if ($su == 1) {
$undr_hour = toHours($undertime,'%d');
$undr_minute = toMinutes($undertime,'%d');	
}
	
}
db_close();

if (($dleave[$sday] == 1) && ($show_leave_note[$sday] == 1)) {
	$work_hour = $wh[date("D",strtotime($sday))];
	$smor_in = $leave_note[$sday];
	$smor_out = $leave_note[$sday];
	$saft_in = $leave_note[$sday];
	$saft_out = $leave_note[$sday];
}
$total_work_hours += $work_hour;
if ( (date("D",strtotime($sday)) == "Sat") || (date("D",strtotime($sday)) == "Sun") ) {
	$saturdays += $work_hour;
} else {
	$regular_days += $work_hour;
}

// $note_mi = "";
// $note_mo = "";
// $note_ai = "";
// $note_ao = "";
$sql = "SELECT note_no, note_fid, note_date, note_log, note_order, enabled FROM dtr_notes WHERE note_date = '$sday'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
for ($i=0; $i<$rc; ++$i) {
	$rec = $rs->fetch_array();
		if ($rec['note_order'] == 1) {
			// $note_mi = $rec['note_log'];
			if ($rec['enabled'] == 1) $smor_in = $rec['note_log'];
		}
		if ($rec['note_order'] == 2) {
			// $note_mo = $rec['note_log'];
			if ($rec['enabled'] == 1) $smor_out = $rec['note_log'];
		}
		if ($rec['note_order'] == 3) {
			// $note_ai = $rec['note_log'];
			if ($rec['enabled'] == 1) $saft_in = $rec['note_log'];
		}
		if ($rec['note_order'] == 4) {
			// $note_ao = $rec['note_log'];	
			if ($rec['enabled'] == 1) $saft_out = $rec['note_log'];
		}	
	}
}		
db_close();

$report_body .= '<tr><td style="text-align: left; padding-left: 3px;">' . date("d",strtotime($sday)) . ' ' . date("-D",strtotime($sday)) . '</td><td>' . $smor_in . '</td><td>' . $smor_out . '</td><td>' . $saft_in . '</td><td>' . $saft_out . '</td><td>' . $undr_hour . '</td><td>' . $undr_minute . '</td></tr>';
$sday = date ("Y-m-d", strtotime("+1 day", strtotime($sday)));
}

?>
<?php
$report_body .= '</tbody>';
$report_body .= '<tfoot>';
$report_body .= '<tr><td colspan="7" style="padding-top: 15px; padding-bottom: 30px; font-style: italic;">I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</td></tr>';
$report_body .= '<tr><td colspan="7" style="width: 100%; border-top: 1px solid; padding-bottom: 25px; text-align: center;">VERIFIED as to the prescribed office hours:</td></tr>';
$report_body .= '<tr><td colspan="7" style="width: 100%; text-align: center; font-weight: bold; font-size: 14px;">' . $incharge . '</td></tr>';
$report_body .= '<tr><td colspan="7" style="width: 100%; border-top: 1px solid; text-align: center;">In Charge</td></tr>';
$report_body .= '</tfoot>';
$report_body .= '</table>';

if ($swh == 0) {
	$regular_days = "&nbsp;";
	$saturdays = "&nbsp;";
}
$report_header = '<div id="header">';
$report_header .= '<p class="left-italic" style="margin-bottom: 10px;">Civil Service Form No. 48</p>';
$report_header .= '<h3>DAILY TIME RECORD</h3>';
$report_header .= '<p style="margin-bottom: 35px;">-----o0o-----</p>';
$report_header .= '<p style="font-size: 16px; font-weight: bold;">' . $full_name . '</p>';
$report_header .= '<p style="width: 100%; border-top: 1px solid; margin-bottom: 25px;">(Name)</p>';	
$report_header .= '<table id="second-header">';
$report_header .= '<tr><td colspan="3" class="left-italic">For the month of <span style="display: inline-block; width: 70%; border-bottom: 1px solid; padding-left: 10px; font-size: 14px;">' . date("F d - ",strtotime($ssday)) . date("F d, ",strtotime($eday)) . date("Y",strtotime($eday)) . '</span></td></tr>';
$report_header .= '<tr><td rowspan="2" class="left-italic" style="width: 40%;">Official hours for arrival<br>and departure</td><td class="right-italic" style="width: 30%;">Regular days&nbsp;&nbsp;</td><td><p style="width: 100%; border-bottom: 1px solid;">' . $regular_days . '</p></td></tr>';
$report_header .= '<tr><td class="right-italic">Saturdays&nbsp;&nbsp;</td><td><p style="width: 100%; border-bottom: 1px solid;">' . $saturdays . '</p></td></tr>';
$report_header .= '</table>';
$report_header .= '</div>';
$report_header .= '<table id="tab-dtr">';
$report_header .= '<thead>';
$report_header .= '<tr><td rowspan="2">Day</td><td colspan="2">A.M.</td><td colspan="2">P.M.</td><td colspan="2">Undertime</td></tr>';
$report_header .= '<tr><td>Arrival</td><td>Departure</td><td>Arrival</td><td>Departure</td><td>Hours</td><td>Minutes</td></tr>';
$report_header .= '</thead>';

$report = $report_header.$report_body;

echo $report;

?>
</div>
<div id="right">
<?php echo $report; ?>
</div>
</div>
</body>
</html>  