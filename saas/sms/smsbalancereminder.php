<?php
//I could not us the config.php file in login so session and date
//have to be initialized and defined here
session_start();
date_default_timezone_set("Africa/Nairobi");
require '../database.php';
require '../headers.php';
require_once dirname(__DIR__, 2) . '/hawlastke.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title> SMS /AIRTIME reminder </title>
<link rel="stylesheet" type="text/css" href="style.css" /><meta name="robots" content="noindex,nofollow" />

</head>
<body>
<div id=wrap><div class=center_content><div class=left_content>
<?php
$user_status = "1";
$bal = "0";
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query = $pdo->prepare("SELECT fname, phone, phonea, email, user_status, bal, airtime_user.airtimeid  
			 FROM airtime_user, sms_users
			 WHERE user_status = ? AND bal < ? AND airtime_user.airtimeid = sms_users.airtimeid");
$query->execute(array($user_status,$bal));

if( $query->rowCount() > 0 ) { 
while($row = $query->fetch()){

$firstname = $row['fname'];
$email = $row['email'];
$subject = "SMS Balance less than zero";
$bal = abs($row['bal']);
$phone = $row['phone'];
$id = $row['airtimeid'];
$account = "AIR" .$id;

//header moved to the headers file
//require (dirname('_FILE_').'/headers.php');

$duetodaymessage = "Hello $firstname,

Thank you for signing up for the HAWLAST airtime/SMS API platform. 

Your SMS/AIRTIME  balance is overdue by $bal

1. Go to M-PESA on your phone
2. Select Payment Services
3. Select PAY BILL
4. Enter Business number 822490
5. Enter Account number $account
6. Enter the Amount  
7. Enter your M-PESA PIN and Send 

Let me hope that all is well.

Regards,
Richard 
support@hawlast.com 
Hawlast Ventures
0720401869 / 0735187782
http://www.hawlast.com
";

$headers = 'From: support@hawlast.com' . "\r\n" .
    'Reply-To: support@hawlast.com' . "\r\n" .
    'Cc: support@hawlast.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

/* Send the message using mail() function */
if (mail($email, $subject, $duetodaymessage, $headers)) {

// SEND ME THE SMS 
require_once dirname(__DIR__, 2) . '/libraries.php';

// Specify your login credentials

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)

$recipients = $phone;
$from = "HAWLAST";
// sms message here
$message = "Your airtime debt is $bal. Top up via Lipa na MPESA, Pay Bill 822490 Account no $account www.hawlast.com/saas.";

// Create a new instance of our awesome gateway class
$AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);

// Thats it, hit send and we'll take care of the rest
$response = $AT->sms()->send(['to' => $recipients, 'message' => $message, 'from' => $from]);
// SMS APP ENDS HERE.
echo ("<p>Email delivered..</p>");

} else { 
echo ("<p>Email delivery failed...</p>");
}

} 
}  else {
echo "you have nothing here";
}

Database::disconnect();
die();
?>
</div>
</body></html>