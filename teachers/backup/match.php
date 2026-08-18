<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'config.php'; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSC Teacher Matching</title>
</head>
<body>
<h1>TSC Teacher Matching (Swap) Results</h1>
<?php

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");


$filename = "searches.txt";  

// Get the POST data
$postData = $_REQUEST;

// Check if there is any POST data
if (!empty($postData)) {
    // Open the file for writing
    $file = fopen($filename, "a");

    // Loop through each POST key and value pair
    foreach ($postData as $key => $value) {
        // Convert the value to a JSON string
        $valueString = json_encode($value);

        // Write the key and value to the file
        fwrite($file, "$key: $valueString\n");
    }

    // Close the file
    fclose($file);
}
  
$county = "";
$school =  "";
$subject1 = "";
$subject2 = "";
$subject3 = "";
$countya =  "";
$countyb =  "";
$countyc =  "";

//User input (replace with how you capture form data)
$county = $_POST["county"];
$school = $_POST["school"];
$subject1 = $_POST["subject1"];
$subject2 = $_POST["subject2"];
$subject3 = $_POST["subject3"];
$countya = $_POST["countya"];
$countyb = $_POST["countyb"];
$countyc = $_POST["countyc"];

//keep testing this query 
$sql = "(SELECT id FROM teachers 
            WHERE countya = ? OR countyb = ? OR countyc = ?)
        UNION
        (SELECT id FROM teachers 
            WHERE county = ? OR county = ? OR county = ?)
        UNION
        (SELECT id FROM teachers 
            WHERE subject1 = ? OR subject2 = ? OR subject1 = ? OR subject2 = ?)
        UNION
        (SELECT id FROM teachers 
            WHERE school = ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$county, $county, $county, $countya, $countyb, $countyc, $subject1, $subject1, $subject2, $subject2, $school]);
//$value = $stmt->rowCount();
if ($stmt->rowCount() > 0) {
$count = $stmt->rowCount(); 
echo "<br>";
}


$sqla = "SELECT id FROM teachers 
                        WHERE countya = ? OR countyb = ? OR countyc = ?";
$stmtx = $pdo->prepare($sqla);
$stmtx->execute([$county, $county, $county]);
if ($stmtx->rowCount() > 0) {
 $value="1";
}

$sqlb = "SELECT id FROM teachers 
                        WHERE county = ? OR county = ? OR county = ?";
$stmtx = $pdo->prepare($sqlb);
$stmtx->execute([$countya, $countyb, $countyc]);
if ($stmtx->rowCount() > 0) {
 $value++;
}

$sqlc = "SELECT id FROM teachers 
                        WHERE 
                        subject1 = ? OR subject2 = ? AND subject1 = ? OR subject2 = ?"; 
$stmtx = $pdo->prepare($sqlc);
$stmtx->execute([$subject1,$subject1, $subject2, $subject2]);
if ($stmtx->rowCount() > 0) {
     $value++;
}

$sqld = "SELECT id FROM teachers 
                        WHERE school = ?";
$stmtx = $pdo->prepare($sqld);
$stmtx->execute([$school]);
if ($stmtx->rowCount() > 0) {
 $value++;
} 

//THE SEARCH HAS MET ALL THE CRITERIO
//IN FUTURE WE CAN SCALE THE RATE OF MATCH
if($value == 4) {
  echo "<b>You have $count matching your criteria!</b><br><br>";
  echo "Make a payment of Ksh 1000 below for us share the details<br><br>";
  echo "Paybill: 822490 Account: Your surname<br><br>";
    echo "Call 0720401869 for any question<br>";
} else {
    echo "<b>We have not found a match for now</b><br><br>";
?>
<p><b></b>Register to get an SMS when a match  is found</b></b></p>
<form action="register.php" method="post">
<input type="hidden" id="county" name=county value="<?php echo $county; ?>" required>
<input type="hidden" id="school" name=school value="<?php echo $school; ?>" required> 
<input type="hidden" id="subject1" name=subject1 value="<?php echo $subject1; ?>" required> 
<input type="hidden" id="subject2" name=subject2 value="<?php echo $subject2; ?>" required> 
<input type="hidden" id="subject3" name=subject3 value="<?php echo $subject3; ?>"> 

<input type="hidden" id="countya" name=countya value="<?php echo $countya; ?>" required>
 <input type="hidden" id="countyb" name=countyb value="<?php echo $countyb; ?>" required>
 <input type="hidden" id="countyc" name=countyc value="<?php echo $countyc; ?>">
 
 <label for="phone">Your phone number:</label>
 <input type="number" id="phone" name=phone value="" placeholder="07" maxlength="10"><br>
 
 <label for="firstname">Your first name:</label>
 <input type="text" id="name" name=firstname value=""><br>
  
<button type="submit">Register</button>

</form>

<?php
}

// Close connections
$stmtx = null;
$pdo = null;
?>
</body>
</html>