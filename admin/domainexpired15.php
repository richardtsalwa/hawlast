<?php
require (dirname('_FILE_').'/config.php');
require (dirname('_FILE_').'/functions.php');
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd><html xmlns=http://www.w3.org/1999/xhtml lang=en xml:lang=en><head><meta http-equiv=Content-Type content="text/html; charset=utf-8" /><link rel=stylesheet type=text/css href=style.css /><meta name=robots content=noindex,nofollow /></head><body>
<div id=wrap><div class=center_content><div class=left_content>
<?php
//Let us find out the date values
$month= date("m"); // Month value
$day= date("d"); //today's date
$year= date("Y"); // Year value

//expired 15 days ago
$date = date('Y-m-d', mktime(0,0,0,$month,($day-15),($year-1))); 

//It was due 15 days ago hence
$due = $date; 

$sql = "SELECT id, firstname, service, cost, url, renewal_date, email, emailalt, phone FROM renewals WHERE renewal_date = '$date'";  

$result = mysqli_query($db,$sql) or die ("Error in query: $sql. " .mysqli_error());
while ($myrow = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {

$firstname = $myrow['firstname'];
$email = $myrow['email'];
$emailalt = $myrow['emailalt'];
$when = "expired and will stop working";
$row = $myrow['url'];
$subject = "Account expired";
$phone = $myrow['phone'];
$service = $myrow['service'];
$cost = $myrow['cost'];
$url = $myrow['url'];
$id = $myrow['id'];
$payurl = "http://www.hawlast.com/a?a=".$id;

//header moved to the headers file
require (dirname('_FILE_').'/headers.php');

//Message moved to the messages file
require (dirname('_FILE_').'/messages.php');

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName,$subject,$expiredmessage, $attachments,$emailalt);

if ($tuma == true) {
    
echo ("<p>Delivered..</p>");
// SEND ME THE SMS 
require_once('hawlastsms.php');

// Specify your login credentials
$username = "hawlast";
$apiKey = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$code ="+254";
$recipients = $code."".$phone;

// sms message here
$message = "Hello $firstname. $url expired 15 days ago.Pay Ksh. $cost to MPESA Paybill 822490 Account: HV$id  HAWLAST 0720401869";

$from ="HAWLAST";

// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message,$from);
// SMS APP ENDS HERE.

} else { echo ("<p>Message delivery failed...</p>");}


} mysqli_free_result($result);
echo "<h2>Hi, no other account expired 15 days ago.</h2></a>";

?>
</div>
</body></html>