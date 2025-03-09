<?php
require 'db.php';
session_start();

// Get token from URL
$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid token.");
}

// Check if token exists and is not expired
$query = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
$query->bind_param("s", $token);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    die("Invalid or expired token.");
}

if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password and clear reset token
    $query = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
    $query->bind_param("ss", $hashed_password, $token);
    $query->execute();

    echo "Password has been successfully reset!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="wrapper">
        <form action="reset_form.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <h2>Set New Password</h2>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
</body>
</html>
