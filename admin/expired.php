<?php
 /*** start the session ***/
    session_start();
    if(isset($_SESSION['adaccess_level']))
        {
                $log_link = 'logout.php';
                $log_link_name = 'Log Out';
                $reg_link = '#';
                $reg_name = $_SESSION['valid_user'];
        }   else {
                $log_link = 'login.php';
                $log_link_name = 'Log In';
                $reg_link = 'register.php';
                $reg_name = 'Register';
	      $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();
require '../config.php';
require '../hawlastke.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>Resend reminder</title>                
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
</head>

<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a> 
<a href="listed.php">Listed</a> 
<a href="user.php?pwd=1">Account</a> 
<a href="addnew.php">Add new</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>   
<div id="form_list">
<?php 
if(isset($_POST['remind'])) {
	$expiredate = protect($_POST['expiredate']);
	
	$email = protect($_POST['email']);
	$website = protect($_POST['url']);
	
	//check if this a valid email address
    $emailalt = protect($_POST['emailalt']);
    
	$firstname = protect($_POST['firstname']);
	$phone= protect($_POST['phone']);
	
	$expiredate = tupu($_POST['expiredate']);
	$email = tupu($_POST['email']);
	$website = tupu($_POST['url']);
        $due = tupu($_POST['cost']);
        $prepaid = $_POST['prepaid'];
        $cost = $due - $prepaid; 
	$firstname = tupu($_POST['firstname']);

	
$id = $_POST['id'];
$payurl = "https://www.hawlast.com/a?a=".$id;

$subject = "Hosting Account expired"; 

$message = "Hello $firstname,

Thank you for your continued business support. 

$website expired on $expiredate.

You need to pay Ksh. $cost urgently for us to restore your services.

Payment details at $payurl

Please note that you may incur extra costs to renew a domain / restore files after expiry.

Regards,
Richard
support@hawlast.com
https://www.hawlast.com
";

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName,$subject,$message, $attachments,$emailalt);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");
} else { 
echo ("<p>Message delivery failed...$email</p>"); 
}

// SEND ME THE SMS 
require_once dirname(__DIR__) . '/libraries.php';

// Specify your login credentials

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$code ="+254";
$recipients = $code."".$phone;

// sms message here
$message = "Hello $firstname. $website expired.Pay Ksh. $cost to MPESA Paybill 822490 Account: HV$id  HAWLAST 0720401869";

$from ="HAWLAST";

// Create a new instance of our awesome gateway class
$AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);

// Thats it, hit send and we'll take care of the rest
$response = $AT->sms()->send(['to' => $recipients, 'message' => $message, 'from' => $from]);
// SMS APP ENDS HERE.

} 
?>
<div id="footer">
By <a href="https://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>