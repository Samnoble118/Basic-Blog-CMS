<?php
require 'db.php';
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32)); 
        $expiry_time = date("Y-m-d H:i:s", strtotime('+1 hour')); 

        // Save token and expiry time in the database
        $query = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $query->bind_param("sss", $token, $expiry_time, $email);
        $query->execute();

        // Send the email
        $reset_link = "http://yourwebsite.com/reset_form.php?token=" . $token;

        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $reset_link";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "Password reset link has been sent to your email.";
        } else {
            echo "There was an issue sending the email.";
        }
    } else {
        echo "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="wrapper">
        <form action="reset_password.php" method="POST">
            <h2>Reset Password</h2>
            <div class="form-group">
                <label for="email">Enter your email address:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Send Reset Link</button>
        </form>

        <br>
        <a href="login.php" class="btn btn-secondary">Go Back to Login</a>
    </div>
</body>
</html>
