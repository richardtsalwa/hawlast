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
<a href="index.php">Home</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>    
<a href="listed.php">Listed</a>    
<div id="form_container">
<?php 
if(isset($_POST['id'])) {
	$id = protect($_POST['id']);
	$id = tupu($_POST['id']);
	$id = marks($_POST['id']);

$sql = "DELETE FROM renewals WHERE id = '$id'";
$result = mysqli_query($db, $sql) or die("Deletion of the account failed.");
echo '<br />';
echo '<b>Delete successful</b>'; 
echo '<br />';

echo '<b>You will be redirected in 3 seconds...</b>'; 

//If successful show us the updated results
header( 'refresh: 3; url=listed.php' );

} else { 
echo "<br />";
echo "<h2>Please select an account you want to delete by clicking on this link"; 
echo "<br />";
echo "<a href=\"listed.php\">Client list</a>  and selecting edit.<h2>";
}
?>
<div>
<div id="footer">
By <a href="http://www.hawlast.com">HAWLAST Ventures</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>