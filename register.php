<?php
// Register user
require 'db.php';
session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    //password hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //check if user exists
    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "User already exists";
    } else {
        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        $result = $conn->query($query);

        if ($result) {
            echo "User registered successfully";
        } else {
            echo "Error registering user";
        }
    }
}
$query = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$query->bind_param("ss", $username, $email);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Register</title>
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
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="submit">Register</button>
        </form>
    </body>