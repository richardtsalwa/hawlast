<?php
//THIS FILE ACTIVATES A USER ONCE THE ADMIN CLICK A LINK IN HIS EMAIL
//I could not us the config.php file in login so session and date
//have to be initialized and defined here
date_default_timezone_set("Africa/Nairobi");
require 'database.php';
require 'headers.php';
require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Verification for the code</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<h2>Verification of your account</h2>
<?php

		if($_GET['vc']){ 
		$vc = protect($_GET['vc']);
		$errors = array();
		/*** check verification code is a string of 13 chars ***/
		if(strlen($_GET['vc']) != 13) {$errors[] = "Invalid verification code";}
        $newvc ="1";
     	// CHECK IF THE VC IS IN THE DATABASE,SO AS TO VERIFY 
		$pdo = Database::connect();
       	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       	$query = $pdo->prepare("UPDATE sms_users SET user_status = ? WHERE user_status =?");
		$query->execute(array($newvc,$vc));
		# If no row is found for query
		if( $query->rowCount() == 0 ) {
		$errors[] = "Your verification code is invalid.";
        } else {
        //We have a valid verification code, inform the client
		echo "Thank you for signing up for HAWLAST dashboard.<br />
		You can login at <a href=\"http://www.hawlast.com/saas/\">http://www.hawlast.com/saas/</a> to start sending sms or buy airtime online.<br />
		Thank you in advance.<br />
		Online Team,<br />
		Hawlast Ventures<br />
		http://www.hawlast.com"; 
	       
		} 
        } else {
        $errors[] = "No verification code provided.";
		}
		if(count($errors) > 0){
		foreach($errors AS $error){
		echo "<b style=\"color:brown;\">";
		echo $error . "<br>";
		echo "</b><br />";
		} 
		}
?>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">Hawlast.com/saas/</a>
</div>
</div>

</div>

</body></html>
