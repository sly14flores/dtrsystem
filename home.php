<?php

require 'config.php';
require 'verify-session.php';
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
    <meta name="description" content="Home">
    <meta name="author" content="sly@unlimited">
    <link rel="shortcut icon" href="favicon.ico">

    <title>Home | DTR System</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="bootstrap/docs/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body role="document">

    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">	  
        <div class="navbar-collapse collapse">
		  <ul class="nav navbar-nav">
            <li class="active"><a href="home.php">Home</a></li>
            <li><a href="departments.php">Departments</a></li>
            <li><a href="employees.php">Employees</a></li>
            <?php if ($previleges == 1000) echo '<li><a href="accounts.php">User Accounts</a></li>'; ?>
            <li><a href="schemes.php">Schemes</a></li>
            <li><a href="dtr.php">DTR</a></li>
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
	
	<div class="container main-content" role="main">
		<div class="jumbotron">
		<h1>eDTR | Information System</h1>
		<ul>
		<li><p>Portable platform: import time logs from any standalone biometrics device.</p></li>
		<li><p>Create hassle-free reports (CSC Form 48) for employees.</p></li>
		<li><p>Quick and easy access on employee's 201 record.</p></li>
		</ul>
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
	
	<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/dist/js/jquery.min.js"></script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="bootstrap/dist/js/validator.min.js"></script>		
	<script src="js/global.js"></script>
	<script src="js/jquery.showpassword.js"></script>	
	<script src="js/home.js"></script>
  </body>
</html>
