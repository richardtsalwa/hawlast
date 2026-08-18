<?php
require '../hawlastke.php';
require '../config.php';
ob_start();

//Delete the cookie after purchase in the mpesa.php and paypal.php 
if (isset($_COOKIE['affiliate'])) { 
$product = $_SESSION['ddomain'];
$affid = $_COOKIE['affiliate'];
$price = $_SESSION['totalcost'];
$date = date("Y-m-d");

$one="1";
$sql4 = "INSERT INTO affiliates_sales (affid, product, date, price, verify) VALUES (?,?,?,?,?)";
$manu=$pdo->prepare($sql4);
$manu->execute([$affid,$product,$date,$price,$one]);
$hero = $manu->rowCount();
if ($hero)
{
$date_of_expiry = time() - 60 ;
//setcookie( "affiliate", $_COOKIE['affiliate'], $date_of_expiry, "/", "hawlast.com" );
setcookie("affiliate", "", time() - 3600);

} else { echo "Error recording affiliate sale."; 

}
}

// SEND ME THE SMS 
require_once('../AfricasTalkingGateway.php');

// Specify your login credentials
$username = "hawlast";
$apiKey      = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = "+254720401869";
$from ="HAWLAST";
// sms message here
$message = "PayPal payments for" . $_SESSION['phone']." ".$_SESSION['ddomain'];

// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message, $from);
// SMS APP ENDS HERE.

session_unset();
session_destroy();
  
/*** redirect ***/
 header("Location: thankyou.html");

?>