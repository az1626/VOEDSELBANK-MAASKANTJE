<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
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
    <title>Beheer Medewerkers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .btn-container a {
            text-decoration: none;
        }

        .btn-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-container button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        td {
            background-color: #fff;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .no-data {
            text-align: center;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Beheer Medewerkers</h1>

    <!-- Add Medewerker Button -->
    <div class="btn-container">
        <a href="add_medewerkers.php">
            <button type="button">Voeg nieuwe medewerker toe</button>
        </a>
    </div>

    <?php
    // Fetch users from the database and display them
    $sql = "SELECT idGebruikers, Gebruikersnaam, Wachtwoord, Rol, Email FROM gebruikers";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Gebruikersnaam</th>
                <th>Rol</th>
                <th>Actie</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            // Updated role display logic
            $role = ($row['Rol'] == 1) ? 'Admin' : (($row['Rol'] == 2) ? 'Medewerker' : 'Vrijwilliger');

            echo "<tr>
                <td>{$row['idGebruikers']}</td>
                <td>{$row['Email']}</td>
                <td>{$row['Gebruikersnaam']}</td>
                <td>{$role}</td>
                <td>
                    <a href='edit_medewerkers.php?id={$row['idGebruikers']}'>Edit</a> | 
                    <a href='delete_medewerkers.php?id={$row['idGebruikers']}' onclick='return confirm(\"Weet je zeker dat je deze gebruiker wilt verwijderen?\")'>Delete</a>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='no-data'>Geen gebruikers gevonden.</div>";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
