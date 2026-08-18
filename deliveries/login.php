<?php
session_start();

$password = "EEP6/0200/24";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
//echo $hashed_password;

include 'config.php';
require '../hawlastke.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';
$success_message = '';
$show_forgot_form = false;

// Check if the request is for password reset
if (isset($_POST['forgot_password_email'])) {

    $show_forgot_form = true; // keep forgot password form visible
    $email = trim($_POST['forgot_password_email']);

    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Always show same success message for security
    $success_message = "If an account with that email exists, a password reset link has been sent.";

    if ($result->num_rows > 0) {

        // Generate a new secure password (12 characters, alphanumeric)
        $passnew = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);

        // Hash the new password securely
        $hashed_password = password_hash($passnew, PASSWORD_DEFAULT);

        // Update the user table
        $sql_update = "UPDATE users SET password_hash = ? WHERE email = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ss", $hashed_password, $email);
        $stmt_update->execute();
        $stmt_update->close();

        // Send email with new password
        $message = "Your new password is: $passnew";
        $subject = "Password Update";
        $attachments = "";
        $companyName = "Hawlast Ventures";

        if (!sendEmail($email, $companyName, $subject, $message, $attachments)) {
            $error_message = "Email sending error. Please try again.";
        }
    }

    $stmt->close();

} elseif (isset($_POST['email']) && isset($_POST['password'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT user_id, password_hash FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 2em;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 1em;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5em;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 0.8em;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group button {
            width: 100%;
            padding: 0.8em;
            border: none;
            background-color: #007BFF;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 1em;
            text-align: center;
            padding: 0.8em;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 1em;
            color: #007BFF;
            text-decoration: none;
            cursor: pointer;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="login-container">

    <!-- Display messages at the top -->
    <?php if ($error_message): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <!-- Login form -->
    <div id="login-form-section" class="<?php echo $show_forgot_form ? 'hidden' : ''; ?>">
        <h2>Login</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        <a href="#" id="forgot-password-link" class="forgot-link">Forgot Password?</a>
    </div>

    <!-- Forgot password form -->
    <div id="forgot-password-section" class="<?php echo $show_forgot_form ? '' : 'hidden'; ?>">
        <h2>Forgot Password</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="forgot-password-email">Email</label>
                <input type="email" id="forgot-password-email" name="forgot_password_email" required>
            </div>
            <div class="form-group">
                <button type="submit">Send Reset Link</button>
            </div>
        </form>
        <a href="#" id="back-to-login-link" class="forgot-link">Back to Login</a>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#forgot-password-link').on('click', function(e) {
            e.preventDefault();
            $('#login-form-section').addClass('hidden');
            $('#forgot-password-section').removeClass('hidden');
        });

        $('#back-to-login-link').on('click', function(e) {
            e.preventDefault();
            $('#forgot-password-section').addClass('hidden');
            $('#login-form-section').removeClass('hidden');
        });
    });
</script>

</body>
</html>