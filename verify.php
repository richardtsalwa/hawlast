<?php
require 'hawlastke.php';
require 'config.php';
/*** mysql hostname ***/
$hostname = 'hawlast.accountsupportmysql.com';

/*** mysql username ***/
$username = 'kamata';

/*** mysql password ***/
$password = '#23hawlast';

/*** connect to the database ***/
$dbname = "erp";
$db = new mysqli($hostname, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}



?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8">
<meta name="author" content="HawlasT">
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<title>Affiliate program that works for you</title><meta name=keywords content="affiliates , hawlast affiliate, money online" /><meta name="description" content="Our affiliate works to make money for you automatically. Join to enjoy easy cash" /><link rel=stylesheet type=text/css href=style.css />
<link rel="stylesheet" type="text/css" href="responsive.css"><link rel="shortcut icon" href=images/favicon.ico type=image/x-icon /></head><body>
<div id=wrap>
<div class=header>
<?php
require("includes/header.txt");
?>
</div>
<div class=center_content><div class=left_content>
<h3>Verification</h3>
<?php
/*** check if verification code is present ***/
if(isset($_GET['vc'])) {

$errors = array();
/*** check verification code is a string of 13 chars ***/
if(strlen($_GET['vc']) != 13) {$errors[] = "Invalid verification code";}

/*** escape the code ***/
$verification_code = $_GET['vc'];

/*** select username, email and password given verification code ***/
$sql = "SELECT
affid,
fname,
surname,
email,
access_level
FROM
affiliates
WHERE
user_status='{$verification_code}'";

$result = mysqli_query($db,$sql);

if(mysqli_num_rows($result) != 1) { $errors[] = "Looks like you are already verified.Login on the right."; } else {
 
 /*** fetch result row ***/
 $row = mysqli_fetch_array($result);

 /*** prepare to email ***/
 $affid = $row['affid'];
 $email = $row['email'];
 $fname = $row['fname'];
 $surname = $row['surname'];
 $url= "http://www.hawlast.com/?a=".$affid;
 $subject = 'Affiliate link';
 $message = "Dear $fname $surname,
 
 Your affiliate link is $url 

 You can send it to your friends via email/ Facebook or post it on your website to start making money.
 
 You can always track your earnings via http://www.hawlast.com/affiliates.php.
 
 Thank you in advance.\n
 
 Hawlast Affiliate Manager
 P. O. BOX 9816-00100
 NAIROBI
 Tel: 0720 401 869 // 0754 778 851
 http://www.hawlast.com";
 
 /*** set some headers ***/
 $headers = 'From: Hawlast Hosting <support@hawlast.com>' . "\r\n";
 $headers .= "Reply-To: support@hawlast.com\r\n";

 
 /*** update the user status ***/
 if($db)
 {
 /*** the update SQL ***/
 $sql = "UPDATE
 affiliates
 SET
 user_status=1
 WHERE
 user_status='{$verification_code}'";

 /*** run the query ***/
 $result = mysqli_query($db,$sql);

 /*** check for affected rows ***/
 if(mysqli_affected_rows($db) != 1)
 {$errors[] = "You are not yet verified";} else {

 /*** send the email ***/
 if(mail($email, $subject, $message, $headers)) { 
 echo 'Confirmation emailed to you .';  echo "Verification Complete";
 echo "Login using the form on your right.</a>";
 }    

 } 

 } 

 }//we have the verification code in database
 
 }
?>
<p>
<?php 
if(count($errors) > 0){
foreach($errors AS $error){
echo "<b style=\"color:red;\">";
echo $error . "<br>";
echo "</b><br />";
}
}
?>
</div>

<div class=right_content>
<?php
require("includes/affiliates_left.txt");
?>
</div>
<?php
require("includes/footer.txt");
?>
</body></html>