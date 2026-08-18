<?php
require 'config.php';
require 'database.php';
require 'functions.php';
error_reporting(-1);
?><!DOCTYPE html>
<html lang="en">
<head>
<title>PayPal to KES Convert</title>
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
<h3>PayPal to KES </h3>
<?php
 if(isset($_POST['amount'])) {
$usd= $_POST['amount'];
$exg = exchange();  //functions.php 
echo "Exchange rate is 1USD/KES " . $exg;
echo "<br/>If you send USD $usd <br/>";
$charge = $usd*0.065;
$usd2 = $usd - $charge - 1;
echo "Charges are 6.5% + 1 USD <br/>";
$amount = ceil($usd2 * $exg); 
echo "You will receive KES $amount via MPESA";
?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="charset" value="utf-8">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="support@hawlast.com">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="Hawlast USD 2 KES">
<input type="hidden" name="custom" value="<?php echo $_SESSION['valid_user']; ?>" >
<input type="hidden" name="amount" value="<?php echo $usd;  ?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="return" value="http://www.hawlast.com/saas/" />
<input type="hidden" name="rm" value="2" />
<input type="hidden" name="cbt" value="Hawlast Ventures" />
<input type="hidden" name="cancel_return" value="http://www.hawlast.com/saas/" />
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
<input type="image" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_buynow_pp_142x27.png" border="0" name="submit" alt="Pay using PayPal">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php

        }
        else
        {
?>
<br/>
Enter USD to convert to KES<br />
<form action="pay2kes.php" method="post">
<input type="number" name="amount" min="10" max="100" placeholder="From 10 - 100"><br/>
<input type="submit" name="submit" Value="Submit">
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