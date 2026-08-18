<?php
$File = "xxxreserve.txt"; 
$Handle = fopen($File, 'a');
$date = date("Y-m-d H:i:s"); 
if (isset($_GET['src'])) {
$src = $_GET['src'];
} else {
$src = "non";
}
$Data = "$ddomain $date $src \n";
fwrite($Handle, $Data); 
//print "Data Added"; 
fclose($Handle); 
?>        


