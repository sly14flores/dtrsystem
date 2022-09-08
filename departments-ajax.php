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
$data['dept_head'] = departmentHeadId($data['dept_head']);
$data['dept_date_added'] = 'CURRENT_TIMESTAMP';
$data['dept_uid'] = $uid;

$add_dept = new dbase('departments');
$add_dept->auto_incr_one();
$add_dept->execute();
$add_dept->add($data);
$add_dept->execute();
// print_r($add_dept->debug_r());

$str_response = "Department successfully added.";

echo $str_response;
break;

case "update":
$data = $_POST;
$data['dept_update']['update'][0]['dept_head'] = departmentHeadId($data['dept_update']['update'][0]['dept_head']);
$data['dept_update']['update'][0]['dept_uid'] = $uid;

$update_dept = new dbase('departments');
$update_dept->update($data['dept_update']['update'][0],$data['dept_id']['pk'][0]);
$update_dept->execute();
// print_r($update_dept->debug_r());

$str_response = "Department successfully updated.";

echo $str_response;
break;

case "edit":
$deptid = (isset($_GET['deptid'])) ? $_GET['deptid'] : 0;

$sql = "SELECT dept_name, (select concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) from employees where employee_id = dept_head) department_head, dept_note FROM departments WHERE dept_id = $deptid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array(MYSQLI_ASSOC);
	$rec['dept_name'] = stripslashes($rec['dept_name']);
	$rec['department_head'] = $rec['department_head'];
	$rec['dept_head'] = $rec['department_head'];
	$rec['dept_note'] = stripslashes($rec['dept_note']);
	$str_response = json_encode($rec);
}
db_close();

echo $str_response;
// print_r($rec);
break;

case "contents":
$per_page = 10;
$total_num_rows = 0;
$total_pages = 0;

$d = (isset($_GET['d'])) ? $_GET['d'] : 0;
$current_page = (isset($_GET['cp'])) ? $_GET['cp'] : 1;

$fdeptname = (isset($_GET['fdeptname'])) ? $_GET['fdeptname'] : "";

$filter = " WHERE dept_id != 0";
$c1 = " and dept_name like '$fdeptname%'";

if ($fdeptname == "") $c1 = "";

$filter .= $c1;

$sql = "SELECT count(*) FROM departments $filter";

/** */
$pagination = new pageNav('rfilterDept()',$per_page,$current_page,$d);
$row_page = $pagination->row_page($sql);
$last_page = "|".$pagination->total_pages;
/* **/

$str_response  = '<form name="frmContent" id="frmContent">';
$str_response .= '<table class="table table-hover">';
$str_response .= '<thead>';
$str_response .= '<tr><td><input type="checkbox" name="chk_checkall" id="chk_checkall" onclick="Check_all(this.form, this);"></td><td><strong>Name</strong></td><td><strong>Head</strong></td><td><strong>Note</strong></td><td><strong>Date added</strong></td></tr>';
$str_response .= '</thead>';

$sql = "SELECT dept_id, dept_name, (select concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) from employees where employee_id = dept_head) department_head, dept_note, dept_date_added, dept_uid FROM departments $filter $row_page";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$c = 1;
$str_response .= '<tbody>';
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$str_response .= '<tr>';
		$str_response .= '<td><input type="checkbox" name="chk_' . $rec['dept_id'] . '" id="chk_' . $rec['dept_id'] . '" onClick="Uncheck_Parent(\'chk_checkall\',this);"></td>';
		$str_response .= '<td>' . $rec['dept_name'] . '</td>';
		$str_response .= '<td>' . $rec['department_head'] . '</td>';
		$str_response .= '<td>' . $rec['dept_note'] . '</td>';
		$str_response .= '<td>' . date("M j, Y",strtotime($rec['dept_date_added'])) . '</td>';
		$str_response .= '</tr>';
		$c++;
	}
if ($c < $per_page) {
	for ($i=$c; $i<=$per_page; ++$i) {
		$str_response .= '<tr><td colspan="5">&nbsp;</td></tr>';
	}
}	
$str_response .= '</tbody>';
}
db_close();

$str_response .= '<tfoot>';
$str_response .= $pagination->getNav('<tr><td colspan="5">','</td></tr>');
$str_response .= '</tfoot>';
$str_response .= '</table>';
$str_response .= '</form>' . $last_page;

echo $str_response;
break;

case "delete":
$data = $_POST;

$delete_dept = new dbase('departments');
$delete_dept->delete($data['dept_del']['pk'][0]);
$delete_dept->execute();

$str_response = "Department(s) successfully deleted.";

echo $str_response;
break;

case "typeahead_dept":
$sql = "SELECT dept_name FROM departments ORDER BY dept_name";

$json = '[';
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array(MYSQLI_ASSOC);
		$json .= "'" . $rec['dept_name'] . "', ";
	}
$json = substr($json,0,strlen($json)-2);
}
db_close();
$json .= ']';

echo $json;
break;

}

function departmentHeadId($fullname) {
		
	global $db_con;
	
	$eid = 0;
	
	$sql = "select employee_id from employees where concat(employee_firstname, ' ', employee_middlename, ' ', employee_lastname) = '$fullname'";
	db_connect();
	$rs = $db_con->query($sql);
	$rc = $rs->num_rows;
	if ($rc) {
		$rec = $rs->fetch_array();
		$eid = $rec['employee_id'];
	}
	db_close();
	
	return $eid;
		
}

?>