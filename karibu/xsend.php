<?php
include '../hawlastke.php';
include '../config.php';

require_once('../AfricasTalkingGateway.php');

ob_start();
?>
<!DOCTYPE html><head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8" />
<link rel=stylesheet type=text/css href=style.css />
<meta name=robots content=noindex,nofollow /></head>
<body>
<div id=wrap><div class=center_content>
<div class=left_content>
<?php

$dates = "2022-12-30";

$sql = 'SELECT id,firstname,email,phone,url,renewal_date,cost FROM renewals WHERE cost > 1499 and renewal_date > ? GROUP BY email ASC';
$mg=$pdo->prepare($sql);
$mg->execute(['$dates']); 
while($myrow = $mg->fetch()) {
$firstname = $myrow['firstname'];
$email = $myrow['email'];
$phone = $myrow['phone'];
$phone = add254($phone);
$url = $myrow['url'];
$id = $myrow['id'];
$date = date("Y-m-d");

$tot =  "Date: $date Email:$email Phone: $phone Name:$firstname Website: $url<br>";

$subject = "Phishing Alert: Protect Yourself";


//Message moved to the messages file
require (dirname('_FILE_').'/xincludemessages.php');

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName, $subject, $messages, $attachments);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");
$fp = fopen("xsend.txt", "a") or die("Unable to open file!");
fwrite($fp, $tot);
fclose($fp);

} else { 
echo ("<p>Message delivery failed...$email</p>"); 

}


/*
// Specify your login credentials
$username = "hawlast";
$apiKey      = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = $phone;
$from ="HAWLAST";
// sms message here
$message = "Hi $firstname, You can now pay for your hosting in instalmments. Check $email for more cool ideas.";

// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message, $from);
// SMS APP ENDS HERE.


*/

} while ($myrow = $mg->fetch());

?>
</div>
</body></html>