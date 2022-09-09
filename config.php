<?php

require 'endec.php';

$dec = new endec();

/* Database Configuration */
$DB_HOST = "localhost";
$DB_USER = "root";
// $DB_PWD	 = $dec->dec("zX5t4m0kXMSrAtUJ4ubzoNVZrWCG9dBpJhxvAUa8bKU=");
$DB_PWD	 = "";
$DB_FILE = $dec->dec("ydsFR+oyub9Ivyp/YjpB45jLrzD86wZDTRRZyjrN3C4=");
$DB_PORT = 3306;

function db_connect() {
	global $db_con, $DB_HOST, $DB_USER, $DB_PWD, $DB_FILE, $DB_PORT;
	$db_con = new mysqli($DB_HOST, $DB_USER, $DB_PWD, $DB_FILE, $DB_PORT);
}

function db_close() {
	global $db_con;
	$db_con->close();
}

?>