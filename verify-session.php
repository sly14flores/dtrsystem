<?php

session_start();

if (isset($_SESSION['user_account_id'])) {

} else {
	header("location: index.php");
}

?>
