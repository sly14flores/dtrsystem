<?php

require 'verify-session.php';
$user = $_SESSION['user'];
$uadesc = $_SESSION['ua_description'];
$previleges = $_SESSION['previleges'];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Daily Time Record">
    <meta name="author" content="sly@unlimited">
    <link rel="shortcut icon" href="favicon.ico">

    <title>Daily Time Record | DTR System</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="bootstrap/datepicker/css/datepicker3.css" rel="stylesheet">	
	<link href="bootstrap/dist/css/typeahead.css" rel="stylesheet">
	<link href="bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <link href="css/table-fixed-header.css" rel="stylesheet">	
	
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
		
		#filter {
			margin-top: 10px;
		}
		
		.tog-alert {
			display: none;
		}

		#header {
			margin-bottom: 0;
		}	
		
		#frmContent table thead td {
			text-align: center;
		}
		
		#parentModal .modal-dialog {
			width: 800px;
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
			<li class="active"><a href="dtr.php">DTR</a></li>
            <?php if ($previleges == 1000) echo '<li><a href="preferences.php">Preferences</a></li>'; ?>
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
	
	<div class="container-fluid main-content" role="main">
		<div class="row">
			<div class="col-lg-2">
				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title">Toolbox</h3>
				  </div>
				  <div class="panel-body">				  
					<ul class="nav nav-pills nav-stacked">
					  <li><a href="javascript: uploadDTR();"><span class="glyphicon glyphicon-upload"></span> Upload DTR</a></li>
					  <li><a href="javascript: printDTR();"><span class="glyphicon glyphicon-print"></span> Print DTR</a></li>
					</ul>
					<div id="filter">
						<div class="form-group">
							<label for="dtr-view">View As:</label>
							<select id="dtr-view" class="form-control" style="width: 200px;">
							<option value="1">CS Form 48</option>
							<option value="2">Raw Records</option>
							</select>
						</div>
						<div class="form-group">							
							<select id="fdept" class="form-control" style="width: 200px;"></select>	
						</div>
						<div class="form-group">							
							<select id="fsename" class="form-control" style="width: 200px;"></select>	
						</div>						
						<div class="form-group">
							<input type="text" class="form-control" id="feid" placeholder="Employee ID" style="width: 200px;">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" id="fename" placeholder="Last Name, First Name" style="width: 200px;">
						</div>
						<div id="view-filter">
							<div class="form-group">
								<select id="rcmonth" class="form-control" style="width: 200px">
								<option value="00">--Month--</option>
								<option value="01">January</option>
								<option value="02">February</option>
								<option value="03">March</option>
								<option value="04">April</option>
								<option value="05">May</option>
								<option value="06">June</option>
								<option value="07">July</option>
								<option value="08">August</option>
								<option value="09">September</option>
								<option value="10">October</option>
								<option value="11">November</option>
								<option value="12">December</option>
								</select>
							</div>
							<div class="form-group">
								<input type="text" id="rcyear" class="form-control" placeholder="Enter year" style="width: 200px;" value="<?php echo (date("M") == "Jan") ? date("Y",strtotime("-1 year", time())) : date("Y"); ?>">
							</div>
							<div class="form-group">
								<select id="rcperiod" class="form-control" style="width: 200px;">
								<option value="first-half">First-half</option>
								<option value="second-half">Second-half</option>
								</select>
							</div>
						</div>
						<button id="dtr-filter" type="submit" class="btn btn-primary pull-right">Go!</button>
					</div>					
				  </div>
				</div>		
			</div>
			<div class="col-lg-10">
				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title" id="dtr-title">CS Form 48</h3>
				  </div>
				  <div id="dtr-page" class="panel-body"></div>
				</div>			
			</div>
		</div>
	</div>	
	
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

	<div class="modal fade bs-example-modal-sm" id="modal-progress" tabindex="-1" role="dialog" aria-labelledby="progress" aria-hidden="true" data-backdrop="static">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title" id="progress">Please wait...</h4>
		  </div>		
		  <div class="modal-body" style="text-align: center;"><img src="image/progress.gif"></div>
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
    <script src="bootstrap/datepicker/js/bootstrap-datepicker.js"></script>
	<script src="bootstrap3-editable/js/bootstrap-editable.js"></script>	
	<script src="js/global.js"></script>
	<script src="js/table-fixed-header.js"></script>	
	<script src="js/jquery.showpassword.js"></script>	
	<script src="js/dtr.js?ver=1.0.0.1"></script>
  </body>
</html>
