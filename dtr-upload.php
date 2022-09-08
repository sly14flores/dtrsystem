<?php

$dir = "dtr/";
$dfile = "dtr.txt";

move_uploaded_file($_FILES["dtr-file"]["tmp_name"], $dir . $dfile);

?>

