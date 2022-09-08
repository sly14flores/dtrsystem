<form role="form-horizontal" id="frmSchemes" onSubmit="return false;">
<div class="row">
<div class="col-lg-8">
	<div class="form-group">
	<label for="scheme_name">Name</label>
	<input type="text" class="form-control" id="scheme_name" placeholder="Enter scheme name" data-error="Please fill out scheme name" required>
	<span class="help-block with-errors"></span>
	</div>	
</div>
<div class="col-lg-4">	
	<div class="form-group">
	<label for="grace_period">Grace Period</label>
	<input type="text" class="form-control" id="grace_period" placeholder="Enter grace period" data-error="Please fill out grace period" required>
	<span class="help-block with-errors"></span>
	</div>	
</div>
</div>
<div class="row">
<div class="col-lg-12">
<h4 style="text-align: center; margin-bottom: -15px;">Schedules</h4>
<hr>
<table class="table table-bordered" style="text-align: center;">
<thead>
<tr><td>&nbsp;</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td><td>Sun</td></tr>
</thead>
<tbody style="text-align: left;">
<tr>
<td>Day Off?</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="mon_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="tue_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="wed_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="thu_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="fri_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="sat_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
<td>
<div class="checkbox"><input style="margin-left: auto; margin-right: auto;" id="sun_off" type="checkbox" onchange="skipValidation(this);"></div>
</td>
</tr>
<tr>
<td style="font-weight: bold;">AM Arrival</td>
<td>
<div class="form-group">
<input type="text" class="form-control validate-time-format" id="mon_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="tue_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="wed_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="thu_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="fri_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sat_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sun_am_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
</tr>
<tr>
<td style="font-weight: bold;">AM Departure</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="mon_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="mon_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="tue_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="tue_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="wed_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="wed_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="thu_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="thu_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="fri_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="fri_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sat_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="sat_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sun_am_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="sun_am_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
</tr>
<tr>
<td style="font-weight: bold;">PM Arrival</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="mon_pm_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="mon_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="tue_pm_arrival" placeholder="00:00" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="tue_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="wed_pm_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="wed_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="thu_pm_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="thu_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="fri_pm_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="fri_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sat_pm_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="sat_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sun_pm_arrival" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="sun_pm_in_t" type="checkbox" disabled> Transverse</label></div>
</td>
</tr>
<tr>
<td style="font-weight: bold;">PM Departure</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="mon_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="mon_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="tue_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="tue_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="wed_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="wed_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="thu_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="thu_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="fri_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="fri_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sat_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="sat_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sun_pm_departure" placeholder="00:00" data-error="" required>
<span class="help-block with-errors"></span>
</div>
<div class="checkbox"><label><input id="sun_pm_out_t" type="checkbox" disabled> Transverse</label></div>
</td>
</tr>
<tr>
<td>Required Hours</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="mon_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="tue_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="wed_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="thu_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="fri_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sat_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
<td>
<div class="form-group">
<input type="text" class="form-control" id="sun_rh" placeholder="" data-error="" required>
<span class="help-block with-errors"></span>
</div>
</td>
</tr>
<tr>
<td>Transitional</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'mon');" style="margin-left: auto; margin-right: auto;" id="mon_t" type="checkbox"></div>
</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'tue');" style="margin-left: auto; margin-right: auto;" id="tue_t" type="checkbox"></div>
</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'wed');" style="margin-left: auto; margin-right: auto;" id="wed_t" type="checkbox"></div>
</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'thu');" style="margin-left: auto; margin-right: auto;" id="thu_t" type="checkbox"></div>
</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'fri');" style="margin-left: auto; margin-right: auto;" id="fri_t" type="checkbox"></div>
</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'sat');" style="margin-left: auto; margin-right: auto;" id="sat_t" type="checkbox"></div>
</td>
<td>
<div class="checkbox"><input onchange="transverse(this.checked,'sun');" style="margin-left: auto; margin-right: auto;" id="sun_t" type="checkbox"></div>
</td>
</tr>
</tbody>
</table>
Note: <span style="font-style: italic;">Check transitional if shift spans 2 days i.e., Monday - Tuesday. Define which Time will extend to the next day by checking Transverse.</span>
</div>
</div>
<div class="row">
<div class="col-lg-12">
	<div class="form-group" style="padding-bottom: 5px;">
	<button type="submit" class="btn btn-primary pull-right" disabled="disabled">Submit</button>
	</div>
</div>
</div>
</form>