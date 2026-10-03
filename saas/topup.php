<?php
require 'config.php';
require 'database.php';
require_once dirname(__DIR__) . '/hawlastke.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>TOP UP USER ACCOUNT</title>
<meta charset="utf-8">
<meta name="description" content="">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">

        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
 <link   href="css/bootstrap.min.css" rel="stylesheet">
 <script src="js/bootstrap.min.js"></script>
</head>
 
<body>
<div class="container-fluid">
<p><a href="./" class="btn btn-success">Home</a></p>
<p><a href="/saas/sms" class="btn btn-success">Send bulk SMS</a></p>


<br />
<?php
if (!empty($_POST['id'])) {

$amount = protect($_POST['amount']);
$amount = safe($_POST['amount']);

$id = protect($_POST['id']);
$id = safe($_POST['id']);
//echo $id;
echo "<br/>";
//echo $amount; 

$pdo = Database::connect();

$sql = "UPDATE sms_users SET bal = bal+$amount WHERE id=$id";
$stmt = $pdo->prepare($sql);
$stmt->execute();

if($stmt->rowCount() > 0)
{
echo "The account is topped up with KES $amount";
}

Database::disconnect();

} else {
echo "The form has not been posted with an amount.";
}
 ?>
<p><a href="logout.php" class="btn btn-success">Log out</a></p>

<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas/</a>
</div>
</div></div>
</body></html>