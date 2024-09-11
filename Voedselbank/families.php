<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Fetch data from the database
$sql = "SELECT Klanten.*, GROUP_CONCAT(Dieetwensen.naam SEPARATOR ', ') AS dieetwensen
        FROM Klanten
        LEFT JOIN Klanten_has_Dieetwensen ON Klanten.idKlanten = Klanten_has_Dieetwensen.Klanten_idKlanten
        LEFT JOIN Dieetwensen ON Klanten_has_Dieetwensen.Dieetwensen_idDieetwensen = Dieetwensen.idDieetwensen
        GROUP BY Klanten.idKlanten";
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
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h1 {
        font-size: 24px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
        vertical-align: middle;
    }

    th {
        background-color: #007bff;
        color: #ffffff;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .action-link {
        color: #007bff;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 4px;
        transition: background-color 0.3s, color 0.3s;
    }

    .action-link:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    p {
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .container {
            width: 100%;
            padding: 10px;
        }

        table {
            font-size: 14px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 14px;
        }
    }
</style>


</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Manage Klanten Data</h1>
    <a href="add_family.php" class="btn">Add New Family</a>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Adres</th>
            <th>Telefoonnummer</th>
            <th>Email</th>
            <th>Aantal Volwassenen</th>
            <th>Aantal Kinderen</th>
            <th>Aantal Babys</th>
            <th>Dieetwensen</th>
            <th>Actions</th>
            </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['idKlanten']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['adres']}</td>
                <td>{$row['telefoonnummer']}</td>
                <td>{$row['email']}</td>
                <td>{$row['aantal_volwassenen']}</td>
                <td>{$row['aantal_kinderen']}</td>
                <td>{$row['aantal_babys']}</td>
                <td>{$row['dieetwensen']}</td>
                <td>
                    <a href='edit_family.php?id={$row['idKlanten']}' class='action-link'>Edit</a>
                    <a href='delete_family.php?id={$row['idKlanten']}' class='action-link' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
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
