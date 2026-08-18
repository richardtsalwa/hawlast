<?php
 /*** start the session ***/
    session_start();
    if(isset($_SESSION['access_email']))
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
                $reg_link = '#';
                $reg_name = 'Register';
	      $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();

require_once('classes/paginator.class.php');
require 'config.php';
require 'functions.php';
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Karibu Web : Powered by Hawlast.com</title>
<meta name=robots content=noindex,nofollow />
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">

</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>    

<table> <tbody><tr> 
<td align="left"> 
<form action="search.php" method="post" name="search">
<input name="search" value=""> 
<input type="submit" value="Search" style="font-size: 15px; height: 1.50em; vertical-align: middle;" disabled> 
</form> 
</td><td>Karibu <?php echo $reg_name; ?> </td> </tr> </tbody>

</table> 

<div id="form_list">
<?php
if (!isset($_GET['id'])){
$email = $_SESSION['access_email'];
// Introduce the pagination class
$sql ="SELECT COUNT(*) AS num_row FROM renewals WHERE email = '{$email}' ORDER BY renewals.signup_date";
$result = mysql_query($sql);
$row = @mysql_fetch_array($result);
   $pages = new Paginator;  
   $pages->items_total = $row["num_row"];  
   $pages->mid_range = 9;  
   $pages->paginate();  
   echo $pages->display_pages();  

$color = "#D8DBFE";

// get list in ASC by date of sign up.
$sql ="SELECT id, firstname,lastname,service,cost,url,signup_date,email,emailalt, phone FROM renewals WHERE email = '{$email}' ORDER BY renewals.signup_date $pages->limit";
$result = mysql_query($sql,$db); 
if ($myrow = @mysql_fetch_array($result)) {
?>
<!--- the table below shows client list --->
<table border="1" width="96%">
<tr bgcolor="#fff">
<td colspan="10"><p align="center"><small><font face="Verdana"><b>Oldest to latest account</font></small></td>
</tr>
<tr bgcolor="#fff">
<td width="10%"><p align="center"><small><font face="Verdana">First Name</font></small></td>
<td width="10%"><p align="center"><small><font face="Verdana">Last Name</font></small></td>
<td width="10%"><p align="center"><small><font face="Verdana">Service</font></small></td>
<td width="10%"><p align="center"><small><font face="Verdana">Cost</font></small></td>
<!--- <td width="15%"><p align="center"><small><font face="Verdana">Website</font></small></td>--->
<td width="10%"><p align="center"><small><font face="Verdana">Email</font></small></td>
<td width="10%"><p align="center"><small><font face="Verdana">Phone</font></small></td>
<td width="10%"><p align="center"><small><font face="Verdana">Sign up date</font></small></td>
<td width="5%"><p align="center"><small><font face="Verdana">Edit</font></small></td>
<td width="5%"><p align="center"><small><font face="Verdana">Renew</font></small></td>
<td width="5%"><p align="center"><small><font face="Verdana">Status</font></small></td><?php
do 
{ 
   print("</tr>");

   print("<tr><td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[firstname]</font></small></center></td>"); 

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[lastname]</font></small></center></a></td>"); 
   
   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[service]</font></small></center></a></td>"); 
    
   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[cost]</font></small></center></a></td>"); 
 
  // print("<td width=\"20%\" bgcolor=\"$color\"><a href=\"$myrow[url]\" target=\"_blank\"><center><small>");
  // print("<font face=\"Verdana\">$myrow[url]</font></small></center></a></td>"); 

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[email]<br />$myrow[emailalt]</font></small></center></td>");

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[phone]</font></small></center></td>");

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[signup_date]</font></small></center></td>"); 

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">
   <form method=\"post\" action=\"#\">
   <input name=\"id\" type=\"hidden\" value=$myrow[id]>
   <input name=\"renewals\" type=\"submit\" value=\"Edit\" disabled>
   </form></font></small></center>  
   <a href=\"$myrow[url]\" target=\"_blank\"><center><small>");
   print("<font face=\"Verdana\">$myrow[url]</font></small></td>");

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">
   <form method=\"post\" action=\"#\">
   <input name=\"signup_date\" type=\"hidden\" value=\"$myrow[signup_date]\">
   <input name=\"service\" type=\"hidden\" value=\"$myrow[service]\">
   <input name=\"email\" type=\"hidden\" value=\"$myrow[email]\">
   <input name=\"url\" type=\"hidden\" value=\"$myrow[url]\">
    <input name=\"id\" type=\"hidden\" value=\"$myrow[id]\">
      <input name=\"renew\" type=\"submit\" value=\"Renew\" disabled>
        </form>
          </font></small></center></td>");

// We want send reminders here
$signup_date = $myrow['signup_date'];

//Let us find out the date values
$day= day("$signup_date"); //today's date
$month= month("$signup_date"); // Month value
$year= year("$signup_date"); // Year value

//Get next year, same day
$expiredate = date('Y-m-d', mktime(0,0,0,$month,($day),($year+1))); 

$signupdate = dmy($myrow['signup_date']);
$today = date('d-m-Y'); 
$datediff = dateDiff("-", $today, $signupdate);
$expired = expired($datediff);

if ($expired == "Expired") {
print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
print("<font face=\"Verdana\">
<form method=\"post\" action=\"expired.php\">
<input name=\"firstname\" type=\"hidden\" value=\"$myrow[firstname]\">
<input name=\"url\" type=\"hidden\" value=\"$myrow[url]\">
   <input name=\"expiredate\" type=\"hidden\" value=\"$expiredate\">
    <input name=\"email\" type=\"hidden\" value=\"$myrow[email]\">
    <input name=\"cost\" type=\"hidden\" value=\"$myrow[cost]\">
      <input name=\"remind\" type=\"submit\" color =\"red\" value=\"Expired\" disabled>
        </form>
          </font></small></center></td>");
 
} else { 
print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
print("<font face=\"Verdana\">$expired</font></small></center></td>");
}

} while ($myrow = mysql_fetch_array($result));
echo "</table>\n";
mysql_free_result($result);
echo $pages->display_pages();  
} else {
echo "<br />";
echo "<h2>No records here yet.</h2>";
}
}	
?>
</div>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>