<?php
ob_start();
require '../config.php';
require '../hawlastke.php';
require_once('../AfricasTalkingGateway.php');

// Specify your login credentials
$username = "hawlast";
$apiKey      = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = "+254720401869";
$from ="HAWLAST";
// sms message here
//$message = $_SESSION['phone'] . "  " . $_SESSION['ddomain'];
$message = "Check paypal payment...renewal ";

// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message, $from);
// SMS APP ENDS HERE.

//session_unset();
//session_destroy();
  
/*** redirect ***/
 header("Location: thankyou.html");

?>