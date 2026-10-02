<?php
ob_start();
require '../config.php';
require '../hawlastke.php';

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");
//echo "Thank you. We will reconfirm the payments and renew.";
//exit();
if (isset($_POST['reference'])) {
$reference = $_POST['reference'];
$ddomain = $_POST['domain'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];
$id = $_POST['id'];

/* Check all form inputs */
$errors = array();
if(!$reference) {$errors[] = "Please enter an MPESA reference!";}
if(count($errors) == 0) {

/* Let's prepare the message for the e-mail */
$message = "MPESA reference: $reference
$ddomain 
$email
$amount
$phone
";

$subj = "Payment for";
$subject =  $subj . "  " . $ddomain;

// Additional headers
$headers = 'From: Hawlast Ventures <support@hawlast.com>' . "\r\n";
$headers .= "Reply-To: support@hawlast.com\r\n";
$email = "support@hawlast.com";

/* Send the message using mail() function */
if ( mail($email, $subject, $message, $headers) ) {
/* Redirect visitor to thank you page */

// SEND ME THE SMS 
require_once dirname(__DIR__) . '/libraries.php';

//Specify your credentials
$username = "hawlast";
$apiKey = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = "+254720401869";

// sms message here
$message = "MPESA reference: $reference $ddomain $email $amount $phone";
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
  echo "Encountered an error while sending: ".$e->getMessage();
}

// SMS APP ENDS HERE.


// SEND SMS TO CLIENT  
// Specify the numbers that you want to send to in a comma-separated list
$code ="+254";
$phonenumber = substr($phone, 1);
$recipients = $code."".$phonenumber;
$from = "HAWLAST";
// sms message here
$message = "Thank you for paying on our website. We will confirm and renew the account if payment are successful.HAWLAST.COM";
        
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
} else { header('Location: https://www.hawlast.com/a/?a='.$id);}
?>