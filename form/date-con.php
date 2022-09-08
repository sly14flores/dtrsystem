<?php

require '../config.php';
$fid = $_GET['fid'];
$ld = $_GET['ld'];
$st = $_GET['st'];
$tld = date ("Y-m-d", strtotime("+1 day", strtotime($ld)));

$dcovered = date("F j, Y - l",strtotime($ld));
if ($st == 1) $dcovered = date("F j (l) - ",strtotime($ld)) . date("F j (l), Y",strtotime($tld));

?>
<form role="form" id="frmDateConTimeLogs" onSubmit="return false;">
<p><?php echo "Time Logs for: <strong>" . $dcovered . "</strong>"; ?></p>
<table class="table table-striped table-bordered" style="text-align: center;">
<thead><td>Date</td><td>#</td><td>Order</td><td>Time</td><td>Source</td><td>Encoded by</td><td>Explicit Log</td><td>Ignored?</td><?php if ($st == 1) echo '<td>Transitional</td>'; ?></thead>
<tbody>
<?php

$sql = "SELECT dtr_no, date_time_log, time_is_manual, if(time_is_manual=0,CONCAT('Device No. ', machine_id),'Manual') source, if(log_order=0,'N/A',(select case log_order when 1 then 'Arrival AM' when 2 then 'Departure AM' when 3 then 'Arrival PM' else 'Departure PM' end)) dt_order, if(time_is_manual=1,(SELECT CONCAT(user_account_firstname, ' ', user_account_lastname) FROM user_accounts WHERE user_account_id = dtr_uid),'N/A') encoder, explicit_log, ignored, transitional_order FROM dtr WHERE dtr_employee = $fid";
$c1 = " AND substr(date_time_log,1,10) = '$ld'";
if ($st == 1) {
$c1 = " AND date_time_log >= '$ld 00:00:00' AND date_time_log <= '$tld 23:59:00'";
}
$sql .= $c1;
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	for ($i=0; $i<$rc; ++$i) {
	$no = $i + 1;
	$rec = $rs->fetch_array();
	echo '<tr>';
	echo '<td>' . date("M j",strtotime($rec['date_time_log'])) . '</td>';
	echo '<td>' . $no . '</td>';
	echo '<td>' . $rec['dt_order'] . '</td>';
	echo '<td>' . date("h:i A",strtotime($rec['date_time_log'])) . '</td>';
	echo '<td>' . $rec['source'] . '</td>';
	echo '<td>' . $rec['encoder'] . '</td>';

	$to_am_in_exp = "";
	$to_am_out_exp = "";
	$to_pm_in_exp = "";
	$to_pm_out_exp = "";	
	if ($rec['explicit_log'] == 1) $to_am_in_exp = 'selected="selected"';
	if ($rec['explicit_log'] == 2) $to_am_out_exp = 'selected="selected"';
	if ($rec['explicit_log'] == 3) $to_pm_in_exp = 'selected="selected"';
	if ($rec['explicit_log'] == 4) $to_pm_out_exp = 'selected="selected"';
	if ($rec['time_is_manual'] == 1) {
	echo '<td>N/A</td>';	
	} else {
	echo '<td>';	
	echo '<select onchange="explicitLog(' . $rec['dtr_no'] . ',' . $no . ');" id="log-t' . $no . '" class="form-control" style="width: 100px;">';
	echo '<option value="0">N/A</option>';
	echo '<option value="1" ' . $to_am_in_exp . '>AM In</option>';
	echo '<option value="2" ' . $to_am_out_exp . '>AM Out</option>';
	echo '<option value="3" ' . $to_pm_in_exp . '>PM In</option>';
	echo '<option value="4" ' . $to_pm_out_exp . '>PM Out</option>';
	echo '<select>';
	echo '</td>';
	}
	
	$ignored = "";
	if ($rec['ignored'] == 1) $ignored = 'checked="checked"';	
	echo '<td><div class="checkbox" style="width: 10px; margin-right: auto; margin-left: auto;"><input onchange="ignoreLog(' . $rec['dtr_no'] . ',this.checked)" type="checkbox" ' . $ignored . '></div></td>';
	
	$to_am_in = "";
	$to_am_out = "";
	$to_pm_in = "";
	$to_pm_out = "";	
	if ($rec['transitional_order'] == 1) $to_am_in = 'selected="selected"';
	if ($rec['transitional_order'] == 2) $to_am_out = 'selected="selected"';
	if ($rec['transitional_order'] == 3) $to_pm_in = 'selected="selected"';
	if ($rec['transitional_order'] == 4) $to_pm_out = 'selected="selected"';
	if ($st == 1) {
	echo '<td>';	
	echo '<select onchange="tranLog(' . $rec['dtr_no'] . ',' . $no . ');" id="log-t' . $no . '" class="form-control" style="width: 100px;">';
	echo '<option value="0">N/A</option>';
	echo '<option value="1" ' . $to_am_in . '>AM In</option>';
	echo '<option value="2" ' . $to_am_out . '>AM Out</option>';
	echo '<option value="3" ' . $to_pm_in . '>PM In</option>';
	echo '<option value="4" ' . $to_pm_out . '>PM Out</option>';
	echo '<select>';
	echo '</td>';
	}
	echo '</tr>';
	}
}
db_close();

?>
</tbody>
</table>
</form>

<p>Notes for: <strong><?php echo $dcovered; ?></strong></p>
<form role="form" id="frmDateConNotes" onSubmit="return false;">
<table class="table table-striped table-bordered" style="text-align: center;">
<thead><td>Date</td><td>#</td><td>Order</td><td>Note</td><td>Encoded by</td><td>Shown</td><?php if ($st == 1) echo '<td>Transitional</td>'; ?></thead>
<tbody>
<?php

$sql = "SELECT note_no, note_date, note_order, if(note_order=0,'N/A',(select case note_order when 1 then 'Arrival AM' when 2 then 'Departure AM' when 3 then 'Arrival PM' else 'Departure PM' end)) n_order, note_log, enabled, (SELECT CONCAT(user_account_firstname, ' ', user_account_lastname) FROM user_accounts WHERE user_account_id = note_uid) encoder, note_transitional_order FROM dtr_notes WHERE note_fid = $fid";
$c1 = " AND note_date = '$ld'";
if ($st == 1) {
$c1 = " AND note_date >= '$ld' AND note_date <= '$tld'";
}
$sql .= $c1;
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {	
	for ($i=0; $i<$rc; ++$i) {				
	$to_am_in = "";
	$to_am_out = "";
	$to_pm_in = "";
	$to_pm_out = "";
	$no = $i + 1;
	$rec = $rs->fetch_array();
	
	$merged_note = '<select id="merged-note" onchange="mergedNote('.$rec['note_no'].',this.value);">';
	if ($rec['note_order'] == 1) $merged_note .= '<option value="1" selected>Arrival AM</option>';
	else $merged_note .= '<option value="1">Arrival AM</option>';
	if ($rec['note_order'] == 5) $merged_note .= '<option value="5" selected>Whole Day</option>';
	else $merged_note .= '<option value="5">Whole Day</option>';
	if ($rec['note_order'] == 6) $merged_note .= '<option value="6" selected>Morning</option>';
	else $merged_note .= '<option value="6">Morning</option>';
	if ($rec['note_order'] == 7) $merged_note .= '<option value="7" selected>Afternoon</option>';
	else $merged_note .= '<option value="7">Afternoon</option>';
	$merged_note .= '</select>';
	
	echo '<tr>';
	echo '<td>' . date("M j",strtotime($rec['note_date'])) . '</td>';
	echo '<td>' . $no . '</td>';
	$n_order = $rec['n_order'];
	if (($rec['note_order'] == 1) || ($rec['note_order'] == 5) || ($rec['note_order'] == 6) || ($rec['note_order'] == 7)) {
		$n_order = $merged_note;
	}
	echo '<td>' . $n_order . '</td>';
	echo '<td>' . $rec['note_log'] . '</td>';
	echo '<td>' . $rec['encoder'] . '</td>';
	$enabled = "";
	if ($rec['enabled'] == 1) $enabled = 'checked="checked"';
	echo '<td><div class="checkbox" style="width: 10px; margin-right: auto; margin-left: auto;"><input onchange="enableNote(' . $rec['note_no'] . ',this.checked)" type="checkbox" ' . $enabled . '></div></td>';
	if ($rec['note_transitional_order'] == 1) $to_am_in = 'selected="selected"';
	if ($rec['note_transitional_order'] == 2) $to_am_out = 'selected="selected"';
	if ($rec['note_transitional_order'] == 3) $to_pm_in = 'selected="selected"';
	if ($rec['note_transitional_order'] == 4) $to_pm_out = 'selected="selected"';
	if ($st == 1) {
	echo '<td>';	
	echo '<select onchange="tranNote(' . $rec['note_no'] . ',' . $no . ');" id="note-t' . $no . '" class="form-control" style="width: 100px;">';
	echo '<option value="0">N/A</option>';
	echo '<option value="1" ' . $to_am_in . '>AM In</option>';
	echo '<option value="2" ' . $to_am_out . '>AM Out</option>';
	echo '<option value="3" ' . $to_pm_in . '>PM In</option>';
	echo '<option value="4" ' . $to_pm_out . '>PM Out</option>';
	echo '<select>';
	echo '</td>';
	}
	echo '</tr>';
	}
}
db_close();

?>
</tbody>
</table>
</form>
</form>
<p><strong>Date Options</strong></p>
<?php

$onduty = "";
$hday = "";
$dleave = "";
$leave_note = "";
$show_leave_note = "";
$sql = "SELECT option_id, option_fid, option_date, on_duty, halfday, dtr_leave, leave_note, show_leave_note FROM dtr_options WHERE option_fid = '$fid' AND option_date = '$ld'";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	if ($rec['on_duty'] == 1) $onduty = 'checked="checked"';
	if ($rec['halfday'] == 1) $hday = 'checked="checked"';
	if ($rec['dtr_leave'] == 1) $dleave = 'checked="checked"';
	$leave_note = $rec['leave_note'];
	if ($rec['show_leave_note'] == 1) $show_leave_note = 'checked="checked"';
}
db_close();

?>
<table class="table table-bordered table-striped">
<thead><tr><td>On-Duty</td><td>Halfday</td><td>Leave</td><td>Note</td></tr></thead>
<tbody>
<tr>
<td>
<div class="checkbox" style="width: 10px; margin-left: auto; margin-right: auto;"><input id="onduty" type="checkbox" onchange="onDuty(<?php echo "'$fid','$ld'"; ?>,this.checked);" <?php echo $onduty; ?>></div>
</td>
<td>
<div class="checkbox" style="width: 10px; margin-left: auto; margin-right: auto;"><input id="halfday" type="checkbox" onchange="enableHalfday(<?php echo "'$fid','$ld'"; ?>,this.checked);" <?php echo $hday; ?>></div>
</td>
<td>
<div class="checkbox" style="width: 10px; margin-left: auto; margin-right: auto;"><input id="leave" type="checkbox" onchange="enableLeave(<?php echo "'$fid','$ld'"; ?>,this.checked);" <?php echo $dleave; ?>></div>
</td>
<td>
<div class="form-group">
<input type="text" id="leave-note" placeholder="Enter leave type" class="form-control" value="<?php echo $leave_note; ?>">
<div class="checkbox"><label><input type="checkbox" id="ln-shown" onchange="showLN(<?php echo "'$fid','$ld'"; ?>,this.checked);" <?php echo $show_leave_note; ?>> Show on report</label></div>
<input type="hidden" id="hfid" value="<?php echo $fid; ?>">
<input type="hidden" id="hld" value="<?php echo $ld; ?>">
</div>
</td>
</tr>
</tbody>
</table>