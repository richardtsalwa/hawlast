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
    <title>Register TSC Match</title>
</head>
<body>
    <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v19.0&appId=202015096126" nonce="fuJg6fvS"></script>

<?php
$filename = "register.txt";  

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
  
$county = $_POST["county"];
$school = $_POST["school"];
$subject1 = $_POST["subject1"];
$subject2 = $_POST["subject2"];
$subject3 = $_POST["subject3"];
$countya = $_POST["countya"];
$countyb = $_POST["countyb"];
$countyc = $_POST["countyc"];
$phone = $_POST["phone"];
$firstname = $_POST["firstname"];

$excludedFields = ["subject3", "countyc"];  // Fields to exclude from empty check

$emptyFields = [];  // Array to store the names of empty fields

// Check each field except the excluded ones
foreach ($_POST as $fieldName => $fieldValue) {
  if (!in_array($fieldName, $excludedFields) && empty($fieldValue)) {
    $emptyFields[] = $fieldName;
  }
}

// Check if there are any empty fields
if ($emptyFields) {
  echo "The following fields are empty: " . implode(', ', $emptyFields);
  echo "<a href=\"index.php\">Go back to home page</a>";
} else {
  //echo "All fields are filled.";

$date = date('Y-m-d');

try {
  $data = [
    'firstname' => $firstname,
    'phone' => $phone,
    'county' => $county,
    'subject1' => $subject1,
    'subject2' => $subject2,
    'subject3' => $subject3,
    'school' => $school,
    'countya' => $countya,
    'countyb' => $countyb,
    'countyc' => $countyc,
    'date' => $date,
  ];
  
    // Check for duplicate phone number before insert
  $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM teachers WHERE phone = :phone');
  $checkStmt->execute([':phone' => $phone]);
  $existingCount = $checkStmt->fetchColumn();

  if ($existingCount > 0) {
    // Handle duplicate phone number (e.g., log error, return specific message)
    throw new PDOException("Phone number already registered. We will keep checking for a match. Call 0720401869 for support");
      }
  
  $insertStmt = $pdo->prepare('INSERT INTO teachers (firstname, phone, county, subject1, subject2, subject3, school, countya, countyb, countyc, date) VALUES (:firstname, :phone, :county, :subject1, :subject2, :subject3, :school, :countya, :countyb, :countyc, :date)');
  $success = $insertStmt->execute($data);

  if ($success) {
    // Data inserted successfully (optional: handle success)
    //return true;
   echo "<h1>Registration Successful!</h1>";
      echo "<p>You are now registered. <br>Share this with your fellow teachers to increase your chances of finding a match.<br><a href=\"index.php\">Try a diffrent search with diffrent county combinations</a></p>";

    ?>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="share-buttons">
  <a href="https://wa.me//send?text=TSC Teacher County to County Swap https://hawlast.com/teachers" title="Share on WhatsApp">
    <i class="fab fa-whatsapp fa-2x"></i>
  </a>
  <a href="https://twitter.com/share?url=[https://hawlast.com/teachers]&text=TSC Teacher to Teacher Swap -  County to County Swap " target="_blank" title="Share on Twitter">
    <i class="fab fa-twitter fa-2x"></i>
  </a>

<div class="fb-share-button" data-href="https://www.hawlast.com/teachers/" data-layout="" data-size=""><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.hawlast.com%2Fteachers%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>

<?php
	} else {
    // Insertion failed (optional: handle error)
    throw new PDOException("Insert failed");
  }

} catch (PDOException $e) {
  echo $e->getMessage();
  return false;
}

}
?>
</body>
</html>