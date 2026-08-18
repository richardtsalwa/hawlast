<?php
require 'config.php';
require 'database.php';
require 'functions.php';
?><!DOCTYPE html>
<html lang="en">
<head>
<title>Airtime and SMS app</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">

        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    $(".aboveage1").click(function(){

    if ($('input[name=pay]:checked').val() == "airtel" ) {
       $( "<div id=form_cash class=replace><br/>Go to your Airtel line<br/>Go to Airtel money<br /> Select send money<br/>Select phone number<br/>Enter <b>0735187782</b><br/>Enter amount and press OK<br/>Enter PIN and press OK<br/>Reference enter HV<?php echo $_SESSION['user_id']; ?><br/>Then give us 5 minutes to load your account.</div>" ).replaceAll( ".replace" );

        } 

    if ($('input[name=pay]:checked').val() == "mpesa" ) {
       $( "<div id=form_cash class=replace><br/>Go to your  M-PESA Menu<br/>Go to Lipa Na M-PESA<br /> Select Pay Bill <br/>Enter business number <b>525 900</b><br/>Account <b>hawlast.api</b><br/>Enter amount and press OK<br/>Enter PIN and press OK<br/><br/>Then give us 5 minutes to load your account.</div>" ).replaceAll( ".replace" );

        } 

       if ($('input[name=pay]:checked').val() == "coop" ) {
       $( "<div id=form_cheque class=replace><br/>Deposit the amount at Coop Agent/bank<br/>Account number 01148129850600<br/>Whatsapp the slip to 0720401869</div>" ).replaceAll( ".replace" );
        } 

       if ($('input[name=pay]:checked').val() == "paypal" ) {
      $( "<div id=form_cheque class=replace><br/><a href=pay.php><img src=https://www.paypalobjects.com/webstatic/en_US/btn/btn_buynow_pp_142x27.png alt=PayPal /></a></div>" ).replaceAll( ".replace" );
        } 
       });
});
</script>
</head>
<body>
<div class="container-fluid">
<a href="./" class="btn btn-success">Home</a>   
<br />
<h3>Float Top up</h3>

<br/>
<br/>
<b>Select a mode of payment:</b>
<div class=form_row>
<input type="radio" name="pay" value="mpesa" class="aboveage1" />MPESA</input>
<input type="radio" name="pay" value="airtel" class="aboveage1" />Airtel</input>
<input type="radio" name="pay" value="coop" class="aboveage1" />Coop Bank</input>
<input type="radio" name="pay" value="paypal" class="aboveage1" />PayPal</input>
</div>
<div class=replace>
</div>
<br/>

<p><a href="/saas/sms" class="btn btn-success">Send bulk SMS</a></p>
<p><a href="logout.php" class="btn btn-success">Log out</a></p>

<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas</a></div></div></div>
</div>
</body></html>