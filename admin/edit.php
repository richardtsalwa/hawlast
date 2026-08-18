<?php
 /*** start the session ***/
session_start();
ini_set('display_errors',1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
    
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

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />
<title>New client welcome info</title>                
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
<link rel="stylesheet" type="text/css" href="images/calendar.css" media="all">
<script type="text/javascript" src="images/calendar.js"></script>
</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a> 
<a href="listed.php">Listed</a>  
<a href="user.php?pwd=1">Account</a> 
<a href="addnew.php">Add new</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>  
<div id="form_container">
<?php
 if(isset($_GET['id'])) {	
	$id = protect($_GET['id']);
	$id = tupu($_GET['id']);
	$id = marks($_GET['id']);
	$sql ="SELECT id, firstname, lastname, service, cost, url, renewal_date, email, emailalt, phone FROM renewals WHERE id = ?";
	$stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
	if ($myrow = $stmt->fetch(PDO::FETCH_ASSOC))  {
	// show form
	?>
	<form class="appnitro" method="post" action="renewals.php">
	<div class="form_description"><h2>Update client information</h2></div>							
	<ul>
	<li id="li_1"><label class="description" for="element_1">First name: </label>
	<div><input id="element_1" name="firstname" class="element text medium" type="text" maxlength="255" value="<?php echo $myrow["firstname"]; ?>"/> 
	</div> 
	</li>		
	<li id="li_2" >
	<label class="description" for="element_2">Second Name: </label>
	<div>
	<input id="element_2" name="lastname" class="element text medium" type="text" maxlength="255" value="<?php echo $myrow["lastname"]; ?>"/>
	</div> 
	</li>		
<li id="li_4" >
<label class="description" for="element_4"><b style="color:red;">Date of renewal:</b> </label>
<input id="element_7" name="date1" class="element text medium" type="date" value="<?php echo $myrow["renewal_date"]; ?>"/>
		
	</li>
           <li id="li_5" >
		<label class="description" for="element_5">Phone number </label>
		<div>
		<input id="element_5" name="phone" class="element text medium" type="text" maxlength="18" value="<?php echo $myrow["phone"]; ?>"/> 
		</div> 
		</li><li id="li_7">
		<label class="description" for="element_7">Email: </label>
		<div>
		<input id="element_7" name="email" class="element text medium" type="text" maxlength="255" value="<?php echo $myrow["email"]; ?>"/>
		</div> 
		</li>	
           <li id="li_7">
		<label class="description" for="element_7">Alternative email: </label>
		<div>
		<input id="element_12" name="emailalt" class="element text medium" type="text" maxlength="255" value="<?php echo $myrow["emailalt"]; ?>"/>
		</div> 
		</li>
	   <li id="li_6">
		<label class="description" for="element_6">Web Site </label>
		<div>
		<input id="element_6" name="url" class="element text medium" type="text" maxlength="255" value="<?php echo $myrow["url"]; ?>"/>
		</div> 
		</li>		
		<li id="li_9">
		<label class="description" for="element_9">Service:</label>
		<div>
		<select class="element select medium" id="element_9" name="service"> 
                <option selected="selected" value="<?php echo $myrow["service"]; ?>"><?php echo $myrow["service"]; ?></option>
		<?php
		// Prepare and execute the query
$query = "SELECT service_name, service_cost FROM dservices ORDER BY service_cost ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch and display the results
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $service_name = htmlspecialchars($row['service_name']);
    $service_cost = htmlspecialchars($row['service_cost']);
    echo "<option value=\"$service_name\">$service_name $service_cost</option>";
}


?>
		</div> 
		</li>	
		<li id="li_8" >
		<label class="description" for="element_8">Cost </label>
		<span class="symbol">Ksh.</span>
		<span>
		<input id="element_8_1" name="cost" class="element text currency" size="10" type="text" value="<?php echo $myrow["cost"]; ?>"/>
		</span>
		</li>				
	<li class="buttons">
	<input type="hidden" name="id" value="<?php echo $myrow["id"]; ?>"/>	
	<input type="hidden" name="form_id" value="122314" />	    
	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
	</li>
	</ul>
	</form>
<br />
<br />
<br />
 <form method="POST" action="delete.php">
 <input name="id" type="hidden" value="<?php echo marks($_GET['id']); ?>">
 <input type="submit" value="Delete this account">
 </form>
<?php } } ?>
<div id="footer">
By <a href="https://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>