<?php
$File = "xxxequiry.txt"; 
$Handle = fopen($File, 'a');
$date = date("Y-m-d H:i:s"); 
$totol = $_SESSION['totalcost'];
$Data = "$date $yourname $lastname $email $ddomain $totol $src \n";
fwrite($Handle, $Data); 
fclose($Handle); 
?>