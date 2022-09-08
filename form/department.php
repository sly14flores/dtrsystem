<form role="form" id="frmDepartments" onSubmit="return false;">
	<div class="form-group">
	<label for="dept-name">Name</label>
	<input type="text" class="form-control" id="dept_name" placeholder="Enter department name" data-error="Please fill out department name" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="dept-head">Department Head</label>
	<input style="width: 555px;" type="text" class="form-control" id="dept_head" placeholder="Enter department head (optional)">
	</div>
	<div class="form-group">
	<label for="dept-note">Note</label>
	<input type="text" class="form-control" id="dept_note" placeholder="Enter note (optional)">
	</div>
	<div class="form-group" style="padding-bottom: 5px;">
	<button type="submit" class="btn btn-primary pull-right" disabled="disabled">Submit</button>
	</div>	
</form>