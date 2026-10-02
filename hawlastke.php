<?php
// This is used in the mpesa/confirmation.php to record commissions
function addCommission(PDO $pdo, int $lastid, string $affiliate, float $amount): bool {
    $stmt = $pdo->prepare("INSERT INTO airtime_commission (mpesaId, affiliate, amount) VALUES (?,?,?)");
    $stmt->execute([$lastid,$affiliate, $amount]);
    return $stmt->rowCount() > 0;
}

function addRenewalsprepaid($pdo,int $renewalsid,int $transamount,string $transid) {
$stmt = $pdo->prepare("INSERT INTO renewalsprepaid (renewalsid,prepaid,TransID) VALUES (?,?,?)");
    $stmt->execute([$renewalsid,$transamount,$transid]);
    return $stmt->rowCount() > 0;
}

//this is in the mpesa/confirmation.php
function calculateAirtime($amount) {
  // Define the ranges and their corresponding values
  $ranges = array(
    array('min' => 0, 'max' => 100, 'value' => 0),
    array('min' => 101, 'max' => 500, 'value' => 5),
    array('min' => 501, 'max' => 1000, 'value' => 10),
    array('min' => 1001, 'max' => 1500, 'value' => 15),
    array('min' => 1501, 'max' => 2500, 'value' => 20),
    array('min' => 2501, 'max' => 3500, 'value' => 25),
    array('min' => 3501, 'max' => 5000, 'value' => 34),
    array('min' => 5001, 'max' => 7500, 'value' => 42),
    array('min' => 7501, 'max' => 10000, 'value' => 48),
    array('min' => 10001, 'max' => 15000, 'value' => 57),
    array('min' => 15001, 'max' => 20000, 'value' => 62),
    array('min' => 20001, 'max' => 25000, 'value' => 67),
    array('min' => 25001, 'max' => 30000, 'value' => 72),
    array('min' => 30001, 'max' => 35000, 'value' => 83),
    array('min' => 35001, 'max' => 40000, 'value' => 99),
    array('min' => 40001, 'max' => 45000, 'value' => 103),
    array('min' => 45001, 'max' => PHP_INT_MAX, 'value' => 108) 
  );

  // Find the matching range
  foreach ($ranges as $range) {
    if ($amount >= $range['min'] && $amount <= $range['max']) {
      return $amount + $range['value']; 
    }
  }

  // If no match is found, return 0
  return 0;
}


function birthday($birthday) {
 $age ="";
 $age = date_create($birthday)->diff(date_create('today'))->y;
    return $age;
}

function isMobileDevice() {
  $user_agent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? 'none');
  if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent)) {
    return true;
  } else {
    return false;
  }
}


function kes($str) {
$final = preg_replace('/\s+/', '', $str);
return trim($final, "KES");
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


function addplusona254($phone) {
   $phonn = substr($phone,0, 1);
   if ($phonn != 0 ) { $plus254 = "+".''.$phone; } else { $phonn = substr($phone,1); $plus254 = "+254".''.$phonn; }
   return "$plus254";
}


function add254stk(string $phone): string
{
    // Remove all non-numeric characters and trim whitespace
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if the number starts with a '0' and remove it
    if (str_starts_with($phone, '0')) {
        $phone = substr($phone, 1);
    }
    
// Check if the number already has the '254' prefix and do nothing if it does
    if (str_starts_with($phone, '254')) {
        return $phone;
    }
    // Add the '254' prefix
    return '254' . $phone;
}

$dollar = "125";

$email = "2500";
$starter = "6500";
$premier = "12000";
$coke = "1000";
$ke = "8000";
$com = "1700";

function copyrightYear($created) {
    $current = date('Y',time());
    return ($current > $created) ? $created.'-'.$current : $current;
  }

function safe($value) {
    htmlentities( $value, ENT_QUOTES, 'utf-8');
    return $value;
  }

function page($current){
$page = basename($_SERVER['PHP_SELF']);  
if ($current == $page)
echo " class=\"selected\"";
}

//find out if page is index and provide domain search form
function thispage($current){
$page = basename($_SERVER['PHP_SELF']);  
if ($current == $page){
require __DIR__ . "/domain-registration/includes/domainsearchform.php"; }
}

//Given an email address ...get the domain name
function getwebsite($email){ 
$email_pieces = explode('@', $email);
$domains = $email_pieces[1];
return $domains;
}

function getnumberfromstring($string){ 
$number = preg_replace("/[^0-9]/", '', $string);
return $number;
}

function domainprices($ext) 
{

$coke = "1000";
$ke = "8000";
$com = "1700";
$biz = "1700";
$travel = "15000";
if ($ext == "co.ke") { return $coke; } 
if ($ext == "or.ke") { return $coke; } 
if ($ext == "ac.ke") { return $coke; } 
if ($ext == "me.ke") { return $coke; } 
if ($ext == "ke") { return $ke; } 	              			
if ($ext == "com") { return $com; } 	              			
if ($ext == "net") { return $com; } 	
if ($ext == "info") { return $com; } 	
if ($ext == "biz") { return $biz; } 	
if ($ext == "org") { return $com; } 
if ($ext == "travel") { return $travel; } 
}

function totalhost($ext,$plan) 
{

$coke = "1000";
$ke = "8000";
$com = "1700";
    
if ($ext == "co.ke") { return $coke+$plan-1000; } 
if ($ext == "or.ke") { return $coke+$plan-1000; } 
if ($ext == "ac.ke") { return $coke+$plan-1000; } 
if ($ext == "me.ke") { return $coke+$plan-1000; } 
if ($ext == "ke") { return $ke+$plan-1000; } 	              			
if ($ext == "com") { return $plan+$com; } 	              			
if ($ext == "net") { return $plan+$com;; } 	
if ($ext == "info") { return $plan+$com;; } 	
if ($ext == "biz") { return $plan+$com;; } 	
if ($ext == "org") { return $plan+$com;; } 
if ($ext == "travel") { return $travel+$plan-1000; } 
}

function protect($string){
$string = trim($string);
//$string = mysqli_real_escape_string($db,$string);
return $string;
}

function tupu($var){
If (empty($var)){ die("Enter data in the required form fields.");} else { return $var; }
}

function noresponse($response){
if ($response == ""){ 
return "No response";
} else { return $response; } 
}


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

//This function removes the NULL or 0 on empty enties
function null($pricex){
if ($pricex == NULL || $pricex == 0){ 
return "";
} else { return $pricex; } 
}


//this is great but ereg_replace is depreciated
function GetDomain($url)
{
$url = strtolower($url);
$nowww = preg_replace('/^www\./i', '', $url);
$domain = parse_url($nowww);
if(!empty($domain["host"]))
    {
     return $domain["host"];
     } else
     {
     return $domain["path"];
     }
}

//get a url http://www.example.co.ke returns 0 or 1
function validate_url($url)
{ 
return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(\.[a-z0-9-]+)*(\.[a-z]{2,4})$|i', $url);  
} 


function dateDiffx($dformat, $endDate, $beginDate)
{
           $date_parts1=explode($dformat, $beginDate);
           $date_parts2=explode($dformat, $endDate);
           $start_date=gregoriantojd($date_parts1[1], $date_parts1[0], $date_parts1[2]);
           $end_date=gregoriantojd($date_parts2[1], $date_parts2[0], $date_parts2[2]);
           return $end_date - $start_date;
}

function dateDiff($dformat, $endDate, $beginDate)
{
    try {
        // Create DateTime objects
        $start = DateTime::createFromFormat($dformat, $beginDate);
        $end = DateTime::createFromFormat($dformat, $endDate);

        // Ensure dates are valid
        if (!$start || !$end) {
            throw new Exception("Invalid date format or input.");
        }

        // Calculate the difference
        $difference = $start->diff($end);

        // Return the difference in days
        return (int)$difference->format('%R%a'); // Includes a sign (+/-) for direction
    } catch (Exception $e) {
        // Handle exceptions
        echo "Error: " . $e->getMessage();
        return false;
    }
}


function sendEmail($email, $companyName, $subject, $mailContent, $attachments,$emailalt = null) {

  // Require PHPMailer via the central library loader
  require_once __DIR__ . '/libraries.php';

  $mail = new PHPMailer(true); // Enable exceptions

  try {
    // Configure SMTP settings (Replace with your actual values)
    
        $mail->isSMTP();
        $mail->Host = 'secure350.servconfig.com';
        $mail->Port = 465;               // SSL port
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = 'api@hawlast.com';
        $mail->Password = 'CEOuBER2023#';
        $mail->CharSet = 'UTF-8';
    
        $mail->SMTPDebug = 0;  // Enable verbose debug output
        
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Sender = 'support@hawlast.com'; 
        $mail->setFrom('support@hawlast.com', $companyName);
        $mail->addReplyTo('support@hawlast.com', $companyName);

        //recepient 
    $mail->addAddress($email);
       //$mail->addCC('hawlast@gmail.com', 'Tsalwa');     // Add CC recipient(s)
    $mail->addBCC('hawlast@gmail.com', 'Tsalwa');     // Add CC recipient(s)'
        //$mail->addBCC('bcc@example.com', 'BCC Name'); 
    
    // Fixed CC Logic: Check if it's actually a string with content
    if (!empty($emailalt) && filter_var($emailalt, FILTER_VALIDATE_EMAIL)) {
        $mail->addCC(trim($emailalt));
    }
    
    // Add attachments if provided
    if (!empty($attachments)) {
      //foreach ($attachments as $attachment) {
        //$mail->addAttachment($attachment['path'], $attachment['filename']);
      //}
    $mail->addAttachment($attachments);
    }

    // Set email content and format
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body = $mailContent;

    // Send email and handle errors
    $mail->send();
    
    return true;

  } catch (Exception $e) {

  $makosa = $mail->ErrorInfo;
        //Write on file 
        $time = date("Y:m:d h:i:sa");
        $both = "<br/> $time $email $makosa";
      $fp = fopen("TumaEmailError.txt", "a") or die("Unable to open file!");
	fwrite($fp, $both);
	fclose($fp);
    return false;
  }
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
  error_log($curlerror); // Log the error for debugging
    //file_put_contents('smsError.log', "There was a cURL error: " . $curlerror . PHP_EOL, FILE_APPEND);
    $fp = fopen("TumaSmsError.txt", "a") or die("Unable to open file!");
	fwrite($fp, $curlerror);
	fclose($fp);
	
    return false;
}
}

?>