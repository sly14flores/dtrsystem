<?php
session_start();

unset($_SESSION['user_account_id']);
unset($_SESSION['user']);
unset($_SESSION['previleges']);

header("location: index.php");
?>