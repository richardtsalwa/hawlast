<?php
 /*** start the session ***/
    session_start();
    if(isset($_SESSION['adaccess_level']))
        {
                $log_link = 'logout.php';
                $log_link_name = 'Log Out';
                $reg_link = '#';
                $reg_name = $_SESSION['valid_user'];
        }        else        {
                $log_link = 'login.php';
                $log_link_name = 'Log In';
                $reg_link = 'register.php';
                $reg_name = 'Register';
	      $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();
require '../config.php';
require '../hawlastke.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>

<title>New client welcome info</title>
<meta name=robots content=noindex,nofollow />
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<link rel="stylesheet" type="text/css" href="images/calendar.css" media="all">
<script type="text/javascript" src="images/calendar.js"></script>

</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="index.php">Home</a> <a href="<?php echo $log_link; ?>"<?php $current = '/login.php' || '/register.php'; page($current); ?>><?php echo $log_link_name; ?></a>    
<a href="listed.php">Listed</a>   
<div id="form_container">
<?php
if (isset($_POST['submit'])) {

$errors ="";

        if ($_POST['id'] != "") {
            $id = intval($_POST['id']);
            if (!intval($id)) {
                $errors .= "$id is <strong>NOT</strong> valid number.<br/><br/>";
            }
        } else {
            $errors .= 'Please go to the list of accounts and start again.<br/>';
        }

        if ($_POST['firstname'] != "") {
            $firstname = $_POST['firstname'];
            $firstname = ucfirst(strtolower($firstname));
            if ($_POST['firstname'] == "") {
                $errors = 'Please enter a valid first name.<br/><br/>';
            }
        } else {
            $errors .= 'Please enter your first name.<br/>';
        }

        if ($_POST['lastname'] != "") {
            $lastname = $_POST['lastname'];
            $lastname = ucfirst(strtolower($lastname));
            if ($_POST['lastname'] == "") {
                $errors .= 'Please enter a valid last name.<br/><br/>';
            }
        } else {
            $errors .= 'Please enter your last name.<br/>';
        }

        if ($_POST['service'] != "") {
            $service = protect($_POST['service']);
            if ($_POST['service'] == "") {
                $errors .= 'Please enter a valid service.<br/><br/>';
            }
        } else {
            $errors .= 'Please enter the service.<br/>';
        }
 
        if ($_POST['cost'] != "") {
            $cost = intval($_POST['cost']);
            if (!intval($cost)) {
                $errors .= "$cost is <strong>NOT</strong> valid number.<br/><br/>";
            }
        } else {
            $errors .= 'Please enter cost as a number.<br/>';
        }

        if ($_POST['url'] != "") {
        $url = protect($_POST['url']); 
        $url = GetDomain($url);
        $finalurl = "https://www.".$url;
        if (!validate_url($finalurl)) 
          {$errors .= "$finalurl is <strong>NOT</strong> a valid DOMAIN.<br/><br/>";}
        } else {
            $errors .= 'Please enter a website.<br/>';
        }

         //renewal date 
        if ($_POST['date1'] != "") {
    $renewal_date = $_POST['date1'];
        } else {
            $errors .= 'Please enter your renewal date.<br/>';
        }

        if ($_POST['email'] != "") {

            $email = strtolower($_POST['email']);            
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors .= "$email is <strong>NOT</strong> a valid email address.<br/><br/>";
            }
        } else {
            $errors .= 'Please enter your email address.<br/>';
        }

        if ($_POST['emailalt'] != "") {
            $emailalt = strtolower($_POST['emailalt']);            
            $emailalt = filter_var($emailalt, FILTER_SANITIZE_EMAIL);
            if (!filter_var($emailalt, FILTER_VALIDATE_EMAIL)) {
                $errors .= "$emailalt is <strong>NOT</strong> a valid email address.<br/><br/>";
            }
        } else { 
            $emailalt = "";
        }

        if ($_POST['phone'] != "") {
            $phone = protect($_POST['phone']);
            } else {
            $errors .= 'Please enter a phone number.<br/>';
        }

        if (!$errors) {
	
$sql = "UPDATE renewals SET firstname=?, lastname=?, service=?, cost=?, url=?, renewal_date=?, email=?, emailalt=?, phone=? WHERE id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$firstname, $lastname, $service, $cost, $finalurl, $renewal_date, $email, $emailalt, $phone, $id]);

$rows_affected = $stmt->rowCount(); // Check how many rows were updated

    if ($rows_affected > 0) {
    header("Refresh: 3; url=listed.php"); // 3 seconds delay
   echo "Client updated... Redirecting in 3 seconds"; 
	exit;
    } else {
        echo "No record found with that ID or no changes were made.";
    }
    
      } else { require 'includes/formerror.txt'; }

    } else { echo "Nothing to update."; }
?>
<div id="footer">
By <a href="https://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>