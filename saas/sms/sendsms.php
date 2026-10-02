<?php
if ( !empty($_POST)) {
//THIS FILES IS USED BY THE MPESA CONFIRMATION SCRIPT

require '../config.php';
require '../database.php';
require '../functions.php';

$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("anypost.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Send BULK SMS</title>
 <meta charset="utf-8">
<meta name="description" content="">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
<link rel="shortcut icon" href="../img/favicon.ico" type=image/x-icon />
<link   href="../css/bootstrap.min.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<a href="../index.php" class="btn btn-success">Home</a>   
<br />
<?php
if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");

if (isset($_POST['user_id'])) {
$phonenumber = safe($_POST['phone']);

//chehck if we have several numbers comma separated
if (strpos($phonenumber, ',') === false) {
// Single phone number
$phonenumber = add254($phonenumber);
} 
    
$message = safe($_POST['message']);
$user_id = safe($_POST['user_id']);
$bala = safe($_POST['bal']);

$message = protect($message);
$user_id = protect($user_id);
$balance = protect($bala);

//THIS FILES IS ALSO USED for THE MPESA CONFIRMATION SCRIPT

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

if(count($errors) == 0){  
 
// SEND sms to the admin 
require_once dirname(__DIR__, 2) . '/libraries.php';

// Specify your login credentials 
$username = "hawlast";
$apiKey      = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 

$recipients =$phonenumber;
$mimi = "HAWLAST";
// Create a new instance of our awesome gateway class
$gateway  = new AfricaStalkingGateway($username, $apiKey);

try 
{ 
  // Thats it, hit send and we'll take care of the rest. 
$results = $gateway->sendMessage($recipients,$message,$mimi);
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
if( $stmt->rowCount() == 0 ) {
echo "Error in insert of sms logs.";
} 

}

$totalCost = array_sum($total);

// Update the balance
$query="UPDATE sms_users SET bal = (bal - ?) WHERE airtimeid =?";
$sth = $pdo->prepare($query);
$sth->execute(array($totalCost,$smsuserID));
# If no row is found for query
if( $sth->rowCount() == 0 ) {
echo "Balance has not been updated.";
}

Database::disconnect(); // Free memory used in this query

//If successful show us the updated results
$path = dirname($_SERVER['REQUEST_URI']);
$final = 'https://'.$_SERVER['HTTP_HOST'].$path.'/thankyou.php';
$user = '?user_id=';
$id = $user_id;
header("Location: $final$user$id");
exit;

fclose($Handle);
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while sending: ".$e->getMessage();
}
	// SMS APP ENDS HERE.

//if there are errors
} else { foreach($errors AS $error){ 
echo "<b style=\"color:brown;\">"; echo $error; echo "</b><br />";





}  }

//DISPLAY FORM
?>
<h3>Send your message</h3>
<form name="demo" method="POST" action="sendsms.php">
<input id=user_id name=user_id type=hidden value="<?php if(isset($_SESSION['user_id'])) {echo $_SESSION['user_id']; } ?>" />

<input id=bal name=bal type=hidden value="<?php echo $balance; ?>" /> 

<div class=form_row>
<label>The phone numbers in format 07XXyyyZZZ </b></label>
</div>

<div class=form_row>
<label class="contact"><b>Phone Numbers:</b></label>
<input id=Phone name=phone height="20px" type=text class=contact_input rows="10" cols="50" value="07" required/>
</div>

<div class=form_row>
<label class=contact><b>Message:</b></label>
<textarea id=Message name="message" rows="7" cols="25" class="contact_textarea" maxlength="160" required ></textarea>
</div>
<div class=contact>
<input id="Submit" name="Submit" value="Send SMS" class="button_text" type="submit"><br />
</div>
</form>
 <p><a href="/saas/airtime" class="btn btn-success">Send airtime</a></p>
<p><a href="https://www.hawlast.com/domain-registration/" target="_blank" class="btn btn-success">Order a website</a></p>
 <p><a href="../logout.php" class="btn btn-success">Log out</a></p>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="https://www.hawlast.com/saas/">www.hawlast.com/saas/</a></div>
</div>

</div>

</body></html>
<?php 
//Client has not submittted the form with user id
} else { header('Location: ./index.php');}

///THIS FILES IS USED by THE MPESA API CONFIRMATION SCRIPT SO IF YOU CHANGE THE POST VARIABLES 
}
?>