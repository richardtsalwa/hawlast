<?php
//$response = get_web_page("https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials");
$Consumer_Key=	"Azs2KejU1ARvIL5JdJsARbV2gDrWmpOB";
$Consumer_Secret="hipGvFJbOxri330c";
$credentials = base64_encode($Consumer_Key.':'.$Consumer_Secret);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic ". $credentials,
    "Content-Type: application/json",
    "cache-control: no-cache"
  ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
	 $token=json_decode($response)->access_token;
  echo $response;
  echo $token;
}
?>