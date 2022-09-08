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

case "add":
$data = $_POST;
$data['employee_dateadded'] = 'CURRENT_TIMESTAMP';
$data['employee_dob'] = date("Y-m-d",strtotime($data['employee_dob']));
$data['employee_uid'] = $uid;

$add_employee = new dbase('employees');
$add_employee->auto_incr_one();
$add_employee->execute();
$add_employee->add($data);
$add_employee->execute();
// print_r($add_employee->debug_r());

$str_response = "Employee successfully added.";

echo $str_response;
break;

case "update":
$data = $_POST;
$data['employee_update']['update'][0]['employee_dob'] = date("Y-m-d",strtotime($data['employee_update']['update'][0]['employee_dob']));
$data['employee_update']['update'][0]['employee_uid'] = $uid;

$update_employee = new dbase('employees');
$update_employee->update($data['employee_update']['update'][0],$data['employee_id']['pk'][0]);
$update_employee->execute();
// print_r($update_employee->debug_r());

$str_response = "Employee successfully updated.";

echo $str_response;
break;

case "edit":
$eid = (isset($_GET['eid'])) ? $_GET['eid'] : 0;

$sql = "SELECT employee_fid, employee_mid, employee_lastname, employee_firstname, employee_middlename, employee_gender, employee_dob, employee_age, employee_contacts, employee_address, employee_position, employee_attainment, employee_eligibility, employee_years, employee_appointment, employee_sss, employee_gsis, employee_philhealth, employee_hdmf, employee_tin, employee_remarks, employee_dept, employee_scheme FROM employees WHERE employee_id = $eid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array(MYSQLI_ASSOC);
	$rec['employee_fid'] = stripslashes($rec['employee_fid']);
	$rec['employee_mid'] = ($rec['employee_mid'] == 0) ? 1 : $rec['employee_mid'];	
	$rec['employee_dob'] = date("m/d/Y",strtotime($rec['employee_dob']));
	if ($rec['employee_dob'] == "01/01/1970") $rec['employee_dob'] = "";	
	$rec['employee_age'] = ($rec['employee_age'] == 0) ? "" : $rec['employee_age'];
	$rec['employee_contacts'] = stripslashes($rec['employee_contacts']);
	$rec['employee_address'] = stripslashes($rec['employee_address']);
	$rec['employee_position'] = stripslashes($rec['employee_position']);
	$rec['employee_attainment'] = stripslashes($rec['employee_attainment']);
	$rec['employee_eligibility'] = stripslashes($rec['employee_eligibility']);
	$rec['employee_sss'] = stripslashes($rec['employee_sss']);
	$rec['employee_gsis'] = stripslashes($rec['employee_gsis']);
	$rec['employee_philhealth'] = stripslashes($rec['employee_philhealth']);
	$rec['employee_hdmf'] = stripslashes($rec['employee_hdmf']);
	$rec['employee_tin'] = stripslashes($rec['employee_tin']);
	$rec['employee_remarks'] = stripslashes($rec['employee_remarks']);
	
	$str_response = json_encode($rec);	
}
db_close();

echo $str_response;
// print_r($rec);
break;

case "employee_departments":
$sql = "SELECT dept_id, dept_name, dept_head, dept_note, dept_date_added FROM departments";

$str_response = '<option value="0">None</option>';
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

case "employee_schemes":
$sql = "SELECT scheme_id, scheme_name FROM schemes";

$str_response = '<option value="0">Undefined</option>';
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
	$rec = $rs->fetch_array();
	$str_response .= '<option value="' . $rec['scheme_id'] . '">' . $rec['scheme_name'] . '</option>';
	}
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

$feid = (isset($_GET['feid'])) ? $_GET['feid'] : "";
$fename = (isset($_GET['fename'])) ? $_GET['fename'] : "";
$fdept = (isset($_GET['fdept'])) ? $_GET['fdept'] : 0;

$filter = " WHERE employee_id != 0";
$c1 = " and employee_fid = '$feid'";
$c2 = " and concat(employee_lastname, ', ', employee_firstname, ' ', employee_middlename) like '$fename%'";
$c3 = " and employee_dept = '$fdept%'";

if ($feid == "") $c1 = "";
if ($fename == "") $c2 = "";
if ($fdept == 0) $c3 = "";

$filter .= $c1 . $c2 . $c3 . " order by employee_lastname, employee_firstname";

$sql = "SELECT count(*) FROM employees $filter";

/** */
$pagination = new pageNav('rfilterEmployee()',$per_page,$current_page,$d);
$row_page = $pagination->row_page($sql);
$last_page = "|".$pagination->total_pages;
/* **/

$str_response  = '<form name="frmContent" id="frmContent">';
$str_response .= '<table class="table table-hover">';
$str_response .= '<thead>';
$str_response .= '<tr><td><input type="checkbox" name="chk_checkall" id="chk_checkall" onclick="Check_all(this.form, this);"></td><td><strong>Machine ID</strong></td><td><strong>Full Name</strong></td><td><strong>ID</strong></td><!--<td><strong>Date of Birth</strong></td><td><strong>Age</strong></td><td><strong>Contact(s)</strong></td><td><strong>Address</strong></td>--><td><strong>Department</strong></td><td><strong>Position</strong></td><td><strong>Scheme</strong></td><td><strong>Action</strong></td></tr>';
$str_response .= '</thead>';

$sql = "SELECT employee_id, employee_mid, employee_fid, if(employee_dept = 0,'',(select dept_name from departments where dept_id = employee_dept)) deparment, concat(employee_lastname, ', ', employee_firstname, ' ', employee_middlename) full_name, employee_gender, employee_dob, employee_age, employee_contacts, employee_address, employee_position, employee_attainment, employee_eligibility, employee_years, employee_appointment, employee_sss, employee_philhealth, employee_hdmf, employee_tin, employee_remarks, employee_dateadded, employee_uid, ifnull((select scheme_name from schemes where scheme_id = employee_scheme),'Undefined') employee_scheme_desc FROM employees $filter $row_page";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$c = 1;
$str_response .= '<tbody>';
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$str_response .= '<tr>';
		$str_response .= '<td><input type="checkbox" name="chk_' . $rec['employee_id'] . '" id="chk_' . $rec['employee_id'] . '" onClick="Uncheck_Parent(\'chk_checkall\',this);"></td>';
		$str_response .= '<td>' . $rec['employee_mid'] . '</td>';
		$str_response .= '<td>' . $rec['full_name'] . '</td>';
		$str_response .= '<td>' . stripslashes($rec['employee_fid']) . '</td>';
		// $cdob = "";
		// if ($rec['employee_dob'] != "0000-00-00") $cdob = date("M j, Y",strtotime($rec['employee_dob']));
		// $str_response .= '<td>' . $cdob . '</td>';
		// $cage = "";
		// if ($rec['employee_age'] != 0) $cage = $rec['employee_age'];
		// $str_response .= '<td>' . $cage . '</td>';
		// $str_response .= '<td>' . stripslashes($rec['employee_contacts']) . '</td>';
		// $str_response .= '<td>' . stripslashes($rec['employee_address']) . '</td>';
		$str_response .= '<td>' . $rec['deparment'] . '</td>';
		$str_response .= '<td>' . stripslashes($rec['employee_position']) . '</td>';
		$str_response .= '<td>' . stripslashes($rec['employee_scheme_desc']) . '</td>';
		$str_response .= '<td><a data-toggle="tooltip" data-placement="right" title="Extract 201 file to excel file" href="javascript: extract201file(' . $rec['employee_id'] . ');"><img src="image/extract.png"></a></td>';
		$str_response .= '</tr>';
		$c++;
	}
if ($c < $per_page) {
	for ($i=$c; $i<=$per_page; ++$i) {
		$str_response .= '<tr><td colspan="8">&nbsp;</td></tr>';
	}
}	
$str_response .= '</tbody>';
}
db_close();

$str_response .= '<tfoot>';
$str_response .= $pagination->getNav('<tr><td colspan="8">','</td></tr>');
$str_response .= '</tfoot>';
$str_response .= '</table>';
$str_response .= '</form>' . $last_page;

echo $str_response;
break;

case "delete":
$data = $_POST;

$delete_employee = new dbase('employees');
$delete_employee->delete($data['employee_del']['pk'][0]);
$delete_employee->execute();

$str_response = "Employee(s) successfully deleted.";

echo $str_response;
break;

case "select_department":
$sql = "SELECT dept_id, dept_name, dept_head, dept_note, dept_date_added FROM departments";

$str_response .= '<option value="0">None</option>';
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

case "select_employees":
$fsel_dept = $_GET['fsel_dept'];

$sql = "SELECT employee_fid, concat(employee_lastname, ', ', employee_firstname) full_name FROM employees";
if ($fsel_dept != 0) $sql .= " WHERE employee_dept = $fsel_dept";
$sql .= " ORDER BY employee_lastname, employee_firstname";

db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$str_response .= '<option value="0">--select employee--</option>';	
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$str_response .= '<option value="' . $rec['employee_fid'] . '">' . $rec['full_name'] . '</option>';
	}
} else {
$str_response .= '<option value="0">None</option>';
}
db_close();

echo $str_response;
break;

case "typeahead_employees":
$sql = "SELECT concat(employee_lastname, ', ', employee_firstname) full_name FROM employees ORDER BY employee_lastname, employee_firstname";

$json = '[';
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$json .= "'" . $rec['full_name'] . "', ";
	}
$json = substr($json,0,strlen($json)-2);
}
db_close();
$json .= ']';

echo $json;
break;

case "typeahead_employees_fullname":
$sql = "SELECT concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) full_name FROM employees ORDER BY employee_lastname, employee_firstname";

$json = '[';
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$json .= "'" . $rec['full_name'] . "', ";
	}
$json = substr($json,0,strlen($json)-2);
}
db_close();
$json .= ']';

echo $json;
break;

case "typeahead_fid":
$sql = "SELECT employee_fid FROM employees ORDER BY employee_fid";

$json = '[';
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$json .= "'" . $rec['employee_fid'] . "', ";
	}
$json = substr($json,0,strlen($json)-2);
}
db_close();
$json .= ']';

echo $json;
break;

}

?>