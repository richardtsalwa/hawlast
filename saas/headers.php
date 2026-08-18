<?php
// YOUR EMAIL HERE, BCC SENDS YOU A COPY OF THE EMAIL
$headers = 'From: Hawlast SAAS <support@hawlast.com>' . "\r\n";
$headers .= "BCC: hawlast@gmail.com\r\n";
if(!empty($emailalt)) {$headers .= "CC: $emailalt\r\n";}
$returnto = "support@hawlast.com";
$returnpath = "-f" . $returnto;

/*** email to ***/
$adminemail = "support@hawlast.com";

?>


