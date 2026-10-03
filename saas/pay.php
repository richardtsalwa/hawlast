<?php
require 'config.php';
require 'database.php';
require_once dirname(__DIR__) . '/hawlastke.php';
error_reporting(-1);
?><!DOCTYPE html>
<html lang="en">
<head>
<title>Pay : : Airtime and SMS app</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script></head>
<body>
<div class="container-fluid">
<a href="./" class="btn btn-success">Home</a>   
<br />
<h3>PayPal to KES Top up</h3>
<?php
 if(isset($_POST['amount'])) {
$usd= $_POST['amount'];
$exg = exchange();  //functions.php 
echo "Exchange rate is 1USD/KES " . $exg;
echo "<br/>";
$charge = $usd*0.034;
$usd2 = $usd - $charge - 0.30;
$amount = ceil($usd2 * $exg * 1.04); 
echo "You are about to pay USD " . $usd ."  for KES " . $amount;
?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="charset" value="utf-8">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="support@hawlast.com">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="Hawlast SaaS Float">
<input type="hidden" name="amount" value="<?php echo $usd;  ?>">
<input type="hidden" name="custom" value="<?php echo $_SESSION['valid_user']; ?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="return" value="http://www.hawlast.com/saas/" />
<input type="hidden" name="rm" value="2" />
<input type="hidden" name="cbt" value="Hawlast Ventures" />
<input type="hidden" name="cancel_return" value="http://www.hawlast.com/saas/payments.php" />
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
<input type="image" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_buynow_pp_142x27.png" border="0" name="submit" alt="Pay with PayPal">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>



<?php

        }
        else
        {
?>
<br/>
Enter Amount (USD) you intend to send
<form action="pay.php" method="post">
<input type="number" name="amount" min="5" max="100" placeholder="Enter 5 -100"><br/>
<input type="submit" name="submit" value="Submit">
</form>

<?php
        }

?>
<p><a href="/saas/sms" class="btn btn-success">Send bulk SMS</a></p>
<a href="logout.php" class="btn btn-success">Log out</a>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas</a></div></div></div>
</div>
</body></html>