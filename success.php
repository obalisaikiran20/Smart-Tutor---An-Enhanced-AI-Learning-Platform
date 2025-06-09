<?php
include 'config.php';
include 'header.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["user"];

// Fetch user details from `users` table
$user = $conn->query("SELECT id, username FROM users WHERE username = '$username'")->fetch_assoc();
$userId = $user["id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Success - Smart Tutor</title>
</head>
<body>
    <div style="text-align: center; padding: 50px;">
        <h2>Profile Updated Successfully!</h2>
        <p>Hello World</p>
    </div>
</body>
</html>
