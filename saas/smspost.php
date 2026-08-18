<?php

if ( !empty($_POST)) {

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("smspost.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);

} else {

echo "Nothing";

}
?>
