<?php
require 'config.php';
require 'database.php';
require_once dirname(__DIR__) . '/hawlastke.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Affiliates</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
<link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<?php
if(isset($_SESSION['access_level']))
{ ?>
<h2>HAWLAST SAAS</H2>
<p><a href="/saas/" class="btn btn-success">Home</a></p>

<?php
//IF ADMINISTRATOR 
if ($_SESSION['access_level'] == 2 ) {
$pdo = Database::connect();

//IF USER WANTS TO MARK COMMISSION AS PAID
if (isset($_GET['a'])) {
$affiliate = $_GET['a'];
$amount = $_GET['amt'];
echo markAllCommissionsPaid($pdo,$affiliate,$amount);
    
}
$airtimeid = $_SESSION['user_id']; 
echo "<h3>Administrator view</h3>";


$affiliate = "D";
echo "</br>Airtime sold by Dan Ksh."; $amount= getCommissionSum($pdo,$affiliate); echo  $amount;
echo "<a href=affiliates.php?a=$affiliate&amt=$amount> Mark as paid</a>";


$affiliate = "J";
echo "</br>Airtime sold by Jay Ksh."; $amount= getCommissionSum($pdo,$affiliate); echo  $amount;
echo "<a href=affiliates.php?a=$affiliate&amt=$amount> Mark as paid</a>";


$affiliate = "K";
echo "</br>Airtime sold by Kelvin  Ksh."; $amount= getCommissionSum($pdo,$affiliate); echo  $amount;
echo "<a href=affiliates.php?a=$affiliate&amt=$amount> Mark as paid</a>";

$affiliate = "L";
echo "</br>Airtime sold by Linah (Maseno)  Ksh."; $amount= getCommissionSum($pdo,$affiliate); echo  $amount;
echo "<a href=affiliates.php?a=$affiliate&amt=$amount> Mark as paid</a>";


$affiliate = "M";
 echo "</br>Airtime sold by Manu Ksh."; $amount= getCommissionSum($pdo,$affiliate); echo  $amount;
echo "<a href=affiliates.php?a=$affiliate&amt=$amount> Mark as paid</a>";


$affiliate = "W";
 echo "</br>Airtime sold by Margaret -Blessed Ksh."; $amount= getCommissionSum($pdo,$affiliate); echo  $amount;
echo "<a href=affiliates.php?a=$affiliate&amt=$amount> Mark as paid</a>";

} else { 

$airtimeid = $_SESSION['user_id']; 

}

}
?>
<br/>
<br/>
<br/>

<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas</a></div>
</div>
</div>
</div>
</body></html>