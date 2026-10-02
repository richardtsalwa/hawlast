<?php
if (session_status() == PHP_SESSION_NONE) {
session_start();
} 
ob_start();
require '../config.php';
require '../hawlastke.php';

if(isset($_POST['TransAmount'])) {
$BillRefNumber = trim($_POST['BillRefNumber']);

$TransID = trim($_POST['TransID']);

$amount = trim($_POST['TransAmount']);

$id = protect($_POST['id']);
$payurl="https://www.hawlast.com/domain-registration/reserve.php?checkout=1";

$postdata ="BillRefNumber=".$BillRefNumber."&TransID=".$TransID."&TransAmount=".$amount;

$url = 'https://www.hawlast.com/pesa/check.php';
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
$result = curl_exec($ch);

$curlerror ="";

if($result === 'Success') {
 //$curlerror = $_error($ch);
session_unset();

session_destroy();

header("Location: ./thankyou.html?id=success&acc=$id");

} else {

$_SESSION['mpesaerror']= $result;

header("Location: $payurl");
}
}

if(isset($_POST['phone'])) {	
$phone = protect($_POST['phone']);
$amount = protect($_POST['amount']);
$account = protect($_POST['account']);
$id = protect($_POST['id']);
$payurl = "https://www.hawlast.com/domain-registration/reserve.php?checkout=1";

$postdata ="phone=".$phone."&amount=".$amount."&account=".$account;

$url = 'https://www.hawlast.com/pesa/stk.php';
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
$curlerror = curl_error($ch);
    
session_unset();

session_destroy();

header("Location: ./thankyou.html?id=success&acc=$id");

} else {

$curlerror = curl_error($ch);
$_SESSION['stkerror']= "Iko Shida";
echo $curlerror;

//header("Location: $payurl");
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<meta name=Description content="Register a Kenyan domain name with us. Professional email accounts will give your business credibility and increase sales." />
<meta name=Keywords content="domain Kenya, kenya domain names, cheap domains, Domain registration Kenya" />
<meta name=robots content=noindex,follow />
<title>Domain names Kenya, Cheap Domain registration in Kenya</title>
<link rel="stylesheet" href="../font-awesome.min.css" type="text/css">
<link rel="stylesheet" type="text/css" href="../style.css" />
<link rel="shortcut icon" href=../images/favicon.ico type=image/x-icon />
<title>Domain names Kenya, Cheap Domain registration in Kenya</title>
<?php require __DIR__ . "/includes/header.php"; ?>
</div>
<div class=center_content>
<?php
//if (!isset($_GET['domain']) && !isset($_GET['checkout'])){ header("Location: ./"); exit();} 

//MAIN LOGIC, GIVEN A DOMAIN NAME AND HOSTING PLAN, PROVIDE THE FORM TO CHECK OUT
if (!empty($_GET['domain']) && isset($_GET['h']) && isset($_GET['src'])){
$ddomain = strtolower($_GET['domain']); 
$plan = $_GET['h'];

if (!empty($_GET["ext"]))
{
$ext = strtolower($_GET['ext']);
} else {
$ext = "com";
}

if (!empty($_GET['src']))
{
$src = strtolower($_GET['src']);
} else {
$src ="none";
}


?>
<div class=left_content><div class=in><h1>Fill the form below to complete the hosting order (Step 2 of 3):</h1>
<br /><br />
<p style="color: #cc0000;">
<?php if (isset($_SESSION['errors'])){ echo "<b>Please correct the following errors:</b><br />";  
    //print_r($_SESSION['errors']);
foreach($_SESSION['errors'] as $element): 
 echo $element; echo "<br />";
endforeach; 
 
} ?> </p>
<p style="color:#0000FF;" align="center"><b>
<?php if ($ddomain !== "example.com" && !isset($_SESSION['error'])) { echo $ddomain ." is available!"; echo "Fill the form below to get the domain plus ". $_GET['h'] ." plan";} else { echo "Fill the order below."; } ?></b>
</p>
<form name="demo" method="POST" action="enquiry.php">
<table border="0" align="center" cellpadding="0" cellspacing="5" class="Basic_Container">
<?php 
if ($ddomain == "example.com") { ?>
<tr>
<td class="note"><div align="left">Enter your domain:</div></td>
<td><b>www.</b><input name="ddomain" type="text" id="ddomain" size="20" maxlength="25" value="" required /></td>
</tr>
<?php } else { ?>
<input name="ddomain" type="hidden" id="ddomain" value="<?php echo $ddomain; ?>" size="30" maxlength="50" /> 
<?php }?>
<tr>
<td class="note"><div align="left">Your hosting plan:</div></td>
<td class="note" size="30">
<select name="hosting">
<!---check if the hosting plan is selected and show it -->
<?php 
if ($plan =="domain") { 
?>
<option value="Domain only (Ksh. <?php echo $_GET['amount']; ?> p.a.)">Domain only Ksh. <?php echo $_GET['amount']; ?> p.a.</option>
<?php
}
if ($plan =="email")  { 
?>
<option value="Email hosting (Ksh. <?php echo $_GET['amount']; ?> p.a.)" >Email Hosting Ksh. <?php echo $_GET['amount'];  ?> p.a.</option>
<?php
}

if ($plan =="starter") { 
?>
<option value="Starter plan (Ksh. <?php echo $_GET['amount']; ?> p.a.)">Starter Plan Ksh. <?php echo $_GET['amount']; ?> p.a.</option>
<?php
}
if ($plan =="premier") { 
?>
<option value="Premier plan (Ksh. <?php echo $_GET['amount']; ?> p.a.)">Premier Plan Ksh. <?php echo $_GET['amount']; ?> p.a.</option>
<?php
}
?>

</select>
</td>
</tr>
<tr>
<td class="note"><div align="left">First Name:</div></td>
<td><input name="fname" type="text" id="FName" size="30" maxlength="25" value="<?php if (isset($_SESSION['fname'])){ echo $_SESSION['fname'];} ?>" required /></td>
</tr>
<tr>
<td class="note"><div align="left">Last Name:</div></td>
<td><input name="lname" type="text" id="lName" size="30" maxlength="25" value="<?php if (isset($_SESSION['lname'])){ echo $_SESSION['lname'];} ?>" required /></td>
</tr>
<tr>
<td class="note"><div align="left">Your Email:</div></td>
<td><input name="email" type="email" id="Email" size="30" value="<?php if (isset($_SESSION['email'])){ echo $_SESSION['email'];} ?>" required /></td>
</tr>
<tr>
<td class="note"><div align="left">Phone:</div></td>
<td><input name="phone" type="text" id="Phone" size="30" maxlength="10" placeholder="07" value="<?php if (isset($_SESSION['phone'])){ echo $_SESSION['phone'];} ?>" required /></td>
</tr>
<tr>
<td class="note"><div align="left">Means of payment:</div></td>
<td class="note" colspan="2" style="height: 20px; font-size: 12px; font-family: Arial;">
 <br />
<input type="radio" name="payments" value="mpesa" checked="checked"> MPESA  <input type="radio" name="payments" value="paypal"> PAYPAL <input type="radio" name="payments" value="cheque"> CHEQUE<br />
 <br />
<input type="hidden" name="type" value="MERCHANT" />
<br />
<input type="hidden" name="amount" value="<?php echo $_GET['amount']; ?>" />
<input type="hidden" name="h" value="<?php echo $_GET['h']; ?>" />
<input type="hidden" name="src" value="<?php echo $src; ?>" /> 
<input type="hidden" name="ext" value="<?php if ($ddomain =="example.com") { echo "com"; } else { echo $ext; } ?>" /> 
</td>
</tr>
<tr>
<td class="note"><div align="left">Agree to <a href="../terms-of-service.html" target="_blank"><b>terms of service</b></a></div></td>
<td>
<input type=checkbox name=agree value='yes'>
</td>
</tr>
<tr><td></td>
<td> &nbsp;&nbsp;&nbsp;<input type="submit" name="Submit" value="Place the order" /></td></tr>
<!--- <tr><td colspan="2" style="color:red">You incur no extra costs if you pay with M-PESA PAYBILL</td></tr> --->

 </table>
</form>
<?php require 'includes/WriteDomain.php';
}
if (isset($_GET['checkout'])) {
require("includes/checkout.php");

} 
?>
<br /><br /><p>We are now web hosting the Kenya cTLD domains aka .ke with options to register a domain like .co.ke,or.ke and ac.ke.</p></div>
<div class=clear></div></div>
<div class=right_content><?php require __DIR__ . "/includes/other_left.php";?></div>
<?php require __DIR__ . "/includes/footer.php";?>
</body></html>