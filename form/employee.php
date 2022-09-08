<form class="form-horizontal" role="form" id="frmEmployees" onSubmit="return false;">
<div class="container">
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_fid">Employee ID</label>
	<input type="text" class="form-control" id="employee_fid" placeholder="Enter employee ID" data-error="Please fill out employee ID" required>
	<span class="help-block with-errors"></span>
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_dept">Department</label>
	<select id="employee_dept" class="form-control" placeholder="Select department">
	<option value="0">None</option>
	</select>
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_mid">Machine ID</label>
	<input type="text" class="form-control" id="employee_mid" placeholder="Enter machine ID" data-error="Please fill out machine ID" required>
	<span class="help-block with-errors"></span>
	</div>
</div>
</div>
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_lastname">Last Name</label>
	<input type="text" class="form-control" id="employee_lastname" placeholder="Enter last name" data-error="Please fill out last name" required>
	<span class="help-block with-errors"></span>
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_firstname">First Name</label>
	<input type="text" class="form-control" id="employee_firstname" placeholder="Enter first name" data-error="Please fill out first name" required>
	<span class="help-block with-errors"></span>
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_middlename">Middle Name</label>
	<input type="text" class="form-control" id="employee_middlename" placeholder="Enter middle name" data-error="Please fill out middle name" required>
	<span class="help-block with-errors"></span>
	</div>
</div>	
</div>
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_gender">Gender</label>
	<select id="employee_gender" class="form-control" placeholder="Select gender"><option value="Male">Male</option><option value="Female">Female</option></select>
	</div>
</div>
<div class="col-md-4">			
	<div class="form-group">
	<label for="employee_dob">Date of Birth</label>
	<input id="employee_dob" type="text" class="form-control" data-provide="datepicker" placeholder="mm/dd/yyyy">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_age">Age</label>
	<input type="text" class="form-control" id="employee_age" placeholder="Enter age">	
	</div>
</div>	
</div>	
<div class="row">
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_contacts">Contact No(s)</label>
	<input type="text" class="form-control" id="employee_contacts" placeholder="Enter contact no(s)">	
	</div>
</div>
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_address">Address</label>
	<input type="text" class="form-control" id="employee_address" placeholder="Enter address">	
	</div>
</div>
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_position">Position</label>
	<input type="text" class="form-control" id="employee_position" placeholder="Enter position">
	</div>
</div>	
</div>
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_attainment">Educational Attainment</label>
	<input type="text" class="form-control" id="employee_attainment" placeholder="Enter educational attainment">
	</div>
</div>
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_eligibility">Eligibility</label>
	<input type="text" class="form-control" id="employee_eligibility" placeholder="Enter eligibility">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_years">Years in service</label>
	<input type="text" class="form-control" id="employee_years" placeholder="Enter years in service">
	</div>
</div>	
</div>
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_appointment">Status of appointment</label>
	<input type="text" class="form-control" id="employee_appointment" placeholder="Enter status of appointment">
	</div>
</div>
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_sss">SSS</label>
	<input type="text" class="form-control" id="employee_sss" placeholder="Enter SSS No">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_gsis">GSIS</label>
	<input type="text" class="form-control" id="employee_gsis" placeholder="Enter GSIS No">
	</div>
</div>	
</div>
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_philhealth">Philhealth</label>
	<input type="text" class="form-control" id="employee_philhealth" placeholder="Enter Philhealth ID">
	</div>
</div>
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_hdmf">HDMF</label>
	<input type="text" class="form-control" id="employee_hdmf" placeholder="Enter HDMF ID">
	</div>
</div>
<div class="col-md-4">	
	<div class="form-group">
	<label for="employee_tin">TIN</label>
	<input type="text" class="form-control" id="employee_tin" placeholder="Enter TIN No">
	</div>
</div>	
</div>
<div class="row">
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_remarks">Remarks</label>
	<input type="text" class="form-control" id="employee_remarks" placeholder="Enter remarks">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
	<label for="employee_scheme">Scheme</label>
	<select id="employee_scheme" class="form-control" placeholder="Select scheme"><option value="0">Undefined</option></select>	
	</div>
</div>
<div class="col-md-4">&nbsp;</div>
</div>
<div class="row">
<div class="col-md-12">	
	<div class="form-group" style="margin-right: 100px;">
	<button type="submit" class="btn btn-primary pull-right" disabled="disabled">Submit</button>
	</div>
</div>	
</div>	
</div><!-- container -->
</form>