<?php
require '../hawlastke.php';
require '../config.php';
ob_start();

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");

if (isset($_POST['reference'])) {
$reference = $_POST['reference'];

$hosting = $_SESSION['hosting'];
$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$ddomain = $_SESSION['ddomain'];
$email = $_SESSION['email'];
$payments = $_SESSION['payments'] ;
$phone = $_SESSION['phone'];
$totalcost = $_SESSION['totalcost'];

/* Check all form inputs */
$errors = array();
if(!$reference){$errors[] = "Please enter an MPESA reference!";}
if(count($errors) == 0)

{

/* Let's prepare the message for the e-mail */
$message = "MPESA reference: $reference
$hosting 
$fname $lname
$ddomain 
$payments
$email
$totalcost
$phone
";

$subject = $_SESSION['ddomain'];
// Additional headers
$headers = 'From: Hawlast Ventures <support@hawlast.com>' . "\r\n";
$headers .= "Reply-To: support@hawlast.com\r\n";
$email = "support@hawlast.com";

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

/* Send the message using mail() function */
if ( mail($email, $subject, $message, $headers) ) 

{


/* Redirect visitor to thank you page */

// SEND ME THE SMS 
require_once('../AfricasTalkingGateway.php');

//Specify your credentials
$username = "hawlast";
$apiKey = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = "+254720401869";

// sms message here 
$message = $reference
." " . $_SESSION['phone'] . "  " . $_SESSION['ddomain'];
$from = "HAWLAST";
// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

try 
{ 
  // Thats it, hit send and we'll take care of the rest. 
$results  = $gateway->sendMessage($recipients, $message, $from);
  foreach($results as $result) {
    // Note that only the Status "Success" means the message was sent
    echo " Number: " .$result->number;
    echo " Status: " .$result->status;
    echo " MessageId: " .$result->messageId;
    echo " Cost: "   .$result->cost."\n";
  }
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while sending the sms. You will receive a phone call and email soon: ".$e->getMessage();

header('Location: ./thankyou.html');
}

// SMS APP ENDS HERE.


// SEND SMS TO CLIENT  
// Specify the numbers that you want to send to in a comma-separated list
$code ="+254";
$phonenumber = substr($phone, 1);
$recipients = $code."".$phonenumber;
$from = "HAWLAST";
// sms message here
$message = "Thank you for ordering on our website.We are verifying your payments.HAWLAST.COM";
        
// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message, $from);
// SMS APP ENDS HERE.

session_unset();

session_destroy();

header('Location: ./thankyou.html');

} else { echo "Error sending the reference via email"; }

//if there are errors
} else { foreach($errors AS $error){ echo $error. "<br>"; }  }

//Client has not picked a reference
} else { header('Location: ./reserve.php?checkout=1');}
?>
