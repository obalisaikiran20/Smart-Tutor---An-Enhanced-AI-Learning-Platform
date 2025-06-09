<?php
include 'config.php';
include 'header.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $username = trim($_POST["username"]);

    if (empty($email) || empty($password) || empty($confirm_password) || empty($username)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password before saving to database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
            
            // Redirect after success
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php'; // Redirecting to login page
                }, 3000); // Wait for 3 seconds before redirect
            </script>";
        } else {
            $error = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - Smart Tutor</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; margin: 0; padding: 0; }

        /* Wrapper for layout */
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        /* Registration form container */
        .registration-form {
            width: 400px;
            padding: 20px;
            background-color: aliceblue; /* Light Alice Blue */
            border-radius: 16px;
            border: 3px solid green; /* Green border */
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        /* Form input fields and button */
        input, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: orangered;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error-message, .success-message {
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            color: red;
        }

        .error-message { color: red; }
        .success-message { color: green; }

        .loading {
            display: none;
            margin: 20px auto;
            position: relative;
            width: 80px;
            height: 80px;
        }

        .loading img {
            width: 50px;
            position: absolute;
            top: 15px;
            left: 15px;
        }

        .loading::after {
            content: "";
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 5px solid blue;
            border-top-color: transparent;
            position: absolute;
            top: 0;
            left: 0;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        a {
            color: blue;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Centered registration form -->
    <div class="registration-form">
        <h2>Register</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <p class="error-message"><?php if ($error) echo $error; ?></p>
        <p class="success-message"><?php if ($success) echo $success; ?></p>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<div id="loading" class="loading">
    <img src="assets/images/loading.jpeg" alt="Loading">
</div>

</body>
</html>
