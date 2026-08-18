<?php
ob_start();
require '../config.php';
require '../hawlastke.php';
?>
<!DOCTYPE html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>Renew in 60 days</title>                
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
</head>
<body>

<div id=wrap><div class=center_content><div class=left_content>
<?php
//Let us find out the date values
$month= date("m"); // Month value
$day= date("d"); //today's date
$year= date("Y"); // Year value

//60 Days before one year ago
$date = date('Y-m-d', mktime(0,0,0,$month,($day+61),($year-1))); 

//Due on = databased date + 1 year 
$due = date('Y-m-d', mktime(0,0,0,$month,($day+61),$year)); 

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
$subject = "Renew in 60 days";
$phone = $myrow['phone'];
$service = $myrow['service'];
$cost = $totalcost - $prepaid;
$url = $myrow['url'];
$payurl = "https://www.hawlast.com/a/?a=".$id;

//Message moved to the messages file
require (dirname('_FILE_').'/messages.php');

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email,$companyName,$subject,$dueinsixzeromessage, $attachments,$emailalt);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");
} else { 
echo ("<p>Message delivery failed...$email</p>"); 
}
}
}
?>
</div>
</body></html>