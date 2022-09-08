<?php

require '../config.php';

$scid = (isset($_GET['scid'])) ? $_GET['scid'] : 0;

$sname = "";
$ama = "";
$amd = "";
$pma = "";
$pmd = "";
$wh = 0;
$gp = 0;
$st = "";

$sql = "SELECT scheme_name, am_arrival, am_departure, pm_arrival, pm_departure, scheme_wh, transitional, grace_period FROM schemes WHERE scheme_id = $scid";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$sname = $rec['scheme_name'];
	$ama = date("H:i",strtotime($rec['am_arrival']));
	$amd = date("H:i",strtotime($rec['am_departure']));
	$pma = date("H:i",strtotime($rec['pm_arrival']));
	$pmd = date("H:i",strtotime($rec['pm_departure']));
	$wh = $rec['scheme_wh'];
	$gp = $rec['grace_period'];
	if ($rec['transitional'] == 1) $st = 'checked="checked"';
}
db_close();

?>
<form role="form" id="frmSchemes" onSubmit="return false;">
	<div class="form-group">
	<label for="scheme-name">Name</label>
	<input type="text" class="form-control" id="scheme-name" placeholder="Enter scheme name" data-error="Please fill out scheme name" value="<?php echo $sname; ?>" required>
	<span class="help-block with-errors"></span>
	</div>	
	<div class="form-group">
	<label for="am-arrival">AM Arrival</label>
	<input type="text" class="form-control" id="am-arrival" placeholder="Enter AM arrival, i.e., 08:00" data-error="Please fill out AM arrival" value="<?php echo $ama; ?>" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="am-departure">AM Departure</label>
	<input type="text" class="form-control" id="am-departure" placeholder="Enter AM departure, i.e., 12:00" data-error="Please fill out AM departure" value="<?php echo $amd; ?>" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="pm-arrival">PM Arrival</label>
	<input type="text" class="form-control" id="pm-arrival" placeholder="Enter PM arrival, i.e., 13:00" data-error="Please fill out PM arrival" value="<?php echo $pma; ?>" required>
	<span class="help-block with-errors"></span>
	</div>	
	<div class="form-group">
	<label for="pm-departure">PM Departure</label>
	<input type="text" class="form-control" id="pm-departure" placeholder="Enter PM departure, i.e., 17:00" data-error="Please fill out PM departure" value="<?php echo $pmd; ?>" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="scheme-wh">Work Hours</label>
	<input type="text" class="form-control" id="scheme-wh" placeholder="Enter required work hours per day" data-error="Please fill out required work hours per day" value="<?php echo $wh; ?>" required>
	<span class="help-block with-errors"></span>
	</div>	
	<div class="form-group">
	<label for="grace-period">Grace Period</label>
	<input type="text" class="form-control" id="grace-period" placeholder="Enter grace period" data-error="Please fill out grace period" value="<?php echo $gp; ?>" required>
	<span class="help-block with-errors"></span>
	</div>	
	<div class="checkbox">
	<label><input id="scheme-transitional" type="checkbox" <?php echo $st; ?>> Transitional <br><span style="font-style: italic;">(Checked this option if shift spans 2 days i.e., Monday - Tuesday)</span></label>
	</div>	
	<div class="form-group" style="padding-bottom: 5px;">
	<button type="submit" class="btn btn-primary pull-right" disabled="disabled">Submit</button>
	</div>	
</form>