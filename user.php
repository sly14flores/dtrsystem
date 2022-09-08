<?php

$uname	= (isset($_POST["username"])) ? $_POST["username"] : "";
$upass	= (isset($_POST["password"])) ? $_POST["password"] : "";

require 'config.php';
require 'globalf.php';

db_connect();
$sql	= "SELECT user_account_id, user_account_username, user_account_password, user_account_firstname, concat(user_account_firstname, ' ', user_account_mi, '. ', user_account_lastname) user, user_account_mi, user_account_lastname, user_account_email, user_account_contact, user_account_privileges, user_account_builtin, user_account_date_added, user_account_deleted FROM user_accounts WHERE  user_account_username = '$uname' and user_account_password = '" . enc($upass) . "' and user_account_deleted = 0";
$rs = $db_con->query($sql);
$rc = $rs->num_rows;

if ($rc>0) {
	session_start();
	$row = $rs->fetch_array();
	$_SESSION['user_account_id'] = $row['user_account_id'];
	$_SESSION['user'] = $row['user'];
	$_SESSION['previleges'] = $row['user_account_privileges'];
	$_SESSION['ua_description'] = "Administrator";
	if ($row['user_account_privileges'] == 100) $_SESSION['ua_description'] = "Manager";
	header("location: home.php");
} else {
	header("location: signin.php?m=1");
}

db_close();

?>