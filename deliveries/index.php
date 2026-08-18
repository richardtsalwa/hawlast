<?php
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();  
    
}

include 'config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = date("Y-m-d");

//fetch orders order
$sql = "SELECT *  FROM orders";

if ($stmt = $conn->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    die("Error preparing statement: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders with pending delivery</title>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"; margin: 20px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 90%; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        h2, h3 { color: #2c3e50; }
        .student-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 12px; background-color: #ecf0f1; border-radius: 6px; }
        .student-item:nth-child(even) { background-color: #fafbfb; }
        .present-btn { 
            background-color: #e74c3c; /* Red for Absent */
            color: white; 
            border: none; 
            padding: 8px 12px; 
            cursor: pointer; 
            border-radius: 5px; 
            transition: background-color 0.3s ease; 
        }
        .present-btn.present { 
            background-color: #2ecc71; /* Green for Present */
        }
        .present-btn:hover { opacity: 0.9; }
        #previous-day-form { margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 6px; border: 1px solid #e9ecef; }
        #previous-day-form label { font-weight: bold; margin-right: 10px; }
        #previous-day-form input[type="date"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        #previous-day-form button { padding: 8px 15px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        #previous-day-form button:hover { background-color: #2980b9; }
        #previous-attendance-results { margin-top: 20px; }
        #previous-attendance-results ul { list-style: none; padding: 0; }
        #previous-attendance-results ul li { padding: 8px 0; border-bottom: 1px solid #eee; }

    /* Add this CSS for the responsive table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border: 1px;
    }
    th, td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }
    th {
        background-color: #3498db;
        color: white;
    }
    tr:hover {
        background-color: #f5f5f5;
    }
    .present-btn {
        background-color: #e74c3c; /* Red for Absent */
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    .present-btn.present {
        background-color: #2ecc71; /* Green for Present */
    }
    .present-btn:hover {
        opacity: 0.9;
    }
    /* Responsive styles for small screens */
    @media screen and (max-width: 600px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        tr {
            border: 1px solid #ccc;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: right;
        }
        td:before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
        }
        .present-btn {
            width: 100%;
            text-align: center;
        }
    }
    
    /* CSS for the Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}
.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 500px;
    position: relative;
}
.closeBtn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}
.closeBtn:hover,
.closeBtn:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-group input {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.submitBtn, #addStudentBtn {
    padding: 10px 20px;
    background-color: #2ecc71;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}
.submitBtn:hover, #addStudentBtn:hover {
    background-color: #27ae60;
}
</style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        
         <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Orders - <?php echo date("F j, Y"); ?></h2>
        <a href="logout.php" style="color: #e74c3c; text-decoration: none; font-weight: bold;">Logout</a>
    </div>
    

       <div id="client-list">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Order Number</th>
                 <th>Amount</th>
                             <th>Phone</th>    
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
  <?php
  if ($result->num_rows > 0) {
  // Rewind the result set to the beginning
                $result->data_seek(0);
                while($row = $result->fetch_assoc()) {
                    $status_class = $row["status"] ? 'Paid' : '';
                    $status_text = $row["status"] ? 'Paid' : 'Pending';
 echo '<tr>';

echo '<td data-label="Name">' . htmlspecialchars($row["client_name"]) . '</td>';
    
echo '<td data-label="Order Number">' . htmlspecialchars($row["order"]) . '</td>';
                    
echo '<td data-label="Amount">' . htmlspecialchars($row["amount"]) . '</td>';
       
echo '<td data-label="Phone">' . htmlspecialchars($row["phone"]) . '</td>';
               
echo '<td data-label="Status">';
                    echo '<button type="button" class="present-btn ' . $status_class . '" data-student-id="' . $row["status"] . '">' . $status_text . '</button>';
                    echo '</td>';
                    
                    echo '</tr>';
                }
            } else {
                echo "<tr><td colspan='3'>No orders found in the database.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

	
    </div>

    <script>
        $(document).ready(function() {
            // AJAX for marking attendance
            $('#client-list').on('click', '.present-btn', function() {
                var btn = $(this);
                var orderId = btn.data('order_id');
                var isPresent = btn.hasClass('present');
                
                $.ajax({
                    url: 'update_attendance.php',
                    type: 'POST',
                    data: {
                        order_id: studentId,
                        status: !isPresent // Toggle status
                    },
                    success: function(response) {
                        if (response === "success") {
                            btn.toggleClass('paid');
                            btn.text(btn.hasClass('paid') ? 'Paid' : 'Pending');
                        } else {
                            // Display the error message from the server
                            alert("Error updating attendance: " + response);
                            console.error("Server error:", response);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("AJAX Error: Could not connect to the server.");
                        console.error("AJAX Error:", status, error);
                    }
                });
            });

         
        });
    </script>
    

</body>
</html>
<?php
$conn->close();
?>