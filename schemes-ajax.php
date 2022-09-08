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

case "verifiy_scheme":
$pst = (isset($_POST['pst'])) ? $_POST['pst'] : "";

if (preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $pst)) echo 1;
else echo 0;

break;

case "add":
$data = $_POST;

$is_scheme = array("scheme_name","grace_period");
$arr_scheme = [];
$arr_scheme['scheme_uid'] = $uid;
$arr_schedule = [];

foreach($data as $key => $value) {
	if (searchInArray($is_scheme,$key)) {
		$arr_scheme[$key] = $value;
	} else {
		$arr_schedule[$key] = $value;
	}
}

$add_scheme = new dbase('schemes');
$add_scheme->auto_incr_one();
$add_scheme->execute();
$add_scheme->add($arr_scheme);
$add_scheme->sql_get_id();

$arr_schedule['sched_sid'] = $add_scheme->last_auto_id;
$add_sched = new dbase('schedules');
$add_sched->auto_incr_one();
$add_sched->execute();
$add_sched->add($arr_schedule);
$add_sched->execute();

// print_r($data);
// print_r($add_scheme->debug_r());
// print_r($add_sched->debug_r());
// print_r($arr_scheme);
// print_r($arr_schedule);

$str_response = "Scheme successfully added.";
echo $str_response;
break;

case "update":
$data = $_POST;

$is_scheme = array("scheme_name","grace_period");
$arr_scheme = [];
$arr_scheme['scheme_uid'] = $uid;
$arr_schedule = [];

$arr_scheme['scheme_uid'] = $uid;
foreach($data['scheme_update']['update'][0] as $key => $value) {
	if (searchInArray($is_scheme,$key)) {
		$arr_scheme[$key] = $value;
	} else {
		$arr_schedule[$key] = $value;
	}
}

$update_scheme = new dbase('schemes');
$update_scheme->update($arr_scheme,$data['scheme_id']['pk'][0]);
$update_scheme->execute();
// print_r($update_scheme->debug_r());

$update_sched = new dbase('schedules');
$update_sched->update($arr_schedule,array("sched_sid"=>$data['scheme_id']['pk'][0]['scheme_id']));
$update_sched->execute();
// print_r($update_sched->debug_r());

$str_response = "Scheme info successfully updated.";

echo $str_response;
break;

case "edit":
$scid = (isset($_GET['scid'])) ? $_GET['scid'] : 0;

$sql = "SELECT scheme_id, scheme_name, grace_period, sched_id, sched_sid, mon_off, tue_off, wed_off, thu_off, fri_off, sat_off, sun_off, mon_am_arrival, tue_am_arrival, wed_am_arrival, thu_am_arrival, fri_am_arrival, sat_am_arrival, sun_am_arrival, mon_am_departure, tue_am_departure, wed_am_departure, thu_am_departure, fri_am_departure, sat_am_departure, sun_am_departure, mon_pm_arrival, tue_pm_arrival, wed_pm_arrival, thu_pm_arrival, fri_pm_arrival, sat_pm_arrival, sun_pm_arrival, mon_pm_departure, tue_pm_departure, wed_pm_departure, thu_pm_departure, fri_pm_departure, sat_pm_departure, sun_pm_departure, mon_rh, tue_rh, wed_rh, thu_rh, fri_rh, sat_rh, sun_rh, mon_t, tue_t, wed_t, thu_t, fri_t, sat_t, sun_t, mon_am_out_t, mon_pm_in_t, mon_pm_out_t, tue_am_out_t, tue_pm_in_t, tue_pm_out_t, wed_am_out_t, wed_pm_in_t, wed_pm_out_t, thu_am_out_t, thu_pm_in_t, thu_pm_out_t, fri_am_out_t, fri_pm_in_t, fri_pm_out_t, sat_am_out_t, sat_pm_in_t, sat_pm_out_t, sun_am_out_t, sun_pm_in_t, sun_pm_out_t FROM schemes LEFT JOIN schedules ON schemes.scheme_id = schedules.sched_sid WHERE scheme_id = $scid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array(MYSQLI_ASSOC);
	$rec['mon_am_arrival'] = date("H:i",strtotime($rec['mon_am_arrival']));
	$rec['tue_am_arrival'] = date("H:i",strtotime($rec['tue_am_arrival']));
	$rec['wed_am_arrival'] = date("H:i",strtotime($rec['wed_am_arrival']));
	$rec['thu_am_arrival'] = date("H:i",strtotime($rec['thu_am_arrival']));
	$rec['fri_am_arrival'] = date("H:i",strtotime($rec['fri_am_arrival']));
	$rec['sat_am_arrival'] = date("H:i",strtotime($rec['sat_am_arrival']));
	$rec['sun_am_arrival'] = date("H:i",strtotime($rec['sun_am_arrival']));
		
	$rec['mon_am_departure'] = date("H:i",strtotime($rec['mon_am_departure']));
	$rec['tue_am_departure'] = date("H:i",strtotime($rec['tue_am_departure']));
	$rec['wed_am_departure'] = date("H:i",strtotime($rec['wed_am_departure']));
	$rec['thu_am_departure'] = date("H:i",strtotime($rec['thu_am_departure']));
	$rec['fri_am_departure'] = date("H:i",strtotime($rec['fri_am_departure']));
	$rec['sat_am_departure'] = date("H:i",strtotime($rec['sat_am_departure']));
	$rec['sun_am_departure'] = date("H:i",strtotime($rec['sun_am_departure']));
	
	$rec['mon_pm_arrival'] = date("H:i",strtotime($rec['mon_pm_arrival']));
	$rec['tue_pm_arrival'] = date("H:i",strtotime($rec['tue_pm_arrival']));
	$rec['wed_pm_arrival'] = date("H:i",strtotime($rec['wed_pm_arrival']));
	$rec['thu_pm_arrival'] = date("H:i",strtotime($rec['thu_pm_arrival']));
	$rec['fri_pm_arrival'] = date("H:i",strtotime($rec['fri_pm_arrival']));
	$rec['sat_pm_arrival'] = date("H:i",strtotime($rec['sat_pm_arrival']));
	$rec['sun_pm_arrival'] = date("H:i",strtotime($rec['sun_pm_arrival']));
	
	$rec['mon_pm_departure'] = date("H:i",strtotime($rec['mon_pm_departure']));
	$rec['tue_pm_departure'] = date("H:i",strtotime($rec['tue_pm_departure']));
	$rec['wed_pm_departure'] = date("H:i",strtotime($rec['wed_pm_departure']));
	$rec['thu_pm_departure'] = date("H:i",strtotime($rec['thu_pm_departure']));
	$rec['fri_pm_departure'] = date("H:i",strtotime($rec['fri_pm_departure']));
	$rec['sat_pm_departure'] = date("H:i",strtotime($rec['sat_pm_departure']));
	$rec['sun_pm_departure'] = date("H:i",strtotime($rec['sun_pm_departure']));	
		
	$str_response = json_encode($rec);
}
db_close();

echo $str_response;
break;

case "contents":
$per_page = 10;
$total_num_rows = 0;
$total_pages = 0;

$d = (isset($_GET['d'])) ? $_GET['d'] : 0;
$current_page = (isset($_GET['cp'])) ? $_GET['cp'] : 1;

$fscheme = (isset($_GET['fscheme'])) ? $_GET['fscheme'] : "";

$filter = " WHERE scheme_id != 0";
$c1 = " and scheme_name like '$fscheme%'";

if ($fscheme == "") $c1 = "";

$filter .= $c1;

$sql = "SELECT count(*) FROM schemes $filter";

/** */
$pagination = new pageNav('rfilterScheme()',$per_page,$current_page,$d);
$row_page = $pagination->row_page($sql);
$last_page = "|".$pagination->total_pages;
/* **/

$str_response  = '<form name="frmContent" id="frmContent">';
$str_response .= '<table class="table table-hover">';
$str_response .= '<thead>';
$str_response .= '<tr><td><input type="checkbox" name="chk_checkall" id="chk_checkall" onclick="Check_all(this.form, this);"></td><td><strong>Name</strong></td><td><strong>Grace Period</strong></td></tr>';
$str_response .= '</thead>';

$sql = "SELECT scheme_id, scheme_name, grace_period FROM schemes $filter $row_page";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$c = 1;
$str_response .= '<tbody>';
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$str_response .= '<tr>';
		$str_response .= '<td><input type="checkbox" name="chk_' . $rec['scheme_id'] . '" id="chk_' . $rec['scheme_id'] . '" onClick="Uncheck_Parent(\'chk_checkall\',this);"></td>';
		$str_response .= '<td>' . $rec['scheme_name'] . '</td>';
		$str_response .= '<td>' . $rec['grace_period'] . '</td>';	
		$str_response .= '</tr>';
		$c++;
	}
if ($c < $per_page) {
	for ($i=$c; $i<=$per_page; ++$i) {
		$str_response .= '<tr><td colspan="3">&nbsp;</td></tr>';
	}
}	
$str_response .= '</tbody>';
}
db_close();

$str_response .= '<tfoot>';
$str_response .= $pagination->getNav('<tr><td colspan="3">','</td></tr>');
$str_response .= '</tfoot>';
$str_response .= '</table>';
$str_response .= '</form>' . $last_page;

echo $str_response;
break;

case "delete":
$data = $_POST;

$delete_scheme = new dbase('schemes');
$delete_scheme->delete($data['scheme_del']['pk'][0]);
$delete_scheme->execute();

$delete_sched = new dbase('schedules');
$delete_sched->delete(array("sched_sid"=>$data['scheme_del']['pk'][0]['scheme_id']));
$delete_sched->execute();

$str_response = "Scheme(s) successfully deleted.";

echo $str_response;
break;

}

?>