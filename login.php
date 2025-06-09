<?php
include 'config.php';
include 'header.php';
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $username, $hashed_password);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION["user"] = $username;
                $success = "Login Successful! Redirecting...";
                echo "<script>
                    setTimeout(function(){
                        window.location.href = 'canti.php'; // Directly redirect after 3 seconds
                    }, 3000);
                </script>";
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Smart Tutor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Outer Box Styling */
        .outer-box {
            background-color: aliceblue;
            border: 2px solid green;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .outer-box:hover {
            transform: scale(1.05);
            box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.3);
        }

        /* Container Styling */
        .container {
            width: 100%;
            background: white;
            border-radius: 8px;
            padding: 20px;
        }

        input, button {
            width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc;
        }

        button { background-color: orangered; color: white; border: none; }

        .error-message, .success-message { font-size: 14px; margin-top: 10px; text-align: center; }
        .error-message { color: red; }
        .success-message { color: green; }

    </style>
</head>
<body>

<!-- Outer Box placed around the existing login form -->
<div class="outer-box">
    <div class="container">
        <h2>Login</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="error-message"><?php if ($error) echo $error; ?></p>
        <p class="success-message"><?php if ($success) echo $success; ?></p>
        <p>Don't have an account? <a href="register.php" style="color: blue; text-decoration: none;">Register</a></p>

    </div>
</div>

</body>
</html>
