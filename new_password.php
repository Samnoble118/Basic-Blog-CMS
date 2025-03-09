<?php
require 'db.php';
session_start();

if (isset($_POST['submit'])) {
    // Get the new password and confirmation
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $token = $_GET['token'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Validate token from the URL
    $query = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires > ?");
    $query->bind_param("si", $token, date("U"));
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Token is valid
        $reset_request = $result->fetch_assoc();
        $email = $reset_request['email'];

        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $query = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $query->bind_param("ss", $hashed_password, $email);
        $query->execute();

        // Delete the token from the database as it's no longer needed
        $query = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $query->bind_param("s", $token);
        $query->execute();

        echo "Your password has been successfully reset. You can now <a href='login.php'>login</a> with your new password.";
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }
        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="text-center">Set New Password</h2>
        <form action="new_password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="POST">
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
