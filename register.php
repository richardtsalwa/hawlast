<?php
require 'hawlastke.php';
require 'config.php';

/*** mysql hostname ***/
$hostname = 'localhost';

/*** mysql username ***/
//$username = 'root';
$username = 'n2a33d5_hasepm';

/*** mysql password ***/
//$password = '';
$password = '#23hawlast';

$dbname = "n2a33d5_erp";

$charset = 'utf8mb4';

$db = new mysqli($hostname, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">

<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">

<title>Affiliate program that works for you</title><meta name=keywords content="affiliates , hawlast affiliate, money online" />
<meta name=description content="Our affiliate works to make money for you automatically. Join to enjoy easy cash" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css">
<link rel=stylesheet type=text/css href=style.css />
<link rel="stylesheet" type="text/css" href="responsive.css"><link rel="shortcut icon" href=images/favicon.ico type=image/x-icon />
<?php
require("includes/header.txt");
?>
</div>
<div class=center_content><div class=left_content>

<h1><div class=title>Join our affiliate program</div></h1>
<div class=feat_prod_box_details>
<?php 
if(!isset($_POST['submit'])){
echo "<table border=\"0\" cellspacing=\"3\" cellpadding=\"3\" align=\"center\">\n";
echo "<form method=\"post\" action=\"".basename($_SERVER['PHP_SELF'])."\">\n";
echo "<tr><td>First name</td><td><input type=\"text\" name=\"fname\"></td></tr>\n";
echo "<tr><td>Surname</td><td><input type=\"text\" name=\"surname\"></td></tr>\n";
echo "<tr><td>Email</td><td><input type=\"text\" name=\"email\"></td></tr>\n";
echo "<tr><td>Phone 07** *** ***</td><td><input type=\"text\" name=\"phone\"></td></tr>\n";
echo "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>\n";
echo "<tr><td>Confirm Password</td><td><input type=\"password\" name=\"passconf\"></td></tr>\n";
echo "<tr><td>&nbsp;&nbsp;&nbsp; </td><td align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Register\"></form></td></tr>\n";
echo "</form></table>\n";
}else {

$fname = protect($_POST['fname']);
$surname = protect($_POST['surname']);
$email = protect($_POST['email']);
$phone = protect($_POST['phone']);
$password = protect($_POST['password']);
$confirm = protect($_POST['passconf']);

//make sure the name starts with a capital letter
$fname = ucfirst(strtolower($fname));

//make sure the name starts with a capital letter
$surname = ucfirst(strtolower($surname));

$errors = array();

if(!$fname){$errors[] = "Please enter your first name!";}
if(!$surname){$errors[] = "Please enter your surname!";}
if(!$email){$errors[] = "Please enter your email address!";}
if (!$phone) {$errors[] = "Please enter your phone number!";} 
if (strlen($phone)!="10") {$errors[] = "Phone number should be 10 digits!";}
if(!$password){$errors[] = "Please enter a password!";}

//Password should be at least 5 characters
$range = range(5,20);
if(!in_array(strlen($password),$range)){$errors[] = "Password should be at least 5 characters!";}

if($password){if(!$confirm){$errors[] = "Please confirm the password!";}}

if($password && $confirm){if($password != $confirm){$errors[] = "Passwords do not match!";}}

if($email){
$sql = "SELECT email FROM `affiliates` WHERE `email`='".$email."'";
$res = mysqli_query($db,$sql) or die(@mysqli_error());

if(mysqli_num_rows($res) > 0){
$errors[] = "The email you supplied is already in use!";
}
}

if(count($errors) > 0){

echo "<table border=\"0\" cellspacing=\"3\" cellpadding=\"3\" align=\"center\">\n";
echo "<form method=\"post\" action=\"".basename($_SERVER['PHP_SELF'])."\">\n";
echo "<tr><td>First name</td><td><input type=\"text\" name=\"fname\"></td></tr>\n";
echo "<tr><td>Surname</td><td><input type=\"text\" name=\"surname\"></td></tr>\n";
echo "<tr><td>Email</td><td><input type=\"text\" name=\"email\"></td></tr>\n";
echo "<tr><td>Phone 07** *** ***</td><td><input type=\"text\" name=\"phone\"></td></tr>\n";
echo "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>\n";
echo "<tr><td>Confirm Password</td><td><input type=\"password\" name=\"passconf\"></td></tr>\n";
echo "<tr><td>&nbsp;&nbsp;&nbsp; </td><td align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Register\"></form></td></tr>\n";
echo "</form></table>\n";

foreach($errors AS $error){
echo "<b style=\"color:red;\">";
echo $error . "<br>";
echo "</b><br />";

}

}else {
       /*** create a verification code ***/
      $verification_code = uniqid();
       $newpassword = password_hash($password, PASSWORD_DEFAULT);
       $date = date("Y-m-d");
	$sql4 = "INSERT INTO affiliates
	(affid,fname,surname,phone, email, password, access_level, user_status, date)
	VALUES (NULL,'{$fname}','{$surname}','{$phone}','{$email}','{$newpassword}',1,'{$verification_code}','{$date}')";
	
	//If insert successful, send an email to the user to verify
	if (mysqli_query($db,$sql4)) {

         /*** email subject ***/
         $subject = 'Hawlast Affiliate Program';

         /*** email to ***/
         $sendto = $email;

         /*** the message ***/
         $path = dirname($_SERVER['REQUEST_URI']);
         $url = 'http://'.$_SERVER['HTTP_HOST'].$path.'verify.php?vc='.$verification_code;
$message = "
Dear $fname $surname,

Thank you for joining our affiliate program.

You need to activate your account by clicking at $url

Thank you in advance.

Hawlast Affiliate Manager
P. O. BOX 9816-00100
NAIROBI
Tel: 0720401869 / 0735187782
http://www.hawlast.com";
                
                /*** set some headers ***/
                $headers = 'From: Hawlast Hosting <support@hawlast.com>' . "\r\n";
                $headers .= "Reply-To: support@hawlast.com\r\n";

               /*** send the email ***/
               if(!mail($sendto, $subject, $message, $headers))
                {
                 echo "Unable to send verification email";
                  //echo $message;
               } else { 

                echo 'Sign up almost complete.<br />'; echo 'Check your email for the activation link.<br />';
                
               }

	 } else { echo "SQL: Error in registration";}


}
}
?>

</div><div class=clear></div>
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