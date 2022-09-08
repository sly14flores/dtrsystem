<?php

$dname = "";
$dhead = "";
$dnote = "";

require '../config.php';

$deptid = (isset($_GET['deptid'])) ? $_GET['deptid'] : 0;

$sql = "SELECT dept_name, dept_head, dept_note FROM departments WHERE dept_id = $deptid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$dname = stripslashes($rec['dept_name']);
	// $dhead = $rec['dept_head'];
	$dnote = stripslashes($rec['dept_note']);
}
db_close();

?>
<form role="form" id="frmSettings" onSubmit="return false;">
	<h3>Change Password</h3>
	<hr>
	<div class="form-group">
	<label for="current">Current</label>
	<input type="password" class="form-control" id="current" placeholder="Type current password" data-error="Please fill out current" value="" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="pnew">New</label>
	<input type="password" class="form-control" id="pnew" placeholder="Type new password" data-error="Please fill out new password" value="" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="rnew">Re-type new</label>
	<input type="password" class="form-control" id="rnew" placeholder="Re-type new password" data-error="Please fill out re-type new password" value="" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<span class="show-verify" style="color: #A94442;"></span>
	</div>
	<div class="form-group" style="padding-bottom: 5px;">
	<button type="submit" class="btn btn-primary pull-right" disabled="disabled">Update</button>
	</div>	
</form>