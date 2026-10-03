<?php

/*
 * BASE_URL is derived from DOCUMENT_ROOT, NOT from scheme + HTTP_HOST.
 * Locally XAMPP serves this project from d:\xampp\htdocs\hawlast, so the base
 * path is "/hawlast"; in production the project sits at the site root, so the
 * base path is empty. Deriving it from the host alone yields
 * http://localhost/images/... locally (the /hawlast segment is missing) and
 * 404s every asset and link.
 */
function hawlast_base_url(): string {
    $docRoot   = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $configDir = rtrim(str_replace('\\', '/', __DIR__), '/');

    $basePath = '';
    if ($docRoot !== '' && $docRoot !== '/' && stripos($configDir . '/', $docRoot . '/') === 0) {
        $basePath = substr($configDir, strlen($docRoot));
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    $scheme = $isHttps ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'www.hawlast.com';

    return rtrim($scheme . '://' . $host . rtrim($basePath, '/'), '/');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', hawlast_base_url());
}

function hawlast_url(string $path = ''): string {
    $base = rtrim(BASE_URL, '/');
    $path = trim((string) $path, '/');
    return $path === '' ? $base : $base . '/' . $path;
}


// M-PESA paybill that receives airtime payments. Used by the public buy airtime page.
if (!defined('AIRTIME_PAYBILL')) {
    define('AIRTIME_PAYBILL', '822490');
}

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

/**
 * Builds a unique voucher reference, e.g. HV260301A1B2C3D.
 * Used by saas/airtime/index.php
 */
function generateHVRandomString(): string {
    date_default_timezone_set('Africa/Nairobi');

    // 1) Prefix (2 chars)
    $prefix = 'HV';

    // 2) Short timestamp: year, month, day (6 chars) e.g. 260301
    $datePart = date('ymd');

    // 3) Random characters (7 chars)
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomPart = '';
    for ($i = 0; $i < 7; $i++) {
        $randomPart .= $characters[random_int(0, strlen($characters) - 1)];
    }

    // Total: HV (2) + date (6) + random (7) = 15
    return $prefix . $datePart . $randomPart;
}

// GET COMMISSIONS FOR AIRTIME SELLER - saas/affiliates.php
function getCommissionSum(PDO $pdo, string $affiliate): ?float {
    try {
        $stmt = $pdo->prepare("SELECT SUM(amount) AS total_amount
                               FROM airtime_commission
                               WHERE affiliate = ? AND status = 1");
        $stmt->execute([$affiliate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_amount'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error getting commission sum: " . $e->getMessage());
        return null;
    }
}

// saas/affiliates.php
function markAllCommissionsPaid(PDO $pdo, string $affiliate, int $amount): bool {
    try {
        $stmt = $pdo->prepare("UPDATE airtime_commission SET status = '2' WHERE affiliate = ? AND status = '1'");
        $stmt->execute([$affiliate]);
        if ($stmt->rowCount() > 0) {
            $logFilePath = 'commission_logs.txt';
            $logData = date('Y-m-d H:i:s') . " - Affiliate: $affiliate - Sales:  $amount marked as paid.\n";
            file_put_contents($logFilePath, $logData, FILE_APPEND);
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Error marking commissions paid for affiliate '$affiliate': " . $e->getMessage());
        return false;
    }
}

// Passthrough helper retained from saas/functions.php - saas/airtime/index.php
function isInteger($input) {
    return strval($input);
}

// Get a number from a string (kept from saas/functions.php)
function getnumberfromstringsum($string) {
    $str = substr($string, 0, -3);
    return preg_replace("/[^0-9]/", '', $str);
}

// Live KES/USD rate scrape - saas/pay.php, saas/pay2kes.php
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

/**
 * Send an SMS directly via the Africa's Talking SDK (no remote cURL round-trip).
 *
 * @param  string $message  SMS body.
 * @param  string $phone    Recipient(s). Accepts 07xxxxxxxx, 2547xxxxxxxx or
 *                          +2547xxxxxxxx. Multiple numbers may be comma separated.
 * @param  string $senderID Alphanumeric sender ID. Defaults to "HAWLAST".
 * @return bool             true when AT accepted the request, false on failure.
 *                          Failures are written to TumaSmsError.txt.
 */
function TumaSMS($message, $phone, $senderID = 'HAWLAST') {

  // AT credentials live in config.php. Not every caller loads it, so pull it in on demand.
  if (!defined('ATUSER') || !defined('ATAPIKEY')) {
    require_once __DIR__ . '/config.php';
  }

  // Central library loader: Composer autoloader for AfricasTalking\SDK\AfricasTalking.
  require_once __DIR__ . '/libraries.php';

  // Normalise every recipient to +2547XXXXXXXX (the old remote script did this too).
  $recipients = [];
  foreach (explode(',', (string) $phone) as $number) {
    $number = add254($number);
    if ($number !== false) {
      $recipients[] = $number;
    }
  }

  $message = trim((string) $message);

  if ($message === '' || empty($recipients)) {
    $error = 'TumaSMS: invalid message or recipient(s).';
    error_log($error);
    $fp = fopen(__DIR__ . '/TumaSmsError.txt', 'a');
    if ($fp) { fwrite($fp, $error . PHP_EOL); fclose($fp); }
    return false;
  }

  try {
    $AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);

    $result = $AT->sms()->send([
      'to'      => $recipients,
      'message' => $message,
      'from'    => $senderID,
    ]);

    if (isset($result['status']) && $result['status'] === 'success') {
      return true;
    }

    $error = 'TumaSMS: AT rejected the request - ' . json_encode($result);

  } catch (\Throwable $e) {
    $error = 'TumaSMS: ' . $e->getMessage();
  }

  error_log($error);
  $fp = fopen(__DIR__ . '/TumaSmsError.txt', 'a');
  if ($fp) { fwrite($fp, $error . PHP_EOL); fclose($fp); }

  return false;
}

/**
 * Send airtime to a recipient through Africa's Talking.
 *
 * Mirrors TumaSMS(): credentials come from config.php (ATUSER / ATAPIKEY) and the
 * SDK is loaded through the central libraries.php loader.
 *
 * Extracted from the inline logic in saas/airtime/buyapinew.php and
 * saas/airtime/buynew.php. The M-PESA callback (pesa/confirmation.php) posts to
 * buyapinew.php, which in turn calls this.
 *
 * @param  string|array $phone        Recipient number. Accepts 07xxxxxxxx,
 *                                    2547xxxxxxxx, +2547xxxxxxxx. An array sends
 *                                    airtime to several numbers in one request.
 * @param  string|int   $amount       Amount per recipient, in $currencyCode.
 * @param  string       $currencyCode ISO currency code. Defaults to 'KES'.
 * @return array|false                On success, the decoded result with a
 *                                    'sent' key. False when validation or the
 *                                    gateway call fails. Failures are appended to
 *                                    TumaAirtimeError.txt.
 */
function tumaAirtime($phone, $amount, $currencyCode = 'KES') {

  // AT credentials live in config.php. Not every caller loads it, so pull it in on demand.
  if (!defined('ATUSER') || !defined('ATAPIKEY')) {
    require_once __DIR__ . '/config.php';
  }

  // Central library loader: Composer autoloader for AfricasTalking\SDK\AfricasTalking.
  require_once __DIR__ . '/libraries.php';

  // Accept a single number or a list, and normalise every recipient to +2547XXXXXXXX.
  $incoming = is_array($phone) ? $phone : explode(',', (string) $phone);

  $recipients = [];
  foreach ($incoming as $number) {
    $number = add254($number);
    if ($number !== false) {
      $recipients[] = [
        'phoneNumber'  => $number,
        'currencyCode' => $currencyCode,
        'amount'       => (string) $amount,
      ];
    }
  }

  if (empty($recipients) || !is_numeric($amount) || $amount <= 0) {
    $error = 'tumaAirtime: invalid recipient(s) or amount.';
    error_log($error);
    $fp = fopen(__DIR__ . '/TumaAirtimeError.txt', 'a');
    if ($fp) { fwrite($fp, $error . PHP_EOL); fclose($fp); }
    return false;
  }

  // Idempotency key so a retried request is not double-sent by the gateway.
  $idempotencyKey = substr(hash('sha256', uniqid((string) microtime(true), true)), 0, 16);

  try {
    $AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);

    $result = $AT->airtime()->send(
      ['recipients' => $recipients],
      ['idempotencyKey' => $idempotencyKey, 'maxNumRetry' => 3]
    );

    // Africa\'s Talking returns numSent / responses rather than a status string.
    $result['sent'] = isset($result['data']->numSent) ? (int) $result['data']->numSent : 0;

    if ($result['sent'] > 0) {
      return $result;
    }

    $error = 'tumaAirtime: gateway sent no airtime - ' . json_encode($result);

  } catch (\Throwable $e) {
    $error = 'tumaAirtime: ' . $e->getMessage();
  }

  error_log($error);
  $fp = fopen(__DIR__ . '/TumaAirtimeError.txt', 'a');
  if ($fp) { fwrite($fp, $error . PHP_EOL); fclose($fp); }

  return false;
}

?>