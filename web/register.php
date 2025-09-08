<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <div id="Header">
        <h2>Please Register Below</h2>
        </div>
        <div id="RegisterForm">
            <form action="register.php" method="post">
            <label>Username:</label><br>
            <input type="text" name="username"><br>
            <label>Password:</label><br>
            <input type="text" name="password"><br>
            <input type="submit" value="Login">
            </form>
        </div>
    </body>
</html>

<?php
session_start();
require 'db.php'; // PDO connection to database

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        $error = "Username already taken.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        $_SESSION["auth"] = true;
        $_SESSION["user"] = $username;
        header("Location: index.php");
        exit();
    }
}
?>