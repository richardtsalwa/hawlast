<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

$BillRefNumber = trim($_POST['BillRefNumber']);

$TransID = trim($_POST['TransID']);
$TransID = strtoupper($TransID);


$amount = trim($_POST['TransAmount']);

$TransAmount = floatval($amount);

$stmt = $pdo->prepare("SELECT * from mpesaapi WHERE TransID = ? and TransAmount=? and BillRefNumber =? and done=0");
$stmt->execute([$TransID,$TransAmount,$BillRefNumber]);
$rows = $stmt->fetch();
if ($rows) { 
    
//Need this to update the done status from zero to 1
$id = $rows['Auto'];

echo "Success";

// Update to show that this transaction is checked
$query="UPDATE mpesaapi SET done = 1 WHERE Auto =?";
$sth = $pdo->prepare($query);
$sth->execute([$id]);

$email="support@hawlast.com"; 
$subject ="Check MPESA Payment";
$headers = 'From: Hawlast <support@hawlast.com>' . "\r\n";

$message ="$BillRefNumber $TransID $amount $TransAmount";

/*** send the email ***/
if(!mail($email, $subject, $message, $headers))
{
$mailerror =" Not able to send verification email";
} 

} else {
echo "Failed";
}

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("mpesa-check-log.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);
}
?>