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

<meta name="robots" content="noindex,nofollow" />

<title>Update Services or add new services</title>
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
if(isset($_POST['service_cost']))
{
      $service_name = protect($_POST['service_name']);
      $service_cost = protect($_POST['service_cost']);

      $service_name = tupu($_POST['service_name']);
      $service_cost = tupu($_POST['service_cost']);

     $service_cost = marks($_POST['service_cost']);

// process form

$sql = "INSERT INTO dservices VALUES (NULL,'$service_name','$service_cost')";
$result = mysqli_query($db, $sql) or die ("Error in service insert.");

header("Location: ./");
exit;
} else if(isset($_GET['addservices'])) {
// show form
?>
<b> Add a service</b>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
<div class=form_row>
<label class=contact><b>Service Name <img src="images/must.gif" height="5" width="5">:</b></label>
<input name=service_name maxlength="25" type=text class=contact_input />
<font size="-3" color="red">Example: Domain only, Elephant plan</font>
</div>
<div class=form_row>
<label class=contact><b>Cost (Ksh.)<img src="images/must.gif" height="5" width="5">:</b></label>
<input name=service_cost type=text maxlength="10" class=contact_input /><br /><br />
<font size="-3" color="red">Example: 100, 1000, 2500 - no space or commmas.</font><br />
</div>
<div class=form_row>
<label class=contact>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label> 
<input type="Submit" value="Submit">
</div>
<?php }?>
<div>

<?php 
if(isset($_GET['services']))
{
$color = "#D8DBFE";
$sql ="SELECT id,service_name,service_cost FROM dservices ORDER BY id";
$result = mysqli_query($db,$sql); 
if ($myrow = mysqli_fetch_array($result)) {
?>
<!--- the table below shows classes on offer --->
<p><b>You can change the service name, charges(cost) by clicking on update.</b></p>
<table border="1" width="45%">
<tr bgcolor="#fff">
<td width="15%"><p align="center"><small><font face="Verdana">Service Name</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Cost</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Update</font></small></td>
<?php
do 
{ 
    print("</tr><tr>");

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[service_name]</font></small></center></td>"); 
   
   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[service_cost]</font></small></center></td>");

   print("<td width=\"15%\" bgcolor=\"$color\"><center>");
   print("<form method=\"get\" action=\"updates.php\"/>
	  <input type=\"hidden\" name=\"services\" value=\"$myrow[id]\"/>
	  <input type=\"submit\" value=\"Update\"/>
	  </form></center></td>");

} while ($myrow = mysqli_fetch_array($result));
echo "</table>\n";
mysqli_free_result($result);
} else {
echo "<h2>No services yet </h2>";
}

} 	
?>
</div>

</div>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast Ventures</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>