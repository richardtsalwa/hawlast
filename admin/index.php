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
require '../config.php';
require '../hawlastke.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>Domain Renewal Manager</title>    
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />            
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<link rel="stylesheet" type="text/css" href="images/calendar.css" media="all">
<script type="text/javascript" src="images/calendar.js"></script>
</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="#">Home</a>  
<a href="listed.php">Listed</a>  
<a href="user.php?pwd=1">Account</a>  
<a href="addnew.php">Add new client</a>
<a href="<?php echo $log_link; ?>"<?php $current = '/login.php' || '/register.php'; page($current); ?>><?php echo $log_link_name; ?></a>     

<table> <tbody><tr> 
<td align="left"> 
<form action="search.php" method="GET" name="search">
<input name="search" value=""> 
<input type="submit" value="Search" style="font-size: 15px; height: 1.50em; vertical-align: middle;"> 
</form>
</td> </tr> </tbody>

</table> 
<div id="form_container">
<table border="1" width="96%">
<tr><td>Domain only</td><td>Email plan</td><td>Starter plan </td><td>Premier plan </td></tr>
<tr>
<td>
<?php 
$day= date ("d"); // //today's date
$month= date ("m"); // // Month value
$year= date ("Y"); // Year value
$date = date('Y-m-d', mktime(0,0,0,$month,($day),($year-1))); 
$sql ="SELECT COUNT(*) AS num_row FROM renewals WHERE renewal_date > '$date' AND service LIKE 'Domain%'";
//$result = mysqli_query($db, $sql);
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
//$row = @mysqli_fetch_array($result);
echo $row["num_row"];
$domain = $row["num_row"];
?> 
</td>
<td>
<?php 
$sql ="SELECT COUNT(*) AS num_row FROM renewals WHERE renewal_date > '$date' AND service LIKE 'Email%'";
//$result = mysqli_query($db, $sql);
//$row = @mysqli_fetch_array($result);
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row["num_row"];
$email = $row["num_row"];
?> 
</td>
<td>
<?php 
$sql ="SELECT COUNT(*) AS num_row FROM renewals WHERE service LIKE 'Starter%' AND renewal_date > '$date'";
//$result = mysqli_query($db, $sql);
//$row = @mysqli_fetch_array($result);
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row["num_row"];
$starter = $row["num_row"];
?> 
</td>
<td>
<?php 
$sql ="SELECT COUNT(*) AS num_row FROM renewals WHERE service LIKE 'Premier%' AND renewal_date > '$date'";
//$result = mysqli_query($db, $sql);
//$row = @mysqli_fetch_array($result);
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row["num_row"];
$premier = $row["num_row"];
?> 
</td>
</tr>
<tr>
<td> @ 50 </td><td> @ 400</td><td> @ 1200 </td><td> @ 2</td>
</tr>
<tr>
<td><?php echo $domain * 50 ?> </td><td><?php echo $email * 400 ?> </td>  <td> <?php echo $starter * 1200 ?></td><td><?php echo $premier * 2 ?> </td>
</tr>
</table>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>