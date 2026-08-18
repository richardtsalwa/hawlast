<?php
ob_start();
require '../config.php';
require '../hawlastke.php';

if(isset($_POST['TransAmount'])) {
$BillRefNumber = trim($_POST['BillRefNumber']);

$TransID = trim($_POST['TransID']);

$amount = trim($_POST['TransAmount']);

$id = protect($_POST['id']);
$payurl="./prepay.php?a=$id";

$postdata ="BillRefNumber=".$BillRefNumber."&TransID=".$TransID."&TransAmount=".$amount;

$url = 'https://www.hawlast.com/pesa/checkprepay.php';
$ch = curl_init(); 
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_REFERER, $url); 
curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
curl_setopt ($ch, CURLOPT_POST, 1); 
$result = curl_exec ($ch); 

$curlerror ="";

if($result === 'Success') {
 //$curlerror = $_error($ch);
session_unset();

session_destroy();

header("Location: ./thankyou.html?id=success&acc=$id");

} else {

$_SESSION['mpesaerrorx']= $result;

header("Location: $payurl");
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="Content-Language" content="en"><title>Lipa Pole Pole</title><meta name=Description content="Make Payments" />
<link rel=stylesheet type=text/css href=../style.css />
<link rel=stylesheet type=text/css href=../responsive.css />
<link rel="shortcut icon" href=../images/favicon.ico type=image/x-icon />
<?php require("../includes/header.txt"); ?>
</div>
<div class=center_content><div class=left_content>
<h2>Lipa pole pole</h2>
<?php
if(isset($_GET['a'])) {	
$a = protect($_GET['a']);
$a = tupu($_GET['a']);
$a = marks($_GET['a']);
$sql ="SELECT id,firstname, lastname, service, cost, url, signup_date, email, emailalt, phone FROM renewals WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$a]);
$uko = $stmt->rowCount();
$ottah = $stmt->fetch(PDO::FETCH_ASSOC);
if($uko == 1) {
//echo print_r($ottah);
//Let us find out if there is any prepaid
$sql ="SELECT prepaid FROM renewalsprepaid WHERE renewalsid = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$a]);
$joe = $stmt->fetch(PDO::FETCH_ASSOC);

// show data
$signup_date = $ottah['signup_date'];
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
Amount KES. <?php echo $ottah["cost"]; ?><br />
Prepaid KES. <?php echo $joe["prepaid"] ?? 'NIL'; ?><br />
<?php
$prepaid = $joe["prepaid"] ?? '0';
$due = $ottah["cost"] - $prepaid;
if ($due > 0) { ?>
<b>Amount Due KES <?php echo $due;  ?></b><br />
<?php
} else {
?> 
<b>Overpaid by KES <?php echo -1*$due;  ?></b><br />
<?php
} 
?>

Sign up date <?php echo $ottah["signup_date"]; ?><br />
<b>Due by <?php echo $newsignupdate; ?> </b>(YYYY MM DD)<br />
<br />
<br />
<?php
//session_unset();
//session_destroy();
if(isset($_SESSION['mpesaerrorx'])){
$onyesha = $_SESSION['mpesaerrorx']; 
echo "<span STYLE=\"color:red\"><b>$onyesha.</b></span><br>";

unset($_SESSION["mpesaerrorx"]);
}
?>
<b>Go to M-PESA MENU on your phone.</b><br>
1. Select <b>Lipa na M-PESA</b><br /> 
2. Select <b>Pay Bill</b><br /> 
3. Enter business number: <b>822490</b><br /> 
4. Account: <b><?php echo "HV"; echo $ottah['id']; ?> </b><br /> 
5. Enter the Amount Ksh. <?php echo $due; ?> <b>or less</b> <br /> 
6. Enter your M-PESA PIN and Send <br /> 
7.You will receive an SMS from M-PESA with a confirmation Code<br />
8. Enter the M-PESA reference below: 
<br />
<form action="prepay.php" method="post">
M-PESA Ref:<input type="text" name="TransID" value="" maxlength="10" required><br>
Amount: <input type="number" name="TransAmount" value="" required><br>
<input type="hidden" name="BillRefNumber" value="<?php echo "HV"; echo $ottah['id']; ?>">
<input type="hidden" name="id" value="<?php echo $ottah['id']; ?>">
<input type="submit" name="submit" value="Submit">
</form>
<br />

<br />

<br />
<b>PAY VIA AIRTEL MONEY</b>
<br />
1. Go to Airtel Money  on your phone and send money to 0735187782<br />
2. Enter the Airtel Money reference number below: 
<br />
<form action="mpesa.php" method="post">
Airtel ref:<input type="text" name="reference" value="" maxlength="15" required><br>
<input type="hidden" name="domain" value="<?php echo $ottah['url']; ?>">
Amount: <input type="number" name="amount" value="" required>
<input type="hidden" name="id" value="<?php echo $ottah['id']; ?>">
<input type="hidden" name="email" value="<?php echo $ottah['email']; ?>">
<input type="hidden" name="phone" value="<?php echo $ottah['phone']; ?>"><br>
<input type="submit" name="submit" value="Submit">
</form>
<br />
<br />
<?php 
} else {
echo "<h3>This id does not exist.</h3>";
} 
} 
?>
</div>
<div class=right_content>
<div class=about>
<H3 STYLE="color:green" align:centre;>OVER <?php echo birthday('2010-05-10');  ?> YEARS OF WEB HOSTING</H3>
</div>
<div class=about>
<br /><br />
</div>
<br /><br />
</div>
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