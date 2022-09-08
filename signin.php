<?php

$m = (isset($_GET['m'])) ? $_GET['m'] : 0;
$company_name = "The Company";
$logo_path = "logo";
$default_logo = "$logo_path/time.png";
$company_logo = "$logo_path/logo.png";
$tm = "";

$logo = $default_logo;
if (file_exists($company_logo)) $logo = $company_logo;

require 'config.php';

db_connect();
$sql = "SELECT preference_company_name, preference_company_logo FROM preferences WHERE preference_id = 1";
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$company_name = $rec['preference_company_name'];
}
$sql = "SELECT trademark FROM about WHERE about_id = 1";
$rs = $db_con->query($sql);
$rc = $rs->num_rows;
if ($rc) {
	$rec = $rs->fetch_array();
	$k = 'qJB0rGtIn5UB1xG03efyCp';
	$tm = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $k ), base64_decode( $rec['trademark'] ), MCRYPT_MODE_CBC, md5( md5( $k ) ) ), "\0");
}
db_close();



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Accounting System">
    <meta name="author" content="sly@unlimited">
    <link rel="shortcut icon" href="favicon.ico">

    <title><?php echo $company_name ?> | DTR System</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
	
    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">
    <link href="css/sticky-footer.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="bootstrap/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<style type="text/css">
		
		#forgot-password {		
			margin-top: 5px;
			color: #fa5f5c;
		}
		
	</style>	
  </head>

  <body>

    <div class="container">
	
	<div class="signin-header">
	<div class="signin-logo"><img src="<?php echo $logo; ?>" class="img-responsive" height="160" width="160" alt="Company Logo"></div>
	<h2><?php echo $company_name; ?></h2>
	<h4>eDTR | Information System</h4>
	</div>
	
      <form class="form-signin" role="form" method="post" action="user.php">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="text" class="form-control" name="username" placeholder="Username" required autofocus>
        <input type="password" class="form-control" name="password" placeholder="Password" required>	
		<?php if ($m == 1) echo '<div class="alert alert-danger">Username or password is incorrect.</div>'; ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		<a id="forgot-password" class="pull-right" href="javascript: forgotPassword();">Forgot Password?</a>
      </form>
	
    </div> <!-- /container -->
	<div id="footer">
	<p>Copyright <?php echo date("Y,"); ?> <?php echo $tm; ?>, All rights reserved.</p>
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
	
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script src="bootstrap/dist/js/jquery.min.js"></script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bootstrap/dist/js/validator.min.js"></script>			
	<script src="js/global.js"></script>
	<script src="js/signin.js"></script>
  </body>
</html>
