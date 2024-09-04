<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Manage Gezinnen Data</h1>
        <?php
        // Fetch data from the database and display it
        $sql = "SELECT * FROM gezinnen";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>
            <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Volwassenen</th>
            <th>Kinderen</th>
            <th>Postcode</th>
            <th>Email</th>
            <th>Telefoonnummer</th>
            <th>Wensen</th>
            <th>Pakket</th>
            <th>Actions</th>
            </tr>";

            while($row = $result->fetch_assoc()) {
                echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['volwassenen']}</td>
                <td>{$row['kinderen']}</td>
                <td>{$row['postcode']}</td>
                <td>{$row['mail']}</td>
                <td>{$row['telefoonnummer']}</td>
                <td>{$row['wensen']}</td>
                <td>{$row['pakket']}</td>
                <td>
                    <a href='edit_data.php?id={$row['id']}'>Edit</a> | 
                    <a href='delete_data.php?id={$row['id']}'>Delete</a>
                </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "No data found.";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
