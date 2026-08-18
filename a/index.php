<?php
ob_start();
require '../config.php';
require '../hawlastke.php';
if(isset($_POST['TransAmount'])) {
//CHECK FROM MPESA API TABLE 
$BillRefNumber = trim($_POST['BillRefNumber']);

//$TransID = trim($_POST['TransID']);
//stop hassling customers,0,o can be a challenge
$TransID = "XXXXXXXXXX";

$amount = trim($_POST['TransAmount']);

$TransAmount = floatval($amount);

$renewalId = protect($_POST['id']);

$result = "";

$stmt = $pdo->prepare("SELECT * from mpesaapi WHERE TransAmount=? and BillRefNumber =? and done=1");
$stmt->execute([$TransAmount,$BillRefNumber]);
$rows = $stmt->fetch();
$id = $rows["Auto"] ?? '0';
if (!empty($id)) { 
    
$result = "Success";

// Update to show that this transaction is checked
$query="UPDATE mpesaapi SET done = 2 WHERE Auto =?";
$sth = $pdo->prepare($query);
$sth->execute([$id]);

$email="support@hawlast.com"; 
$subject ="Check MPESA Payment";
$headers = 'From: Hawlast <support@hawlast.com>' . "\r\n";

$message ="$BillRefNumber $TransID $amount $TransAmount";

/*** send the email ***/
//UPDATE TO USE PHPMAILER 
if(!mail($email, $subject, $message, $headers))
{
$mailerror =" Not able to send verification email";
} 

} else {
$result = "Failed";
}
//end of check from MPESA API TABLE 

if($result === 'Success') {
 //$curlerror = $_error($ch);
session_unset();

session_destroy();

header("Location: ./thankyou.php?id=success&acc=$id");

} else {

$_SESSION['mpesaerror']= $result;

header("Location: index.php?a=$renewalId");
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="Content-Language" content="en"><title>Payments Page</title><meta name=Description content="Make Payments" />
<link rel=stylesheet type=text/css href=../style.css />
<link rel=stylesheet type=text/css href=../responsive.css />
<link rel="shortcut icon" href=../images/favicon.ico type=image/x-icon />
<?php require("includes/header.txt"); ?>
</div>

<div class=center_content><div class=left_content>
<h2>Payments Options</h2>
<?php
if(isset($_GET['a'])) {	
$a = protect($_GET['a']);
$a = tupu($_GET['a']);
$a = marks($_GET['a']);
$sql ="SELECT id, firstname, lastname, service, cost, url, renewal_date, email, emailalt, phone FROM renewals WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$a]);
$uko = $stmt->rowCount();
$ottah = $stmt->fetch(PDO::FETCH_ASSOC);
if($uko == 1) {

//Let us find out if there is any prepaid
//status 1 means this have not been utilised already
$sql ="SELECT SUM(prepaid) as prepaid FROM renewalsprepaid WHERE renewalsid = ? AND status=1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$a]);
$joe = $stmt->fetch(PDO::FETCH_ASSOC);

// show data
$signup_date = $ottah['renewal_date'];
//Let us find out the date values
$day= day("$signup_date"); //day value
$month= month("$signup_date"); // Month value
$year= year("$signup_date"); // Year value
//Get next year, same day
$newsignupdate = date('Y-m-d', mktime(0,0,0,$month,($day),($year+1))); 
?>
<?php echo $ottah["firstname"]; ?> <?php echo $ottah["lastname"]; ?><br />  
Service: <?php echo $ottah["service"]; ?><br />
Domain name <?php echo $ottah["url"]; ?><br />
<?php
$prepaid = $joe["prepaid"] ?? '0';
$due = $ottah["cost"] - $prepaid;
if ($due > 0) { ?>
<b>Amount Due KES <?php echo $due;  ?></b><br />
<b>Due by <?php echo $newsignupdate; ?> </b>(YYYY MM DD)<br />
<?php
} else {
?> 
<b>Overpaid by KES <?php echo -1*$due;  ?></b>
<?php
} 
?>
<br />
<br />
<?php
//session_unset();
//session_destroy();
if(isset($_SESSION['mpesaerror'])){
$onyesha = $_SESSION['mpesaerror']; 
echo "<span STYLE=\"color:red\"><b>$onyesha.</b></span><br>";

unset($_SESSION["mpesaerror"]);
}
?>
<b>LIPA NA M-PESA</b><br>
1. Select <b>Lipa na M-PESA</b><br /> 
2. Select <b>Pay Bill</b><br /> 
3. Enter business number <b>822490</b><br /> 
4. <b>Account <?php echo "HV"; echo $ottah['id']; ?></b> <br /> 
5. Enter the Amount <b>Ksh. <?php echo $due; ?></b> <br /> 
6. Enter your M-PESA PIN and Send <br /> 
7. You will receive an SMS from M-PESA with a confirmation Code<br />
<br />
<form action="index.php" method="post">
<input type="hidden" name="TransAmount" value="<?php echo $due; ?>">
<input type="hidden" name="BillRefNumber" value="<?php echo "HV"; echo $ottah['id']; ?>">
<input type="hidden" name="id" value="<?php echo $ottah['id']; ?>">
<input type="submit" name="submit" value="Refresh">
</form>
<br>
<br>
<br>
<!--
<b>PAY VIA AIRTEL MONEY</b>
1. Go to Airtel Money  on your phone and send money to 0735187782<br />
2. Enter the Airtel Money reference number below: 
<br />
<form action="mpesa.php" method="post">
<input type="text" name="reference" value="" maxlength="15" required>
<input type="hidden" name="domain" value="<?php echo $ottah['url']; ?>">
<input type="hidden" name="amount" value="<?php echo $due; ?>">
<input type="hidden" name="id" value="<?php echo $ottah['id']; ?>">
<input type="hidden" name="email" value="<?php echo $ottah['email']; ?>">
<input type="hidden" name="phone" value="<?php echo $ottah['phone']; ?>">
<input type="submit" name="submit" value="Submit">
</form>
-->
<br />
<br />

<img src="images/pay-hv.jpg" /><br />
<b>Once the cheque is ready, let us know.</b>

<br/> 
<a href="https://www.hawlast.com/a/invoice.php?=<?php echo $ottah["id"]; ?>">Download invoice</a><br />

<?php 
} else {

echo "<h3>This id does not exist.</h3>";

} 
} 
?>
</div>
<div class=right_content>
<div class=about>
<br>
<br>
<?php
if(isset($ottah)) {
?>
<b>YOU CAN PAY VIA PAYPAL</b><br>
<b>Click the buy button to pay KES <?php echo $due; ?></b><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="SRCCCZLH9ZBJW">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="<?php echo $ottah['url']; ?>">
<input type="hidden" name="amount" value="<?php $give = $due/$dollar; echo ceil ($give * 1.034); ?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="return" value="https://www.hawlast.com/a/paypal.php">
<input type="hidden" name="cancel_return" value="https://www.hawlast.com/a/?a=<?php echo $ottah['id']; ?>">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
<input type="image" src="images/paypal.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<br>
<?php
} ?>
</div>
<div class=about>
<br /><br />
</div>
<br /><br />
</div>
<div class=clear></div>
</div>
<div class=footer>
<div class=left_footer><a href=https://www.hawlast.com/ title="web hosting Kenya">HAWLAST.COM</a>&copy; <?php echo copyrightYear(2010); ?></div>
<div class=right_footer><a href=../>home</a> <a href=../terms-of-service.html>terms of services</a> <a href=../privacy-policy.html>privacy policy</a> <a href=../contactus.html>contact us</a> <a href=../saas/>Buy Airtime</a>
</div>
</div>
</div>
</body>
</html>