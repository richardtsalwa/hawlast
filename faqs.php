<?php 
require 'config.php';
require 'hawlastke.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
<title>FAQ on Hosting &ndash; hawlast</title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
<meta name=description content="Check our FAQ on domains, web design, web hosting and software developementa" />
<meta name=keywords content="web hosting kenya" />
<link rel="shortcut icon" href=images/favicon.ico type=image/x-icon />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" 
type="text/css">
<link rel="stylesheet" type="text/css" href="style.css" /><link rel="stylesheet" type="text/css" href="style.css">
<script src="jquery.min.js"></script>
<!-- JS -->
<script type="text/javascript">
  $(document).ready(function($) {
    $('#accordion').find('.accordion-toggle').click(function(){

      //Expand or collapse this panel
      $(this).next().slideToggle('fast');

      //Hide the other panels
      $(".accordion-content").not($(this).next()).slideUp('fast');

    });
  });
</script>
<!-- CSS -->
<style>
  .accordion-toggle {color:blue;}
  .accordion-toggle {cursor: pointer;}
  .accordion-content {display: none;}
  .accordion-content.default {display: block;}
</style>

<?php
require __DIR__ . "/includes/header.php";
?>
</div>
<div class=center_content>

<div class=left_content id="accordion">

<div class=in>
<h2>FAQ on domains, Emails and Hosting</h2>
<br/>

<h3 class="accordion-toggle">+ Control Panel </h3>
<div class="accordion-content">
<P>If you are on Premier plan, you can access the Control panel via http://your-domain.com/cpanel. You will need a username and password.</b></p>
</div>
  <h3 class="accordion-toggle">+ Domain names </h3>
  <div class="accordion-content">
  <p>You can choose to register domains with extensions such as .com, .net, .biz, .info, .org, and also the Kenya cTLD domains aka .ke with options to register a domain like .co.ke,or.ke and ac.ke.</p>
  <p>You can search and register a domain at <a href="<?php echo hawlast_url('domain-registration/'); ?>">domain registration page</a></div>
  

<h3 class="accordion-toggle">+ Email management : Webmail : Outlook : Change password</h3>
  <div class="accordion-content">
<br />
<p><b>Webmail access:</b>
<p>You can access emails via http://hawlast.com.com/webmail. Replace <i>hawlast.com</i> with your domain name.</p>
<p><b>Microsoft Outlook set up [Watch a video <a href="https://www.hawlast.com/blog/how-to-configure-set-up-outlook-on-windows-pc/">on how to setup Outlook</a>]:</b><br/>
# Manually configure server settings<br/> 
# The type of e-mail account: POP3<br/>
# Your user name: full email address<br/>
# Your currrent password: xxxxxxx <br/> 
# The POP3 server name or Incoming mail server: mail.your-domain.com<br/>
# The SMTP server name or Outgoing mail server: mail.your-domain.com<br/>

# More setting: Outgoing server (SMTP) requires authentication -same settings as incoming mail server</p> 
<p><b>Change email password</b><br />
<b>OPTION I</b>:If you have Cpanel username and password<br/>
Step 1: Login to the cpanel<br/>
Step 2: Click Mail central<br/>
Step 3: Click on the email address and input the new password<br/>

<b>OPTION II:</b>No cpanel access<br/>
Step 1: Visit this email link</a><br/>
Step 2: Enter your full mailbox address and password for the mailbox and click 'Manage Mail'.<br/>
Step 3: Under 'Manage Mail' you can change the email passwords.<br/>
Step 4: To log out - look at the footer of the page for the log out button.<br/>
</p>
</div> 

<h3 class="accordion-toggle">+ MX Records </h3>
<div class="accordion-content">
Log into the control panel with the account username and password.
<ol>
<li>Click on ‘Domain Central’ under the ‘Domain’ section.</li>
<li>Click on the domain name for which you want to manage the MX records.</li>
<li>Click on ‘DNS’.</li>
<li>Select the option ‘MX Record’ from the ‘Modify’ dropdown menu.</li>
<li>To add new MX record, enter the priority, host name and destination and click on the ‘Add’ button.</li>
<li>To edit the existing MX record, select ‘Edit’ from the ‘Action’ dropdown menu. Make necessary changes and click on ‘Save’ to save the changes.</li>
<li>To delete existing MX records, select ‘Remove’ from the ‘Action’ dropdown menu. It will ask for the confirmation. Click on the ‘Ok’ button.</li>
<ol>
Once the MX records are updated, it will take 1-12 hours to reflect the changes.
</div>
  
<h3 class="accordion-toggle">+ Website File upload, FTP, Filezilla</h3>
<div class="accordion-content">
<p>You can edit and upload your website via FTP. FTP username/ password is same as your cpanel's. You will require FTP software like Filezilla Client.More articles about <a href="https://www.hawlast.com/blog/how-do-i-upload-pages-to-my-account.htm">file upload and FTP</a></p>
<p></p></div>

<h3 class="accordion-toggle">+ MySQL Database Host </h3>
<div class="accordion-content">
<p>If you are hosting on our Starter plan, we can set up the database for you. </p> 
<p>If you are hosting on our Premier plan accounts, you can set up and access the database by logging into our cpanel.
=> Click on MySQL Database Icon <br />
=> Database host is in the format <i><u>username</u></i>.accountsupportmysql.com. Replace username with your cpanel username. </p> 
</div>

<h3 class="accordion-toggle">+ Payment options - M-PESA PAY BILL, PAYPAL </h3>
<div class="accordion-content">
<P>You can make payments via:<br/>
<B>M-PESA</B><br/>
Select Lipa na M-PESA<br/> 
Select Buy Goods and Services<br/> 
Enter Till no. 773599<br/> 
** Bank deposit or Cheque to <b>Hawlast Ventures</b><br/>
** PayPal to support(at)hawlast.com.</p>
</div>

</div><!--end of class in --->
<div class=clear></div>
</div>

<div class=right_content>
<?php
require __DIR__ . "/includes/other_left.php";
?>
</div>
<?php require __DIR__ . "/includes/footer.php"; ?></body></html>