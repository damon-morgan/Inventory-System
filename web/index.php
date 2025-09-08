<?php
session_start();
require 'db.php'; // PDO connection to database
// Check to verify authenication
if (isset($_SESSION["auth"]) && $_SESSION["auth"] == false) {
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

if (isset($_SESSION['message'])) {
    $msg = $_SESSION['message'];
    $color = $msg['type'] === 'error' ? 'red' : 'green';
    echo "<p style='color:$color; text-align:center; font-weight:bold;'>{$msg['text']}</p>";
    unset($_SESSION['message']);
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
    body {
        font-family: 'Verdana', sans-serif;
        font-size: 16px;
        background-color: #f4f7fa;
        color: #333;
        margin: 0;
        padding: 20px;
    }

    h1 {
        text-align: left;
        color: #0D3B66;
        margin: 20px 0;
    }

    input[type="text"],
    input[type="number"],
    input[type="submit"],
    .selection {
        font-family: 'Verdana', sans-serif;
        font-size: 14px;
        padding: 8px 10px;
        border: 1px solid #A9C5EB;
        border-radius: 5px;
        outline: none;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        transition: border 0.3s, box-shadow 0.3s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus {
        border-color: #0D3B66;
        box-shadow: 0 0 5px rgba(13, 59, 102, 0.3);
    }

    input[type="submit"],
    .button {
        cursor: pointer;
        background-color: #0D3B66;
        color: white;
        border: none;
        transition: background-color 0.3s, transform 0.2s;
    }

    input[type="submit"]:hover,
    .button:hover {
        background-color: #145DA0;
        transform: translateY(-2px);
    }

    fieldset {
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #A9C5EB;
        margin-bottom: 20px;
        background-color: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    legend {
        font-size: 14px;
        font-weight: bold;
        color: #0D3B66;
        padding: 0 10px;
    }

    .inventory-table {
        border-collapse: collapse;
        width: 95%;
        margin: 20px auto;
        font-family: 'Verdana', sans-serif;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        overflow: hidden;
    }

    .inventory-table th,
    .inventory-table td {
        border: 1px solid #ddd;
        padding: 12px 15px;
        text-align: left;
    }

    .inventory-table th {
        background-color: #0D3B66;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .inventory-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .inventory-table tr:hover {
        background-color: #e1f0ff;
        transition: background-color 0.3s;
    }

    .outputStyle {
        color: #0D3B66;
        text-align: center;
        margin: 20px auto;
        width: 90%;
        font-size: 14px;
    }

    #pick {
        height: 150px;
    }

    #receive {
        height: 150px;
    }

    #invent {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: start;
        justify-items: center;
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
                <input type="number" name="part_number"><br><br>
                <label>Description:</label>
                <input type="text" name="part_description"><br><br>
                <label>Quantity:</label>
                <input type="number" name="add_quantity">
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
                <input type="number" name="pick_partnumber"><br><br>
                <label>Quantity to Pick:</label>
                <input type="number" name="pick_quantity" required min="1">
                <input type="submit" value="Pick">
                <br><br>
            </form>
            <?php
            // Pick Part From Inventory
            // Check if server receives a POST request before continuing
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pick_partnumber"])) {
                // Store values entered by user
                $id = $_POST["pick_partnumber"];
                $pickQty = (int) $_POST["pick_quantity"];
                // Fetch current quantity
                $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE id = ?");
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // Verify quantity entered with inventory quantity
                if (!$row) {
                    // ID is incorrect
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Part not found.'];
                } else {
                    $currentQty = (int)$row['quantity'];

                    if ($pickQty > $currentQty) {
                        // Pick quantity is greater than inventory quantity
                        $_SESSION['message'] = ['type' => 'error', 'text' => "Cannot pick more than available quantity ($currentQty)."];
                    } elseif ($pickQty === $currentQty) {
                        // Pick quantity is equal to inventory quantity
                        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
                        $stmt->execute([$id]);
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Part picked completely and removed from inventory.'];
                    } else {
                        // Pick quantity is subtracted from inventory quantity
                        $newQty = $currentQty - $pickQty;
                        $stmt = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
                        $stmt->execute([$newQty, $id]);
                        $_SESSION['message'] = ['type' => 'success', 'text' => "Quantity updated successfully. Remaining: $newQty"];
                    }
                }
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
                    foreach ($inventory as $inventory):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($inventory['id']) ?></td>
                            <td><?= htmlspecialchars($inventory['partnum']) ?></td>
                            <td><?= htmlspecialchars($inventory['partdesc']) ?></td>
                            <td><?= htmlspecialchars($inventory['quantity']) ?></td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
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