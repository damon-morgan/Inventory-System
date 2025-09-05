<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id="Header">
      <h2>Please Authenticate Below</h2>
    </div>
    <div id="LoginForm">
        <form action="login.php" method="post">
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
    require 'db.php';

    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["auth"] = true;
        $_SESSION["user"] = $_POST["username"];
        header("Location: index.php");
        exit();
    } 
    else {
        $_SESSION["auth"] = false;
        echo"Incorrect username or password";
    }
    
?>