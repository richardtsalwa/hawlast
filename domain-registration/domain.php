<?php
session_start();

if (!isset($_GET['domain'])) { header("Location: ./"); exit(); }

	$req_dump = print_r($_REQUEST, TRUE);
	$fp = fopen("domainsearch.txt", "a") or die("Unable to open file!");
	fwrite($fp, $req_dump);
	fclose($fp);

$errors = "";

$domain = htmlspecialchars($_GET['domain']);

$domain = preg_replace('/\s+/', '', $domain);

if(substr_count($domain, '.') >0 ){
$errors .= "Your domain name should not have a dot<br/>";
} 
$ext = htmlspecialchars($_GET['ext']);
$option = htmlspecialchars($_GET['option']);
$src = htmlspecialchars($_GET['src']);
$domainext = "$domain.$ext";

if($errors) {
$_SESSION["ddd"]=$domainext;   
$_SESSION["status"]=$errors;    
       //echo $errors;
header("Location: ./");
exit();

}

//FUNCTION TO CHECK FOR AVAILABLE DOMAINS
function checkDomain($domain,$server,$findText) {

// Open a socket connection to the whois server
$con = fsockopen($server, 43, $errno, $errstr, 30);

if (!$con) {
 return "ERROR:Could not connect to whois server";

} 
      
  // Send the requested domain name
        fputs($con, $domain."\r\n");

        // Read and store the server response
        $response = ' :';
        while(!feof($con)) {
            $response .= fgets($con,128); 
        }

        // Close the connection
        fclose($con);

        // Check the response stream whether the domain is available
        if (strpos($response, $findText)){
            return "Available";
        }
        else {
           // return false;  
            return "Not Available";
        }
    
}

//Define whois servers 
if($ext=="africa"){ $server ="africa-whois.registry.net.za"; $txt = "Available";}
if($ext=="com"||$ext=="net"){ $server ="whois.crsnic.net"; $txt = "No match for";}
if($ext=="biz"){ $server ="whois.biz"; $txt = "No Data Found";}
if($ext=="org"){ $server ="whois.pir.org"; $txt = "NOT FOUND";}
if($ext=="info"){ $server ="whois.afilias.net"; $txt = "NOT FOUND";}
if($ext=="travel"){ $server ="whois.nic.travel"; $txt = "No Data Found";}
if($ext=="co.ke"||$ext=="ke"||$ext=="me.ke"||$ext=="ac.ke"||$ext=="sc.ke"||$ext=="or.ke"||$ext=="info.ke"){ 
$server ="whois.kenic.or.ke"; 
$txt = "No Object Found"; 
}		

$status = checkDomain($domainext,$server,$txt);

if(strcasecmp($status,"Available")==0){

//IF DOMAIN IS AVAILABLE
        if (isset($_GET['hosting'])){
        $hosturl ="&h=";
	    $hostvar = $_GET['hosting'];
        $src = $_GET['src'];
        $url = "hosting.php?src=$src&domain=$domainext$hosturl$hostvar&ext=$ext";
		header("Location: $url");
		} else {
        $src = $_GET['src'];
        $url = "hosting.php?src=$src&domain=$domainext&ext=$ext";
		header("Location: $url");
		}


} else {

//IF DOMAIN IS NOT AVAILABLE
    $_SESSION["ddd"]=$domainext;   
	$status .= $errors;
    $_SESSION["status"]=$status;    
  header("Location: ./"); exit();

}

//.ac.ke - for Institutions of Higher Educations.( Requires Supporting Documents)
//.sc.ke - for Lower and Middle Institutes of Learning. (Requires Supporting Documents)
?>