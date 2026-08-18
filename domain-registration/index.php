<?php
require '../hawlastke.php';
require '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<meta name="description" content="Register a domain name. A domain name gives your business credibility and increase sales." />
<meta name="keywords" content="domain Kenya, kenya domain names, cheap domains, Domain registration Kenya" />
<title>Domain names Kenya, Cheap Domain registration in Kenya</title>
<link rel=stylesheet type=text/css href=../style.css />
<link rel="stylesheet" href="../font-awesome.min.css" 
type="text/css">
<link rel="stylesheet" type="text/css" href="../responsive.css">
<link rel="shortcut icon" href=../images/favicon.ico type=image/x-icon />
<style>
  .alert {
   background-color:yellow; text-align:center; font-color: red; color:red;  border-color: green;
    border-width: 1px;
    border-style: solid;
    border-radius: 20%;
  }
</style>
</head>
<body>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-16346251-1', 'auto');
  ga('send', 'pageview');

</script>
<div id="wrap">
<div class="header">
<div class="logo"><a href="#"><img src="../images/domain-registration.gif" height="60" width="246" alt="HAWLAST.COM" title="Domain registration in Kenya" /></a>
</div>
<div id="menu">
<ul>
<li><a href="../">Home</a></li>
<li class="selected"><a href="../domain-registration/">Domain Registration</a></li>
<li><a href="../software.html">Software</a></li>
<li><a href="../portfolio.html">Portfolio</a></li> 
<li><a href="../blog/">Blog</a></li>
<li><a href="../online-marketing.html" title="Online Marketing in Kenya">Online Marketing</a></li>
<li><a href="../affiliates.php">Affiliates</a></li>
<li><a href="../faq.html">FAQ</a></li>
<li><a href="../contactus.html">Contacts</a></li>
</ul>
</div>
</div>
<div class="center_content"><div class="left_content">
<div class=in>
<h1>Step 1: What do you want to call your website/blog?</h1><br /> <br /> 

<!-- Find out if this is index page at domain-registration and provide domain search form --->
<br />

<b>Enter a name you would like to register for your website (domain name):</b>
<form method="get" action="domain.php">
    <label><b>www.</b></label>
    <input type="text" name="domain">
    <select size="1" name="ext">
    <option selected value="com">.com</option>
    <option value="net">.net</option>
    <option value="org">.org</option>
    <option value="or.ke">.or.ke</option>
    <option value="info">.info</option>
    <option value="biz">.biz</option>
    <option value="co.ke">.co.ke</option>
    <option value="ke">.ke</option>
    <option value="travel">.travel</option>
    <option value="africa">.africa</option>
    <option value="academy">.academy</option>
    <option value="ac.ke">.ac.ke</option>
    <option value="sc.ke">.sc.ke</option>
    <option value="me.ke">.me.ke</option>
    <option value="mobi.ke">.mobi.ke</option>
    <option value="info.ke">.info.ke</option>
    </select>
    <input type="hidden" name="option" value="check">
    <input type="hidden" name="src" value="dr">
    <?php if (isset($_POST['hosting']))
    { ?>
    <input type="hidden" name="hosting" value="<?php echo $_POST['hosting']; ?>">
   <?php } ?>

<input type="submit" value="Check">
</form>

<?php
if(isset($_SESSION['ddd']))
{?>
 <div class="alert"><?php echo $_SESSION['status']; echo ".<br/> "; echo $_SESSION['ddd']; unset($_SESSION['ddd']); unset($_SESSION['status']); session_destroy(); ?></div>
<?php
} ?>


<?php
if (isset($_POST['hosting']))
{
?>
<p><b><a href="./reserve.php?h=<?php echo $_POST['hosting']; ?>&domain=example.com&src=dr">Already own a domain name?Skip the domain search</b></a></p>
<?php

} else {?>
<p> <b>Already own a domain name? <a href="./hosting.php?domain=example.com&src=dr">Add emails and hosting.</b></a></p>
<?php
}
?>
<!--DOMAIN RESULTS-->
</p>
<br />
<br />
<h2>How to choose a domain name for registration.</h2>
<p>We register .africa, .com, .net, .org, .biz, .info domains <b>free</b> if you are buying any of our hosting plans.Read here on <a href="/blog/web-hosting-and-domain-registration-the-difference.htm">the difference between domain registration and hosting.</a></p>

<p>
A domain name can help you rank better in search engines. Therefore, choose a <b>domain name</b> that includes a few of the keywords that relate to your website's content. Also, go for the shortest available domain name and avoid domain names that have hyphens and underscores because these are not easy for users to remember. 
</p>
<p>
You can choose to register domains with extensions such as .com, .net, .biz, .info, .org, and also the Kenya cTLD domains aka .ke with options to register a domain like .co.ke,or.ke and ac.ke.
</p>
<p>
A domain whois lookup enables to get details on when a domain name was registered, who owns it and where it is hosted. You can use the domain search form above to do a whois lookup for any given domain name.<br />	
</p>
</div><div class=clear></div></div>
<div class="right_content">
<div class="about">
<br />
<H3 STYLE="color:green" align:centre;>OVER <?PHP  echo birthday('2010-05-10'); ?> YEARS OF WEB HOSTING</H3>
<H3 STYLE="color:green" align:centre;>WE ARE HOSTING OVER
<?php
$sql ="SELECT * FROM renewals"; 
$result = $pdo->prepare($sql); $result->execute(); echo $result->rowCount(); ?> WEBSITES </H3>
<br />
<fb:like href="http://www.facebook.com/pages/Hawlast/" show_faces="true" width="250" action="like"></fb:like>
<br />
<br />
<!-- Place this tag where you want the +1 button to render -->
<g:plusone annotation="inline"></g:plusone>
<br />
<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://www.hawlast.com" data-text="Web Hosting in Kenya" data-count="horizontal" data-via="hawlast">Tweet</a>
<br />
</div>
<div class=about>
<h2>Domain registration and hosting</h2><p>
Thank you for choosing hawlast.com. <br />
1 free domain forever (.com,.org,.biz,.net,.info) <br />
(Extra Ksh. <?php echo  $coke; ?> per year for the co.ke domain)<br />
Free Joomla or Wordpress set up <br />
Free email autoresponder <br />
Free Microsoft Outlook set up<br />
Read emails on your mobile phone <br />
Free email marketing set up (500 subscribers)<br />
Your account will be up <b>10 minutes after payments</b> <br />
24/7 support <br />
Pay via MPESA /Cheque /Direct deposit<br />
<br /><br />

<br /><br />
</div>
<br /><br />
</div>
<div class="clear"></div></div>
<?php require("includes/footer.txt"); ?>
<script async type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
</body></html>
