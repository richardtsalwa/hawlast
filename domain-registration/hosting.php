<?php
require '../hawlastke.php';
require '../config.php';
ob_start();
if ($_SERVER["REQUEST_METHOD"] <> "GET")  die("You can only reach this page by posting from the html form");
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
<title>Domain names Kenya, Cheap Domain registration in Kenya</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<meta name=Description content="Register a domain name instantly for free with our hosting plans. A domain name gives your business credibility and increase sales." />
<meta name=Keywords content="domain Kenya, kenya domain names, cheap domains, Domain registration Kenya" />
<link rel="stylesheet" href="../font-awesome.min.css" type="text/css">
<link rel="stylesheet" type="text/css" href="../style.css" />
<link rel="shortcut icon" href=../images/favicon.ico type=image/x-icon />
<style type="text/css">
.no-thanks-link{
        margin-top: 1px;
    }

 .no-thanks-link{
        text-decoration: none;
    }

.no-thanks-parent {
    background: #fff;
    padding: 1px 0;
    border-radius: 0 0 4px 4px;
}


a.no-thanks-link {
    background: #4CAF50;
    border: 1px solid #18900b;
    color: #fff;
    cursor: pointer;
    font-family: 'proxima_nova', Arial;
    font-size: 10px;
    font-weight: 10;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    text-shadow: none;
    height: 16px;
    line-height: 10px;
    width: 314px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}

.no-thanks-link {
    color: #2265ac;
    font: bold 10px Arial, Helvetica, sans-serif;
    display: block;
    text-align: center;
    padding: 0 0 1px 0;
    text-align: center;
    text-decoration: none;
    text-shadow: 1px 1px 1px #fff;
}
</style>
<?php
require __DIR__ . "/includes/header.php";
?>
</div>
<div class=center_content>
<div class=left_content>
<div class=in>

<?php 
$ddomain = strtolower($_GET['domain']);

if (!empty($_GET["ext"]))
{
$ext = strtolower($_GET['ext']);
} else {
$ext = "com";
}

if (!empty($_GET['src']))
{
$src = strtolower($_GET['src']);
} else {
$src ="none";
}

if ($ddomain !== "example.com") { 
?> 
<h1 style="color:#008000; align=center;">Your domain <?php echo $_GET['domain']; ?> is available!</h1> 
<br /><br />
<h3>Would you like to buy a hosting package? </h3> 
<br />
<?php 
} else {?>

<br />
<h1 style="color:#008000; align=center;">Select a web hosting package below: </h1> 
<?php

} ?>
<div class="pricingrow">
<div class="tableLong">
<table class="pricingTable" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<th width="80">
<strong>Email</strong>
</th>
<th width="100">
<strong>Starter</strong>
</th>
<th width="100">
<strong>Premier</strong>
</th>
</tr>


<tr>
<td align="center" width="80"> Single domain<br/><i> <b>1 free domain</b></i></td>
<td align="center" width="100"> Single domain<br/><i> <b>1 free domain</b></i></td>
<td align="center" width="100"> Unlimited domains<br/><i> <b>1 free domain</b></i></td>
</tr>

<tr>
<td align="center" width="80"> 20GB Disk Space </td>
<td align="center" width="100"> Unlimited Disk Space </td>
<td align="center" width="100"> Unlimited Disk Space </td>
</tr>

<tr>
<td align="center" width="80"> 10 Email accounts</td>
<td align="center" width="100"> 25 Email accounts </td>
<td align="center" width="100"> Unlimited Email accounts </td>
</tr>

<tr>
<td align="center" width="80"> Webmail access </td>
<td align="center" width="100"> Webmail access</td>
<td align="center" width="100"> Webmail access </td>
</tr>

<tr>
<td align="center" width="80"> - </td>
<td align="center" width="100"> FTP access </td>
<td align="center" width="100"> Control panel</td>
</tr>



<tr>
<td align="center" width="80">Ksh. <?php echo totalhost($ext,$email); ?>/year</td>
<td align="center" width="100">Ksh. <?php echo totalhost($ext,$starter); ?>/year</td>
<td align="center" width="100">Ksh. <?php echo totalhost($ext,$premier); ?>/year</td>
</tr>

<tr>
<td align="center" width="80">
<form method="get" action="reserve.php">
<input type="hidden" name="h" value="email">
<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>">
<input type="hidden" name="src" value="<?php echo $src; ?>">
<input type="hidden" name="amount" value="<?php echo totalhost($ext,$email); ?>">
<input type="hidden" name="ext" value="<?php echo $ext; ?>">
<input type="submit" value="Buy Now">
</form>
</td>

<td align="center" width="100">
<form method="get" action="reserve.php">
<input type="hidden" name="h" value="starter">
<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>">
<input type="hidden" name="src" value="<?php echo $src; ?>">
<input type="hidden" name="amount" value="<?php echo totalhost($ext,$starter); ?>">
<input type="hidden" name="ext" value="<?php echo $ext; ?>">
<input type="submit" value="Buy Now">
</form>
</td>

<td align="center" width="100">
<form method="get" action="reserve.php">
<input type="hidden" name="h" value="premier">
<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>">
<input type="hidden" name="amount" value="<?php echo totalhost($ext,$premier); ?>">
<input type="hidden" name="src" value="<?php echo $src; ?>">
<input type="hidden" name="ext" value="<?php echo $ext; ?>">

<input type="submit" value="Buy Now">
</form>
</td>
</tr>
<?php
if ($ddomain !== "example.com") { 
?>
<tr>
<td class="leftCol" colspan="5">
<br /><br />
<div class="no-thanks-parent"><a class="no-thanks-link" href="reserve.php?h=domain&domain=<?php echo $_GET['domain']; ?>&src=<?php echo $src; ?>&amount=<?php echo domainprices($ext); ?>">No thanks.Proceed and pay Ksh  <?php echo domainprices($ext); ?> for the domain.</a>  <br /> Search <a href="<?php echo hawlast_url('domain-registration/'); ?>">for another domain name</a>.</div>

</td>
</tr>
<?php
} else {
?>
<tr>
<td class="leftCol" colspan="5"><div>Search <a href="<?php echo hawlast_url('domain-registration/'); ?>">for another domain name</a>.</div>
</td>
</tr>

<?php
}
?>

</tbody>
</table>
<br />
- FTP and cpanel access on Premier plan<br />
- FTP access on Starter plan<br />
- 24/7 support<br /> 
- Pay via MPESA /Cheque /PayPal<br /> 
- Free Microsoft Outlook set up<br /> 
- Access the emails on your mobile phone<br /> 
- Free email marketing set up (500 subscribers)<br /> 

</div>
</div>

</div>
<div class=clear></div>

</div>
<div class=right_content>
<?php
require __DIR__ . "/includes/other_left.php";
?>
</div>
<?php
require __DIR__ . "/includes/footer.php";
?>

</body></html>