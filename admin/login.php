<?php
session_start();
/*** if a user is logged in ***/
  if(isset($_SESSION['adaccess_level']))
        {
	      header("Location: index.php");
   	      exit;	
        }        else        {
                $log_link = 'login.php';
                $log_link_name = 'Login';

                $reg_link = 'register.php';
                $reg_name = 'Register';
        }
 

error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
require '../config.php';
require '../hawlastke.php';
$reset_error = "";
$success = "";
$email_error ="";

//IF RESET IS POSTED
if(isset($_POST['reset'])){

    $path = dirname($_SERVER['REQUEST_URI']);
    $loginurl = 'http://'.$_SERVER['HTTP_HOST'].$path.'/login.php?login=do';

    $dateupdated = date("Y-m-d");

    if (!empty($_POST['email'])) {
    $email = trim($_POST['email']);
	$checkemail = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
	if(!preg_match($checkemail, $email)){
	$email_error = "E-mail is not valid, must be name@server.tld";
	}
	} else { 
    $email_error = "Enter an email address<br/>";
    }
	
    $newpwd = uniqid();
    $password = password_hash($newpwd, PASSWORD_DEFAULT);
    // PDO supports positional (?) and named (:email) placeholders
$stmt = $pdo->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->execute([$password,$email]);
$updated = $stmt->rowCount();

if($updated == 1) {
        
    $subject = "New password for Hawlast Admin";

    $message = "
    Greetings, 
	
    Your password has been reset. 

    Your new password is $newpwd
       
    Login at $loginurl

    Once you login, create a memorable password on your user profile page.

    Robots :),

    Hawlast Web";

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName, $subject, $message, $attachments);

if ($tuma == true) {
     $success = "We have emailed a new password to $email. Check your inbox/spam/bulk folder in less than 5 minutes."; } else { 
     $reset_error = "The system failed to send an email.";
      }
      } else { // Update failed 
      $reset_error = "Could not reset the password / The email address in not in our system.";
     }
	
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>Login -- Domain Renewal Manager</title>   
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />             
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
</head><body>
<div id="wrapper"><div id="content">
<?php
if (isset($_POST['submit'])) {
	// Assign Variables
	$password = protect($_POST['password']);
	$email = protect($_POST['email']);

      /*** check for existing email and get the password ***/
$countQuery = "SELECT
            email,
            username,
            password,
            user_access_level
            FROM
            users
            WHERE
            email = ?
            AND
            user_status=1";
$stmt = $pdo->prepare($countQuery);
$stmt->execute([$email]);
$ngapi = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ngapi && password_verify($password, $ngapi['password'])) {
                
             /*** set the access level ***/
 $_SESSION['adaccess_level'] = $ngapi["user_access_level"];
 $_SESSION['valid_user'] = $ngapi["username"];
 
 //after login we redirect to home page
  header("Location: https://www.hawlast.com/admin/listed.php"); 
   exit();
     
		} else {
echo "<h3 style=\"color:red;\" align=\"center\">Wrong email and or password.</h3>";         

?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<table width="400" border="0" align="center" cellpadding="1" cellspacing="1">
<tr>
<td width="100">Email:</td>
<td><input name="email" type="text" value="<?php echo $email; ?>"></td>
</tr>
<tr>
<td width="100">Password:</td>
<td><input name="password" type="password"></td>
</tr>
<tr>
<td width="100">&nbsp;</td>
<td><input type="submit" name="submit" value="Login"></td>

<td><a href="?reset=1">Forgot password?</a></td> 

</tr>
</table>
</form>
<?php
		    
		}

} else { 

?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<table width="400" border="0" align="center" cellpadding="1" cellspacing="1">
<tr>
<td width="100">Email:</td>
<td><input name="email" type="text"></td>
</tr>
<tr>
<td width="100">Password:</td>
<td><input name="password" type="password"></td>
</tr>
<tr>
<td width="100">&nbsp;</td>
<td><input type="submit" name="submit" value="Login"></td>
<td><a href="?reset=1">Forgot password?</a></td> 
</tr>
</table>
</form>
<?php
}
if (isset($_GET['reset'])) { ?>
<div class = "login">
<form action ="login.php?reset=do" method="post">
<label>Email address <span>Enter registered email  address</span> </label>
<input name="email" type="email" value="" required autofocus>
<input type="submit" name="reset" value="Reset"> OR <a href="login.php?login=do">Login</a><br/>
<div><?php echo $success; echo $reset_error; ?></div>
</form>
</div> 
<?php
}
?>
</div>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="https://www.hawlast.com">Hawlast.com</a></div></div></div></body></html>