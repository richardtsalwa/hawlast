<?php
require 'hawlastke.php';
require 'config.php';
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>Affiliate program that works for you</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<meta name=keywords content="affiliates , hawlast affiliate, money online" /><meta name=description content="Our affiliate works to make money for you automatically. Join to enjoy easy cash" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css">
<link rel=stylesheet type=text/css href=style.css /><link rel="stylesheet" type="text/css" href="responsive.css" /><link rel="shortcut icon" href=images/favicon.ico type=image/x-icon />
<?php
require("includes/header.txt");
?>
</div>
<div class=center_content>
<div class=left_content>
<h1>Affiliate program for you. Web hosting for you.</h1>
<br />
<?php
if (isset($_SESSION['affid'])) {
$url= "https://www.hawlast.com/?a=".$_SESSION['affid'];
echo "<br/>Your special affiliate link:<br/><br/>";
echo "<h3 style=\"color:blue;\">$url</h3>"; 
echo "<h3>Share the link on Facebook, Twitter, Your blog and WhatsApp.</h3>"; 
$dueon = mktime(0,0,0,date("m"),date("d")-100,date("Y"));
$topay = date("Y-m-d", $dueon);
$affid = $_SESSION['affid'];
$sql ="SELECT affid, product,date, price FROM affiliates_sales WHERE date > ? AND affid = ? ORDER BY date";
$tyler = $pdo->prepare($sql);
$tyler->execute([$topay,$affid]);
if ($myrow = $tyler->fetch()) {
?>
<!--- the table below shows earnings in the last 40 days --->
<table border="1" width="90%">
<tr bgcolor="#fff">
<td width="15%"><p align="center"><small><font face="Verdana">Date</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Product</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Price</font></small></td>
<?php
do 
{ 
    print("</tr><tr>");
    
     $color = "#A6ACFD";

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[date]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[product]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[price]</font></small></center></td>"); 
   
} while ($myrow = $tyler->fetch());
echo "</table>\n";
} else {
echo "<br />";
$dueon = mktime(0,0,0,date("m"),date("d")-40,date("Y"));
echo "<b>Your link has not converted a sale since ".date("Y-m-d", $dueon);
echo "</b><br />";
echo "<br />";
echo "<hr />";
}

$affid = $_SESSION['affid'];
$sql ="SELECT affid,product,date, price, verify FROM affiliates_sales WHERE affid=? ORDER BY date";
$result=$pdo->prepare($sql);
$result->execute([$affid]);
if ($myrow = $result->fetch()) {
?>
<!--- the table below shows earnings in the last 100 days --->
<table border="1" width="90%">
<tr><td colspan="4"><b>Your sales history</b></tr>
<tr bgcolor="#fff">
<td width="15%"><p align="center"><small><font face="Verdana">Date</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Product</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Price</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Verify</font></small></td>

<?php
do 
{ 
    print("</tr><tr>");
    
     $color = "#A6ACFD";

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[date]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[product]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[price]</font></small></center></td>"); 
   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
if($myrow['verify']== 1) {
$he = "Not confirmed"; } else {
$he = "Confirmed";
}
   print("<font face=\"Verdana\">$he</font></small></center></td>");

} while ($myrow = $result->fetch());
echo "</table>\n";
echo "<br />";
echo "<br />";
echo "<hr />";

} else {
echo "<br />";
echo "<br />";
echo "<br />";
echo "<hr />";
echo "No sales from your link";
echo "<br />";
}

} // End of sessions with afflid 


////ADMINISTRATOR MANENOS

if ((isset($_SESSION['affid'])) && ($_SESSION['access_level']==2)){
$dueon = mktime(0,0,0,date("m"),date("d")-100,date("Y"));
$topay = date("Y-m-d", $dueon);
$sql ="SELECT affid, product,date, price FROM affiliates_sales WHERE date > ? ORDER BY date";
$result=$pdo->prepare($sql);
$result->execute([$topay]);
if ($myrow = $result->fetch()) {
?>
<!--- the table below shows earnings in the last 100 days --->
<table border="1" width="90%">
<tr bgcolor="#fff">
<td width="15%"><p align="center"><small><font face="Verdana">Date</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Product</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Price</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Affid</font></small></td><?php
do 
{ 
    print("</tr><tr>");
    
     $color = "#A6ACFD";

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[date]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[product]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[price]</font></small></center></td>"); 
   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[affid]</font></small></center></td>");

} while ($myrow = $result->fetch());
echo "</table>\n";
echo "<br />";
echo "<br />";
echo "<hr />";
} else {
echo "<br />";
$dueon = mktime(0,0,0,date("m"),date("d")-40,date("Y"));
echo "No affiliates in the last 100 days -  ".date("Y-m-d", $dueon);
}

$dueon = mktime(0,0,0,date("m"),date("d")-100,date("Y"));
$signup = date("Y-m-d", $dueon);
$sql ="SELECT affid,fname, email,phone,date FROM affiliates WHERE date > ? AND user_status=1 ORDER BY date ASC";
$result = $pdo->prepare($sql);
$result->execute([$signup]);

if ($myrow = $result->fetch()) {
?>
<!--- the table below shows sign ups  in the last 100 days --->
<table border="1" width="90%">
<tr bgcolor="#fff">
<td width="15%"><p align="center"><small><font face="Verdana">Affid</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Name</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Email</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Phone</font></small></td>
<td width="15%"><p align="center"><small><font face="Verdana">Date</font></small></td>

<?php
do 
{ 
    print("</tr><tr>");
    
     $color = "#A6ACFD";

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[affid]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">$myrow[fname]</font></small></center></td>"); 

   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[email]</font></small></center></td>"); 
   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[phone]</font></small></center></td>");
   print("<td width=\"15%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\"> $myrow[date]</font></small></center></td>");
} while ($myrow = $result->fetch());
echo "</table>\n";
} else {
echo "<br />";
echo "No new affiliate since ".date("Y-m-d", $dueon);
}
} 

///REGISTER  NEW USER 

if(isset($_GET['register'])){
echo "<h2>Join our affiliate program</h2><br />";
echo "<table border=\"0\" cellspacing=\"3\" cellpadding=\"3\" align=\"left\">\n";
echo "<form method=\"post\" action=\"register.php\">\n";
echo "<tr><td>First name</td><td><input type=\"text\" name=\"fname\"></td></tr>\n";
echo "<tr><td>Surname</td><td><input type=\"text\" name=\"surname\"></td></tr>\n";
echo "<tr><td>Email</td><td><input type=\"text\" name=\"email\"></td></tr>\n";
echo "<tr><td>Phone 07** *** ***</td><td><input type=\"text\" name=\"phone\"></td></tr>\n";
echo "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>\n";
echo "<tr><td>Confirm Password</td><td><input type=\"password\" name=\"passconf\"></td></tr>\n";
echo "<tr><td>&nbsp;&nbsp;&nbsp; </td><td align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Register\"></form></td></tr>\n";
echo "</form></table>\n";
} else { ?>
<p>An affiliate program is a system that enables you to earn a passive income through promoting products and services.</p> <p>
 It is very easy to <b>make money</b> via an affiliate program. You just need to <a href="affiliates.php?register=1"><b>register</b></a> and get a special link (URL) that you can send to friends or post on your websites. For every click and successful purchase referred by that special link, you get a commission. </p>
<h3>Our affiliate program </h3>:<b>How it works</b><br />
<p>
*** Register on this website to get an affiliate link.<a href="affiliates.php?register=1"><b>Register now</b></a><br />
*** The affiliate sales can ONLY be tracked and credited to your account when the order and payments are done via the website <a href="http://www.hawlast.com">http://www.hawlast.com</a><br /> 
*** We offer a commission of KES 500 for every purchase above KES 2,499<br />
**** We reserve the right to change the commission.This will not however affect the sales you have already brought in for that particular month.<br />
*** Payments will be sent to you every 5th of the month once they are verified. You can log into your account to confirm verified sales for the month.<br />
</p>
<?php
}
?>
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