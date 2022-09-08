<?php

session_start();
$uid = $_SESSION['user_account_id'];

require 'config.php';
require 'globalf.php';

$req = "";
$START_T = "START TRANSACTION;";
$END_T = "COMMIT;";

if (isset($_GET["p"])) $req = $_GET["p"];

$str_response = "";
$json = "";
$jpage = "";

switch ($req) {

case "upload_dtr":
$pdm = (isset($_POST['pdm'])) ? $_POST['pdm'] : "";
$pdy = (isset($_POST['pdy'])) ? $_POST['pdy'] : "";

if (($pdm == "") || ($pdy == "")) {
	echo "Please select/enter month and year.";
	return false;
}

$dtr_file = "dtr/dtr.txt";

if (!file_exists($dtr_file)) {
	echo "DTR file not found. Please upload the file AGL_0001.TXT.";
	return false;
}

$file = fopen($dtr_file,"rb");
$max_line = count(file($dtr_file));

$c = -1;

while (! feof($file)) {
	$line_txt[$c] = fgetcsv($file, 0, "\t");
	++$c;	
}

$mc = $c - 1;

for ($i=0; $i<$mc; $i++) {
	$dmid[$i] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line_txt[$i][1]);
	$dfid[$i] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line_txt[$i][2]);
	$dlog[$i] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line_txt[$i][9]);
	$dtlog[$i] = substr($dlog[$i],0,strlen($dlog[$i]) - 3) . ":00";
}

$sql = "alter table dtr AUTO_INCREMENT = 1";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$ym = "$pdy-$pdm";

// check if some logs exists in the database
$sql = "SELECT dtr_employee, date_time_log FROM dtr WHERE date_time_log LIKE '$ym-%'";
$existing_logs = "";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
	$rec = $rs->fetch_array();
	$existing_logs .= $rec['dtr_employee'] . " " . $rec['date_time_log'] . " ";
	}
}
db_close();
//

$str_compare = "";
$count_log = 0;
$omitted = 0;
$sql = 'INSERT INTO dtr (machine_id, dtr_employee, date_time_log, dtr_uid) VALUES ';
for ($i=0; $i<$mc; $i++) {
	if (preg_match("/\b" . $ym . "\b/i",$dtlog[$i])) { // filter month year from dtr.txt
		if (preg_match("/\b" . $dfid[$i] . " " . substr($dtlog[$i],0,10) . " " . substr($dtlog[$i], -8) . "\b/i",$existing_logs)) { // filter if logs are alread in the database
			++$omitted;
		} else {
			$sql .= '(' . $dmid[$i] . ', \'' . $dfid[$i] . '\', \'' . $dtlog[$i] . '\', ' . $uid . '),';
			++$count_log;
		}
	}
}

if ($count_log > 0) {
$sql = substr($sql,0,strlen($sql)-1);
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$str_response = "DTR successfully uploaded.";
if ($omitted > 0) $str_response .= "Some time logs were omitted because they were previously uploaded.";
} else {
$str_response = "No time logs for " . date("F ",strtotime("$ym-01")) . date("Y",strtotime("$ym-01")) . " or time logs already added.";
}

echo $str_response;
break;

case "contents_csform48":
$feid = (isset($_GET['feid'])) ? $_GET['feid'] : "";
$fename = (isset($_GET['fename'])) ? $_GET['fename'] : "";
$fsename = (isset($_GET['fsename'])) ? $_GET['fsename'] : 0;

$rcmonth = (isset($_GET['rcmonth'])) ? $_GET['rcmonth'] : "00";
$rcyear = (isset($_GET['rcyear'])) ? $_GET['rcyear'] : "";
$rcperiod = (isset($_GET['rcperiod'])) ? $_GET['rcperiod'] : "first-half";

if ($fsename != 0) $feid = $fsename;
if (($feid == "") && ($fename == "")) {
	echo "To view employee <strong>DTR (CS From 48)</strong> please select from <strong>Employee Dropdown menu</strong> or search by <strong>Employee ID</strong> or <strong>Employee Last Name and First Name</strong>; and select <strong>Month, Year </strong>and <strong>Period</strong> then press Go!";
	return;
}

if (($rcmonth == "00") || ($rcyear == "")) {
	echo "Please select <strong>Month</strong>/<strong>Year</strong>.";
	return;
}

$first_half = "first-half";
$second_half = "second-half";
$period = $rcperiod;

$last_day_of_the_month = date("t",strtotime("$rcyear-$rcmonth-01"));
$sday = ($period == $first_half) ? date("$rcyear-$rcmonth-01") : date("$rcyear-$rcmonth-16"); // first day to 15th day
$eday = ($period == $first_half) ? date("$rcyear-$rcmonth-15") : date("$rcyear-$rcmonth-").$last_day_of_the_month; // 16th day to last day of the month
$sday_b = $sday;

$str_period = "First Half - ";
if ($period == $second_half) $str_period = "Second Half - ";
$str_period .= "<i>" . date("M ",strtotime($sday)) . date("d-",strtotime($sday)) . date("d, ",strtotime($eday)) . $rcyear . "</i>";

$join_employee = "LEFT JOIN employees ON dtr.dtr_employee = employees.employee_fid";
$filter = " WHERE dtr_employee != 0";
$c1 = " and dtr_employee = '$feid'";
$c2 = " and concat(employee_lastname, ', ', employee_firstname) like '$fename%'";

if ($feid == "") $c1 = "";
if ($fename == "") $c2 = "";

if ($fsename != 0) {
	$c1 = " and dtr_employee = '$feid'";
	$c2 = "";
}

$filter .= $c1 . $c2;

$hfid = "";
$full_name = "";
$department = "";
$scheme = 0;
$scheme_name = "";
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

$sql = "SELECT dtr_employee, (select concat(employee_firstname, ' ', SUBSTRING(employee_middlename,1,1), '. ', employee_lastname) from employees where employee_fid = dtr_employee) full_name, (select dept_name from departments where dept_id = employee_dept) department, employee_dept, employee_scheme, (select scheme_name from schemes where scheme_id = employee_scheme) employee_scheme_name FROM dtr $join_employee $filter ORDER BY dtr_employee LIMIT 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array(MYSQLI_ASSOC);
	$hfid = $rec['dtr_employee'];
	$full_name = $rec['full_name'];
	$department = $rec['department'];
	$employee_dept = $rec['employee_dept'];
	$scheme = $rec['employee_scheme'];
	$scheme_name = $rec['employee_scheme_name'];
} else {
	echo "No time logs found.";
	return;
}
db_close();

// print_r($rec);

/**
signatory and title
**/
$department_signatory = "";
$signatory_title = "";
$sql = "SELECT ifnull((select concat(employee_firstname, ' ', SUBSTRING(employee_middlename,1,1), '. ', employee_lastname) from employees where employee_id = dept_head),'') department_signatory, (SELECT employee_position FROM employees WHERE employee_id = dept_head) signatory_title FROM departments WHERE dept_id = $employee_dept";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array(MYSQLI_ASSOC);
	$department_signatory = $rec['department_signatory'];
	$signatory_title = $rec['signatory_title'];
}
db_close();

if ($scheme == 0) {
	echo "No <strong>scheme</strong> selected for <strong>$full_name</strong>.  Go to <strong>Employees</strong> tab then edit and select scheme.";
	return;
}

$department_signatory_global = "";
$signatory_title_global = "";
$signatory_global_enabled = 0;
$sql = "SELECT grace_period, (select gperiod_global_enabled from preferences where preference_id = 1) global_gperiod_enabled, (select preference_company_gperiod from preferences where preference_id = 1) global_gperiod, @preference_company_signatory := (SELECT preference_company_signatory FROM preferences WHERE preference_id = 1) department_signatory_global, (SELECT employee_position FROM employees WHERE concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) = @preference_company_signatory) signatory_title_global, (SELECT signatory_global_enabled FROM preferences WHERE preference_id = 1) signatory_global_enabled FROM schemes WHERE scheme_id = $scheme";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$gperiod = $rec['grace_period'];
	if ($rec['global_gperiod_enabled'] == 1) $gperiod = $rec['global_gperiod'];
	$department_signatory_global = $rec['department_signatory_global'];
	$signatory_title_global = $rec['signatory_title_global'];
	$signatory_global_enabled = $rec['signatory_global_enabled'];
}
db_close();

$str_response .= '<div class="form-group">';
$str_response .= '<button type="button" class="btn btn-primary btn-sm refresh-dtr">Refresh</button>';
// $str_response .= '<div class="pull-right">';
// $str_response .= '<div class="checkbox">';
// $str_response .= '<label><input id="show-hours" type="checkbox"> Show Regular days/Saturdays work hours</label>';
// $str_response .= '</div>';
// $str_response .= '<div class="checkbox">';
// $str_response .= '<label><input id="show-undertime" type="checkbox"> Show Undertime on DTR report</label>';
// $str_response .= '</div>';
// $str_response .= '</div>';

$str_response .= '<div class="pull-right">';
$str_response .= '<div class="checkbox">';
$str_response .= '<label><input id="show-sat-sun" type="checkbox" checked="checked" onchange="showSatSun(this.checked);"> Show Saturday/Sunday</label>';
$str_response .= '</div>';
$str_response .= '</div>';

$str_response .= '</div>';
$str_response .= '<table id="header" class="table">';
$str_response .= '<thead>';
$str_response .= '<tr>';
$str_response .= '<td>Name: <span class="emp">' . $full_name . '</span></td>';
$str_response .= '<td>Department: <span class="emp">' . $department . '</span><input id="hfid" type="hidden" value="' . $hfid . '"></td>';
$str_response .= '<td>Scheme: <span class="emp">' . $scheme_name . '</span></td>';
$str_response .= '<td>Period: <span class="emp">' . $str_period . '</span></td>';
$str_response .= '</tr>';
$str_response .= '</thead>';
$str_response .= '</table>';
$str_response .= '<form name="frmCSForm48" id="frmCSForm48">';
$str_response .= '<table class="table table-bordered table-hover table-fixed-header">';
$str_response .= '<thead class="header">';
$str_response .= '<tr><td rowspan="3" style="vertical-align: middle; width: 85px; text-align: center;">Day</td><td colspan="4" style="text-align: center;">A.M.</td><td colspan="4" style="text-align: center;">P.M.</td><td rowspan="3" style="text-align: center; vertical-align: middle;">Hrs</td><td colspan="2" style="text-align: center;">Undertime</td><td rowspan="3" style="text-align: center; vertical-align: middle;">Remarks</td></tr>';
$str_response .= '<tr><td style="width: 250px; text-align: center;" colspan="2"><strong>Arrival</strong></td><td style="width: 250px; text-align: center;" colspan="2"><strong>Departure</strong></td><td style="width: 250px; text-align: center;" colspan="2"><strong>Arrival</strong></td><td style="width: 250px; text-align: center;" colspan="2"><strong>Departure</strong></td><td rowspan="2" style="vertical-align: middle;">Hours</td><td rowspan="2" style="vertical-align: middle;">Minutes</td></tr>';
$str_response .= '<tr><td style="width: 70px;">Time</td><td style="width: 180px;">Note</td><td style="width: 70px;">Time</td><td style="width: 180px;">Note</td><td style="width: 70px;">Time</td><td style="width: 180px;">Note</td><td style="width: 70px;">Time</td><td style="width: 180px;">Note</td></tr>';
$str_response .= '</thead>';
$str_response .= '<tbody>';

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
	// $am_arr = array("Mon"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['mon_am_arrival']))),"Tue"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['tue_am_arrival']))),"Wed"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['wed_am_arrival']))),"Thu"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['thu_am_arrival']))),"Fri"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['fri_am_arrival']))),"Sat"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['sat_am_arrival']))),"Sun"=>date("H:i",strtotime("+$gperiod Minutes",strtotime($rec['sun_am_arrival']))));
	
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

$tnotify = "";
$istran = 0;
$dtr_date_arr = [];

// date options
$onduty = array();
$halfd = array();
$dleave = array();
$leve_note = array();

while (strtotime($sday) <= strtotime($eday)) {
$tsday = date ("Y-m-d", strtotime("+1 day", strtotime($sday)));

// date options
$sql = "SELECT option_id, option_fid, option_date, on_duty, halfday, dtr_leave, leave_note FROM dtr_options WHERE option_fid = '$feid' AND option_date = '$sday'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$onduty[$sday] = $rec['on_duty'];
	$halfd[$sday] = $rec['halfday'];
	$dleave[$sday] = $rec['dtr_leave'];
	$leave_note[$sday] = $rec['leave_note'];
} else {
	$onduty[$sday] = 0;
	$halfd[$sday] = 0;
	$dleave[$sday] = 0;
	$leave_note[$sday] = "";	
}
db_close();

$mino = 0;
$mono = 0;
$aino = 0;
$aono = 0;

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

// grace periods morning afternoon
$grace_am_arr = date("Y-m-d H:i", strtotime("$sday 08:07"));
$grace_am_dep = date("Y-m-d H:i", strtotime("$sday 12:25"));
$grace_pm_arr = date("Y-m-d H:i", strtotime("$sday 13:07"));
$grace_pm_dep = date("Y-m-d H:i", strtotime("$sday 17:00"));

$am_late = 0;
$am_undr = 0;
$pm_late = 0;
$pm_undr = 0;

$work_hour = 0;
$undertime = 0;
$undr_hour = "";
$undr_minute = "";
$remarks = "On duty";
//

$sql = "SELECT dtr_no, machine_id, dtr_employee, date_time_log, time_is_manual, log_order, explicit_log, ignored, transitional_order FROM dtr $join_employee $filter";
$date_span = " AND substr(date_time_log,1,10) = '$sday'";
if ($transitional[date("D",strtotime($sday))] == 1) {
$date_span = " AND date_time_log >= '$sday 00:00:00' AND date_time_log <= '$tsday 23:59:00'";
}
$sql .= " AND ignored = 0";
$sql .= $date_span . " ORDER BY date_time_log";

db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();

	// regular scheme
	if ($transitional[date("D",strtotime($sday))] == 0) {
		if (($rec['time_is_manual'] == 0) && ($rec['explicit_log'] == 0) && ($rec['transitional_order'] == 0)) { // from biometrics
			if ($i == 0) {
		
				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
												
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;
				
			}
			if ($i == 1) {
	
				$smor_out = date("H:i",strtotime($rec['date_time_log']));
				$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
											
				$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
				if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;

			}
			if ($i == 2) {
		
				$saft_in = date("H:i",strtotime($rec['date_time_log']));
				$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
												
				$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
				if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;

			}
			if ($i == 3) {
			
				$saft_out = date("H:i",strtotime($rec['date_time_log']));
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
												
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;

			}
		}
		

		if (($rec['explicit_log'] != 0) && ($rec['time_is_manual'] == 0) && ($rec['transitional_order'] == 0)) { // explicit logs

		
			if ($rec['explicit_log'] == 1) {

				$smor_in = "";
				$mor_in = "";
				
				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));

				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;

			}
			if ($rec['explicit_log'] == 2) {

				$smor_out = "";
				$mor_out = "";
			
				$smor_out = date("H:i",strtotime($rec['date_time_log']));
				$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));

				$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
				if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;

			}
			if ($rec['explicit_log'] == 3) {

				$saft_in = "";
				$aft_in = "";
			
				$saft_in = date("H:i",strtotime($rec['date_time_log']));
				$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));				

				$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
				if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;

			}
			if ($rec['explicit_log'] == 4) {

				$saft_out = "";
				$aft_out = "";
			
				$saft_out = date("H:i",strtotime($rec['date_time_log']));
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));

				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;

			}		
		}

/* 		
		// from biometrics but logs count is not four
		if (($rec['time_is_manual'] == 0) && ($rec['transitional_order'] == 0)) {
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-2 Hours",strtotime($d_am_arr))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+2 Hours",strtotime($d_am_arr))))) ) {

					$smor_in = date("H:i",strtotime($rec['date_time_log']));
					$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));

			}
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-30 Minutes",strtotime($d_am_dep))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+30 Minutes",strtotime($d_am_dep))))) ) {

					$smor_out = date("H:i",strtotime($rec['date_time_log']));
					$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));

			}
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-30 Minutes",strtotime($d_pm_arr))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+30 Minutes",strtotime($d_pm_arr))))) ) {

					$saft_in = date("H:i",strtotime($rec['date_time_log']));
					$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));

			}
			if ( (strtotime($rec['date_time_log']) >= strtotime(date("Y-m-d H:i",strtotime("-2 Hours",strtotime($d_pm_dep))))) && (strtotime($rec['date_time_log']) <= strtotime(date("Y-m-d H:i",strtotime("+2 Hours",strtotime($d_pm_dep))))) ) {

					$saft_out = date("H:i",strtotime($rec['date_time_log']));
					$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));

			}
		}		
		//
		 */
		
		if (($rec['time_is_manual'] == 1) && ($rec['transitional_order'] == 0)) { // manual time logs
			if ($rec['log_order'] == 1) {

				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$is_manual_mi = 1;
				$mino = $rec['dtr_no'];
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;

			}
			if ($rec['log_order'] == 2) {

				$smor_out = date("H:i",strtotime($rec['date_time_log']));
				$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$is_manual_mo = 1;
				$mono = $rec['dtr_no'];
				$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
				if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;

			}
			if ($rec['log_order'] == 3) {

				$saft_in = date("H:i",strtotime($rec['date_time_log']));
				$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));				
				$is_manual_ai = 1;
				$aino = $rec['dtr_no'];
				$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
				if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;

			}
			if ($rec['log_order'] == 4) {

				$saft_out = date("H:i",strtotime($rec['date_time_log']));
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$is_manual_ao = 1;
				$aono = $rec['dtr_no'];
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;

			}				
		}
	}		
	// end regular scheme

	// transitional scheme	
	if ($transitional[date("D",strtotime($sday))] == 1) {
		$istran = 1;
		if (($rec['time_is_manual'] == 0) && ($rec['transitional_order'] > 0)) { // transitional schemes from biometrics
			if ( ($rec['transitional_order'] == 1) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) ) {

				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$d_am_arr = date("Y-m-d H:i", strtotime(substr($mor_in,0,10)." ".$am_arr[date("D",strtotime($sday))]));
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;
				
			}
			if ($rec['transitional_order'] == 2) {
			if ($am_out_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {
	
						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;

				}				
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {

						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;

				}			
			}
			}
			if ($rec['transitional_order'] == 3) {
			if ($pm_in_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {

						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;

				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {

						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;

				}			
			}
			}
			if ( ($rec['transitional_order'] == 4) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) ) {

				$saft_out = date("H:i",strtotime($rec['date_time_log']));
				$aft_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$d_pm_dep = date("Y-m-d H:i", strtotime(substr($aft_out,0,10)." ".$pm_dep[date("D",strtotime($sday))]));
				$pm_undr = strtotime($d_pm_dep) - strtotime($aft_out);
				if (strtotime($aft_out) > strtotime($d_pm_dep)) $pm_undr = 0;

			}				
		}
		
		if (($rec['time_is_manual'] == 1) && ($rec['transitional_order'] > 0)) { // transitional manual time logs
			if ( ($rec['transitional_order'] == 1) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) ) {

				$smor_in = date("H:i",strtotime($rec['date_time_log']));
				$mor_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));
				$is_manual_mi = 1;
				$mino = $rec['dtr_no'];
				$d_am_arr = date("Y-m-d H:i", strtotime(substr($mor_in,0,10)." ".$am_arr[date("D",strtotime($sday))]));
				$am_late = strtotime($mor_in) - strtotime($d_am_arr);
				if (strtotime($mor_in) < strtotime($d_am_arr)) $am_late = 0;

			}
			if ($rec['transitional_order'] == 2) {
			if ($am_out_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {

						$smor_out = date("H:i",strtotime($rec['date_time_log']));
						$mor_out = date("Y-m-d H:i",strtotime($rec['date_time_log']));
						$is_manual_mo = 1;
						$mono = $rec['dtr_no'];
						$d_am_dep = date("Y-m-d H:i", strtotime(substr($mor_out,0,10)." ".$am_dep[date("D",strtotime($sday))]));
						if ($am_dep[date("D",strtotime($sday))] == "00:00:00") $d_am_dep = date("Y-m-d H:i", strtotime($tsday." ".$am_dep[date("D",strtotime($sday))]));
						$am_undr = strtotime($d_am_dep) - strtotime($mor_out);
						if (strtotime($mor_out) > strtotime($d_am_dep)) $am_undr = 0;

				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {

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
			if ($rec['transitional_order'] == 3) {
			if ($pm_in_t[date("D",strtotime($sday))] == 0) {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($sday))) {

						$saft_in = date("H:i",strtotime($rec['date_time_log']));
						$aft_in = date("Y-m-d H:i",strtotime($rec['date_time_log']));				
						$is_manual_ai = 1;
						$aino = $rec['dtr_no'];
						$d_pm_arr = date("Y-m-d H:i", strtotime(substr($aft_in,0,10)." ".$pm_arr[date("D",strtotime($sday))]));
						$pm_late = strtotime($aft_in) - strtotime($d_pm_arr);
						if (strtotime($aft_in) < strtotime($d_pm_arr)) $pm_late = 0;

				}
			} else {
				if (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) {			

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
			if ( ($rec['transitional_order'] == 4) && (date("d",strtotime($rec['date_time_log'])) == date("d",strtotime($tsday))) ) {

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
	// end transitional scheme
}

$work_hour = $wh[date("D",strtotime($sday))];
if ($rc == 1) $work_hour = 0;
if ($halfd[$sday] == 1) $work_hour = $wh[date("D",strtotime($sday))]/2;
$undertime = $am_late + $am_undr + $pm_late + $pm_undr;
$total_undertime += $undertime;
$undr_hour = toHours($undertime,'%d');
$undr_minute = toMinutes($undertime,'%d');
	
}
db_close();

if ($dleave[$sday] == 1) $work_hour = $wh[date("D",strtotime($sday))];
$total_work_hours += $work_hour;
if ( (date("D",strtotime($sday)) == "Sat") || (date("D",strtotime($sday)) == "Sun") ) {
	$saturdays += $work_hour;
} else {
	$regular_days += $work_hour;
}

if ($work_hour == 0) $remarks = "Absent";
if ($rc == 1) {
$work_hour = 0;
$remarks = '<span style="color: #A94442;">Incomplete Logs</span>';
}
if ($halfd[$sday] == 1) $remarks = "Half day";
if ($doff[date("D",strtotime($sday))] == 1) $remarks = "Day off";
if ($dleave[$sday] ==1 ) $remarks = $leave_note[$sday];

$nmi = 0;
$nmo = 0;
$nai = 0;
$nao = 0;
$note_mi = "";
$note_mo = "";
$note_ai = "";
$note_ao = "";
$bnote_mi = "add";
$bnote_mo = "add";
$bnote_ai = "add";
$bnote_ao = "add";
$note_mi_enabled = 0;
$note_mo_enabled = 0;
$note_ai_enabled = 0;
$note_ao_enabled = 0;
$merge_note_mi = 0;
$merge_note_mo = 0;
$merge_note_ai = 0;
$merge_note_ao = 0;
$sql = "SELECT note_no, note_fid, note_date, note_log, note_order, enabled FROM dtr_notes WHERE note_date = '$sday' AND note_fid = $feid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
for ($i=0; $i<$rc; ++$i) {
	$rec = $rs->fetch_array();
		if ($rec['note_order'] == 1) {
			$note_mi = $rec['note_log'];
			$bnote_mi = $note_mi;
			$nmi = $rec['note_no'];
			$note_mi_enabled = $rec['enabled'];
			$merge_note_mi = $rec['note_order'];
		}
		if ($rec['note_order'] == 2) {
			$note_mo = $rec['note_log'];
			$bnote_mo = $note_mo;
			$nmo = $rec['note_no'];
			$note_mo_enabled = $rec['enabled'];
			$merge_note_mo = $rec['note_order'];			
		}
		if ($rec['note_order'] == 3) {
			$note_ai = $rec['note_log'];
			$bnote_ai = $note_ai;
			$nai = $rec['note_no'];
			$note_ai_enabled = $rec['enabled'];
			$merge_note_ai = $rec['note_order'];
		}
		if ($rec['note_order'] == 4) {
			$note_ao = $rec['note_log'];
			$bnote_ao = $note_ao;
			$nao = $rec['note_no'];
			$note_ao_enabled = $rec['enabled'];
			$merge_note_ao = $rec['note_order'];			
		}
		if ($rec['note_order'] == 5) { // whole day
			$note_mi = $rec['note_log'];
			$bnote_mi = $note_mi;
			$nmi = $rec['note_no'];
			$note_mi_enabled = $rec['enabled'];
			$merge_note_mi = $rec['note_order'];			
		}
		if ($rec['note_order'] == 6) { // morning
			$note_mi = $rec['note_log'];
			$bnote_mi = $note_mi;
			$nmi = $rec['note_no'];
			$note_mi_enabled = $rec['enabled'];
			$merge_note_mi = $rec['note_order'];
		}
		if ($rec['note_order'] == 7) { // afternoon
			$note_mi = $rec['note_log'];
			$bnote_mi = $note_mi;
			$nmi = $rec['note_no'];
			$note_mi_enabled = $rec['enabled'];
			$merge_note_mi = $rec['note_order'];			
		}		
	}
}		
db_close();

$str_response .= '<tr id="row_' . date("Y-m-d",strtotime($sday)) . '">';
if ($istran == 1) $tnotify = 'style="color: #ff0000;"';
$str_response .= '<td style="text-align: left;"><a ' . $tnotify . ' href="javascript: dateCon(\'' . $hfid . '\',\'' . date("Y-m-d",strtotime($sday)) . '\',' . $transitional[date("D",strtotime($sday))] . ');" data-toggle="tooltip" data-placement="right" title="Manage Logs/Notes/Date Option">' . date("j (D)",strtotime($sday)) . '</a></td>';

if ($smor_in == "") $str_response .= '<td><a href="#" id="mora_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="0" data-url="dtr-ajax.php?p=manual_time&o=1" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="00:00">add</a></td>';
else {
	if ($is_manual_mi == 1) $str_response .= '<td><a href="#" id="mora_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $mino . '" data-url="dtr-ajax.php?p=manual_time&o=1" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="' . $smor_in . '">' . $smor_in . '</a></td>';
	else $str_response .= '<td>' . $smor_in . '</td>';
}
$str_response .= '<td><a href="#" id="ma_note_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $nmi . '" data-url="dtr-ajax.php?p=note_log&o=1" data-title="Add note - ' . date("M d",strtotime($sday)) . '" data-value="' . $note_mi . '">' . $bnote_mi . '</a></td>';

if ($smor_out == "") $str_response .= '<td><a href="#" id="morad_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="0" data-url="dtr-ajax.php?p=manual_time&o=2" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="00:00">add</a></td>';
else {
	if ($is_manual_mo == 1) $str_response .= '<td><a href="#" id="morad_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $mono . '" data-url="dtr-ajax.php?p=manual_time&o=2" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="' . $smor_out . '">' . $smor_out . '</a></td>';
	else $str_response .= '<td>' . $smor_out . '</td>';
}
$str_response .= '<td><a href="#" id="md_note_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $nmo . '" data-url="dtr-ajax.php?p=note_log&o=2" data-title="Add note - ' . date("M d",strtotime($sday)) . '" data-value="' . $note_mo . '">' . $bnote_mo . '</a></td>';

if ($saft_in == "") $str_response .= '<td><a href="#" id="afta_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="0" data-url="dtr-ajax.php?p=manual_time&o=3" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="00:00">add</a></td>';
else {
	if ($is_manual_ai == 1) $str_response .= '<td><a href="#" id="afta_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $aino . '" data-url="dtr-ajax.php?p=manual_time&o=3" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="' . $saft_in . '">' . $saft_in . '</a></td>';
	else $str_response .= '<td>' . $saft_in . '</td>';
}
$str_response .= '<td><a href="#" id="aa_note_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $nai . '" data-url="dtr-ajax.php?p=note_log&o=3" data-title="Add note - ' . date("M d",strtotime($sday)) . '" data-value="' . $note_ai . '">' . $bnote_ai . '</a></td>';

if ($saft_out == "") $str_response .= '<td><a href="#" id="aftd_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="0" data-url="dtr-ajax.php?p=manual_time&o=4" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="00:00">add</a></td>';
else {
	if ($is_manual_ao == 1) $str_response .= '<td><a href="#" id="aftd_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $aono . '" data-url="dtr-ajax.php?p=manual_time&o=4" data-title="Manual time - ' . date("M d",strtotime($sday)) . '" data-value="' . $saft_out . '">' . $saft_out . '</a></td>';
	else $str_response .= '<td>' . $saft_out . '</td>';
}
$str_response .= '<td><a href="#" id="ad_note_' . date("Y-m-d",strtotime($sday)) . '" data-type="text" data-pk="' . $nao . '" data-url="dtr-ajax.php?p=note_log&o=4" data-title="Add note - ' . date("M d",strtotime($sday)) . '" data-value="' . $note_ao . '">' . $bnote_ao . '</a></td>';

$str_response .= '<td>' . $work_hour . '</td>';
$str_response .= '<td>' . $undr_hour . '</td>';
$str_response .= '<td>' . $undr_minute . '</td>';
$str_response .= '<td>' . $remarks . '</td>';
$str_response .= '</tr>';

/** generate array to pass to report */
$day_date = date("j",strtotime($sday));
$current_day = date("D",strtotime($sday));


/* if ($smor_in != "") {
	if ((strtotime($mor_in) >= strtotime($d_am_arr)) && (strtotime($mor_in) <= strtotime($grace_am_arr))) $smor_in = "08:00";
}
if ($smor_out != "") {
	if ((strtotime($mor_out) >= strtotime($d_am_dep)) && (strtotime($mor_out) <= strtotime($grace_am_dep))) $smor_out = "12:00";
}
if ($saft_in != "") {
	if ((strtotime($aft_in) >= strtotime($d_pm_arr)) && (strtotime($aft_in) <= strtotime($grace_pm_arr))) $saft_in = "13:00";
}
if ($saft_out != "") {

} */

// $dtr_date[$day_date] = array("morning_arrival_".$day_date=>$smor_in,"morning_departure_".$day_date=>$smor_out,"afternoon_arrival_".$day_date=>$saft_in,"afternoon_departure_".$day_date=>$saft_out);
$dtr_date[$day_date] = array("morning_arrival_".$day_date=>$smor_in,"morning_departure_".$day_date=>$smor_out,"afternoon_arrival_".$day_date=>($saft_in != "") ? date("h:i",strtotime($saft_in)) : "","afternoon_departure_".$day_date=>($saft_out != "") ? date("h:i",strtotime($saft_out)) : "");
$note_date[$day_date] = array("morning_arrival_".$day_date=>$note_mi,"morning_departure_".$day_date=>$note_mo,"afternoon_arrival_".$day_date=>$note_ai,"afternoon_departure_".$day_date=>$note_ao);
$note_enabled[$day_date] = array("morning_arrival_".$day_date=>$note_mi_enabled,"morning_departure_".$day_date=>$note_mo_enabled,"afternoon_arrival_".$day_date=>$note_ai_enabled,"afternoon_departure_".$day_date=>$note_ao_enabled);	
$merge_note_r[$day_date] = array("morning_arrival_".$day_date=>$merge_note_mi);

$dtr_date_arr[] = $dtr_date[$day_date];
$note_date_arr[] = $note_date[$day_date];
$note_enabled_arr[] = $note_enabled[$day_date];
$merge_note_arr[] = $merge_note_r[$day_date];


/* **/

$sday = date ("Y-m-d", strtotime("+1 day", strtotime($sday)));

}
if ($signatory_global_enabled == 1) {
	$department_signatory = $department_signatory_global;
	$signatory_title = $signatory_title_global;
}
$doff_json = '{ "doff": [' . json_encode($doff) . ']}';
$on_duty_json = '{ "onduty": [' . json_encode($onduty) . ']}';
$dtr_date_json = '{ "dtr": ' . json_encode($dtr_date_arr) . '}';
$notes_date_json = '{ "notes": ' . json_encode($note_date_arr) . '}';
$notes_enabled_json = '{ "notes_enabled": ' . json_encode($note_enabled_arr) . '}';
$merge_note_json = '{ "merge_note": ' . json_encode($merge_note_arr) . '}';
$dtr_meta_json = '{ "dtr_meta": [{"employee_name":"' . $full_name . '","dtr_month_year":"' . date("F ",strtotime($sday_b)) . $rcyear . '","department_signatory":"' . $department_signatory . '","signatory_title":"' . $signatory_title .'"}]}';
$str_response .= '</tbody>';
$str_response .= '</table>';
$str_response .= '</form>';
$str_response .= '<table class="table table-bordered">';
$str_response .= '<thead>';
$str_response .= '<tr><td rowspan="2">&nbsp;</td><td colspan="3" style="vertical-align: middle; text-align: center;">Work Hours</td><td colspan="2" style="text-align: center;">Undertime</td></tr>';
$str_response .= '<tr><td style="text-align: center;">Regular days</td><td style="text-align: center;">Saturdays</td><td style="text-align: center;">Total</td><td style="text-align: center;">Hours</td><td style="text-align: center;">Minutes</td></tr>';
$str_response .= '</thead>';
$str_response .= '<tbody>';
$str_response .= '<tr><td>Total:</td><td style="text-align: center;">' . $regular_days . '</td><td style="text-align: center;">' . $saturdays . '</td><td style="text-align: center;">' . $total_work_hours . '</td><td style="text-align: center;">' . toHours($total_undertime,'%d') . '</td><td style="text-align: center;">' . toMinutes($total_undertime,'%d') . '</td></tr>';
$str_response .= '</tbody>';
$str_response .= '</table>';
$str_response .= '<div class="form-group">';
$str_response .= '<button type="button" class="btn btn-primary btn-sm refresh-dtr">Refresh</button>';
$str_response .= '</div>';
$str_response .= '<form method="post" id="frmDTR" action="report/csform48.php?rcmonth=' . $rcmonth . '&rcyear=' . $rcyear . '&rcperiod=' . $rcperiod . '" target="_blank">';
$str_response .= '<input type="hidden" id="onduty_arr" name="onduty_arr" value=\'' . $on_duty_json . '\'">';
$str_response .= '<input type="hidden" id="doff_arr" name="doff_arr" value=\'' . $doff_json . '\'">';
$str_response .= '<input type="hidden" id="dtr_arr" name="dtr_arr" value=\'' . $dtr_date_json . '\'">';
$str_response .= '<input type="hidden" id="notes_arr" name="notes_arr" value=\'' . $notes_date_json . '\'">';
$str_response .= '<input type="hidden" id="notes_enabled_arr" name="notes_enabled_arr" value=\'' . $notes_enabled_json . '\'">';
$str_response .= '<input type="hidden" id="merge_note_arr" name="merge_note_arr" value=\'' . $merge_note_json . '\'">';
$str_response .= '<input type="hidden" id="dtr_meta" name="dtr_meta" value=\'' . $dtr_meta_json . '\'">';
$str_response .= '<input type="hidden" id="show_sat_sun" name="show_sat_sun" value="1">';
$str_response .= '</form>';
debug_log($str_response);
echo $str_response;
break;

case "contents_raw_records":
$per_page = 20;
$total_num_rows = 0;
$total_pages = 0;

$d = (isset($_GET['d'])) ? $_GET['d'] : 0;
$current_page = (isset($_GET['cp'])) ? $_GET['cp'] : 1;

$feid = (isset($_GET['feid'])) ? $_GET['feid'] : "";
$fename = (isset($_GET['fename'])) ? $_GET['fename'] : "";
$fdept = (isset($_GET['fdept'])) ? $_GET['fdept'] : 0;
$fsename = (isset($_GET['fsename'])) ? $_GET['fsename'] : 0;

$rrmonth = (isset($_GET['rrmonth'])) ? $_GET['rrmonth'] : "00";
$rrdate = (isset($_GET['rrdate'])) ? $_GET['rrdate'] : "";
if ($rrdate != "") $rrdate = date("Y-m-d",strtotime($rrdate));
$rryear = (isset($_GET['rryear'])) ? $_GET['rryear'] : "";

if (($feid == "") && ($fename == "") && ($fsename == 0)) {
	echo "To view employee <strong>raw records</strong> please <strong>select employee from dropdown</strong> or enter <strong>Employee ID</strong> or enter <strong>Employee Last Name and First Name</strong>, and enter <strong>Month</strong> and <strong>Year</strong> or select <strong>Date</strong> then press Go!";
	return;
}

if ($rryear == "") {
	echo "Please enter <strong>year</strong>.";
	return;
}

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

$hfid = "";
$full_name = "All Employees";
$department = "All Departments";
$gperiod = 0;

if (($feid != "") || ($fename != "")) {
$sql = "SELECT dtr_employee, (select concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) from employees where employee_fid = dtr_employee) full_name, (select dept_name from departments where dept_id = employee_dept) department, (select grace_period from schemes where scheme_id = employee_scheme) gperiod FROM dtr $join_employee $filter ORDER BY dtr_employee LIMIT 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$hfid = $rec['dtr_employee'];
	$full_name = $rec['full_name'];
	$department = $rec['department'];
	$gperiod = $rec['gperiod'];
} else {
	echo "No time logs found.";
	return;
}
db_close();
}

$str_response .= '<table id="header" class="table">';
$str_response .= '<thead>';
$str_response .= '<tr>';
$str_response .= '<td><strong>Name: ' . $full_name . '</strong></td>';
$str_response .= '<td><strong>Department: ' . $department . '</strong><input id="hfid" type="hidden" value="' . $hfid . '"></td>';
$str_response .= '</tr>';
$str_response .= '</thead>';
$str_response .= '</table>';
$str_response .= '<table class="table table-bordered table-hover">';
$str_response .= '<thead>';
$str_response .= '<tr><td style="width: 100px;">Date</td><td style="width: 50px;">Day</td><td style="width: 100px;">Machine No.</td><td style="width: 100px;">FID</td><td style="width: 300px;">Full Name</td><td style="width: 100px;">Time Log</td><td>Transitional</td></tr>';
$str_response .= '</thead>';
$str_response .= '<tbody>';

$rrfilter = " WHERE dtr_no != 0" . $c1 . $c2 . $c3;
$c4 = " and date_time_log like '$rryear-%'";
if ($rrmonth != "00") $c4 = " and date_time_log like '$rryear-$rrmonth-%'";
$c5 = " and date_time_log like '$rrdate%'";
if ($rrdate == "") $c5 = "";
if ($rrdate != "") $c4 = "";
$rrfilter .= $c4 . $c5 . " ORDER BY date_time_log, dtr_employee";

$sql = "SELECT count(*) FROM dtr $join_employee $rrfilter";
/** */
$pagination = new pageNav('rfilterDTR()',$per_page,$current_page,$d);
$row_page = $pagination->row_page($sql);
$last_page = "|".$pagination->total_pages;
/* **/

$sql = "SELECT dtr_no, machine_id, dtr_employee, (select concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) from employees where employee_fid = dtr_employee) full_name, date_time_log, transitional_order FROM dtr $join_employee $rrfilter $row_page";

db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$c = 1;
	for ($i=0; $i<$rc; ++$i) {
		$to_am_in = "";
		$to_am_out = "";
		$to_pm_in = "";
		$to_pm_out = "";
		$no = $i + 1;		
		$rec = $rs->fetch_array();
		$str_response .= '<tr>';
		$str_response .= '<td style="width: 100px;">' . date("M j, Y",strtotime($rec['date_time_log'])) . '</td>';
		$str_response .= '<td style="width: 50px;">' . date("D",strtotime($rec['date_time_log'])) . '</td>';
		$str_response .= '<td style="width: 100px;">' . $rec['machine_id'] . '</td>';
		$str_response .= '<td style="width: 100px;">' . $rec['dtr_employee'] . '</td>';
		$str_response .= '<td style="width: 300px;">' . $rec['full_name'] . '</td>';
		$str_response .= '<td style="width: 100px;">' . date("H:i",strtotime($rec['date_time_log'])) . '</td>';
		if ($rec['transitional_order'] == 1) $to_am_in = 'selected="selected"';
		if ($rec['transitional_order'] == 2) $to_am_out = 'selected="selected"';
		if ($rec['transitional_order'] == 3) $to_pm_in = 'selected="selected"';
		if ($rec['transitional_order'] == 4) $to_pm_out = 'selected="selected"';		
		$str_response .= '<td>';
		$str_response .= '<select onchange="tranLog(' . $rec['dtr_no'] . ',' . $no . ');" id="log-t' . $no . '" class="form-control" style="width: 100px;">';
		$str_response .= '<option value="0">N/A</option>';
		$str_response .= '<option value="1" ' . $to_am_in . '>AM In</option>';
		$str_response .= '<option value="2" ' . $to_am_out . '>AM Out</option>';
		$str_response .= '<option value="3" ' . $to_pm_in . '>PM In</option>';
		$str_response .= '<option value="4" ' . $to_pm_out . '>PM Out</option>';
		$str_response .= '<select>';		
		$str_response .= '</td>';
		$str_response .= '</tr>';
		$c++;
	}
if ($c < $per_page) {
	for ($i=$c; $i<=$per_page; ++$i) {
		$str_response .= '<tr><td colspan="7">&nbsp;</td></tr>';
	}
}	
}
db_close();

$str_response .= '</tbody>';
$str_response .= '<tfoot>';
$str_response .= $pagination->getNav('<tr><td colspan="6">','</td></tr>');
$str_response .= '</tfoot>';
$str_response .= '</table>' . $last_page;

echo $str_response;
break;

case "manual_time":
$order = (isset($_GET['o'])) ? $_GET['o'] : 0;
$fid = (isset($_POST['fid'])) ? $_POST['fid'] : "";
$ld = (isset($_POST['ld'])) ? $_POST['ld'] : "";
$dbf = (isset($_POST['name'])) ? $_POST['name'] : "";
$pk = (isset($_POST['pk'])) ? $_POST['pk'] : 0;
$v = (isset($_POST['value'])) ? $_POST['value'] : "";
$dtl = "$ld $v:00";

if (($pk != 0) && ($v == "")) {
$sql = "DELETE FROM dtr WHERE dtr_no IN ($pk)";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();
header("HTTP/1.1 200 Time log deleted");
return;
}

if ($v == "") {
header("HTTP/1.1 404 Enter time");
return;
}

if (!preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $v)) {
header("HTTP/1.1 404 Enter valid time format");
return;
}

$sql = "alter table dtr AUTO_INCREMENT = 1";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$asql = "INSERT INTO dtr (machine_id, dtr_employee, date_time_log, time_is_manual, log_order, dtr_uid) VALUES (1, '$fid', '$dtl', 1, $order, $uid)";
$usql = "UPDATE dtr SET date_time_log = '$dtl' WHERE dtr_no = $pk";
$sql = $asql;
if ($pk != 0) $sql = $usql;
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();
break;

case "note_log":
$order = (isset($_GET['o'])) ? $_GET['o'] : 0;
$fid = (isset($_POST['fid'])) ? $_POST['fid'] : "";
$ld = (isset($_POST['ld'])) ? $_POST['ld'] : "";
$dbf = (isset($_POST['name'])) ? $_POST['name'] : "";
$pk = (isset($_POST['pk'])) ? $_POST['pk'] : 0;
$v = (isset($_POST['value'])) ? $_POST['value'] : "";

if (($pk != 0) && ($v == "")) {
$sql = "DELETE FROM dtr_notes WHERE note_no IN ($pk)";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();
header("HTTP/1.1 200 Note deleted");
return;
}

if ($v == "") {
header("HTTP/1.1 404 Enter note");
return;
}

$sql = "alter table dtr_notes AUTO_INCREMENT = 1";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$asql = "INSERT INTO dtr_notes (note_fid, note_date, note_log, note_order, note_uid) VALUES ('$fid', '$ld', '$v', $order, $uid)";
$usql = "UPDATE dtr_notes SET note_log = '$v' WHERE note_no = $pk";
$sql = $asql;
if ($pk != 0) $sql = $usql;
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();
break;

case "ignore_log":
$pchk = (isset($_POST['pchk'])) ? $_POST['pchk'] : 0;
$did = (isset($_POST['did'])) ? $_POST['did'] : 0;

$sql = "UPDATE dtr SET ignored = $pchk WHERE dtr_no = $did";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "enable_note":
$pchk = (isset($_POST['pchk'])) ? $_POST['pchk'] : 0;
$nid = (isset($_POST['nid'])) ? $_POST['nid'] : 0;

$sql = "UPDATE dtr_notes SET enabled = $pchk WHERE note_no = $nid";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "on_duty":
$pchk = (isset($_POST['pchk'])) ? $_POST['pchk'] : 0;
$pfid = (isset($_POST['pfid'])) ? $_POST['pfid'] : 0;
$pod = (isset($_POST['pod'])) ? $_POST['pod'] : 0;

$asql = "INSERT INTO dtr_options (option_fid, option_date, on_duty, option_uid) VALUES ($pfid,'$pod',1,$uid)";
$usql = "UPDATE dtr_options SET on_duty = $pchk WHERE option_fid = '$pfid' AND option_date = '$pod'";
$ssql = $asql;

$sql = "SELECT option_id, option_fid, option_date, on_duty, halfday, dtr_leave, leave_note FROM dtr_options WHERE option_fid = '$pfid' AND option_date = '$pod'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$ssql = $usql;
}
db_close();

db_connect();
$db_con->query($START_T);
$db_con->query($ssql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "enable_halfday":
$pchk = (isset($_POST['pchk'])) ? $_POST['pchk'] : 0;
$pfid = (isset($_POST['pfid'])) ? $_POST['pfid'] : 0;
$pod = (isset($_POST['pod'])) ? $_POST['pod'] : 0;

$asql = "INSERT INTO dtr_options (option_fid, option_date, halfday, option_uid) VALUES ($pfid,'$pod',1,$uid)";
$usql = "UPDATE dtr_options SET halfday = $pchk WHERE option_fid = '$pfid' AND option_date = '$pod'";
$ssql = $asql;

$sql = "SELECT option_id, option_fid, option_date, halfday, dtr_leave, leave_note FROM dtr_options WHERE option_fid = '$pfid' AND option_date = '$pod'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$ssql = $usql;
}
db_close();

db_connect();
$db_con->query($START_T);
$db_con->query($ssql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "enable_leave":
$pchk = (isset($_POST['pchk'])) ? $_POST['pchk'] : 0;
$pfid = (isset($_POST['pfid'])) ? $_POST['pfid'] : 0;
$pod = (isset($_POST['pod'])) ? $_POST['pod'] : 0;

$asql = "INSERT INTO dtr_options (option_fid, option_date, dtr_leave, option_uid) VALUES ($pfid,'$pod',1,$uid)";
$usql = "UPDATE dtr_options SET dtr_leave = $pchk WHERE option_fid = '$pfid' AND option_date = '$pod'";
$ssql = $asql;

$sql = "SELECT option_id, option_fid, option_date, halfday, dtr_leave, leave_note FROM dtr_options WHERE option_fid = '$pfid' AND option_date = '$pod'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$ssql = $usql;
}
db_close();

db_connect();
$db_con->query($START_T);
$db_con->query($ssql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "leave_note":
$pln = (isset($_POST['pln'])) ? $_POST['pln'] : 0;
$pfid = (isset($_POST['pfid'])) ? $_POST['pfid'] : 0;
$pod = (isset($_POST['pod'])) ? $_POST['pod'] : 0;

$asql = "INSERT INTO dtr_options (option_fid, option_date, leave_note, option_uid) VALUES ($pfid,'$pod','$pln',$uid)";
$usql = "UPDATE dtr_options SET leave_note = '$pln' WHERE option_fid = '$pfid' AND option_date = '$pod'";
$ssql = $asql;

$sql = "SELECT option_id, option_fid, option_date, halfday, dtr_leave, leave_note FROM dtr_options WHERE option_fid = '$pfid' AND option_date = '$pod'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$ssql = $usql;
}
db_close();

db_connect();
$db_con->query($START_T);
$db_con->query($ssql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "show_leave_note":
$pchk = (isset($_POST['pchk'])) ? $_POST['pchk'] : 0;
$pfid = (isset($_POST['pfid'])) ? $_POST['pfid'] : 0;
$pod = (isset($_POST['pod'])) ? $_POST['pod'] : 0;

$usql = "UPDATE dtr_options SET show_leave_note = $pchk WHERE option_fid = '$pfid' AND option_date = '$pod'";

$sql = "SELECT option_id, option_fid, option_date, halfday, dtr_leave, leave_note, show_leave_note FROM dtr_options WHERE option_fid = '$pfid' AND option_date = '$pod'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$db_con->query($START_T);
$db_con->query($usql);
$db_con->query($END_T);
}
db_close();

echo $str_response;
break;

case "transitional_log":
$tlid = (isset($_POST['tlid'])) ? $_POST['tlid'] : 0;
$tlo = (isset($_POST['tlo'])) ? $_POST['tlo'] : 0;

$sql = "UPDATE dtr SET transitional_order = $tlo WHERE dtr_no = $tlid";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "transitional_note":
$tnid = (isset($_POST['tnid'])) ? $_POST['tnid'] : 0;
$tno = (isset($_POST['tno'])) ? $_POST['tno'] : 0;

$sql = "UPDATE dtr_notes SET note_transitional_order = $tno WHERE note_no = $tnid";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "populate_department":
$sql = "SELECT dept_id, dept_name, dept_head, dept_note, dept_date_added FROM departments";

$str_response .= '<option value="0">All</option>';
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
	$rec = $rs->fetch_array();
	$str_response .= '<option value="' . $rec['dept_id'] . '">' . $rec['dept_name'] . '</option>';
	}
}
db_close();

echo $str_response;
break;

case "explicit_log":
$tlid = (isset($_POST['tlid'])) ? $_POST['tlid'] : 0;
$tlo = (isset($_POST['tlo'])) ? $_POST['tlo'] : 0;

$sql = "UPDATE dtr SET explicit_log = $tlo WHERE dtr_no = $tlid";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

case "merge_note":
$nid = (isset($_POST['nid'])) ? $_POST['nid'] : 0;
$psel = (isset($_POST['psel'])) ? $_POST['psel'] : 1;

$sql = "UPDATE dtr_notes SET note_order = $psel WHERE note_no = $nid";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

echo $str_response;
break;

}

?>
