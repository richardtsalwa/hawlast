<?php
 /*** start the session ***/
session_start();
/*** if a user is logged in ***/
  if(isset($_SESSION['access_email']))
        {
	      header("Location: index.php");
   	      exit;	
        }
        
ob_start();
require 'config.php';
require 'functions.php';
?>
<!DOCTYPE HTML><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="en">
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>Domain Manager :: hawlast.com</title>                
<link href="style/screen.css" media="screen" rel="stylesheet" type="text/css">    

</head><body>
<div id="wrapper"><div id="content">
<?php
if (isset($_REQUEST['submit'])) {
	// Assign Variables
	$phone = protect($_REQUEST['phone']);
	$email = protect($_REQUEST['email']);

        /*** test for db connection ***/
        if($db)
        {
      /*** check for existing username and phone number ***/
            $sql = "SELECT
		 firstname,
            email,
		 phone
            FROM
            renewals
            WHERE
            email = '{$email}'
            AND
            phone = '{$phone}'";
            $result = mysql_query($sql);
            if(mysql_num_rows($result) != 0)
            {
             /*** fetch result row ***/
             $row = mysql_fetch_array($result);
             /*** set the sessions ***/
             $_SESSION['access_email']= $row["email"];
             $_SESSION['valid_user']=$row["firstname"];


// Record the visit
$File = "log.txt"; 
$Handle = fopen($File, 'a');
$date = date("Y-m-d H:i:s"); 
$Data = "$email $phone $date\n";
fwrite($Handle, $Data); 
fclose($Handle);

//after login we redirect to home page
 header("Location: index.php"); 
     
		} else {
echo "<h3 style=\"color:red;\" align=\"center\">Wrong Email / Phone combination.</h3>"; 
// ERROR VISIT
$File = "logx.txt"; 
$Handle = fopen($File, 'a');
$date = date("Y-m-d H:i:s"); 
$Data = "$email $phone $date\n";
fwrite($Handle, $Data); 
fclose($Handle);     
?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" />
<table width="400" border="0" align="center" cellpadding="1" cellspacing="1">
<tr>
<td width="100">Email:</td>
<td><input name="email" type="text" value="<?php echo $email; ?>"></td>
</tr>
<tr>
<td width="100">Phone:</td>
<td><input name="phone" type="password"></td>
</tr>
<tr>
<td width="100">&nbsp;</td>
<td><input type="submit" name="submit" value="Login"></td>

<td>Phone number in the format 720401869</a></td> 

</tr>
</table>
</form>
<?php
       }
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
<td width="100">Phone:</td>
<td><input name="phone" type="password"></td>
</tr>
<tr>
<td width="100">&nbsp;</td>
<td><input type="submit" name="submit" value="Login"></td>

<td>Phone number in the format 720401869</a></td> 

</tr>
</table>
</form>
<?php
}
?>
</div>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com">Hawlast.com</a></div></div></div></body></html>