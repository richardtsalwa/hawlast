<?php
require '../hawlastke.php';
require '../config.php';
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
//$sql = "SELECT DISTINCT email, phone FROM affiliates"; 
$sql = "SELECT affid, fname, email, phone FROM affiliates";  
$mg=$pdo->prepare($sql);
$mg->execute(); 
while($myrow = $mg->fetch()) {
$affid = $myrow['affid'];
$email = $myrow['email'];
$phone = $myrow['phone'];
$fname = $myrow['fname'];
$subject = "Best wishes and Airtel + Telkom Airtime offer";

$date = date("Y-m-d");

//Message moved to the messages file
require (dirname('_FILE_').'/includemessages.php');

$tot =  "Date: $date Email:$email Phone: $phone Name:$fname <br>";

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName, $subject, $messages, $attachments);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");
$fp = fopen("Affiatesend.txt", "a") or die("Unable to open file!");
fwrite($fp, $tot);
fclose($fp);

} else { 
echo ("<p>Message delivery failed...$email</p>"); 
}

} while ($myrow = $mg->fetch());


?>
</div>
</body></html>