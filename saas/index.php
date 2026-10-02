<?php
require 'config.php';
require 'database.php';
require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Hawlast Airtime and SMS app</title>
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
{ 
?>
<h2>HAWLAST SAAS</H2>
<?php
$id = $_SESSION['user_id'];
$pdo = Database::connect();
$sql = "SELECT bal FROM sms_users WHERE airtimeid=$id";
$stmt = $pdo->query($sql);
$row =$stmt->fetchObject();
echo 'Your <b>float</b> balance is KES ' . $row->bal;

$salesSql = "SELECT SUM(amount) as total_sales
FROM airtime_transactions
WHERE date >= '2024-12-19 00:00:00' 
  AND status = 'Success'";
$salesstmt = $pdo->query($salesSql);
$totalsales =$salesstmt->fetchObject();
echo '<br/>Sales from 20 Dec  KES ' . $totalsales->total_sales;

// Query to get the total count of unique phone numbers from 2024-12-20
    $sql = "SELECT COUNT(DISTINCT `phone`) AS total_unique_phones
        FROM airtime_transactions
        WHERE `date` >= '2024-12-20';";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch the total count of unique phone numbers
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUniquePhones = $result['total_unique_phones'];

    echo "<br>Total Unique Phone no.  2024-12-20: " . $totalUniquePhones;
    Database::disconnect();
    
    
    // Be sure to include the library loader
require_once dirname(__DIR__) . '/libraries.php';

// Specify your login credentials
$username = "hawlast";
$apiKey   = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 
	
// Create a new instance of our awesome gateway class
$gateway    = new AfricasTalkingGateway($username, $apiKey);

// Any gateway errors will be captured by our custom Exception class below, 
// so wrap the call in a try-catch block
try
{ 
  // Fetch the data from our USER resource and read the balance
  $data = $gateway->getUserData();
  $apibal = $data->balance; 
  echo "<br/> Available A.T. float: $apibal";
}
catch ( AfricasTalkingGatewayException $e )
{
echo "Encountered an error while fetching user data: ".$e->getMessage()."\n";
}



?>
<br/>

<p><a href="affiliates.php" class="btn btn-success">Affiliates</a></p>
<p><a href="dashboard.php" class="btn btn-success">Dashboard</a></p>
<p><a href="airtime" class="btn btn-success">Send airtime</a></p>
<p><a href="sms" class="btn btn-success">Send SMS</a></p>
<p><a href="payments.php" class="btn btn-success">Buy more float</a><br/></p>
<p><a href="http://www.hawlast.com?a=saas" target="_blank" class="btn btn-success">Get a website</a> <br /></p>
<p><a href="statement.php" class="btn btn-success">Airtime  Statement</a> <br /></p>
<p><a href="pay2kes.php" class="btn btn-success">PayPal $ to KES</a> <br /></p>
<?php 

if ($_SESSION['access_level'] == 2 ) {

?>
<form method="POST" action="topup.php" />
<input type="number" name="amount" size="5" value="" required />
<br/>
<select name="id" size="1" required>
<?php
// get user list
$pdo = Database::connect();
$que = "SELECT airtime_user.airtimeid,airtime_user.fname,airtime_user.lname, sms_users.id, sms_users.bal FROM airtime_user,sms_users WHERE airtime_user.airtimeid = sms_users.airtimeid ORDER BY airtime_user.airtimeid ASC";
//Prepare the select statement.
$stmt = $pdo->query($que);
//Execute the statement.
//$stmt->execute();
//Retrieve the rows using fetchAll.
$users = $stmt->fetchAll();

foreach($users as $user): ?>

<option value="<?= $user['id']; ?>"><?= $user['fname'] . ' ' . $user['lname'] . ' ' . $user['bal']; ?></option>
    
<?php endforeach; 

Database::disconnect();

?>
</select>
<br/>
<input type="submit" name="topup" value="Top Up"><br/>
<br/>
</form>
<?php
}

?>
<p><a href="logout.php" class="btn btn-success">LOG OUT</a></p>
<!---User not signed in, display login form ---->
<?php
} else {
?>
<h2>HAWLAST SAAS</h2>
<p>Login to send bulk SMS at 1KES/SMS or transfer airtime.</p>
<p>The airtime will be sent to a safaricom or airtel number of your choice.</p>
 <form method="post" action="login.php" />
<input name="email" type="email" placeholder="Enter your email address" value="" required><br />
<input name="password" placeholder="Password" type="password" required><br />
<input type="submit" name="submit" value="Login"><br/>
<a href="register.php"><b>Register</b></a> 
<a href="login.php?reset=1"><b>Forgot password?</b></a><br/>
</form>
<p><b>You will pay via Airtel money/ PayPal/ Visa /MasterCard/MPESA</b></p>
<?php
}
?>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas</a></div></div></div>
</div>
</body></html>