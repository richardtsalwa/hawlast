<?php
session_start();
date_default_timezone_set("Africa/Nairobi");
ob_start();
$host = "http://www.hawlast.com";
require '../database.php';
require '../functions.php';

//if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");

if (isset($_POST['user_id'])) {

$phonenumber = safe($_POST['phone']);
$client = safe($_POST['p']);
$message = safe($_POST['message']);
$user_id = safe($_POST['user_id']);
$bala = safe($_POST['bal']);

$client = protect($_POST['p']);
$message = protect($message);
$user_id = protect($user_id);
$balance = protect($bala);

/* Check all form inputs */
$errors = array();
if(strlen($phonenumber)<13) { 
$errors[] = "Please enter a phone number in the format +2547xxYYYZZZ!";
} else {
$smses = substr_count($phonenumber, ",") +1;
if (($smses) > $balance) { $errors[] = "Your SMS balance is not sufficient to send to those numbers";}
}
// BREAK THE NUMBERS AT THE COMMA AND APPROXIMATE HOW MANY NUMBERS THEY ARE
if(!$message) { $errors[] = "Please enter a message";}

 if (empty($errors)){

// SEND sms to the admin 
require_once('hawlastsms.php');

// Specify your login credentials
$username = "hawlast";
$apiKey      = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

$recipients = "$phonenumber";
$from = "HAWLAST";
// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

try 
{ 
  // Thats it, hit send and we'll take care of the rest. 
$results = $gateway->sendMessage($recipients, $message, $from);
$File =$user_id.".txt";
$Handle = fopen($File, 'a');
$date = date("Y-m-d H:i:s");
$total = array ();

foreach($results as $result) {

// Note that only the Status "Success" means the message was sent
         //echo " Number: " .$result->number;
         //echo " Status: " .$result->status;
         //echo " MessageId: " .$result->messageId;

$receiver = $result->number; 		
$smsuserID = 	$user_id;					 
$date = date("Y-m-d H:i:s");            
$cost= $result->cost;   
		$cost = kes($cost);			
$status = $result->status; 
$messageID = $result->messageId; 

$Data = "$receiver $smsuserID $message $date $cost $status $messageID $date \n"; 
fwrite($Handle, $Data); 
$total[] = $cost;
$id = "";

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("INSERT INTO sms_logs (sms_logsID,receiver,smsuserID,message,status,messageID,date,cost)VALUES(?,?,?,?,?,?,?,?)");
$stmt->execute(array($id,$receiver,$smsuserID,$message,$status,$messageID,$date,$cost));

# If no row is found for query
if( $stmt->rowCount() == 0 ) { $senderrors [] = "Error in insert of sms logs.";}
}

$totalCost = array_sum($total);

// Update the balance
$query="UPDATE sms_users SET bal = (bal - ?) WHERE airtimeid =?";
$sth = $pdo->prepare($query);
$sth->execute(array($totalCost,$smsuserID));
# If no row is found for query
if( $sth->rowCount() == 0 ) { $senderrors [] = "Database balance has not  been updated.";}

Database::disconnect(); // Free memory used in this query

//If successful show us the updated results
$final = 'http://www.airtimekenya.com/thankyou.htm';
header("Location: $final");
exit;

fclose($Handle);
}

catch ( AfricasTalkingGatewayException $e) { $senderrors [] = $e->getMessage(); }
	
 if (!empty($senderrors)) {
    $txt = implode(" ",$senderrors);
    $file = fopen("rainisenderrors.txt", "a");
    $date = date("Y-m-d H:i:s");
    $info = "$date $txt \n"; 
    fwrite($file, $info);
    fclose($file);
	}

	// SMS APP ENDS HERE.

//if there are errors
} else { 

$errorx = implode(" ",$errors);
//echo "<b style=\"color:brown;\">"; echo $error; echo "</b><br />";
$date = date("Y-m-d H:i:s");
$myfile = fopen("rainierrors.txt", "a") or die("Unable to open file!");
$txtx = "$date $errorx \n"; 
fwrite($myfile, $txtx);
fclose($myfile);
}

echo $_GET['user_id'];
//Client has not submitted the form with user id
} 

?>