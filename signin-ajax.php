<?php

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

case "email_password":
$peuname = (isset($_POST['peuname'])) ? $_POST['peuname'] : "";

$sql = "SELECT user_account_username, user_account_password, user_account_email FROM user_accounts WHERE user_account_username = '$peuname'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$uemail = $rec['user_account_email'];
	$upword = $rec['user_account_password'];
	if ($uemail == "") {
		$str_response = "You have not provided an email address in your account. Please contact your administrator.";
	} else {
	
	$to = $uemail;
	$subject = "Username: $peuname - eDTR System | Information System";
	$message  = "Greetings!<br><br>";
	$message .= "Your password is " . dec($upword) . "<br><br>";
	$message .= "Administrator";
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Administrator' . "\r\n";
	$sendmail = mail($to, $subject, $message, $headers);
	if ($sendmail) {
		$str_response = "Your password has been emailed to you. Please check you inbox.";
	} else {
		$str_response = "Password not sent. Please confirm that you have internet connection.";
	}
	
	}
} else {
	$str_response = "Username doesn't exists. Please contact your administrator.";
}
db_close();

echo $str_response;
break;

}

?>