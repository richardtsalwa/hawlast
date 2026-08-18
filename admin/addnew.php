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
<title>Add new to Database :: Domain Renewal Manager</title>       
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />         
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<link rel="stylesheet" type="text/css" href="images/calendar.css" media="all">
<script type="text/javascript" src="images/calendar.js"></script>
</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a> 
<a href="listed.php">Listed</a> 
<a href="user.php?pwd=1">Account</a> 
<a href="addnew.php">Add new</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>  
<div id="form_container">
<?php require 'includes/form.txt'; ?>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>