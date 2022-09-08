<?php

$about = "";

require '../config.php';

$sql = "SELECT about_content FROM about WHERE about_id = 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$about = $rec['about_content'];
}
db_close();

?>
<form role="form" id="frmAbout" onSubmit="return false;">
<?php
$k = 'qJB0rGtIn5UB1xG03efyCp';
$about = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $k ), base64_decode( $about ), MCRYPT_MODE_CBC, md5( md5( $k ) ) ), "\0");
echo $about;
?>
</form>