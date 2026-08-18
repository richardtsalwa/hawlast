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
<title>New Client update</title>             
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
    
<div id="domains">
<?php
if(isset($_POST['updateservice']))
{
      $service_name = protect($_POST['service_name']);
      $cost = protect($_POST['service_cost']);
      $service_id = protect($_POST['id']);

      $service_name = tupu($_POST['service_name']);
      $service_cost = tupu($_POST['service_cost']);
      $id = tupu($_POST['id']);

     $cost = marks($_POST['service_cost']);
     $id = marks($_POST['id']);

// process form
$sql = "UPDATE dservices SET service_name='$service_name',service_cost='$service_cost' WHERE id ='$id'";
$result = mysql_query($sql) or die ("Error in service update.");
header("Location: admin.php?services");
exit;
} else if(isset($_GET['services'])) {
$id = protect($_GET['services']);
$id = tupu($_GET['services']);
$id = marks($_GET['services']);
$sql ="SELECT id,service_name,service_cost FROM dservices WHERE id = $id";
$result = mysql_query($sql,$db); 
if ($myrow = mysql_fetch_array($result)) {
// show form
?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
<div class=form_row>
<label class=contact><b>Service Name <img src="images/must.gif" height="5" width="5">:</b></label>
<input name=service_name maxlength="30" type=text class=contact_input value="<?php echo $myrow["service_name"]; ?>" />
<font size="-3" color="red">Example: Salsa, guitar, Video Disc Jockey</font>
</div>
<div class=form_row>
<label class=contact><b>Cost (Ksh.)<img src="images/must.gif" height="5" width="5">:</b></label>
<input name=service_cost maxlength="5" class=contact_input value=<?php echo $myrow["service_cost"]; ?> />
<font size="-3" color="red">Example: Enter 1000 and not 1,000</font>
</div>
<input type="hidden" name="id" value=<?php echo $myrow["id"]; ?> />
<div class=form_row>
<label class=contact>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
<input type="Submit" name="updateservice" value="Submit">
</div>
</form>
<?php
} else { echo "<h2>No service with the id you have provided.</h2>"; }
}
?>
<div>
</div>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>