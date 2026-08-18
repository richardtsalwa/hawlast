<?php
session_start();
    ini_set('display_errors',1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
    
    if(isset($_SESSION['adaccess_level']))
        {
                $log_link = 'logout.php';
                $log_link_name = 'Log Out';
                $reg_link = '#';
                $reg_name = $_SESSION['valid_user'];
        }        else        {
                $log_link = 'login.php';
                $log_link_name = 'Log In';
                $reg_link = 'register.php';
                $reg_name = 'Register';
	      $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();
require_once('classes/paginator.class.php');

require '../config.php';
require '../hawlastke.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>Client list with those due on top</title>
<meta name="robots" content="noindex,nofollow" />
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

<table> <tbody><tr> 
<td align="left"> 
<form action="search.php" method="GET" name="search">
<input name="search" value=""> 
<input type="submit" value="Search" style="font-size: 15px; height: 1.50em; vertical-align: middle;"> 
</form>
</td> </tr> </tbody>

</table> 

<div id="form_list">
<?php 
if(isset($_GET['renew'])) {
	$id = protect($_GET['id']);
	//$id = tupu($_GET['id']);
	$id = marks($_GET['id']);
	$renewal_date = protect($_GET['renewal_date']);
        $service = protect($_GET['service']);
       //$cost = tupu($_GET['cost']);
	$email = protect($_GET['email']);
      //$email = tupu($_GET['email']);
     if(isset($_GET['emailalt'])) {
    $emailalt = $_GET['emailalt'];
} else {
     $emailalt  = '';
}
      $url = protect($_GET['url']);
      //$url = tupu($_GET['url']);
//Let us find out the date values
$day= day("$renewal_date"); //day value
$month= month("$renewal_date"); // Month value
$year= year("$renewal_date"); // Year value

//Get next year, same day
$newrenewal = date('Y-m-d', mktime(0,0,0,$month,($day),($year+1))); 
$nextyear= date('Y-m-d', mktime(0,0,0,$month,($day),($year+2))); 

 //add 1 year to the renewak date ---
$sql = "UPDATE renewals SET renewal_date = ? WHERE id = ?";
// Prepare the statement
$stmt = $pdo->prepare($sql);

// Execute the statement with positional placeholders
$stmt->execute([$newrenewal, $id]);

///CHECK IF THIS WAS EVER ENTERED
$sqlx ="SELECT id,renewalsid, prepaid FROM renewalsprepaid WHERE renewalsid = ?";
$stmt = $pdo->prepare($sqlx);
$stmt->execute([$id]);

//check if a row exist
$manu = $stmt->fetchColumn();

if ($manu > 0) {
////////// WE NEED TO REDUCE THE PREPAID AMOUNT TO ZERO 
$amount = "0";
$status ="2";
 //ID IS DECLARED UP THERE
$sq = "UPDATE renewalsprepaid SET status =? WHERE renewalsid=?";
$stmtx= $pdo->prepare($sq);
$stmtx->execute([$status,$id]);
}

$File = "renewed.txt"; 
$Handle = fopen($File, 'a');
$date = date("Y-m-d H:i:s"); 
$Data = "$renewal_date  $url $service $email client paid  on $date\n";
fwrite($Handle, $Data); 
fclose($Handle); 

//SEND an email to notify the user
require (dirname('_FILE_').'/includes/renewalemail.php');
$urlz = substr($url,11);
$subject = "$service is now renewed";

/* Send the message using mail() function */
$companyName = "Hawlast Ventures";
$attachments = "";

$tuma = sendEmail($email, $companyName, $subject, $renewedmessage, $attachments);

if ($tuma == true) {
echo ("<p>Email delivered...</p>");
//If successful show us the updated results
header( 'refresh: 3; url=listed.php' );
} else { 
echo ("<p>Message delivery failed...$email</p>"); 
header( 'refresh: 3; url=listed.php' );
}

} 
?>
<div>
<?php
if (!isset($_GET['id'])){

// Introduce the pagination class
$sql = "SELECT COUNT(*) AS num_row FROM renewals ORDER BY renewals.renewal_date";
// Assuming you've already established a PDO connection:
$stmt = $pdo->prepare($sql);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$num_rows = $row['num_row'];

   $pages = new Paginator;  
   $pages->items_total = $num_rows;  
   $pages->mid_range = 9;  
   $pages->paginate();  
   echo $pages->display_pages();  

$color = "#D8DBFE";

// get list in ASC by date of renewal.
//$sql ="SELECT id, firstname,lastname,service,cost,url,renewal_date,email,emailalt, phone FROM renewals where renewal_date > '2023-01-01' ORDER BY renewals.renewal_date $pages->limit";

$sql ="SELECT id, firstname,lastname,service,cost,url,renewal_date,email,emailalt, phone FROM renewals where renewal_date > '2023-01-01' ORDER BY renewal_date $pages->limit";
// Assuming you've already established a PDO connection:
$stmt = $pdo->prepare($sql);
$stmt->execute();

if ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
<td width="10%"><p align="center"><small><font face="Verdana">Renewal date</font></small></td>
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
   print("<font face=\"Verdana\">$myrow[renewal_date]</font></small></center></td>"); 

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">
   <form method=\"get\" action=\"edit.php\">
   <input name=\"id\" type=\"hidden\" value=$myrow[id]>
   <input name=\"renewals\" type=\"submit\" value=\"Edit\">
   </form></font></small></center>  
   <a href=\"$myrow[url]\" target=\"_blank\"><center><small>");
   print("<font face=\"Verdana\">$myrow[url]</font></small></td>");

   print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
   print("<font face=\"Verdana\">
   <form method=\"get\" action=\"listed.php\">
   <input name=\"renewal_date\" type=\"hidden\" value=\"$myrow[renewal_date]\">
   <input name=\"service\" type=\"hidden\" value=\"$myrow[service]\">
   <input name=\"email\" type=\"hidden\" value=\"$myrow[email]\">
 <input name=\"emailalt\" type=\"hidden\" value=\"$myrow[emailalt]\">
 <input name=\"cost\" type=\"hidden\" value=\"$myrow[cost]\">
   <input name=\"url\" type=\"hidden\" value=\"$myrow[url]\">
    <input name=\"id\" type=\"hidden\" value=\"$myrow[id]\">
      <input name=\"renew\" type=\"submit\" value=\"Renew\">
        </form>

    <form method=\"post\" action=\"addnewtodb.php\">
   <input name=\"firstname\" type=\"hidden\" value=\"$myrow[firstname]\">
   <input name=\"lastname\" type=\"hidden\" value=\"$myrow[lastname]\">
   <input name=\"email\" type=\"hidden\" value=\"$myrow[email]\">
   <input name=\"phone\" type=\"hidden\" value=\"$myrow[phone]\">
   <input name=\"submit\" type=\"submit\" value=\"Copy\">
        </form>
          </font></small></center></td>");

// We want send reminders here
$idrow = $myrow['id']; 

$sqlx = "SELECT prepaid FROM renewalsprepaid WHERE renewalsid = ?";

$stmtx = $pdo->prepare($sqlx);
$stmtx->execute([$idrow]);

if($jose = $stmtx->fetch(PDO::FETCH_ASSOC)) {
$prepaid = $jose['prepaid'];
} else {
$prepaid = "0";  
}

$renewal_date = $myrow['renewal_date'];

//Let us find out the date values
$day= day("$renewal_date"); //today's date
$month= month("$renewal_date"); // Month value
$year= year("$renewal_date"); // Year value

//Get next year, same day
$expiredate = date('Y-m-d', mktime(0,0,0,$month,($day),($year+1))); 

$renewaldate = dmy($myrow['renewal_date']);
$today = date('d-m-Y'); 

$dformat = 'd-m-Y'; 

$datediff = dateDiff($dformat, $today, $renewaldate);
$expired = expired($datediff);

if ($expired == "Expired") {
print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
print("<font face=\"Verdana\">
<form method=\"post\" action=\"expired.php\">
<input name=\"firstname\" type=\"hidden\" value=\"$myrow[firstname]\">
<input name=\"url\" type=\"hidden\" value=\"$myrow[url]\">
   <input name=\"expiredate\" type=\"hidden\" value=\"$expiredate\">
    <input name=\"email\" type=\"hidden\" value=\"$myrow[email]\">
<input name=\"emailalt\" type=\"hidden\" value=\"$myrow[emailalt]\">
<input name=\"phone\" type=\"hidden\" value=\"$myrow[phone]\">
    <input name=\"id\" type=\"hidden\" value=\"$myrow[id]\">
    <input name=\"cost\" type=\"hidden\" value=\"$myrow[cost]\">
    <input name=\"prepaid\" type=\"hidden\" value=\"$prepaid\">
      <input name=\"remind\" type=\"submit\" value=\"Remind\">
        </form>
          </font></small></center></td>");
 
} else { 
print("<td width=\"10%\" bgcolor=\"$color\"><center><small>");
print("<font face=\"Verdana\">$expired</font></small></center></td>");
}

} while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC));
echo "</table>\n";

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