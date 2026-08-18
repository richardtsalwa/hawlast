<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'hawlastke.php';
//SESSION  STARTED IN HAWLASTKE.PHP 

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");

/* Set e-mail recipient */
$myemail  = "support@hawlast.com";
$subject  = "Enquiry - hawlast.com";
$ip = $_SERVER['REMOTE_ADDR'];
//$ip = isset($_SERVER[’HTTP_X_FORWARDED_FOR’]) ? $_SERVER[’HTTP_X_FORWARDED_FOR’] : $_SERVER[’REMOTE_ADDR’];

$hostaddress = gethostbyaddr($ip);
$browser = $_SERVER['HTTP_USER_AGENT'];
$referred = $_SERVER['HTTP_REFERER']; 
/* Check all form inputs using check_input function */
$yourname = check_input($_POST['username'], "Enter your name");
$email    = check_input($_POST['email']);
$phone  = check_input($_POST['phone']);
$comments = check_input($_POST['from'], "Write your message");


// Make sure answer exists
if (!isset($_SESSION['answer'])) {
    show_error("Session expired. Please reload the form.");
}

// Validate
if ((int)$_POST['math'] !== (int)$_SESSION['answer']) {
    unset($_SESSION['answer']); // clear used value
    show_error("Incorrect math answer. Please try again.");
} else {

// If correct
unset($_SESSION['answer']);

}

/* If e-mail is not valid show error message */
if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
{
    show_error("E-mail address not valid.");
}

if (preg_match("/\D/",$phone))
{
   show_error("Please enter a valid phone number.");
}

if (!is_numeric($phone))
{
   show_error("Please enter a valid phone number.");
}

if(strlen($phone) <> 10)
{
  show_error("Phone number should be ten digits,not more or less.");
}

if(strlen($yourname) < 3)
{
  show_error("Please enter your name.");
}

if(strlen($comments) < 5)
{
  show_error("Your message should be more than 5 characters.");
}
/* Let's prepare the message for the e-mail */
$message = "

$comments\n

$yourname \n
E-mail: $email \n
Phone: $phone \n
IP: $ip \n
Host address: $hostaddress \n
Browser: $browser \n
Referer: $referred \n
";

$attachments="";
$companyName ="Hawlast Ventures";
/* Send the message using mail() function */
//mail($myemail, $subject, $message,"From: $yourname <$email>");
$tuma = sendEmail($myemail, $companyName, $subject, $message, $attachments); 
if ($tuma == true) {
/* Redirect visitor to the thank you page */
header('Location: thankyou.php');

} else { 
echo ("<p>Message delivery failed...$email</p>"); 
}

exit();

/* Functions we used */
function check_input($data, $problem='')
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($problem && strlen($data) == 0)
    {
        show_error($problem);
    }
    return $data;
}

function show_error($myError)
{
?>

<b>Please correct the following error:</b><br />
<?php echo $myError; ?>

<?php
exit();
}
?>