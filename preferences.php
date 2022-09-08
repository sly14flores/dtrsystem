<?php

require 'verify-session.php';
require 'config.php';
require 'globalf.php';
$user = $_SESSION['user'];
$uadesc = $_SESSION['ua_description'];
$previleges = $_SESSION['previleges'];
$tm = trade_mark();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Preferences">
    <meta name="author" content="sly@unlimited">
    <link rel="shortcut icon" href="favicon.ico">

    <title>Preferences | DTR System</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="bootstrap/dist/css/typeahead.css" rel="stylesheet">
	
    <!-- Custom styles for this template -->
    <link href="css/theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="bootstrap/docs/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
		
	<style type="text/css">
		.tog-alert {
			display: none;
		}
	</style>
  </head>

  <body role="document">

    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-collapse collapse">
		  <ul class="nav navbar-nav">
            <li><a href="home.php">Home</a></li>
            <li><a href="departments.php">Departments</a></li>
			<li><a href="employees.php">Employees</a></li>
            <?php if ($previleges == 1000) echo '<li><a href="accounts.php">User Accounts</a></li>'; ?>
            <li><a href="schemes.php">Schemes</a></li>
			<li><a href="dtr.php">DTR</a></li>
            <li class="active"><a href="preferences.php">Preferences</a></li>
			<li><a href="javascript: about();">About</a></li>
          </ul>
		<p class="navbar-text navbar-right">
		<a href="javascript: settings();" class="navbar-link"><span class="glyphicon glyphicon-cog" data-toggle="tooltip" data-placement="bottom" title="Settings"></span></a>
		<a href="javascript: logout();" class="navbar-link" style="margin-left: 10px;"><span class="glyphicon glyphicon-user" data-toggle="tooltip" data-placement="bottom" title="Logout"></span></a>		
		</p>		  
        </div><!--/.nav-collapse -->
      </div>
    </div>
	<h6 class="account-profile">Logged in as <?php echo "$uadesc | ".date("F d, Y - l"); ?></h6>
	
<?php

$coname = "";
$cosign = "";
$cosigne = "";
$cogperiod = "";
$cogperiode = "";

$sql = "SELECT preference_company_name, preference_company_logo, preference_company_signatory, preference_company_gperiod, gperiod_global_enabled, signatory_global_enabled FROM preferences WHERE preference_id = 1";
db_connect();
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$coname = $rec['preference_company_name'];
	$cosign = $rec['preference_company_signatory'];
	$cosigne = ($rec['signatory_global_enabled'] == 1) ? 'checked="checked"' : '';
	$cogperiod = $rec['preference_company_gperiod'];
	$cogperiode = ($rec['gperiod_global_enabled'] == 1) ? 'checked="checked"' : '';
}
db_close();

?>
	
	<div class="container main-content" role="main">
		<div class="row">
			<div class="col-lg-6">
				<form role="form" id="logo_upload" action="logo-upload.php" method="post" enctype="multipart/form-data" target="upload_target">
				<div class="form-group">
				<label for="company-logo">Company Logo</label>
				<input id="company-logo" name="company-logo" type="file">
				</div>
				<div class="progress active">
				  <div class="upload-progress progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
					0%
				  </div>
				</div>
				<div id="logo-alert" class="alert alert-warning">Supported formats are jpg, gif, and png.</div>
				</form>
				<form role="form" id="frmPreferences" onSubmit="return false;">							
				<div class="form-group">
				<label for="company-name">Company Name</label>
				<input type="text" class="form-control" id="company-name" placeholder="Enter company name" data-error="Please fill out company name" value="<?php echo $coname; ?>" required>
				<span class="help-block with-errors"></span>
				</div>
				<hr>
				<h4>Global Settings</h4>
				<div class="form-group">
				<label for="signatory">Signatory</label>
				<input style="width: 575px;" type="text" class="form-control" id="signatory" placeholder="Enter name (First Name MI. Last Name)" data-error="Please fill out signatory" value="<?php echo $cosign; ?>" required>
				<div class="checkbox">
				<label><input id="gs-enabled" type="checkbox" <?php echo $cosigne; ?>> Enabled</label>
				</div>
				<span class="help-block with-errors"></span>
				</div>
				<div class="form-group">
				<label for="grace-period">Grace Period</label>
				<input type="text" class="form-control" id="grace-period" placeholder="Enter grace period (in minutes i.e., 15)" data-error="Please fill out grace period" value="<?php echo $cogperiod; ?>" required>
				<div class="checkbox">
				<label><input id="ggp-enabled" type="checkbox" <?php echo $cogperiode; ?>> Enabled</label>
				</div>
				<span class="help-block with-errors"></span>
				</div>				
				<div class="form-group">
				<button type="submit" class="btn btn-primary" <?php if (($coname == "") && ($cosign == "") && ($cogperiod == "")) echo 'disabled="disabled"'; ?>>Update</button>
				</div>
				</form>
			</div>
		</div>
	</div>
	
	<?php include 'footer.php'; ?>	
	
	<div class="modal fade bs-example-modal-sm" id="modal-confirm" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true" data-backdrop="static">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="confirmation">Confirmation</h4>
		  </div>
		  <div class="modal-body"></div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btnYes">Yes</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		  </div>
		</div>
	  </div>
	</div>

	<div class="modal fade bs-example-modal-sm" id="modal-notify" tabindex="-1" role="dialog" aria-labelledby="notification" aria-hidden="true" data-backdrop="static">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="notification">Notification</h4>
		  </div>
		  <div class="modal-body"></div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
		  </div>
		</div>
	  </div>
	</div>    
	
	<div class="modal fade" id="parentModal" tabindex="-1" role="dialog" aria-labelledby="parent-modal" aria-hidden="true" data-backdrop="static">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="parent-modal"></h4>
		  </div>
		  <div class="modal-body"></div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>	
	
	<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
	
	<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/dist/js/jquery.min.js"></script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="bootstrap/dist/js/validator.min.js"></script>
	<script src="bootstrap/dist/js/typeahead.bundle.js"></script>	
	<script src="js/global.js"></script>
	<script src="js/jquery.showpassword.js"></script>	
	<script src="js/preferences.js"></script>
  </body>
</html>