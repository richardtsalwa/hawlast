<?php
require '../config.php';
require '../database.php';
require_once dirname(__DIR__, 2) . '/hawlastke.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Send bulk SMS</title>
 <meta charset="utf-8">
<meta name="description" content="">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
<link rel="shortcut icon" href="../img/favicon.ico" type=image/x-icon />
<link   href="../css/bootstrap.min.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<a href="../index.php" class="btn btn-success">Home</a>   
<br />
<p>Thank you. Message sent. Send messages are at  <a href="<?php echo $_SESSION['user_id'].".txt"; ?>" target="_blank">here.</a></p>
<div><b><?php 
$userid = $_SESSION['user_id'];
$pdo = Database::connect();
$sql = "SELECT bal FROM sms_users WHERE airtimeid=$userid";
foreach ($pdo->query($sql) as $row) {

echo '<b>Your float balance is KES ' . $row['bal'];
echo '<br />';
               }
Database::disconnect();
?>

</b> </div>

<p><a href="/saas/airtime" class="btn btn-success">SEND AIRTIME</a></p>
<p><a href="http://www.hawlast.com/domain-registration/" target="_blank" class="btn btn-success">ORDER A WEBSITE</a></p>
<p><a href="../logout.php" class="btn btn-success">Log out</a></p>

<div><b>You can send more SMS at the <a href="http://www.hawlast.com/saas/sms/">SMS Page</a></b> </div>

<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas/</a></div>
</div>

</div>

</body></html>