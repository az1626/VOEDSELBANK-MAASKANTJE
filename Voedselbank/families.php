<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Fetch data from the database
$sql = "SELECT * FROM gezinnen";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Families</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .action-link {
            color: #1e90ff;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Manage Gezinnen Data</h1>
    <a href="add_family.php" class="btn">Add New Family</a>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
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
                    <a href='edit_family.php?id={$row['id']}' class='action-link'>Edit</a>
                    <a href='delete_family.php?id={$row['id']}' class='action-link' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>
