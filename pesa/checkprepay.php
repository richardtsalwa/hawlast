<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

$BillRefNumber = trim($_POST['BillRefNumber']);

$TransID = trim($_POST['TransID']);

$amount = trim($_POST['TransAmount']);

$TransAmount = floatval($amount);

$stmt = $pdo->prepare("SELECT * from mpesaapi WHERE TransID = ? and TransAmount=? and BillRefNumber =?");
$stmt->execute([$TransID,$TransAmount,$BillRefNumber]);
$rows = $stmt->fetch();
$done = $rows['done'] ?? '0';
$id = $rows['Auto'] ?? '0';

if (!empty($id)) { 

	if ($done == 1) {
	//Update to show that this transaction is checked
	$query="UPDATE mpesaapi SET done = 2 WHERE Auto =?";
	$sth = $pdo->prepare($query);
	$sth->execute([$id]);
	echo "success";
	
	
$subject =  "MPESA API";
$message = "$BillRefNumber $TransID $amount";

// Additional headers
$headers = 'From: Hawlast Ventures <support@hawlast.com>' . "\r\n";
$headers .= "Reply-To: support@hawlast.com\r\n";
$email = "support@hawlast.com";

/* Send the message using mail() function */
if ( mail($email, $subject, $message, $headers) ) {
/* Redirect visitor to thank you page */
}



	} else {
	echo "The payment is already confirmed. Your account will be updated soon.";
	}

} else {

//echo "The MPESA REF or Amount is incorrect.";
echo "Failed";

}


$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("mpesa-check-prepay-log.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);
}
?>