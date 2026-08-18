<?php 
date_default_timezone_set("Africa/Nairobi");

if ( !empty($_POST)) {

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("smsdelivery.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);

} else {

echo "Nothing";

}
?>
