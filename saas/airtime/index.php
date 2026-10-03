<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__) . '/auth.php';
require '../database.php';
require_once dirname(__DIR__, 2) . '/hawlastke.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Airtime and SMS app</title>
 <meta charset="utf-8">
<meta name="description" content="">
 <meta name="HandheldFriendly" content="True">
 <meta name="MobileOptimized" content="320">
 <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
 <link rel="shortcut icon" href="../img/favicon.ico" type=image/x-icon />
 <link   href="../css/bootstrap.min.css" rel="stylesheet">
 <link   href="../css/main.css" rel="stylesheet">
 <link   href="../css/menu.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
</head>
 <body>
<div class="container-fluid">
<a href="../" class="btn btn-success">Home</a>   
<br />
 <div><h3>Airtime Purchase</h3></div>
<?php
 if (isset($_POST['submit'])) {

    $errors = array();
    if (!isset($_POST['bal'])){ 
    $errors[] = "Enter the float amount.";
    } else {
        $bal = protect($_POST['bal']);
    }
	

if (empty($_POST['phone'])){ 
    $errors[] = "Enter a phone number.";} else {
	      $phone = protect($_POST['phone']);
		  $phone = add254($phone);
	      if(!preg_match('/^(\+254|0)[1-9]\d{8}$/', $phone)) {  
		  $errors[] = "Enter a phone number in the format 07xxyyyzzz";
		  } 
    }
	
    if ($_POST['amount'] < 4){ 
    $errors[] = "The amount should be 5 and above";
    } 
	
	if ($_SESSION['credit'] == 'N' ){ 
   //Logged in user must have enough floatbal
    if ($_POST['amount'] > $bal ){ 
    $errors[] = "The amount is greater than your float balance.";
    } 

	$id = $_SESSION['user_id'];
	$status = "Sent";
	$pdo = Database::connect();
	$sql = "SELECT SUM(amount) as toamount FROM airtime_transactions WHERE airtimeid='$id' and status ='$status'";
	foreach ($pdo->query($sql) as $row) {
	$row = $row['toamount']; 
		$rowek = $row + $_POST['amount'];
	if ($rowek > $bal ) { $errors[] = "Or some pending transaction(s) exceed your float."; } 
                  } 
	Database::disconnect();
   }
  
    if (isset($_POST['amount'])){ 
    if (!isInteger($_POST['amount'])){ 
    $errors[] = "Enter the amount of airtime.";
    } else {
    $amount = protect($_POST['amount']);
   if ($_POST['apibal'] < $amount) { 
   // $errors[] = "HAWLAST error code 001";
    } 
    }
	} else {
    $errors[] = "Enter the amount of airtime to transfer";
    $amount = "0";
    }
	
    if(count($errors) > 0){
	foreach($errors AS $error){
	echo "<b style=\"color:brown;\">";
	echo $error;
	echo "</b><br />";
	}

//////////////////////////////////////
?>
<p><a href="/saas/airtime" class="btn btn-success">Try again</a></p>
<br />
<br />
<?php
 } else {   	
?>
<div class="notice">This transaction is irreversible; <b>reconfirm</b></div>
<form method="post" action="buynew.php" />
<input type="text" name="phone" length="11" value=<?php echo $phone; ?> required /><br />
<input type="text" name="amount" value=<?php echo $amount; ?> required /><br />
<input type="hidden" name=bal value="<?php echo $bal; ?>" />

<input type="hidden" name="TransID" value="<?PHP $randomId = generateHVRandomString(); echo strtoupper($randomId); //Starts with HV 17 characters ?>" />

<input type="submit" value="Confirm">
</form>
<br />
<br />
<p><a href="index.php" class="btn btn-success">GO BACK</a></p>
<?php	
}
//The user has not submitted data
} else { 
if (isset($_GET['id'])) {
$requestid = $_GET['id'];
?>
<div class="menu_simple">
<ul>
<?php
$pdo = Database::connect();
$sql = "SELECT * FROM airtime_transactions WHERE requestid ='$requestid'";
foreach ($pdo->query($sql) as $row) {
echo '<li><b>Date and Time</b></li>';

echo '<li>'. $row['date'] . '</li>';

echo '<li><b>Receivers no</b></li>';

echo '<li>'. $row['phone'] . '</li>';

echo '<li><b>Amount</b></li>';
echo '<li>'. $row['amount'] . '</li>';

echo '<li><b>Status</b></li>';

echo '<li>'. $row['status'] . '</li>
</ul></div>';
}
Database::disconnect();
if ($row['status'] == "Sent") {
?>
<form method="get" action="<?php $path = $_SERVER['REQUEST_URI']; $url = 'https://'.$_SERVER['HTTP_HOST'].$path; echo $url; ?>" />
<input type="hidden" name="id" value=<?php echo $requestid; ?> />
<input type="submit" value="Refresh">
</form>
<?php
}
} 
?>
<?php
///THE BALANCE QUERY FOR MEMBERS
$id = $_SESSION['user_id'];
$pdo = Database::connect();
$sql = "SELECT bal FROM sms_users WHERE airtimeid=$id";
foreach ($pdo->query($sql) as $row) {

echo '<b>Your balance is KES ' . $row['bal'] . '</b><br/>';
                  }
Database::disconnect();

/// THE AIRTIME BALANCE FROM AT
// Be sure to include the library loader
require_once dirname(__DIR__, 2) . '/libraries.php';

// Specify your login credentials

// Create a new instance of our awesome gateway class
$AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);

// Any gateway errors will be captured by our custom Exception class below, 
// so wrap the call in a try-catch block
try
{ 
  // Fetch the data from our USER resource and read the balance
  $appData = $AT->application()->fetchApplicationData();
  $apibal = $appData['data']->UserData->balance; 
 $apibal =   kes($apibal);
}
catch ( Throwable $e )
{
echo "Encountered an error while fetching user data: ".$e->getMessage()."\n";
}
?>
<?php echo $_SESSION['credit']; ?>
<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
<div class=form_row>
<label><b>Recipient's number<>:</b></label>
<input id=Phone name=phone type=text value="" placeholder="07" required />
</div>
<input id=bal name=bal type=hidden value="<?php echo $row['bal']; ?>" />
<input id=bal name=apibal type=hidden value="<?php echo $apibal; ?>" />
<div class=form_row>
<label><b>Airtime amount > 9:</b></label>
<input id=amount name=amount type=text required />
</div>
<input type="submit" name="submit" value="Buy airtime" />
</form>
<?php
if ($_SESSION['access_level'] == 2 ) {
echo $apibal; 
}
?>
<br />
<p><a href="/saas/sms" class="btn btn-success">Send bulk SMS</a></p>
<p><a href="https://www.hawlast.com/?a=saas" class="btn btn-success">Get a website</a></p>
<p><a href="../payments.php" class="btn btn-success">Buy more float</a></p>
<p><a href="../logout.php" class="btn btn-success">Log out</a> </p>
<?php
}
?>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com">hawlast.com</a></div>
</div>
</div>
</body></html>