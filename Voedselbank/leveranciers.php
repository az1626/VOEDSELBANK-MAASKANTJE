<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM Leveranciers WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to avoid showing message on page refresh
        header("Location: leveranciers.php?deleted=success");
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch suppliers from the database
$sql = "SELECT * FROM Leveranciers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Leveranciers</title>
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
            padding: 15px;
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
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            font-family: inherit;
        }
        .btn:hover {
            background: #45a049;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .action-links a {
            color: #4CAF50;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Beheer Leveranciers</h1>

    <!-- Add Supplier Button -->
    <div>
        <a href="add_leverancier.php" class="btn">Voeg leverancier</a>
    </div>

    <?php
    // Show success message if the 'deleted' parameter is present
    if (isset($_GET['deleted']) && $_GET['deleted'] == 'success') {
        echo "<div class='success-message'>Supplier deleted successfully!</div>";
    }

    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Adres</th>
            <th>Contact Persoon</th>
            <th>Telefoon</th>
            <th>Email</th>
            <th>Volgende Bezorging</th>
            <th>Acties</th>
            </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['idLeveranciers']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['adres']}</td>
                <td>{$row['contactpersoon']}</td>
                <td>{$row['telefoonnummer']}</td>
                <td>{$row['email']}</td>
                <td>{$row['eerstevolgende_levering']}</td>
                <td class='action-links'>
                    <a href='edit_leverancier.php?id={$row['idLeveranciers']}'>Edit</a>
                    <a href='leveranciers.php?delete={$row['idLeveranciers']}' onclick='return confirm(\"Are you sure you want to delete this supplier?\")'>Delete</a>
                </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Geen leverancier gevonden.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>