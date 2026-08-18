<div class=about>
<?php
//Guy wants to change the password
if (isset($_GET['pwd']) && isset($_SESSION['affid']))  {?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<table width="200" border="0" align="center" cellpadding="1" cellspacing="1">
<tr><td colspan="2"><b>Change your password</b></td></tr>

<tr>
<td width="100">New:</td>
<td><input name="newpassword" type="password" value="" required></td>
</tr>
<tr>
<td width="100">New!:</td>
<td><input name="connewpassword" type="password" value="" required></td>
</tr>
<tr>
<td width="100">&nbsp;</td>
<td><input type="submit" name="change" value="Change"></td>
</tr>
</table>
</form>
<?php
}
//the user has posted the form to change the passwords
if (isset($_POST['connewpassword'])) {

$email = $_SESSION['email'];
$newpassword = protect($_POST['newpassword']);
$connewpassword = protect($_POST['connewpassword']);

$errors = "";

if(strlen($_POST['connewpassword']) < 5 || strlen($_POST['connewpassword']) > 20) {
$errors .= "Password should be 5 - 20 characters in length.<br />"; }

if(($newpassword) != $connewpassword) { $errors .= "Passwords do not match.<br />"; } 

if($errors) {  echo $errors . "<br>"; } else {

$connewpassword = password_hash($connewpassword, PASSWORD_DEFAULT);

// process form
$stmt = $pdo->prepare("UPDATE affiliates SET password=? WHERE email=?");
$stmt->execute([$connewpassword,$email]);
$updated = $stmt->rowCount();
$stmt = null;
if($updated == 1) {
echo "Your password has been updated...";
} 
}
}

if (isset($_POST['reset'])) {
	$error ="";
	$email = protect($_POST['email']);
	if($email){
	$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
	if(!preg_match($checkemail, $email)){
	$error .= "E-mail is not valid, must be name@server.tld!";
	}
    if($error) {
	echo $error;
	}else {
	$newpwd = uniqid();
	$pass = password_hash($newpwd, PASSWORD_DEFAULT);

	$stmt = $pdo->prepare("UPDATE affiliates SET password=? WHERE email=?");
	$stmt->execute([$pass,$email]);
	$updated = $stmt->rowCount();
    $stmt = null;
	if($updated == 1) {

	$subject = "New password";

$message = "
       Hello,

	Someone reset the password of your Affiliate Account.
	
	New password is $newpwd

	Affiliate Manager
	http://www.hawlast.com
	Tel: 0720 401 869";

	$headers = 'From: Hawlast Hosting <support@hawlast.com>' . "\r\n";
	$headers .= "Reply-To: support@hawlast.com\r\n";

	/* Send the message using mail() function */
	if (mail($email, $subject, $message, $headers)) {

	echo '<br />';
	echo "<b>We have emailed a new password to $email.</b>"; 
	echo '<br />';
	} else { 
	echo ("<p><b>Message delivery failed...</b></p>");
	echo $message;
	echo '<br />';
	}

} else { echo "The password was not reset or that email is not in our system."; }

} //End of no error, reset
} //if we have email
} //if reset form is submitted 

if (!isset($_SESSION['affid'])) {
//user is not logged in
if (isset($_POST['submit'])) {


	$errors = "";
	// Assign Variables
	 if ($_POST['password'] != "") {
     $password = $_POST['password'];
      } else { 
     $errors .= "Enter a password<br/>";
     }

    if ($_POST['email'] != "") {
    $email = trim($_POST['email']);
    $checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
    if(!preg_match($checkemail, $email)){
	$errors .= "E-mail is not valid, must be name@server.tld<br/>";
	}
	} else { 
	$errors .= "Enter your email.<br/>";
	}
	
	$one = "1";
	$stmt = $pdo->prepare("SELECT affid,fname,surname,password,email,access_level FROM affiliates WHERE email = ? AND user_status=?");
	$stmt->execute([$email,$one]);
	$arr = $stmt->fetch();
    $stmt = null; 

	if ($arr && password_verify($password, $arr['password'])) {
   
             /*** set the access level ***/
             $_SESSION['affid']=$arr["affid"];
             $_SESSION['surname']=$arr["surname"];
             $_SESSION['fname']=$arr["fname"];
             $_SESSION['email']=$arr["email"];
             $_SESSION['access_level']= $arr["access_level"];

	   //after login we redirect listed accounts
     header("Location: affiliates.php"); 
	//exit ();
	} else {
	$errors .= "<br/>Wrong email, password combination<br/>";
	$errors .= "<a href=\"affiliates.php?login=do\">Try again</a> or <a href=\"affiliates.php?reset=1\">Reset</a>";   
	}

if ($errors) {	?> <div class = "login"><?php 	echo $errors; echo "<br/>"; ?></div>
<?php
}

} else {
?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<table width="400" border="0" align="center" cellpadding="1" cellspacing="1">
<tr>
<td width="100" colspan="2"><b>Member Login</b></td>
</tr><tr>
<td width="100">Email:</td>
<td><input name="email" type="email" required></td>
</tr>
<tr>
<td width="100">Password:</td>
<td><input name="password" type="password" required></td>
</tr>
<tr>
<td width="100">&nbsp;</td>
<td><input type="submit" name="submit" value="Login"></td>
</tr>
<tr>
<td><a href="affiliates.php?register=1"><b>Register</b></a></td> 
<td><a href="?reset=1"><b>Forgot password?</b></a></td> 
</tr>
</table>
</form>
<?php
}

///USER SESSION IS SET
} else {	
// the user has logged in
echo "<b>Welcome "; echo $_SESSION['fname']; echo " ";  echo $_SESSION['surname']; 
echo "</b><br />"; 
echo $_SESSION['email']; 
echo " <a href=\"logout.php\"><b>Log out</b></a>";
echo "<br />"; 
echo " <a href=\"affiliates.php?pwd=1\"><b>Change Password</b></a>";
}

if (isset($_GET['reset']) == 1) { 
?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<table width="40%" border="0" align="center" cellpadding="1" cellspacing="1">
<tr>
<td>Enter your email:</td>
<td><input name="email" type="email" value=" "></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="reset" value="Resend password"></td>
</tr>
</table>
</form>
<?php 
}  
?>

<br />

</div>
<br /><br />
<br /><br />
<b>HAWLAST.COM</b><br /> 
<b>Nairobi, Kenya</b><br /> 

</div><div class=clear></div>