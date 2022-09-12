<form role="form" id="frmUploadDTR" action="dtr-upload.php" method="post" enctype="multipart/form-data" target="upload_target">
	<div class="form-group">
	<label for="dtr-file">DTR File</label>
	<input id="dtr-file" name="dtr-file" type="file">
	</div>
	<div class="progress active">
	  <div class="upload-progress progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
		0%
	  </div>
	</div>
	<div id="dtr-alert" class="alert alert-warning">Please select <strong>dat file</strong> file.</div>
	<div class="form-group">
	<label for="dtr-month">Month</label>
		<select id="dtr-month" class="form-control">
		<option <?php if (date("m") == "01") echo 'selected="selected"'; ?> value="01">January</option>
		<option <?php if (date("m") == "02") echo 'selected="selected"'; ?> value="02">February</option>
		<option <?php if (date("m") == "03") echo 'selected="selected"'; ?> value="03">March</option>
		<option <?php if (date("m") == "04") echo 'selected="selected"'; ?> value="04">April</option>
		<option <?php if (date("m") == "05") echo 'selected="selected"'; ?> value="05">May</option>
		<option <?php if (date("m") == "06") echo 'selected="selected"'; ?> value="06">June</option>
		<option <?php if (date("m") == "07") echo 'selected="selected"'; ?> value="07">July</option>
		<option <?php if (date("m") == "08") echo 'selected="selected"'; ?> value="08">August</option>
		<option <?php if (date("m") == "09") echo 'selected="selected"'; ?> value="09">September</option>
		<option <?php if (date("m") == "10") echo 'selected="selected"'; ?> value="10">October</option>
		<option <?php if (date("m") == "11") echo 'selected="selected"'; ?> value="11">November</option>
		<option <?php if (date("m") == "12") echo 'selected="selected"'; ?> value="12">December</option>
		</select>
	</div>
	<div class="form-group">
	<label for="dtr-year">Year</label>
		<select id="dtr-year" class="form-control">
		<option value="<?php echo date("Y",strtotime("-1 Year")); ?>"><?php echo date("Y",strtotime("-1 Year")); ?></option>
		<option value="<?php echo date("Y"); ?>" selected="selected"><?php echo date("Y"); ?></option>		
		</select>
	</div>
	<div class="checkbox">
		<label>
		  <input id="recent-dtr" type="checkbox"> Use recently uploaded DTR.
		</label>
	</div>	
	<div class="form-group" style="padding-bottom: 5px;">
	<button type="button" class="btn btn-primary pull-right" disabled="disabled">Upload</button>
	</div>	
</form>