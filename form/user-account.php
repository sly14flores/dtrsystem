<form role="form" id="frmUserAccounts" onSubmit="return false;">
	<div class="form-group">
	<label for="user_account_privileges">Role</label>
	<select id="user_account_privileges" class="form-control">
	<option value="100">Manager</option>
	<option value="1000">Administrator</option>
	</select>
	</div>
	<div class="form-group">
	<label for="user_account_lastname">Last Name</label>
	<input type="text" class="form-control" id="user_account_lastname" placeholder="Enter last name" data-error="Please fill out last name" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="user_account_firstname">First Name</label>
	<input type="text" class="form-control" id="user_account_firstname" placeholder="Enter first name" data-error="Please fill out first name" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="user_account_mi">Middle Name</label>
	<input type="text" class="form-control" id="user_account_mi" placeholder="Enter middle name" data-error="Please fill out middle name" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="user_account_email">Email</label>
	<input type="email" class="form-control" id="user_account_email" placeholder="Enter email">
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="user_account_contact">Contact(s)</label>
	<input type="text" class="form-control" id="user_account_contact" placeholder="Enter contact(s)">
	</div>		
	<div class="form-group">
	<label for="user_account_username">Username</label>
	<input type="text" class="form-control" id="user_account_username" placeholder="Enter username" data-error="Please fill out username" required>
	<span class="help-block with-errors"></span>
	</div>
	<div class="form-group">
	<label for="user_account_password">Password</label>
	<input type="password" class="form-control" id="user_account_password" placeholder="Enter password" data-error="Please fill out password" data-typetoggle="#show-password" required>
	<label><input id="show-password" type="checkbox" /> Show password</label>
	<span class="help-block with-errors"></span>
	</div>
	<div id="username-exists"><input id="username-exists" type="hidden" value="0"></div>
	<div class="form-group" style="padding-bottom: 5px;">
	<button type="submit" class="btn btn-primary pull-right" disabled="disabled">Submit</button>
	</div>	
</form>