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

case "verify_current_password":
$pcurrent = (isset($_POST['pcurrent'])) ? enc($_POST['pcurrent']) : "";

$str_response = 0;
$sql = "SELECT user_account_password FROM user_accounts WHERE user_account_id = $uid AND user_account_password = '$pcurrent'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$str_response = 1;
}
db_close();

echo $str_response;
break;

case "change_password":
$prnew = (isset($_POST['prnew'])) ? enc($_POST['prnew']) : "";

$sql = "UPDATE user_accounts SET user_account_password = '$prnew' WHERE user_account_id = $uid";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$str_response = "Password successfully changed.";

echo $str_response;

break;

}

?>