<?php
//Login form
require 'db.php';
session_start();

if (isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])){
            // Set session variables after successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to a protected page after login (e.g., dashboard)
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Wrong password";
        }
    } else {
        echo "User not found";
    }
}

// Redirect to login if not logged in (add this check at the top)
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); // Redirect to dashboard if logged in
    exit();
}
?>

<!DOCTYPE html>
<form action="login.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit" name="submit">Login</button>
</form>
