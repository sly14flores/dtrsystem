<?php

$dir = "logo/";
$lfile = "logo.png";

move_uploaded_file($_FILES["company-logo"]["tmp_name"], $dir . $lfile);

?>

