<?php

include 'config.php';

if ($conn->connect_error) {
    // Return a JSON error message for a cleaner AJAX response
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if a date parameter is set and is in the expected format (YYYY-MM-DD)
if (isset($_GET['date']) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_GET['date'])) {
    $date = $_GET['date'];
    
    // SQL query to get student names and their attendance status for the selected date
    $sql = "SELECT s.student_name, a.is_present 
            FROM students s 
            LEFT JOIN attendance a ON s.student_id = a.student_id AND a.attendance_date = ? 
            ORDER BY s.student_name";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Output the results in a formatted HTML list
        echo "<h4>Attendance for " . htmlspecialchars(date("F j, Y", strtotime($date))) . "</h4>";
        echo "<ul>";
        while($row = $result->fetch_assoc()) {
            $status = $row['is_present'] ? 'Present ✅' : 'Absent ❌';
            echo "<li>" . htmlspecialchars($row['student_name']) . ": " . $status . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No students found or no attendance data available for this date.</p>";
    }

    $stmt->close();
} else {
    // Handle invalid or missing date parameter
    http_response_code(400);
    echo "<p>Error: Invalid date format. Please select a valid date.</p>";
}

$conn->close();
?>
