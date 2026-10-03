<?php
ob_start();
require '../config.php';
require '../hawlastke.php';
require_once dirname(__DIR__) . '/libraries.php';

// Specify your login credentials

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = "+254720401869";
$from ="HAWLAST";
// sms message here
//$message = $_SESSION['phone'] . "  " . $_SESSION['ddomain'];
$message = "Check paypal payment...renewal ";

// Create a new instance of our awesome gateway class
$AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);

// Thats it, hit send and we'll take care of the rest
$response = $AT->sms()->send(['to' => $recipients, 'message' => $message, 'from' => $from]);
// SMS APP ENDS HERE.

//session_unset();
//session_destroy();
  
/*** redirect ***/
 header("Location: thankyou.html");

?>