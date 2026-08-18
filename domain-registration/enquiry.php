<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require '../hawlastke.php';
require '../config.php';
ob_start();

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");
//IF A USER HAS CLICKED ON SUBMIT
if (isset($_POST['ddomain'])) {

//type=MERCHANT&
//ext

$yourname = $_POST['fname'];
$lastname = $_POST['lname'];
$email = $_POST['email'];
$hosting = $_POST['hosting'];
$payments = $_POST['payments'];
$phone = $_POST['phone'];
$ddomain = $_POST['ddomain'];
$agree = $_POST['agree'];
$h = $_POST['h'];
$src = $_POST['src'];
$amount = $_POST['amount'];
$ext = $_POST['ext'];

$cookie_name = "affiliate";
if(isset($_COOKIE[$cookie_name])) {
$refcookie = $_COOKIE[$cookie_name];
} else {
$refcookie = "101";
}

/* Check all form inputs */
$errors = array();

if(!$ddomain){$errors[] = "Please select a domain name!";}
if(!$hosting){$errors[] = "Please select a hosting plan!";}
if(!$yourname){$errors[] = "Please enter your first name!";}
if(!$lastname){$errors[] = "Please enter your last name!";}
if(!$email){$errors[] = "Please enter your email address!";}
if(!$phone){$errors[] = "Please enter your phone number!";}
if(!$amount){$errors[] = "Please enter amount!";}
if(!$payments){$errors[] = "Please select a means of payments!";}
if(!$agree){$errors[] = "Please agree to the terms of service!";}

$_SESSION['hosting'] = $hosting;

$_SESSION['fname'] = $yourname;

$_SESSION['lname'] = $lastname;

$_SESSION['ddomain'] = $ddomain;

$_SESSION['email'] = $email;

$_SESSION['payments'] = $payments;

$_SESSION['phone'] = $phone;

$_SESSION['totalcost'] = $amount;

if (!empty($_COOKIE['hawlast'])) { $ref = $_COOKIE['hawlast'];} else { $ref="1";} 

/* If e-mail is not valid show error message */
if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
{$errors[] = "The email address is not valid!";}

if (preg_match("/\D/",$phone))
{$errors[] = "Please enter a valid phone number!";}

if (!is_numeric($amount))
{$errors[] = "Amount should be a number!";}


if (!is_numeric($phone))
{$errors[] = "Phone number should be a number!";}

if(strlen($phone) <> 10)
{$errors[] = "Phone number should be 10 digits!";}

if (!preg_match("/^([\w\-]+\.[\w\-]+)/i",$ddomain))
{$errors[] = "That domain name is not valid!";}

if(count($errors) == 0){

$total = $_SESSION['totalcost'];
require 'includes/WriteToFile.php';

/* Let's prepare the message for the e-mail */
$message = "
Greetings $yourname $lastname,

Thank you for ordering $hosting for $ddomain.

You can make payments via M-PESA PAY BILL or PAYPAL.

http://www.hawlast.com/domain-registration/reserve.php?h=$h&domain=$ddomain&src=$src&amount=$amount&ext=$ext

Ref: $refcookie

Best Regards,

Hawlast Online Team

http://www.hawlast.com
http://www.twitter.com/hawlast
http://www.facebook.com/hawlast
";

$subject = $ddomain;
$attachments = "";

/* Send the message using mail() function */
sendEmail($email, $companyName, $subject, $message, $attachments); 
header('Location: reserve.php?checkout=1');

//if there are errors
} else { 

$_SESSION['errors'] = $errors; 

if(!$ddomain) { $ddomain = "example.com"; }
$url = "reserve.php?h=$h&domain=$ddomain&src=$src&amount=$amount&ext=$ext";

header("Location: $url");

}  

//Client has not selected a domain name
} else { $url = "reserve.php"; header("Location: $url");}
?>