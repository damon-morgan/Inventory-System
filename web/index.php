<?php
    session_start();
    require 'db.php'; // PDO connection to database
    // Check to verify authenication
    if (isset($_SESSION["auth"]) && $_SESSION["auth"] ==false) {
        header("Location: login.php");
        exit();
    }
    // Log user in and display
    else {
        echo "Logged in as:", htmlspecialchars($_SESSION["user"]);
    }


    // View Inventory
    // Run a SELECT query
    $stmt = $pdo->query("SELECT id, partnum, partdesc, quantity FROM inventory ORDER BY id ASC");

    // Fetch all rows
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    #pick {
        height:150px;
    }

    #receive {
        height:150px;
    }

    #invent {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        justify-content: center;
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

    .inventory-table {
        border-collapse: collapse;
        width: 90%;
        margin: 20px auto;
        font-family: Verdana, sans-serif;
    }

    .inventory-table th, .inventory-table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }

    .inventory-table th {
        background-color: #191919;
        color: white;
        font-weight: bold;
    }

    .inventory-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .inventory-table tr:hover {
        background-color: #d0e7ff;
    }

</style>
<body>
    <div id="Header">
        <h1>Welcome to Inventory Management System</h1>
    </div>
<div id='invent'>
    <fieldset id='receive'>
        <legend>Receive Inventory</legend>
        <form action="index.php" method="post">
            <label>Part Number:</label>
            <input type="int" name="part_number"><br><br>
            <label>Description:</label>
            <input type="text" name="part_description"><br><br>
            <label>Quantity:</label>
            <input type="int" name="add_quantity">
            <input type="submit" value="Add">   
            <br><br>
        </form>
        <?php
            // Add Part to Inventory
            // Check if server receives a POST request before continuing
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["part_number"])) {
                $partnum = $_POST["part_number"];
                $desc = $_POST["part_description"];
                $quantity = $_POST["add_quantity"];
                $stmt = $pdo->prepare("INSERT INTO inventory (partnum, partdesc, quantity) VALUES (?, ?, ?)"); // Prepare to add part to inventory
                $stmt->execute([$partnum, $desc, $quantity]); // Execute SQL query above
                header("Location: index.php"); // Reload page to show new results
            }
        ?>
</fieldset>
<fieldset id='pick'>
        <legend>Pick Inventory</legend>
        <form action="index.php" method="post">
            <label>ID Number:</label>
            <input type="text" name="pick_partnumber">
            <input type="submit" value="Pick">
            <br><br>
        </form>
        <?php
            // Delete Part From Inventory
            // Check if server receives a POST request before continuing
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pick_partnumber"])) {
                $id = $_POST["pick_partnumber"];
                $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?"); // Prepare to delete part from inventory
                $stmt->execute([$id]); // Execute SQL query above
                header("Location: index.php"); // Reload page to show new results
            }
        ?>
    </fieldset>   
</div>
<div>
        <fieldset>
            <legend>Inventory</legend>
            <div id="divInventoryOutput" class="outputStyle">
                <table class="inventory-table">
                    <tr>
                        <th>ID</th>
                        <th>Part Number</th>
                        <th>Description</th>
                        <th>Quantity</th>
                    </tr>
                    <?php
                    // Display each line row in a table
                     foreach ($inventory as $inventory): ?>
                        <tr>
                            <td><?= htmlspecialchars($inventory['id']) ?></td>
                            <td><?= htmlspecialchars($inventory['partnum']) ?></td>
                            <td><?= htmlspecialchars($inventory['partdesc']) ?></td>
                            <td><?= htmlspecialchars($inventory['quantity']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </fieldset>
    </div>
    <br><br>


<!-- Working On this

<fieldset>
    <legend>Search Inventory</legend>
    <form action="index.php" method="post">
    <label>Part Number:</label>
        <input type="text" name="partnumber">
        <input type="submit" value="Search">
        <br><br>
        <?php
        /* -- Working on this
        // Searh Inventory For Part Number
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["partnumber"])) {
            $partnumber = $_POST["partnumber"];
            $stmt = $pdo->prepare("SELECT id, partnum, partdesc, quantity FROM inventory WHERE partnum = ? ORDER BY id ASC");
            $stmt->execute([$partnumber]);
            $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } */
        ?>
    </form>
</fieldset>
    -->

    <div id='foot'>
        <p>Damon Morgan © 2025</p>
        <p><a href="https://www.linkedin.com/in/damon-morgan/">LinkedIn</a></p>
      </div>
</body>
</html>

