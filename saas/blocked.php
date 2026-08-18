<?php

if ( !empty($_POST)) {

$req_dump = print_r($_REQUEST, TRUE);
$req_dump .= date(Y-m-d);
$fp = fopen("smsblockno.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);
// $senderId = $_POST['senderId']; //This would contain the shortcode or the alphanumeric sender id
// $phoneNumber = $_POST['phoneNumber'];
} else {

echo "Nothing";

}
?>
