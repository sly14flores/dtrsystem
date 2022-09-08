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
$data['user_account_password'] = enc($data['user_account_password']);
$data['user_account_contact'] = addslashes($data['user_account_contact']);
$data['user_account_date_added'] = 'CURRENT_TIMESTAMP';

$add_ua = new dbase('user_accounts');
$add_ua->auto_incr_one();
$add_ua->execute();
$add_ua->add($data);
$add_ua->execute();
// print_r($add_ua->debug_r());

$str_response = "User account successfully added.";

echo $str_response;
break;

case "update":
$data = $_POST;
$data['ua_update']['update'][0]['user_account_password'] = enc($data['ua_update']['update'][0]['user_account_password']);
$data['ua_update']['update'][0]['user_account_contact'] = addslashes($data['ua_update']['update'][0]['user_account_contact']);

$update_ua = new dbase('user_accounts');
$update_ua->update($data['ua_update']['update'][0],$data['user_account_id']['pk'][0]);
$update_ua->execute();
// print_r($update_ua->debug_r());

$str_response = "User account successfully updated.";

echo $str_response;
break;

case "edit":
$uaid = (isset($_GET['uaid'])) ? $_GET['uaid'] : 0;

$sql = "SELECT user_account_username, user_account_password, user_account_firstname, user_account_mi, user_account_lastname, user_account_email, user_account_contact, user_account_privileges, user_account_deleted FROM user_accounts WHERE user_account_id = $uaid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array(MYSQLI_ASSOC);
	$rec['user_account_contact'] = stripslashes($rec['user_account_contact']);
	$rec['user_account_password'] = dec($rec['user_account_password']);
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

$faccountname = (isset($_GET['faccountname'])) ? $_GET['faccountname'] : "";
$dua = (isset($_GET['dua'])) ? $_GET['dua'] : 0;

$filter = " WHERE user_account_builtin = 0";
$c1 = " and concat(user_account_lastname, ', ', user_account_firstname) like '$faccountname%'";
$c2 = " and user_account_deleted = 0";

if ($faccountname == "") $c1 = "";
if ($dua == 1) $c2 = "";

$filter .= $c1 . $c2;

$sql = "SELECT count(*) FROM user_accounts $filter";

/** */
$pagination = new pageNav('rfilterAccount()',$per_page,$current_page,$d);
$row_page = $pagination->row_page($sql);
$last_page = "|".$pagination->total_pages;
/* **/

$str_response  = '<form name="frmContent" id="frmContent">';
$str_response .= '<table class="table table-hover">';
$str_response .= '<thead>';
$str_response .= '<tr><td><input type="checkbox" name="chk_checkall" id="chk_checkall" onclick="Check_all(this.form, this);"></td><td><strong>Name</strong></td><td><strong>Username</strong></td><td><strong>Email</strong></td><td><strong>Contact</strong></td><td><strong>Role</strong></td></tr>';
$str_response .= '</thead>';

$sql = "SELECT user_account_id, user_account_username, user_account_password, concat(user_account_lastname, ', ', user_account_firstname, ' ', user_account_mi) user_fullname, user_account_email, user_account_contact, if(user_account_privileges = 1000,'Administrator','Manager') user_account_role, user_account_builtin, user_account_date_added, user_account_deleted FROM user_accounts $filter $row_page";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$c = 1;
$str_response .= '<tbody>';
	for ($i=0; $i<$rc; ++$i) {
		$rec = $rs->fetch_array();
		$str_response .= '<tr>';
		$str_response .= '<td><input type="checkbox" name="chk_' . $rec['user_account_id'] . '" id="chk_' . $rec['user_account_id'] . '" onClick="Uncheck_Parent(\'chk_checkall\',this);"></td>';
		$str_response .= '<td>' . $rec['user_fullname'] . '</td>';
		$str_response .= '<td>' . $rec['user_account_username'] . '</td>';
		$str_response .= '<td>' . $rec['user_account_email'] . '</td>';
		$str_response .= '<td>' . $rec['user_account_contact'] . '</td>';
		$str_response .= '<td>' . $rec['user_account_role'] . '</td>';
		$str_response .= '</tr>';
		$c++;
	}
if ($c < $per_page) {
	for ($i=$c; $i<=$per_page; ++$i) {
		$str_response .= '<tr><td colspan="6">&nbsp;</td></tr>';
	}
}	
$str_response .= '</tbody>';
}
db_close();

$str_response .= '<tfoot>';
$str_response .= $pagination->getNav('<tr><td colspan="6">','</td></tr>');
$str_response .= '</tfoot>';
$str_response .= '</table>';
$str_response .= '</form>' . $last_page;

echo $str_response;
break;

case "delete":
$data = $_POST;

$update_ua = new dbase('user_accounts');
$update_ua->update($data['ua_update']['update'][0],$data['user_account_id']['pk'][0]);
$update_ua->execute();
// print_r($update_ua->debug_r());

$str_response = "User account(s) successfully deleted.";

echo $str_response;
break;

case "pop_pword":
$puaid = $_POST['puaid'];

$sql = "SELECT user_account_password FROM user_accounts WHERE user_account_id = $puaid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
$rec = $rs->fetch_array();
$str_response = dec($rec['user_account_password']);
}
db_close();

echo $str_response;
break;

case "verify_uname":
$puname = (isset($_POST['puname'])) ? $_POST['puname'] : "";

$sql = "SELECT user_account_username FROM user_accounts WHERE user_account_username = '$puname'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) echo 1; else echo 0;
db_close();

echo $str_response;
break;

}

?>