<?php
require '../config.php';

$req_dump = print_r($_REQUEST, TRUE);
	$fp = fopen("wpstkpost.txt", "a") or die("Unable to open file!");
	$data = "$req_dump \n";
	fwrite($fp, $data);
	fclose($fp);


date_default_timezone_set('Africa/Nairobi');

$error ="";
#https://developer.safaricom.co.ke/lipa-na-m-pesa-online/apis/post/stkpush/v1/processrequest

# credentials 
$Passkey ="9b2aa55af213959611bdae2c8d388bc5b8eca3c543444f157ed4bc0b4bcf48e2";

$consumerKey = 'cENa8B1cLgQZGc6cdjweNIPNyc6E7PlI'; //Fill with your app Consumer Key
$consumerSecret = 'bmLplh6wVuzbO6aR'; // Fill with your app Secret
  
$BusinessShortCode = $_POST["shortcode"];
  
//Clients phone in format 2547xxxx
$PartyA = $_POST["phone"];
$AccountReference = $_POST["order"];
$Amount = $_POST["amount"];
$TransactionDesc = 'Deposit';

# Get the timestamp, format YYYYmmddhms -> 20181004151020
$Timestamp = date('YmdHis');    

# Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
$Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);
#header for access token
$headers = ['Content-Type:application/json; charset=utf8'];

# M-PESA endpoint urls
$access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$initiate_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

  # callback url
  $CallBackURL = 'https://www.hawlast.com/pesa/confirmation.php';  
  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);
  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
  $result = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  // check for execution errors
  if(curl_errno($curl)) 
	{
 $error = curl_error($curl);
 $access_token ="TisISIT";
	} else {
  $result = json_decode($result);
  $access_token = $result->access_token; 
	}
 
  curl_close($curl);
  # header for stk push
  $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
  # initiating the transaction
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $initiate_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header
  $curl_post_data = array(
    //Fill in the request parameters with valid values
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $PartyA,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
  );
  $data_string = json_encode($curl_post_data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  $curl_response = curl_exec($curl);
  //print_r($curl_response);
  //echo $curl_response;
 $results = json_decode($curl_response, true);
  //$response_code= $results['ResponseCode'];
//$message = $results['errorMessage'];

if(isset($results['errorMessage'])){
if($results['errorMessage'] == 'Bad Request - Invalid Amount'){
$message= $results["errorMessage"].' . The amount allowed by M-PESA is between Ksh. 1 and Ksh. 70,000.'; 
}elseif($results['errorMessage'] =='[STK] - Unable to lock subscriber, a transaction is already in process for the current subscriber'){
  $message= 'We are still processing your recent transaction to your M-PESA number $phone. Please be patient before you retry.';  
}elseif($results['errorMessage'] =='[CBS - ] No ICCID found'){
  $message= 'Wrong Service provider. The phone entered should be a safaricom M-PESA registered number.'; 
  }
  elseif($results['errorMessage'] =='Service is currently under maintenance. Please try again later'){
   $message= 'M-PESA is currently experiencing some delays. Please try again later or call 100.';
  }
   elseif($results['errorMessage'] =='Bad Request - Invalid PhoneNumber'){
  $message= 'Phone number must be from safaricom and start with 2547xxxx.';
  }
  else{
    $message= $results['errorMessage'];  
  }
  
  echo "Failed";
  
  }elseif(isset($results['ResponseCode'])){
 if($results['ResponseCode'] =='0'){
 $message= 'Check the popup on your phone.Enter your M-PESA pin to complete the payment.' ;

//GIVE THEM TIME 
//sleep(10);

//VERIFY THE PAYMENT FROM THE DATABASE 
//COUNT UP TO 10 SEC before reverting with a FAIlure or success. echo "Success";
//$stmt = $pdo->prepare("SELECT * from mpesaapi WHERE TransAmount = ? and BillRefNumber =? and MSISDN =? and done=1");
//$stmt->execute([$TransAmount,$BillRefNumber,$PartyA]);
//$rows = $stmt->fetch();
//$id = $rows['Auto'];
//if (!empty($id)) //{ 
//hhh

echo "Success";

//Update to show that this transaction is checked
//$query="UPDATE mpesaapi SET done = 2 WHERE Auto =?";
//$sth = $pdo->prepare($query);
//$sth->execute([$id]);

//}
//
 
}}else{
  $message= 'MPESA is already working on your request. Please try again after 30 minutes.';  
echo "Success";
}
$fp = fopen("wpstk-push.txt", "a") or die("Unable to open file!");
$dataa = "$req_dump $message\n";
fwrite($fp, $dataa);
fclose($fp)
?>