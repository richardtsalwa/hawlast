<div class=in>

<?php 
$ddomain = strtolower($_GET['domain']);

 $ext = '';
if (isset($_GET["ext"]))
{
    $ext = strtolower($_GET['ext']);
} else {

$ext = ".com";
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
<td align="center" width="100"> 50GB Disk Space </td>
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
<td align="center" width="80">Ksh. <?php echo totalhost($ext,$email); ?></td>
<td align="center" width="100">Ksh. <?php echo totalhost($ext,$starter); ?></td>
<td align="center" width="100">Ksh. <?php echo totalhost($ext,$premier); ?></td>
</tr>

<tr>
<td align="center" width="80">
<form method="get" action="reserve.php">
<input type="hidden" name="h" value="email">
<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>">
<input type="hidden" name="src" value="<?php echo $_GET['src']; ?>">
<input type="hidden" name="amount" value="<?php echo totalhost($ext,$email); ?>">
<input type="submit" value="Buy Now">
</form>
</td>

<td align="center" width="100">
<form method="get" action="reserve.php">
<input type="hidden" name="h" value="starter">
<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>">
<input type="hidden" name="src" value="<?php echo $_GET['src']; ?>">
<input type="hidden" name="amount" value="<?php echo totalhost($ext,$starter); ?>">
<input type="submit" value="Buy Now">
</form>
</td>

<td align="center" width="100">
<form method="get" action="reserve.php">
<input type="hidden" name="h" value="premier">
<input type="hidden" name="domain" value="<?php echo $_GET['domain']; ?>">
<input type="hidden" name="amount" value="<?php echo totalhost($ext,$premier); ?>">
<input type="hidden" name="src" value="<?php echo $_GET['src']; ?>">
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
<div class="no-thanks-parent"><a class="no-thanks-link" href="reserve.php?h=domain&domain=<?php echo $_GET['domain']; ?>&src=<?php echo $_GET['src']; ?>&amount=<?php echo domainprices($_GET['ext']); ?>">No thanks.Proceed and pay Ksh  <?php echo domainprices($_GET['ext']); ?> for the domain.</a>  <br /> Search <a href="<?php echo hawlast_url('domain-registration/'); ?>">for another domain name</a>.</div>

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