<?php
 /*** start the session ***/
    session_start();
    if(isset($_SESSION['adaccess_level']))
        {
                $log_link = 'logout.php';
                $log_link_name = 'Log Out';
                $reg_link = '#';
                $reg_name = $_SESSION['valid_user'];
        }
        else
        {
                $log_link = 'login.php';
                $log_link_name = 'Log In';
                $reg_link = 'register.php';
                $reg_name = 'Register';
	      $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();
require '../config.php';
require '../hawlastke.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>New client welcome info</title>
<meta name=robots content=noindex,nofollow />
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<link rel="stylesheet" type="text/css" href="images/calendar.css" media="all">
<script type="text/javascript" src="images/calendar.js"></script>
</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="index.php">Home</a>  
<a href="listed.php">Listed</a>  
<a href="user.php?pwd=1">Account</a>  
<a href="<?php echo $log_link; ?>"<?php $current = '/login.php' || '/register.php'; page($current); ?>><?php echo $log_link_name; ?></a>     
<div id="form_container">
<?php
if (isset($_POST['submit'])) {

        if ($_POST['firstname'] != "") {
            $firstname = $_POST['firstname'];
            $firstname = ucfirst(strtolower($firstname));
            if ($_POST['firstname'] == "") {
                $errors = 'Please enter a valid first name.<br/><br/>';
            }
        } else {
            $errors .= 'Please enter your first name.<br/>';
        }

        if ($_POST['lastname'] != "") {
            $lastname = $_POST['lastname'];
            $lastname = ucfirst(strtolower($lastname));
            if ($_POST['lastname'] == "") {
                $errors .= 'Please enter a valid last name.<br/><br/>';
            }
        } else {
            $errors .= 'Please enter your last name.<br/>';
        }

        if ($_POST['service'] != "") {
            $service = protect($_POST['service']);
            if ($_POST['service'] == "") {
                $errors .= 'Please enter a valid service.<br/><br/>';
            }
        } else {
            $errors .= 'Please enter the service.<br/>';
        }
 
        if ($_POST['cost'] != "") {
            $cost = intval($_POST['cost']);
            if (!intval($cost)) {
                $errors .= "$cost is <strong>NOT</strong> valid number.<br/><br/>";
            }
        } else {
            $errors .= 'Please enter cost as a number.<br/>';
        }

        if ($_POST['url'] != "") {
        $url = protect($_POST['url']); 
        $url = GetDomain($url);
        $finalurl = "http://www.".$url;
        if (!validate_url($finalurl)) 
          {$errors .= "$finalurl is <strong>NOT</strong> a valid DOMAIN.<br/><br/>";}
        $finalurl1 = "www.".$url;
        $finalurl2 = "http://".$url;
	  $sql = "SELECT url FROM `renewals` WHERE `url`='".$finalurl."' OR `url`='".$finalurl1."' OR `url`='".$finalurl2."'";
	  $res = mysqli_query($db, $sql) or die(mysqli_error());
	  if(mysqli_num_rows($res) > 0){
	  $errors .= "$finalurl is already in the database! <br/>";
	  	}
       } else {
            $errors .= 'Please enter the client website.<br/>';
        }
         

         //sign up date
        if ($_POST['date1'] != "") {
            $signup_date = protect($_POST['date1']);
        } else {
            $errors .= 'Please enter your sign up date.<br/>';
        }

        if ($_POST['email'] != "") {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors .= "$email is <strong>NOT</strong> a valid email address.<br/><br/>";
            }
        } else {
            $errors .= 'Please enter your email address.<br/>';
        }

        if ($_POST['emailalt'] != "") {
            $emailalt = filter_var($_POST['emailalt'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($emailalt, FILTER_VALIDATE_EMAIL)) {
                $errors .= "$emailalt is <strong>NOT</strong> a valid email address.<br/><br/>";
            }
        } else { 
            $emailalt = "";
        }

       if ($_POST['phone'] != "") {
            $phone = protect($_POST['phone']);
            } else {
            $errors .= 'Please enter a phone number.<br/>';
        }

        if (!$errors) {

	// process form
	$sql = "INSERT INTO renewals VALUES (NULL,'$firstname','$lastname','$service','$cost','$finalurl','$signup_date','$email','$emailalt','$phone')";
	$result = mysqli_query($db, $sql) or die ("Error in registration process.");

	//If successful show us the updated results
	header("Location: listed.php");
	exit;
      } else { require 'includes/formerror.txt'; }

    } else { 
 }

?>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>