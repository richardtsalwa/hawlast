<?php
require '../config.php';
require '../hawlastke.php';
date_default_timezone_set('Africa/Nairobi');
if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");

// Check if the 'token' parameter exists in the URL for safaricom requests
if(isset($_GET['token'])) {

$token=$_GET['token']; 
$token = trim($_GET['token']);
$expected_token = "wwwourPU_RstrongPasswordSample$";
if ($token !== $expected_token) { 
//If the token doesn't match, stop processing
exit("Token mismatch. Processing stopped."); 
} 
} else {
$token="none"; 
$url = "https://www.hawlast.com";
header("Location: $url");
exit;
}

if ($request=file_get_contents('php://input'))
{
//Put the json string that we received from Safaricom to an array
$array = json_decode($request, true);

$transactiontype= trim($array['TransactionType']); 

$TransID =  trim($array['TransID']); 
$transid = strtoupper($TransID);

$transtime=  trim($array['TransTime']); 
$transamount =  trim($array['TransAmount']); 
$businessshortcode =   trim($array['BusinessShortCode']); 
$billrefno =   trim($array['BillRefNumber']); 
$invoiceno =   trim($array['InvoiceNumber']); 
$msisdn =   trim($array['MSISDN']); 
$orgaccountbalance =    trim($array['OrgAccountBalance']); 
$firstname = trim($array['FirstName']); 
	$middlename = $array['MiddleName'] ?? '';
	$lastname = $array['LastName'] ?? '';
	
$mpesa_response = array('ResultCode' => 0, 'ResultDesc' => 'Success');

// --- 1. IMMEDIATE RESPONSE (CRITICAL FOR M-PESA TIMEOUT) ---
ob_start();
header("Content-Type: application/json");
echo json_encode($mpesa_response);

// Send the response and close the connection
$size = ob_get_length();
header("Content-Length: $size");
header('Connection: close');
ob_end_flush();
flush();

ignore_user_abort(true);

try {
    
$sql = "INSERT INTO mpesaapi (TransactionType,TransID,TransTime,TransAmount,BusinessShortCode,BillRefNumber,InvoiceNumber,MSISDN,FirstName,MiddleName, LastName,OrgAccountBalance,confirmation_status) VALUE(?,?,?,?,?,?,?,?,?,?,?,?,1)
        ON DUPLICATE KEY UPDATE
            confirmation_status = 1,
            OrgAccountBalance = VALUES(OrgAccountBalance)
    ";
$stmt = $pdo->prepare($sql);
 
$success = $stmt->execute([$transactiontype,$transid,$transtime,$transamount,$businessshortcode,$billrefno,$invoiceno,$msisdn,$firstname,$middlename,$lastname,$orgaccountbalance]);


if ($success) {
        $rows_affected = $stmt->rowCount();
        
        // This check prevents double-crediting if M-PESA retries the Confirmation call
        if ($rows_affected == 1 || $rows_affected == 2) {
            // **PERFORM USER CREDIT / SERVICE FULFILLMENT HERE**
  // The transaction was either inserted (1) or updated (2) to confirmed.
//error_log("SUCCESS: TransID $transid processed. Rows affected: $rows_affected.");
        }} else {
 error_log("DB Execution failed for M-PESA TransID $transid.");
    }

} catch (Exception $e) {
    error_log("M-PESA Confirmation Script Error: " . $e->getMessage());
}

//THE BELOW PROCESS IS TO SELL AIRTIME   
///CHECK IF THE BILL REFERENCE IS A PHONE NUMBER

//Remove all extra spaces within the string
$billrefno = preg_replace('/\s+/', '', $billrefno); 

//Remove any leading or trailing spaces. make upper case for comparison below
$originalbillrefno = $billrefno = strtoupper(trim($billrefno));

//Check if the $billrefno starts with an alphabetical letter (FROM A TO Z) 
//This checks for upper case Alphabet A TO Z
//CONFIRM FORMAT WILL BE IF REF IS L0888888788 for example
if (preg_match('/^[A-Z]\d{10}$/', $billrefno)) {
// Extract the first letter (affiliate)
$affiliate = substr($billrefno, 0, 1); 

//record the commission on airtime_commission table
//This is before the extra airtime we refund for airtime
addCommission($pdo,$lastid,$affiliate,$transamount);

//Extract the phone number part
$billrefno = substr($billrefno, 1); 
}

//Format the phone number +254 
$billrefno = add254($billrefno); 

//Check if the remaining part after the billrefno is a valid Kenyan phone number
if(preg_match('/^(\+254|0)[1-9]\d{8}$/', $billrefno)) {  

$phone = urlencode($billrefno);

//add function to factor in MPESA transaction tariff for >100 
$airtimeAmount = $amount = calculateAirtime($transamount);

$mpesaapi ="10";

$postdata ="token=".$token."&phone=".$phone."&amount=".$airtimeAmount."&mpesaapi=".$mpesaapi."&TransID=".$transid;

$url = 'https://www.hawlast.com/saas/airtime/buyapinew.php';

$refererUrl = "https://www.hawlast.com/pesa/confirmation.php";

$ch = curl_init(); 
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 

// Use a modern agent or name your service (e.g., "HawlastAirtime/1.0")
curl_setopt($ch, CURLOPT_USERAGENT, "HawlastApp/1.0 (+https://www.hawlast.com; support@hawlast.com)");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // Wait 10s to connect
curl_setopt($ch, CURLOPT_TIMEOUT, 30);        // Wait 30s for total response

curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, FALSE);  //do not follow directions
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response BODY as a string

//is if your API provider specifically told you: "We only accept requests that come from https://hawlast.com
//curl_setopt ($ch, CURLOPT_REFERER, $refererUrl); 
curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
curl_setopt ($ch, CURLOPT_POST, true); 
 
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$info = curl_getinfo($ch);
$speed = $info['total_time'];

// Define the path to your custom log file
$logFile = __DIR__ . '/time-transactions.log'; 

// "3" tells PHP to write to a specific file path
error_log("[" . date("d-M-Y H:i:s e") . "] Transaction for $phone ($transid) processed in: {$speed}s [HTTP $httpCode]\n", 3, $logFile);


if ($result === FALSE) {
    $curlrespo = "Curl error: " . curl_error($ch);
    error_log($curlrespo);
} else {
    // Decode the JSON to see what REALLY happened
   // $responseData = json_decode($result, true);
    
    if ($httpCode == 200 || $httpCode == 201) {
        
    //check IF RESULT Is success FUTURE EDIT 
        $curlrespo = "Curl airtime ok. Response: " . $result;
        
        /*
        // Check if the API provider reported an internal error (common in Kenya APIs)
        if (isset($responseData['errorMessage']) && $responseData['errorMessage'] !== 'None') {
            $curlrespo = "API Logic Error: " . $responseData['errorMessage'];
        } else {
            $curlrespo = "Curl airtime ok. Response: " . $result;
        }
        */
    } else {
        $curlrespo = "Network Error: HTTP code $httpCode. Response: " . $result;
    }
}

// 3. Final Cleanup
curl_close($ch);

$date = date("Y-m-d");

 //This will go in the text file
$data = "$postdata $date $firstname $billrefno $transamount $curlrespo";
} else {

//THIS IS NOT AIRTIME CLIENT
// Check if the original starts with "HV" (case insensitive)FOR hosting clients
if (stripos($originalbillrefno,"HV") === 0) {
//the part after "HV"
$renewalsid = (int) substr($originalbillrefno, 2);
   
//Add entry in the prepaid table 
addRenewalsprepaid($pdo,$renewalsid,$transamount,$transid);
} 

//SEND THE CEO AN ALERT 

$data = "$firstname Accx: $originalbillrefno $transamount $transid Bal: $orgaccountbalance";

$phone = "+254720401869";

TumaSMS($data, $phone);

$message = $data; 
$email="support@hawlast.com"; 
$subject ="MPESA Payment";
$attachments = "";
$companyName ="Hawlast Ventures";

/*** send the email ***/
if(!sendEmail($email, $companyName, $subject, $message, $attachments)) {
$mailerror ="Email error";
} 

//Get input stream data and log it in a file
//$fp = fopen("mpesa-confirm-log.txt", "a") or die("Unable to open file!");
//$dump = "$data \n";
//fwrite($fp, $dump);
//fclose($fp);

}

} //IF POST
?>