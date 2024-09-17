<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role (klant)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

// Fetch data from the database for the logged-in user
$sql = "SELECT Klanten.*, GROUP_CONCAT(Dieetwensen.naam SEPARATOR ', ') AS dieetwensen
        FROM Klanten
        LEFT JOIN Klanten_has_Dieetwensen ON Klanten.idKlanten = Klanten_has_Dieetwensen.Klanten_idKlanten
        LEFT JOIN Dieetwensen ON Klanten_has_Dieetwensen.Dieetwensen_idDieetwensen = Dieetwensen.idDieetwensen
        WHERE Klanten.idGebruikers = ?
        GROUP BY Klanten.idKlanten";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Klanten Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            display: inline-block;
            background: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn:hover {
            background: #45a049;
        }
        .action-link {
            color: #4CAF50;
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
    <h1>Beheer Klanten Data</h1>
    <a href="klant_add_family.php" class="btn">Voeg Familie Toe</a>
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

        while ($row = $result->fetch_assoc()) {
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
                        <a href='klant_edit_family.php?id={$row['idKlanten']}' class='action-link'>Edit</a>
                        <a href='klant_delete_family.php?id={$row['idKlanten']}' class='action-link' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                    </td>
                    </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Geen families gevonden.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>
