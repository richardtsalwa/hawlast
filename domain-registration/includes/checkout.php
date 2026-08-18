<?php if (!isset($_SESSION['hosting'])){header("Location: ./"); exit(); } ?> 
<div class=left_content><div class=in><h1>Payments (Step 3 of 3):</h1><br /><br />
Your details: <b><?php echo $_SESSION['fname']; ?></b>
<?php echo " "; ?>
<b><?php echo $_SESSION['lname']; ?></b>
<br />
Hosting plan: <b><?php echo safe($_SESSION['hosting']); ?></b> <br />
Domain: <b> <?php echo $_SESSION['ddomain']; ?></b> <br />
Your email: <b><?php echo $_SESSION['email']; ?></b><br />
Your phone: <b><?php echo $_SESSION['phone']; ?></b><br />
Total cost: <b>Ksh. <?php echo $_SESSION['totalcost']; ?></b><br />
<?php $randacc = rand(1000,9999); ?>
<br />
<?php
if (isset($_SESSION['payments']) && $_SESSION['payments'] == "paypal"){ ?>
<br />
<b>PAY VIA PAYPAL (Click the button below:)</b>
<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="SRCCCZLH9ZBJW">
<input type="hidden" name="lc" value="KE">
<input type="hidden" name="item_name" value="Web hosting">
<input type="hidden" name="amount" value="<?php $total=$_SESSION['totalcost']; echo round(($total)/$dollar); ?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="rm" value="1">
<input type="hidden" name="return" value="https://www.hawlast.com/domain-registration/paypal.php">
<input type="hidden" name="cancel_return" value="https://www.hawlast.com/domain-registration/reserve.php?checkout=1">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
<input type="image" src="images/paypal.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<br />
<br />
<?php 
} 

if (isset($_SESSION['payments']) && $_SESSION['payments'] == "mpesa"){ 
if(isset($_SESSION['stkerror'])){
//session_unset();
//session_destroy();
if(isset($_SESSION['mpesaerror'])){
echo "<span STYLE=\"color:red\"><b>We still can not validate the payment. Try again or call 0720401869.</b></span><br>";
}
?>
<b></b><br>
1. Select <b>Lipa na M-PESA</b><br /> 
2. Select <b>MPESA PAYBILL </b><br /> 
3. Enter <b>Business No: 822490</b><br /> 
4. Enter Account number  <b><?php echo "HV"; echo $randacc; ?></b><br /> 
5. Enter the Amount <b>Ksh. <?php echo $_SESSION['totalcost']; ?></b> <br /> 
6. Enter your M-PESA PIN and Send <br /> 
7. You will receive an SMS from M-PESA with a confirmation Code<br />
8. Enter the M-PESA reference below: 
<br />
<form action="reserve.php" method="post">
<input type="text" name="TransID" value="" maxlength="10" required>
<input type="hidden" name="TransAmount" value="<?php echo $_SESSION['totalcost']; ?>">
<input type="hidden" name="BillRefNumber" value="<?php echo "HV"; echo $randacc; ?>">
<input type="hidden" name="id" value="<?php echo $randacc; ?>">
<input type="submit" name="submit" value="Submit">
</form>
<br />
<?php
} else { 

?>
<br />
<b>PAY VIA M-PESA STK PUSH MAGIC</b><br>
Is the number on M-PESA? Update if need be.<br>
Unlock your phone screen and press "Pay".
<form action="reserve.php" method="post">
<input type="hidden" name="amount" value="<?php echo $_SESSION['totalcost']; ?>">
<input type="number" name="phone" value="<?php echo $_SESSION['phone']; ?>" required>
<input type="hidden" name="account" value="<?php echo "HV"; echo $randacc; ?>">
<input type="hidden" name="id" value="<?php echo $randacc; ?>">
<input type="submit" name="submit" value="Pay">
</form>
<?php
}
?>


<?php 
} 


if (isset($_SESSION['payments']) && $_SESSION['payments'] == "airtel"){ ?>
<br />
<br />
<b>PAY VIA AIRTEL MONEY<!---PAY BILL---></b>
<br />
1. Go to Airtel menu <br />
2. Select <b>Airtel Money</b><br /> 
3. Select <b>Send money</b><br /> 
4. Select <b>Phone number</b><br /> 
5. Enter the phone number 0735187782 <br /> 
6. Enter the Amount <b>Ksh. <?PHP echo $_SESSION['totalcost']; ?></b> <br /> 
7. Enter your Airtel money PIN and Send <br /> 
8. You will receive an SMS from Airtel with a confirmation Code<br />
9. Enter the Airtel reference number below:
<br />
<form action="mpesa.php" method="post">
<input type="text" name="reference" value="" maxlength="15">
<input type="hidden" name="phone" value="<?php echo $_SESSION['phone']; ?>" maxlength="15">
<input type="submit" name="submit" value="Submit">
</form>
<br />
<br />
Once done, we will verify the payments and email the hosting details to <b><?php echo $_SESSION['email']; ?></b>
<br />
<?php 
} 

if (isset($_SESSION['payments']) && $_SESSION['payments'] == "cheque"){ ?>
<br /><br />
Cheques should be payable to <B>HAWLAST VENTURES</B>
<br />
<?php
}
?>