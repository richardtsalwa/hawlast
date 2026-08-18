<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

$phone = trim($_POST['phone']);

$TransID = trim($_POST['TransID']);

$amount = trim($_POST['TransAmount']);

$TransAmount = floatval($amount);

$stmt = $pdo->prepare("SELECT * from mpesaapi WHERE TransID = ? and TransAmount=? and MSISDN =? and done=1");
$stmt->execute([$TransID,$TransAmount,$phone]);
$rows = $stmt->fetch();
$id = $rows['Auto'];
if (!empty($id)) { 
echo "Success";

$email="support@hawlast.com"; 
$subject ="Plugin MPESA Payment";
$headers = 'From: Hawlast <support@hawlast.com>' . "\r\n";

$message ="$BillRefNumber $TransID $amount $TransAmount";

/*** send the email ***/
if(!mail($email, $subject, $message, $headers))
{
$mailerror =" Not able to send verification email";
} 

// Update to show that this transaction is checked
$query="UPDATE mpesaapi SET done = 2 WHERE Auto =?";
$sth = $pdo->prepare($query);
$sth->execute([$id]);

} else {
echo "Failed";
}

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("mpesa-check-log.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);
}
?>