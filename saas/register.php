<?php
//I could not us the config.php file in login so session and date
//have to be initialized and defined here
session_start();
if(isset($_SESSION['access_level']))
        {
        header("Location: ./"); 
}
date_default_timezone_set("Africa/Nairobi");
require 'database.php';
require 'headers.php';
require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>REGISTER - HAWLAST SAAS app</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">

        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
        <link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<h2> Registration form </h2>
<?php

if(!isset($_POST['submit'])){
?>
<!--if no data is posted, display the form --->
<form enctype="multipart/form-data" method="post" action="<?php echo basename($_SERVER['PHP_SELF']) ?>">
<input type="text" placeholder="First name" name="fname" required><br/>
<input type="text" placeholder="Last name"  name="lname" required><br/>
<input type="email" placeholder="Email address"  name="email" required><br/>
Your mobile phone no.<br/>
<input type="text" name="phone" placeholder="07" required><br/>
Alternative phone no.(<b>Optional</b>)<br/>
<input type="text" name="phonea" placeholder="07" required><br/>
<input type="password"  placeholder="Password" name="password" required><br/>
<input type="password"  placeholder="Confirm password" name="passwordconf" required><br/>
<input type="hidden" name="refererid" value="<?php if(!empty($_GET['refererid'])) {echo protect($_GET['refererid']);} else {echo "1";  } ?>"><br/>
<input type="submit" name="submit" value="Register"></form>
<a href="index.php">OR LOGIN</a><br/>
<br/>

<?php
 } else {

	///PROCESS THE POSTED DATA
	$errors = array();

	$id = "";

    	if($_POST['fname']){
	$fname = protect($_POST['fname']); 
	$fname = ucfirst(strtolower($fname));
        } else {
        $errors[] = "Please enter your first name!";
	}

        if($_POST['refererid']){
	$refererid = protect($_POST['refererid']); 
        } else {
      $refererid = "1";
        }

    	if($_POST['lname']){ 
	$lname = protect($_POST['lname']);
	$lname = ucfirst(strtolower($lname));
        } else {
        $errors[] = "Please enter your last name!";
	}

    	if($_POST['email']){ 
	$email = protect($_POST['email']);

     		// CHECK IF THE EMAIL IS IN THE DATABASE,AVOID REPEATS 
		$pdo = Database::connect();
       		 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      		/*** check for existing email and password ***/
       		 $query = $pdo->prepare( "SELECT fname FROM `airtime_user` WHERE `email` = ?" );
		$query->bindValue( 1, $email );
		$query->execute();

		if( $query->rowCount() == 0 ) { # If no row is found for query
        	} else {
        	 $errors[] = "Your email address is already registered. Use the reset link";
        	}

        } else {
        $errors[] = "Please enter your email address!";
	}

    if (empty($_POST['phone'])){ 
       $errors[] = "Enter a phone number";
     } else {
	      $phone = protect($_POST['phone']);
		  $phone = add254($phone);
	      if(!preg_match('/^(\+254|0)[1-9]\d{8}$/', $phone)) {  
		  $errors[] = "Enter a phone number in the format 07xxyyyzzz";
	  }
        // CHECK IF THE phone IS IN THE DATABASE,AVOID REPEATS 
		$pdo = Database::connect();
       		 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      		/*** check for existing email and password ***/
       		 $query = $pdo->prepare( "SELECT phone FROM `airtime_user` WHERE `phone` = ?" );
		     $query->bindValue( 1, $phone );
		     $query->execute();

		    if( $query->rowCount() == 0 ) { 
        	} else {
        	 $errors[] = "Your phone number is already registered.";
        	}	
    }

     if (empty($_POST['phonea'])){ 
     $phonea = "1"; 
     } else {
	      $phonea = protect($_POST['phonea']);
              $phonea = add254($phonea);
	      if(!preg_match('/^(\+254|0)[1-9]\d{8}$/', $phonea)) {  
		  $errors[] = "Enter the alternative phone number in the format 07xxyyyzzz";
	  } 
       }

   	if($_POST['password']){
	$password = protect($_POST['password']);
	//Password should be at least 5 characters
	$range = range(5,20);
	if(!in_array(strlen($password),$range)){$errors[] = "Password should be at least 5-20 characters!";}

 	if($_POST['passwordconf']){ 
	$confirm = protect($_POST['passwordconf']);
        } else {
        $errors[] = "Please reconfirm the password!";
	}

	if($password && $confirm){if($password != $confirm){$errors[] = "Passwords do not match!";}}

       	$password = sha1($password);
        } else {
        $errors[] = "Please enter a password!";
	}
  
	//In future we can request for ID numbers
	$idno = "1";
		
	$credit = "N";
	$creditlimit = "0";
	$signup = date ("Y-m-d");

	//IF WE HAVE VALIDATION ERRORS 
	if(count($errors) > 0){
	foreach($errors AS $error){
	echo "<b style=\"color:brown;\">";
	echo $error;
	echo "</b><br />";

	}

	///Display registration form here
	echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"".basename($_SERVER['PHP_SELF'])."\">\n";
	echo "<input type=\"text\" placeholder=\"First name\" name=\"fname\" required><br/>";
	echo "<input type=\"text\" placeholder=\"Last name\"  name=\"lname\" required><br/>";
	echo "<input type=\"email\" placeholder=\"Email address\"  name=\"email\" required><br/>";
 	echo "Your mobile phone no.<br/>";
	echo "<input type=\"text\" placeholder=\"07\" name=\"phone\" required><br/>";
	echo "Alternative phone no. (<b>Optional</b>)<br/>";
	echo "<input type=\"text\" placeholder=\"07\" name=\"phonea\" ><br/>";
	echo "<input type=\"password\"  placeholder=\"Password\" name=\"password\" required><br/>";
	echo "<input type=\"password\"  placeholder=\"Confirm password\" name=\"passwordconf\" required><br/>";
	echo "<input type=\"hidden\" name=\"refererid\" value=\"".$refererid."\">";
	echo "<input type=\"submit\" name=\"submit\" value=\"Register\"></form>";
	echo "<a href=\"index.php\">OR LOGIN</a><br/>";
	echo "<br/>";

	} else {

	///THERE ARE NO ERRORS HERE

	try {

	$pdo->beginTransaction();

	$stmt = $pdo->prepare("INSERT INTO airtime_user(airtimeid,fname,lname,idno,phonea,phone,credit,password,refererid,creditlimit,email,signup) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->execute(array($id,$fname,$lname,$idno,$phonea,$phone,$credit,$password,$refererid,$creditlimit,$email,$signup));
  
	$airtimeid = $pdo->lastInsertId();
	$access_level = "1";
	$user_status = uniqid();
	$bal = "0";
	$id = "";

	$stmt = $pdo->prepare("INSERT INTO sms_users(id,access_level,user_status,bal,airtimeid) VALUES(?,?,?,?,?)");
	$stmt->execute(array($id,$access_level,$user_status,$bal,$airtimeid));

	$pdo->commit();


	//Email to verify the registration

         /*** email subject ***/
         $subject = 'Confirm your registration';
	//We had assigned a unique id to the user status above
         $verification_code = $user_status; 
		 
         /*** the message ***/
         $mid = dirname($_SERVER['REQUEST_URI']);
         
         $url = 'http://'.$_SERVER['HTTP_HOST'].$mid.'/verify.php?vc='.$verification_code;

         $message = "Dear $fname $lname,

Thank you for signing up for airtime service.

You can activate the account by clicking at $url

HOW IT WORKS
You can log in and transfer the airtime to friends /workmates /neighbours

What networks do you work with
- You can top up Safaricom or Airtel phone numbers 

How to top up your account 
Once you deplete the balance, you can top up by deposit in our Airtel money/ PayPal/ Visa /MasterCard/MPESA
PayPal: support@hawlast.com
Lipa na MPESA: 773599

Referral program (Your special link)
Send this link to your friends http://www.hawlast.com/saas/register.php?refererid=$airtimeid
For every new user who signs up, we give you a KES 10 bonus.

Annual Service fee
After one month trial, you will be expected to pay KES 500 annual subscription. This is the only fee you will pay per year.

For inquiries, call/ SMS on 0720401869 /0735187782

Online Team,
	
Hawlast Ventures

http://www.hawlast.com 
http://www.facebook.com/hawlast
http://www.twitter.com/hawlast";
                
		//*** set some headers ***/
		//Headers already set up in the headers.php

            /*** send the email ***/
             if(!mail($email, $subject, $message, $headers))
                {
		echo "Unable to send verification email";
		//echo $message;
                 } else { 
		echo 'Sign up almost complete.<br />'; 
		echo "Kindly check your inbox $email and verify.<br />";
                 }

        //End of email verification code

	} catch(PDOException $ex) {
   	 //Something went wrong rollback!
    	$pdo->rollBack();
    	echo $ex->getMessage();
	}


} // End of no error test

} 
//END IF DATA IS POSTED 
?>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas</a></div>
</div>
</div>
</body></html>