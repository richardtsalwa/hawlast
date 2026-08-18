<?php
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
//if(isset($_SESSION['access_level'])) {header("Location: ./index.php"); }
date_default_timezone_set("Africa/Nairobi");
require 'database.php';
require 'headers.php';
require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Airtime and SMS app</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
 <link   href="css/bootstrap.min.css" rel="stylesheet">
 <script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<?php
if (isset($_POST['reset'])) {

	$errors = array();

	if (empty($_POST['email'])) {
	$errors[] = "Provide an emaill address";
         $email = "";
	} else {
	$email = protect($_POST['email']);
	}

	$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";

	if(!preg_match($checkemail, $email)){
	$errors[] = "E-mail is not valid, must be name@server.tld";
	}

     /// CHECK IF THE EMAIL IS IN THE DATABASE, IF IT IS RESET THE PASSWORD AND EMAIL TO EMAIL
	$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    /*** check for existing email and password ***/
        $query = $pdo->prepare( "SELECT email FROM airtime_user WHERE email =?" );
	$query->bindValue( 1, $email );
	$query->execute();

	if( $query->rowCount() == 0 ) { # If no row is found for query

	$errors[] = "The e-mail address you supplied does not exist!";
	} 
	
	//IF WE HAVE VALIDATION ERRORS 
	if(count($errors) > 0){
	foreach($errors AS $error){
	echo "<b style=\"color:red;\">";
	echo $error;
	echo "</b><br />";
	}

        } else {
	
	$newpwd = uniqid();
	$password = sha1($newpwd);
	// process form
   /*** check for existing email and password ***/ 
        $pdo = Database::connect();
	$stmt = $pdo->prepare("UPDATE airtime_user SET password=? WHERE email=?");
	$stmt->execute(array($password, $email));

	if( $stmt->rowCount() == 1 ) { # If a row is updated for query
        
        $subject = "New password for Hawlast Account";

	$message = "Hello,

Greetings from Hawlast SaaS

Someone reset the password of your Hawlast SaaS account.

Your new password is $newpwd. You can login at http://www.hawlast.com/saas

Best Regards,

Online team,

Hawlast Ventures
https://www.hawlast.com/
http://www.facebook.com/hawlast
http://www.twitter.com/hawlast";

      /* Send the message using mail() function */
      if (@mail($email, $subject, $message, $headers)) {
      echo '<br />';echo '<br />';
      echo "<h3>We have emailed a new password to $email. </h3>"; 
            } else { 
      echo ("<p><h3>Message delivery failed...</h3></p>");
      echo '<br />';
       }
          

	} else { // Update failed 
     	echo "Could not reset the password";

	}
}
}

//USER HAS ENTERED A USERNAME AND PASSWORD
if (isset($_POST['submit'])) {

	$errors = array();
     	$password ="";
     	$email = "";
	if (empty($_POST['email'])) {
	$errors[] = "E-mail is not valid, must be name@server.tld!";
	} else {
	$email = protect($_POST['email']);
	}

	if (empty($_POST['password'])) {
	$errors[] = "Please enter your password";
	} else {
	$passwordx = protect($_POST['password']);
        $password = sha1($passwordx);
	}

     /// CHECK IF THE EMAIL IS IN THE DATABASE, IF IT IS RESET THE PASSWORD AND EMAIL TO EMAIL
	$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*** check for existing email and password ***/
        $query = $pdo->prepare( "SELECT email FROM airtime_user WHERE email =?" );
	$query->bindValue( 1, $email );
	$query->execute();

	if( $query->rowCount() == 0 ) { # If no row is found for query
    	$errors[] = "The email address is not in our system";
	} 

    //$pdo = Database::connect();
    //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     /*** check for existing email and password ***/
     $sql = "SELECT fname,lname,access_level,credit,password,airtimeid,email FROM sms_users LEFT JOIN airtime_user USING (airtimeid) WHERE email =?";
     $stmt = $pdo->prepare($sql); // Prepare the statement
     if($stmt->execute(array($email)))
    {
     // Loop through the returned results
    while($row = $stmt->fetch()) {
	$correctpassword = $row["password"];
	if ($correctpassword !== $password) { 
	$errors[] = "The password you supplied is wrong";
	}

    $access_level = $row["access_level"];
    $airtimeid =$row["airtimeid"];
    $valid_user=$row["email"];   
    $credit=$row["credit"];   
    } 
    Database::disconnect(); // Free memory used in this query
          
    } 
   
//WE HAVE AN ERROR RETRIEVING THE PASSWORD OR EMAIL OR THEY DO NOT MATCH
//IF WE HAVE VALIDATION ERRORS 
	if(count($errors) > 0){
	foreach($errors AS $error){
	echo "<b style=\"color:brown;\">";
	echo $error; 
	echo "</b><br />";
	}
    ?>
	<BR/> 

	<P>Login to send bulk SMS or top up your Airtel or Telkom</p> 
	<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
	<input name="email" type="email" placeholder="Enter your email address" value="<?php if (!empty($email)){echo $email;} ?>" required><br />
	<input name="password" placeholder="Password" type="password" value="" required><br />
	<input type="submit" name="submit" value="Login"><br/>
	<a href="register.php"><b>Register</b></a> 
	<a href="login.php?reset=1"><b>Forgot password?</b></a><br/>
	</form>
	<?php
      
    } else {
    //after login we redirect to home page
    $_SESSION['access_level']= $access_level;
    $_SESSION['user_id']= $airtimeid;
    $_SESSION['valid_user']=$valid_user;  
    $_SESSION['credit']= $credit;  
    header('Location: index.php');
    exit();
    }
	 
} else {
if (isset($_GET['reset']) == 1) { 
?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<h3>To reset your password</h3>
<input name="email" type="email" placeholder="Enter your email" value="" required><br/>
<input type="submit" name="reset" value="Resend password"><br/>
</form>
<?php 
} 
?>
<h2>HAWLAST AIRTIME </h2>
<p>Login to send bulk SMS at 1KES/SMS or top up airtime.</p>

 <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<input name="email" type="email" placeholder="Enter your email address" value="" required><br />
<input name="password" placeholder="Password" type="password"  required><br />
<input type="submit" name="submit" value="Login"><br/>
<a href="register.php"><b>Register</b></a> 
<a href="login.php?reset=1"><b>Forgot password?</b></a><br/>
</form>
<p><b>You will pay via Airtel money/ PayPal/ Visa /MasterCard/MPESA</b></p>
<?php
}
//End of if user has submitted the data 
?>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">Hawlast.com/saas/</a></div>
</div>
</div></div>
</body>
</html>