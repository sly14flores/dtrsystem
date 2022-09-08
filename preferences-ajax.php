<?php

require 'config.php';

$req = "";
$START_T = "START TRANSACTION;";
$END_T = "COMMIT;";

if (isset($_GET["p"])) $req = $_GET["p"];

$str_response = "";
$json = "";
$jpage = "";

switch ($req) {

case "update":
$pconame = (isset($_POST['pconame'])) ? $_POST['pconame'] : "";
$psign = (isset($_POST['psign'])) ? $_POST['psign'] : "";
$pgse = (isset($_POST['pgse'])) ? $_POST['pgse'] : "";
$pggp = (isset($_POST['pggp'])) ? $_POST['pggp'] : "";
$pggpe = (isset($_POST['pggpe'])) ? $_POST['pggpe'] : "";

$sql = "alter table preferences AUTO_INCREMENT = 1";
db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$asql = "INSERT INTO preferences (preference_company_name, preference_company_logo, preference_company_signatory, signatory_global_enabled, preference_company_gperiod, gperiod_global_enabled) VALUES ('$pconame','','$psign',$pgse,$pggp,$pggpe)";
$usql = "UPDATE preferences SET preference_company_name = '$pconame', preference_company_logo = '', preference_company_signatory = '$psign', signatory_global_enabled = $pgse, preference_company_gperiod = $pggp, gperiod_global_enabled = $pggpe WHERE preference_id = 1";

$sql = $asql;
$qsql = "select * from preferences";
db_connect();
$rs = $db_con->query($qsql);
$rc = $rs->num_rows;
if ($rc) $sql = $usql;
db_close();

db_connect();
$db_con->query($START_T);
$db_con->query($sql);
$db_con->query($END_T);
db_close();

$str_response = "Preferences successfully updated.";

echo $str_response;
break;

}

?>