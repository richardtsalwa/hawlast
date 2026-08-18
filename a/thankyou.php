<?php
require '../config.php';
require '../hawlastke.php';
require_once('../AfricasTalkingGateway.php');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<link rel=icon href=images/favicon.ico type=image/x-icon /><title>Thank you - hawlast.com</title>
<meta name=robots content=noindex,follow />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css">
<link rel=stylesheet type=text/css href=../style.css />
<link rel=stylesheet type=text/css href=../responsive.css />
<?php
require("includes/header.txt");
?>
</div>
<div class=center_content>
<div class=left_content><h1>Thank you</h1><div class=feat_prod_box_details>
<?php

if(isset($_GET['d']) && ($_GET['d']=='bye')) {
echo "<p style=\"font-size: 15px;\">Your payment is successful. We have updated your account.<br /></p>";
}

if (isset($_GET['id']) && isset($_GET['acc'])) {	
$acc = protect($_GET['acc']);
$acc = tupu($_GET['acc']);
$acc = marks($_GET['acc']);
$BillRefNumber = "HV$acc";

//Get from prepaid the amount paid so far
$status = "1";
$sql ="SELECT id,renewalsid, SUM(prepaid) as prepaid FROM renewalsprepaid WHERE renewalsid = ? and status=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$acc,$status]);
$numa = $stmt->fetch(PDO::FETCH_ASSOC);
if ($stmt->rowCount() > 0) {
$prepaid = $numa['prepaid'];
} else {
$prepaid = "0";
}

//Look for the latest transaction with Account ID
$sql ="SELECT id, renewals.firstname, renewals.lastname, service, cost, url, signup_date,email, phone,TransID,TransTime,TransAmount,MSISDN,BillRefNumber FROM renewals, mpesaapi WHERE id = ? and BillRefNumber=? ORDER BY TransTime DESC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$acc,$BillRefNumber]);
$ottah = $stmt->fetch(PDO::FETCH_ASSOC);
if ($stmt->rowCount() > 0) {
$id = $ottah["id"]; 
$service = $ottah["service"]; 
$email = $ottah["email"]; 
$TransID = $ottah["TransID"];
$amount = $ottah["TransAmount"];
$cost = $ottah["cost"]; 
$url = $ottah["url"]; 
$phone = $ottah["phone"]; 
$phonex = $ottah["MSISDN"];
$phone = add254($phone);
//thisnumber is already in format 254777222
$phonex = addplusona254($phonex);
if(strcasecmp($phone, $phonex) !== "0") {
$phone = "$phone,$phonex";
} 

$balancedue = $cost - $prepaid -$amount;

//LET US UPDATE THE PREPAID TABLE
$status = "2";
if ($balancedue < 1) {
$sql = "UPDATE renewalsprepaid SET status=? WHERE renewalsid=?";
$stmt= $pdo->prepare($sql);
$stmt->execute([$status,$id]);

// Get the renewal date from the database
$sql = "SELECT renewal_date FROM renewals WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$renewal_date = $stmt->fetchColumn();

// Make sure a renewal date is returned
if ($renewal_date) {
    // Create a DateTime object from the renewal date
    $date = new DateTime($renewal_date);
    
    // Extract day, month, and year
    $day = $date->format('d');   // Day of the month
    $month = $date->format('m'); // Month (01 to 12)
    $year = $date->format('Y');  // Year

    // Get next year's renewal date (same day and month, next year)
    $nextRenewal = new DateTime();
    $nextRenewal->setDate($year + 1, $month, $day);
    $newRenewalDate = $nextRenewal->format('Y-m-d'); 

    // Output the new renewal dates (for debugging purposes)
   // echo "Next renewal date: " . $newRenewalDate . "<br>";

    // Now update the renewal date in the database with the new date
    $updateSql = "UPDATE renewals SET renewal_date = ? WHERE id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$newRenewalDate, $id]);

} 

} 

//SEND CLIENT SMS to CONFIRM PAYMENTS

//cURL result to space
$result ="";
//if email fails
$mailerror ="";
//log cURL errors 
$curlerror ="";

//My user id at SAAS
$user_id = "1";

$data = "This is to confirm payment of KES $amount for $url. Balance due is now KES $balancedue.";

//Balance as the sms script expects me to have >1 balance
$bal ="20";

//phone is declared up there 
$mpesaapi ="hostingrenewal";

$url = 'https://www.hawlast.com/saas/sms/sendsms.php';

//set POST variables to send to cURL
$fields = array(
	'user_id' => urlencode($user_id),
	'bal' => urlencode($bal),
	'phone' => urlencode($phone),
	'mpesaapi' => urlencode($mpesaapi),
	'message' => urlencode($data)
);

//url-ify the data for the POST
$fields_string ="";
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_FAILONERROR, true); //Fail on error
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Get a response from server 

//execute post
$result = curl_exec($ch);

if (curl_errno($ch)) {
    $curlerror= curl_error($ch);
}

//close connection
curl_close($ch);

echo $curlerror;

$message = "$data"; 
$subject ="MPESA Payment";
$headers = 'From: Hawlast Hosting <support@hawlast.com>' . "\r\n";

/*** send the email ***/
if(!mail($email, $subject, $message, $headers))
{
echo "Not able to send payment confirmation email";
} 


    //End of if we have the Account ID

session_unset();

session_destroy();
?>
<p style="font-size: 15px;">Your payment is successful. We will update your account in a short while.<br /></p>
<?php

header("Location: ./thankyou.php?d=bye");
//Redirect so that this is not recorded twice...


} else {
echo "The system can not verify your payment. Our team will do this manually for you.";
}
}
 

?>

<br />
<p style="font-size: 15px;">For support call 0720401869.</p>

</div><div class=clear></div></div>
<div class=right_content>
<div class=about>
<br />
<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="www.hawlast.com/" show_faces="false" width="250" action="recommend"></fb:like></div>
<div class=about>
<br />
<br />
<br />
Free Joomla or WordPress set up <br />
Free email autoresponder <br />
Free Microsoft Outlook set up<br />
Read emails on your mobile phone<br />
Free email marketing set up (500 Subscribers)<br />
Your account will be up <b>10 minutes after payments</b> <br />
24/7 support <br />
Pay via MPESA /Cheque /Direct deposit<br />
<br /><br />
<br /><br />
</div>
<br /><br />
<div class=clear></div>
</div>
<div class=footer>
<div class=left_footer><a href=http://www.hawlast.com/ title="web hosting Kenya">HAWLAST.COM</a>&copy; <?php echo copyrightYear(2010); ?></div>
<div class=right_footer><a href=../>home</a> <a href=../terms-of-service.html>terms of services</a> <a href=../privacy-policy.html>privacy policy</a> <a href=../contactus.html>contact us</a> <a href=../saas/>Buy Airtime</a>
</div>
</div>
</div>
</body>
</html>