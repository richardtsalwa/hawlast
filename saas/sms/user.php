<?php
require '../config.php';
require '../database.php';
require '../functions.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Change your password - SMS Dashboard</title>
<meta name=robots content=noindex,nofollow />
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<link rel="shortcut icon" href="../img/favicon.ico" type=image/x-icon />
</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a>  
<a href="user.php?pwd=1">Account</a>  
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>  
<div id="domains">
<?php
if(isset($_POST['passconf'])) {
        
      $email = $_SESSION['valid_user'];
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
   $sql = "UPDATE users SET password='$password' WHERE email ='$email'";
    $result = mysql_query($sql) or die ("Error in changing the password.");


	//Successful let the user sign in afresh
   header("Location: logout.php");
   exit;

	} else {

?>
<p><b>Please enter you new password below.</b></p>
Your email is <?php echo $_SESSION['valid_user'];  ?>
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