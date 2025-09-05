<?php
    session_start();
    if (isset($_SESSION["auth"]) && $_SESSION["auth"] ==false) {
        header("Location: login.php");
        exit();
    }

    else {
        echo "Logged in as:", htmlspecialchars($_SESSION["user"]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    h1 {
        text-align: left;
    }

    body {
        font-family: Verdana;
        font-size: 100%;
        background-color: #d8e7ff;
    }

    input[type=number] {
        width: 40px;
    }

    .selection {
        font-family: Verdana;
        font-size: 11pt;
        border: 1px solid #07A2FE;
        color: black;
        background-color: aliceblue;
        height: 25px;
        text-align: left;
    }

    .button {
        font-family: Verdana;
        background-color: aliceblue;
        font-size: 12pt;
        padding: 5px;
    }

    fieldset {
        padding: 20px;
    }

    legend {
        border: 1px solid black;
        color: black;
        font-size: 90%;
        text-align: left;
    }

    .outputStyle {
        color: #523620;
        text-align: center;
        margin: 20px 10px;
        width: 550px;
        height: auto
    }
</style>
<body>
    <div id="Header">
        <h1>Welcome to Inventory Management System</h1>
    </div>
<div>
    <fieldset>
        <legend>Receive Inventory</legend>
        <label>Part Number:</label>
        <input type="text" name="part_number"><br><br>
        <label>Description:</label>
        <input type="text" name="part_description"><br><br>
        <label>Quantity:</label>
        <input type="text" name="add_quantity"><br><br>
        <input type="add" value="Add">   
        <br><br>        
    </fieldset>   
</div>
<div>
        <fieldset>
            <legend>Inventory</legend>
            <div id="divInventoryOutput" class="outputStyle">
                [Inventory List Here]
            </div>
        </fieldset>
    </div>
    <br><br>

    <fieldset>
        <legend>Pick Inventory</legend>
        <label>Line Number:</label>
        <input type="text" name="pick_partnumber"><br>
        <input type="pick" value="Pick">
        <br><br>
    </fieldset>

<fieldset>
    <legend>Search Inventory</legend>
    <label>Quantity:</label>
        <input type="text" name="partnumber"><br><br>
        <input type="search" value="Search">
        <br><br>
</fieldset>


    <div id='foot'>
        <p>Damon Morgan © 2025</p>
        <p><a href="https://www.linkedin.com/in/damon-morgan/">LinkedIn</a></p>
      </div>
</body>
</html>

