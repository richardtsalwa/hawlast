<?php
ob_start();
require '../config.php';
require '../hawlastke.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>Due in 5 days</title>
<link rel="stylesheet" type="text/css" href="style.css" /><meta name="robots" content="noindex,nofollow" /></head>
<body>
<div id=wrap><div class=center_content><div class=left_content>
<?php
//Let us find out the date values
$month= date("m"); // Month value
$day= date("d"); //today's date
$year= date("Y"); // Year value

//5 Days before one year ago
$date = date('Y-m-d', mktime(0,0,0,$month,($day+6),($year-1))); 

//Anniversary
$due = date('Y-m-d', mktime(0,0,0,$month,($day+6),($year))); 

$sql = "SELECT id,firstname, service,cost,url,renewal_date, email, emailalt, phone FROM renewals WHERE renewal_date = ?";  
$stmt = $pdo->prepare($sql);
$stmt->execute([$date]); 
$jawabu = $stmt->fetchAll(\PDO::FETCH_ASSOC);

if (!empty($jawabu)) {
foreach($jawabu as $myrow) {

$id = $myrow['id'];
$sql = "SELECT prepaid FROM renewalsprepaid WHERE renewalsid = ? AND status =1 LIMIT 1";    
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]); 
$sema = $stmt->fetchColumn();

// If $sema has a value, use it; otherwise, default to "0"
$prepaid = ($sema !== false) ? $sema : "0";

$service = $myrow['service'];
$totalcost = $myrow['cost'];
$firstname = $myrow['firstname'];
$email = $myrow['email'];
$emailalt = $myrow['emailalt'];
$row = $myrow['url'];
$subject = "Renew in 5 days";
$phone = $myrow['phone'];
$service = $myrow['service'];
$cost = $totalcost - $prepaid;
$url = $myrow['url'];
$payurl = "https://www.hawlast.com/a?a=".$id;

//Message moved to the messages file
require (dirname('_FILE_').'/messages.php');

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName,$subject,$dueinfivemessage, $attachments,$emailalt);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");
} else { 
echo ("<p>Message delivery failed...$email</p>"); 
}

// SEND ME THE SMS 
require_once dirname(__DIR__) . '/libraries.php';

// Specify your login credentials
$username = "hawlast";
$apiKey = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$code ="+254";
$recipients = $code."".$phone;

// sms message here
$message = "Hello $firstname. $url expires in 5 days. Pay Ksh. $cost via MPESA PAYBILL 822490 Account: HV$id Support 0720401869.";
$from ="HAWLAST";

// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message,$from);
// SMS APP ENDS HERE.

}
}
?>
</div>
</body></html>