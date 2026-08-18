<?php
require '../config.php';
require '../database.php';
require '../functions.php';
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
<a href="../" class="btn btn-success">Home</a>   
<br />
<div><h2>Send bulk SMS</h2></div>
Send bulk SMS instantly, at 1KES/SMS.<br/><br/>
<?php 
$userid = $_SESSION['user_id'];
$pdo = Database::connect();
$sql = "SELECT bal FROM sms_users WHERE airtimeid=$userid";
foreach ($pdo->query($sql) as $row) {

echo '<b>Your float balance is KES ' . $row['bal'];
echo '</b><br />';
               }
Database::disconnect();
?>
<br/>
<div class=feat_prod_box_details>
<div class=contact_form>
<form name="demo" method="POST" action="sendsms.php">
<input id=user_id name=user_id type=hidden value="<?php echo $_SESSION['user_id']; ?>" />
<input id=bal name=bal type=hidden value="<?php echo $row['bal']; ?>" />

<div class=form_row>
<label class="contact"><b>Phone Numbers:</b></label>
<input id=Phone name=phone height="30px" type=text class=contact_input placeholder="07" rows="10" cols="50" required />
</div>
<div class=form_row>
<label class=contact><b>Message:</b></label>
<textarea id=Message name="message" rows="6" cols="25" class="contact_textarea" maxlength="160" required ></textarea>
</div>

<div class=contact>
<input id="Submit" name="Submit" value="Send SMS" class="button_text" type="submit"><br />
</div>

</form>


</div>

	<div>
 <p><a href="/saas/airtime" class="btn btn-success">Send airtime</a></p>
 <p><a href="http://www.hawlast.com/?a=saas" class="btn btn-success">Order a website</a></p>
<p><a href="../logout.php" class="btn btn-success">Log out</a> </p>

      </div>

</div>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">Hawlast.com/saas</a></div>
</div>

</div>

</body></html>