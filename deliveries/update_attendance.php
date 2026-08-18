<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
    $is_present = isset($_POST['is_present']) ? filter_var($_POST['is_present'], FILTER_VALIDATE_BOOLEAN) : false;
    $today = date("Y-m-d");

    // This statement will insert a new row if one doesn't exist for the student and date.
    // If a row already exists (e.g., you click the button multiple times on the same day),
    // it will simply update the 'is_present' status.
    $sql = "INSERT INTO attendance (student_id, attendance_date, is_present) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE is_present = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $student_id, $today, $is_present, $is_present);

    if ($stmt->execute()) {
        echo "success";
    } else {
        error_log("Error updating record: " . $stmt->error);
        echo "error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>