<?php
 /*** start the session ***/
    session_start();
    if(isset($_SESSION['adaccess_level']))
        {
                $log_link = 'logout.php';
                $log_link_name = 'Log Out';
                $reg_link = '#';
                $reg_name = $_SESSION['valid_user'];
        }
        else
        {
                $log_link = 'login.php';
                $log_link_name = 'Log In';
                $reg_link = 'register.php';
                $reg_name = 'Register';
	      $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();
require 'config.php';
require '../hawlastke.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>Domain Renewal Manager</title>                
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
</head>

<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a> 
<a href="listed.php">Listed</a> 
<a href="user.php?pwd=1">Account</a> 
<a href="addnew.php">Add new</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>  
<div id="domains">
<?php
if(isset($_POST['passconf'])) {
        
      $username = $_SESSION['valid_user'];
	$password = protect($_POST['password']);
      $password = tupu($_POST['password']);
	$confirm = protect($_POST['passconf']);
      $confirm = tupu($_POST['passconf']);

      if(strlen($_POST['passconf']) < 6 || strlen($_POST['passconf']) > 20)
		{
		echo "Confirm password should be 6 - 20 characters in length.";
		exit;
		}
        /*** check the length of the password ***/
    	if(strlen($_POST['password']) < 6 || strlen($_POST['password']) > 20)
    	{
	echo "Password should be 6 - 20 characters in length.";
	exit;
    	}

     if(($_POST['password']) != $_POST['passconf']){
	 echo "Passwords do not match!";
       exit;
	}

      /*** encrypt the password ***/
      $password = sha1($_POST['password']);

	// process form
   $sql = "UPDATE users SET password='$password' WHERE username ='$username'";
    $result = mysql_query($sql) or die ("Error in changing the password.");


	//Successful let the user sign in afresh
   header("Location: logout.php");
   exit;

	} else {

?>
<p><b>Please enter you new password below.</b></p>
Your username is <?php echo $_SESSION['valid_user'];  ?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
<div class=form_row>
<label class=contact><b>Enter new password: <img src="images/must.gif" height="5" width="5">:</b></label>
<input name="password" type="password" maxlength="30" class=contact_input /><br /><br />
</div>
<div class=form_row>
<label class=contact><b>Confirm new password: <img src="images/must.gif" height="5" width="5">:</b></label>
<input name="passconf" type="password" maxlength="30" class=contact_input /><br /><br />
</div>
<div class=form_row>
<label class=contact>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label> 
<input type="Submit" value="Submit">
</div>
<?php
} 
?>
</div>

</div>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>