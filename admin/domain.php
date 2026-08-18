<?php
ob_start();
require '../config.php';
require '../hawlastke.php';
require '../saas/database.php';

try {
    $pdo = Database::connect();
    // 1. Get Max Connections
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'max_connections'");
    $max = $stmt->fetch()['Value'];

    // 2. Get Current Active Connections
    $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
    $current = $stmt->fetch()['Value'];

    Database::disconnect();

    // 3. Calculate Percentage
    $usagePercent = ($current / $max) * 100;
    $threshold = 80; // Alert me at 80% usage

    if ($usagePercent >= $threshold) {
        $msg = "CRITICAL: MySQL usage at $usagePercent% ($current/$max connections). Check Hamilton server immediately.";
        $adminPhone = "+254720401869"; // Your number
        
        TumaSMS($msg, $adminPhone);
        error_log("Alert sent: Database connections high.");
    }

} catch (Exception $e) {
    error_log("Monitor failed: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html><head>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta name="robots" content="noindex,nofollow" />
<title>Due today</title>
</head>
<body>
<div id=wrap><div class=center_content><div class=left_content>
<?php
//Let us find out the date values
$month= date("m"); // Month value
$day= date("d"); //today's date
$year= date("Y"); // Year value

//0 Days before one year ago
$due = date('Y-m-d', mktime(0,0,0,$month,($day+1),($year-1))); 

$sql = "SELECT id,firstname,service,cost,url,renewal_date,email,emailalt,phone FROM renewals WHERE renewal_date = ?";  
$stmt = $pdo->prepare($sql);
$stmt->execute([$due]); 
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
$subject = "Renew today";
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

//check if mail or alt email is valid before using
//???

$tuma = sendEmail($email,$companyName,$subject,$duetodaymessage, $attachments,$emailalt);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");

} else { 
echo ("<p>Message delivery failed...$email</p>"); 

}

// SEND THE CLIENT AN SMS 
//require_once('../hawlastsms.php');
require_once('../AfricasTalkingGateway.php');

// Specify your login credentials
$username = "hawlast";
$apiKey   = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$code ="+254";
$recipients = $code."".$phone;

// sms message here
$message = "Hello $firstname. $url expires today. Send Ksh. $cost via paybill  822490 Account HV$id. Support: 0720401869.";
$from ="HAWLAST";

// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

// Thats it, hit send and we'll take care of the rest
$results  = $gateway->sendMessage($recipients, $message,$from);
// SMS APP ENDS HERE.

}

}else {echo $due; }

?>
</div>
</body></html>