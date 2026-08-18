<?php

//AT CREDENTIALS 
define("ATUSER", "hawlast");
define("ATPASSWORD","d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8");

function kes($amount) {
$amount = str_replace("KES ", "", $amount);
$amount = trim($amount);
// Convert the string to a float
$amount = (float) $amount;
return $amount;
}


function generateHVRandomString(): string {
    // Ensure the time is Nairobi time
    date_default_timezone_set('Africa/Nairobi');
    
    // 1. Prefix (2 chars)
    $prefix = 'HV';

    // 2. Short Timestamp: Year, Month, Day (6 chars)
    // Example: March 1st 2026 becomes 260301
    $datePart = date('ymd');

    // 3. Random Characters (7 chars)
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomPart = '';
    for ($i = 0; $i < 7; $i++) {
        $index = random_int(0, strlen($characters) - 1);
        $randomPart .= $characters[$index];
    }
    
    // Total: 2 (HV) + 6 (date) + 7 (random) = 15 characters
    return $prefix . $datePart . $randomPart;
}

//GET COMMISSIONS FOR AIRTIME SELLER saas/affiliates.php
function getCommissionSum(PDO $pdo, string $affiliate): ?float {
    try {
        $stmt = $pdo->prepare("SELECT SUM(amount) AS total_amount 
                               FROM airtime_commission 
                               WHERE affiliate = ? AND status = 1");
        $stmt->execute([$affiliate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_amount'] ?? 0; 
    } catch (PDOException $e) {
        echo "Error getting commission sum: " . $e->getMessage();
        return null;
    }
}

// saas/affiliates.php
function markAllCommissionsPaid(PDO $pdo, string $affiliate, int $amount): bool {
    try {
$stmt = $pdo->prepare("UPDATE airtime_commission SET status = '2' WHERE affiliate = ? AND status = '1'");
$stmt->execute([$affiliate]);
if ($stmt->rowCount() > 0) {
            // Log the successful update to a file
            $logFilePath = 'commission_logs.txt'; 
            $logData = date('Y-m-d H:i:s') . " - Affiliate: $affiliate - Sales:  $amount marked as paid.\n";
            file_put_contents($logFilePath, $logData, FILE_APPEND); 
            return true;
        } else {
            return false; 
        }
    } catch (PDOException $e) {
        // Log the error (e.g., to a file or database)
        error_log("Error marking commissions paid for affiliate '$affiliate': " . $e->getMessage()); 
        return false;
    }
}


//UPDATED 27 Dec 2024. More robust checks allows 07,+254,254,720
function add254($phone) {
  // Remove any non-digit characters
  $phone = preg_replace('/[^0-9]/', '', $phone); 

  // Check if the phone number starts with "254" or "0" 
  if (substr($phone, 0, 3) === "254" || substr($phone, 0, 1) === "0") {
    // Remove leading "254" or "0"
    $phone = ltrim($phone, "2540"); 
  }

  // Check if the remaining phone number has 9 digits 
  if (strlen($phone) !== 9) {
    return false; // Or handle invalid phone numbers as needed
  }

  return "+254" . $phone;
}



function isInteger($input){
    //return(ctype_digit(strval($input)));
    return(strval($input));
} 

// Given date as 2010-12-31
function dmy($ymd)
{
	list($year,$month,$day) = explode("-",$ymd);
	return "$day"."-"."$month"."-"."$year";
}

function monthyear($ymd)
{
	list($year,$month,$day) = explode("-",$ymd);
	return "$month"."$year";
}

function day($ymd)
{
	list($year,$month,$day) = explode("-",$ymd);
	return "$day";
}
function month($ymd)
{
	list($year,$month,$day) = explode("-",$ymd);
	return "$month";
}

function year($ymd)
{
	list($year,$month,$day) = explode("-",$ymd);
	return "$year";
}

function expired($datediff)
{
if ($datediff > 365) { return "Expired"; 
} else { $daystogo = 365 - $datediff; return "$daystogo ";}
}

function marks($num){
if (preg_match("/\D/",$num)){ die("Please enter numbers only");} else { return $num; }
}

//Get a number from a string
function getnumberfromstring($string){ 
$str = substr($string, 0, -3);
$number = preg_replace("/[^0-9]/", '', $str);
return $number;
}

//Get a number from a string
function getnumberfromstringsum($string){ 
$str = substr($string, 0, -3);
$number = preg_replace("/[^0-9]/", '', $str);
return $number;
}



//This function removes the NULL or 0 on empty enties
function null($pricex){
if ($pricex == NULL || $pricex == 0){ 
return "";
} else { return $pricex; } 
}

function protect($string){
$string = trim($string);
//$string = mysql_real_escape_string($string);
return $string;
}

function copyrightYear($created) {
    $current = date('Y',time());
    return ($current > $created) ? $created.'-'.$current : $current;
  }
function safe($value) {
    htmlentities( $value, ENT_QUOTES, 'utf-8');
    return $value;
  }

function page($current){
$uri = dirname($_SERVER['PHP_SELF']);
$page = substr($_SERVER['PHP_SELF'], strlen($uri)); 
if ($current == $page)
echo " class=\"current\"";
}

//this is great but ereg_replace is depreciated
function GetDomain($url)
{
$url = strtolower($url);
$nowww = @ereg_replace('www\.','',$url);
$domain = parse_url($nowww);
if(!empty($domain["host"]))
    {
     return $domain["host"];
     } else
     {
     return $domain["path"];
     }
}

//get a url http://www.exapme.co.ke returns 0 or 1
function validate_url($url)
{ 
return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(\.[a-z0-9-]+)*(\.[a-z]{2,4})$|i', $url);  
} 

function getwebsite($email){ 
$email_pieces = explode('@', $email);
$domains = $email_pieces[1];
return $domains;
}

function tupu($var){
if (empty($var)){ die("Enter all the required fields.");} else { return $var; }
}

function dateDiff($dformat, $endDate, $beginDate)
{
           $date_parts1=explode($dformat, $beginDate);
           $date_parts2=explode($dformat, $endDate);
           $start_date=gregoriantojd($date_parts1[1], $date_parts1[0], $date_parts1[2]);
           $end_date=gregoriantojd($date_parts2[1], $date_parts2[0], $date_parts2[2]);
           return $end_date - $start_date;
}


function TumaSMS($message, $phone) {

//cURL result to space
$result ="";
//if email fails
$mailerror ="";
//log cURL errors 
$curlerror ="";

//Innocent user id at SAAS
$user_id = "1";

//Balance as the sms script expects me to have >1 balance
$bal ="20";

$mpesaapi ="hawlast";

$url = 'https://www.hawlast.com/saas/sms/sendsms.php'; // cURL endpoint

// Prepare POST data
$fields = [
    'user_id' => $user_id,
    'bal' => $bal,
    'phone' => $phone,
    'mpesaapi' => $mpesaapi,
    'message' => $message,
];

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
//curl_setopt($ch, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NATIVE_CA);

curl_setopt($ch, CURLOPT_USERAGENT, "HawlastApp/1.0 (+https://www.hawlast.com; support@hawlast.com)");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Wait 10s to connect
curl_setopt($ch, CURLOPT_TIMEOUT, 30);        // Wait 30s for total response

curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, FALSE); 
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields)); // Automatically formats data for POST
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Get a response from the server
curl_setopt ($ch, CURLOPT_POST, true); 
// Add headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json',
]);


// Execute POST
$result = curl_exec($ch);

// Initialize success flag
$isSuccessful = false;

// Check for errors
if ($result === false) {
    $curlerror = "There was a cURL error: " . curl_error($ch);
    $isSuccessful = false;
} else {
    $curlerror = ""; // Clear error message if successful
    $isSuccessful = true;
}


// Close cURL
curl_close($ch);


// Return based on success or error
if ($isSuccessful) {
    return true;
} else {
    //error_log($curlerror); // Log the error for debugging
    //file_put_contents('smsError.log', "There was a cURL error: " . $curlerror . PHP_EOL, FILE_APPEND);
    $fp = fopen("TumaSmsError.txt", "a") or die("Unable to open file!");
	fwrite($fp, $curlerror);
	fclose($fp);
	
    return false;
}
}

function exchange()
{
$string1 = file_get_contents("http://ke.equitybankgroup.com/");
$needle = "KES";
if(strpos($string1,"$needle") === false) { 
return "98.2";
} else {
$result_string = substr("$string1",strpos($string1,$needle)+17,strpos($string1,$needle)+strlen($string1));
$result1 = trim(substr($result_string,0,-58000));
$exch = str_replace("Buying: ", chr(8), $result1);
$new =  floatval (preg_replace("/[^0-9\.]/","",$exch));
if($new == 0) { return "98.3"; } else {return $new; }
}
}
?>