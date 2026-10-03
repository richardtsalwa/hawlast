<?php
require 'hawlastke.php';
require 'config.php';
if(isset($_GET['a'])) {
$cookie_name = "affiliate";
$cookie_value = $_GET['a'];
setcookie($cookie_name, $cookie_value, time() + (86400 * 60), "/");
//Cookie is available on entire website and will expire after 60 days
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta name="google-site-verification" content="YZPXJE-6W2iSvgrFkQPcGRTh3md86Qv7PnNa0DTcA2g" />
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<meta name=keywords content="Kenya web hosting, wordpress hosting,  website hosting kenya, kenya hosting, web host, joomla hosting." />
<meta name=description content="Domain registration, web hosting and web design and online marketing in Kenya. Hosting Wordpress website with a free domain." />
<title>WordPress Web Hosting & Domain Name Registration - Kenya</title>
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />
<link rel=stylesheet type=text/css href=style.css />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css">
<link rel=stylesheet type=text/css href=style.css />
<link rel=stylesheet type=text/css href=responsive.css />

<script  type="application/ld+json" id="website-json-ld">
{
    "@context":"http://schema.org",
    "@type":"WebSite",
    "name":"HAWLAST",
    "alternateName":"HAWLAST VENTURES",
    "url":"https://www.hawlast.com"
}
</script>
<script  type="application/ld+json" id="social-json-ld">
{
    "@context":"http://schema.org",
    "@type":"Organization",
    "name":"Hawlast Ventures",
    "url":"https://www.hawlast.com",
    "logo": "https://www.hawlast.com/images/domain-registration.gif",
    "sameAs":[
        "https://www.facebook.com/hawlast",
        "https://twitter.com/hawlast"
    ]
}
</script>

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@hawlast">
<meta name="twitter:title" content="Get started with your website or blog idea now.">
<meta name="twitter:description" content="Website or Blog? Get started. 90% of clients working with us keep growing their sales and profits.">
<meta name="twitter:creator" content="@RichardTsalwa">
<!-- Twitter Summary card images must be at least 120x120px -->
<meta name="twitter:image" content="https://www.hawlast.com/images/hawlast-get-online.jpg">

<!-- Open Graph data -->
<meta property="og:title" content="Get started with your website or blog idea now." />
<meta property="og:type" content="article" />
<meta property="og:url" content="https://www.hawlast.com/" />
<meta property="og:image" content="https://www.hawlast.com/images/hawlast-get-online.jpg" />
<meta property="og:description" content="Get started with your website or blog idea today." /> 
<meta property="og:site_name" content="Hawlast" />
<meta property="fb:admins" content="1166791883" />
<meta property="fb:app_id" content="202015096126" />

<?php require("includes/header.txt"); ?>
</div>
<div class=center_content>
<div class=left_content>
<?php
if ((date('m')==12)&& (date('d')==25)) { ?>
<div class=in><h1>Merry Christmas</h1> <br />
<p><img title="Mombasa Christmas" src="/images/christmas-red.jpg" alt="Merry Christmas" width="500" height="137" /></p> 
<?php } else { 
echo "<div class=in><h1>Web design and hosting &ndash; Kenya</h1> <br />";
} ?>

<br />
Do you need a website? Start by registering a <b>FREE domain name*</b> below. <b>Register your domain name</b>:<br /><br />
<form method="get" action="domain-registration/domain.php">
    <label><b>www.</b></label>
    <input type="text" name="domain">
    <select size="1" name="ext">
    <option value="ke">.ke</option>
    <option selected value="com">.com</option>
    <option value="net">.net</option>
    <option value="org">.org</option>
    <option value="info">.info</option>
    <option value="biz">.biz</option>
    <option value="co.ke">.co.ke</option>
    <option value="info.ke">.info.ke</option>
    <option value="mobi.ke">.mobi.ke</option>
    <option value="or.ke">.or.ke</option>
    <option value="ac.ke">.ac.ke</option>
    <option value="me.ke">.me.ke</option>
    </select>
    <input type="hidden" name="option" value="check">
   <input type="hidden" name="src" value="home">
    <input type="submit" value="Check">
    </form>
Already own a domain name? <a href="domain-registration/hosting.php?domain=example.com&src=home"><b>Add emails & hosting.</b></a>
<br />
<br />
<br />
<h2>Web Hosting Plans: </h2>
<div class="pricingrow">
<div class="tableLong">
<table class="pricingTable" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<th width="80">
<strong>Email</strong>
</th>
<th width="80">
<strong>Starter</strong>
</th>
<th width="80">
<strong>Premier</strong>
</th>
</tr>

<tr>
<td align="center" width="80"> 2 GB Disk Space </td>
<td align="center" width="80">  32 GB Disk Space </td>
<td align="center" width="80"> Unlimited Disk Space </td>
</tr>

<tr>
<td align="center" width="80"> 100 GB Monthly Bandwidth</td>
<td align="center" width="80">  400 GB Monthly Bandwidth</td>
<td align="center" width="80"> Unlimited Bandwith </td>
</tr>

<tr>
<td align="center" width="80"> 10 Email accounts</td>
<td align="center" width="80"> 25 Email accounts </td>
<td align="center" width="80"> Unlimited Email accounts </td>
</tr>

<tr>
<td align="center" width="80">Python, MySQL & PHP 
</td>

<td align="center" width="80"><a href="/blog/a-beginners-guide-to-python-programming.htm">Python</a>, MySQL &  PHP 
</td>
<td align="center" width="80"> Python, MySQL & PHP   </td>
</tr>
<tr>
<td align="center" width="80"> Control Panel </td>
<td align="center" width="80"> Control Panel </td>
<td align="center" width="80"> Control panel</td>
</tr>

<tr>
<td align="center" width="80">Ksh. <?php echo $email; ?>/ year</td>
<td align="center" width="80">Ksh. <?php echo $starter; ?>/ year</td>
<td align="center" width="80">Ksh. <?php echo $premier; ?>/ year</td>
</tr>

<tr>
<td align="center" width="80">
<form method="post" action="domain-registration/">
<input type="hidden" name="hosting" value="email">
<input type="hidden" name="src" value="tdhome">
<input type="hidden" name="amount" value="<?php echo $email; ?>">
<input type="submit" value="Buy Now">
</form>
</td>
<td align="center" width="80">
<form method="post" action="domain-registration/">
<input type="hidden" name="hosting" value="starter">
<input type="hidden" name="src" value="tdhome">
<input type="hidden" name="amount" value="<?php echo $starter; ?>">
<input type="submit" value="Buy Now">
</form>
</td>

<td align="center" width="80">
<form method="post" action="domain-registration/">
<input type="hidden" name="hosting" value="premier">
<input type="hidden" name="src" value="tdhome">
<input type="hidden" name="amount" value="<?php echo $premier; ?>">
<input type="submit" value="Buy Now">
</form>
</td>
</tr>
<tr>
<td class="leftCol" colspan="3"><div>Search <a href="/domain-registration/">for a domain name</a>.
</div>
</td>
</tr>
</tbody>
</table>
<br/>
- Cpanel access on all plans<br />
- 24/7 support<br /> 
- Pay via MPESA PAYBILL /Paypal<br /> 
- Free Microsoft Outlook set up<br /> 
- Access the emails on your mobile phone<br /> 

</div>
</div>

</div>
<div class=clear></div>
</div>
<div class=right_content>
<div class=about>

<H3 STYLE="color:green" align:centre;>OVER <?PHP  echo birthday('2010-05-10'); ?> YEARS OF WEB HOSTING</H3>
<br />
<b>Register your domain name</b>:<br/><br/>
<form method="get" action="domain-registration/domain.php">
<label><b>www.</b></label>
<input type="text" name="domain">
  <select size="1" name="ext">
    <option value="ke">.ke</option>
    <option selected value="com">.com</option>
    <option value="net">.net</option>
    <option value="org">.org</option>
    <option value="info">.info</option>
    <option value="biz">.biz</option>
    <option value="co.ke">.co.ke</option>
    <option value="ke">.ke</option>
    <option value="info.ke">.info.ke</option>
    <option value="mobi.ke">.mobi.ke</option>
    <option value="or.ke">.or.ke</option>
    <option value="ac.ke">.ac.ke</option>
    <option value="me.ke">.me.ke</option>
    </select>
<input type="hidden" name="option" value="check">
<input type="hidden" name="src" value="homeright">
<input type="submit" value="Check">
</form>
Already own a domain name? <a href="domain-registration/hosting.php?domain=example.com&src=homeright"><b>Add emails & hosting.</b></a>
<br/>
<br />

<?php

if(isMobileDevice()){ ?>
<a href="whatsapp://send?phone=+254720401869&text=%2FI%20need%20more%20information%20on%20web%20hosting"><img src="/images/WhatsApp.png" alt="Chat with us on WhatsApp"></a>
<?php }
else {
?>
<a href="https://api.whatsapp.com/send?phone=+254720401869&text=%2FI%20need%20more%20information%20on%20web%20hosting"><img src="/images/WhatsApp.png" alt="Chat with us on WhatsApp"></a>
<?php
}
?>

<br />
<br />
<div class="fb-like" data-href="https://www.facebook.com/hawlast/" data-layout="standard" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>
<br />
<br />
<a href="https://twitter.com/hawlast?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-lang="en" data-show-count="true">Follow @hawlast</a><br />
</div>
<div class=in>
<br />

</div>
<div class=facebook>
<iframe src="https://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FHawlast%2F161102860571010&amp;width=292&amp;connections=10&amp;stream=false&amp;header=true&amp;height=287" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:287px;" allowTransparency="true"></iframe>
</div>

</div>
<div class=clear></div>
</div>
<?php
require("includes/footer.txt");
?>
<script async type="text/javascript">FB.init("b19b13ba56c51b3fb7c239f8a4d65c8b");</script> 
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
</body></html>