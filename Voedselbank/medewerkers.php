<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle deletion if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check if the user is trying to delete themselves
    if ($id == $_SESSION['user_id']) {
        echo "Je kunt je eigen account niet verwijderen.";
        exit;
    }

    // Delete the user from the database
    $sql = "DELETE FROM gebruikers WHERE idGebruikers=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Gebruiker succesvol verwijderd!";
        header("Location: medewerkers.php");
        exit;
    } else {
        $_SESSION['error'] = "Fout: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Medewerkers</title>
    <link rel="stylesheet" href="CSS/medewerkers.css">
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
    // Display success or error messages
    if (isset($_SESSION['success'])) {
        echo "<p class='success'>{$_SESSION['success']}</p>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<p class='error'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }

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
                    <a href='?id={$row['idGebruikers']}' onclick='return confirm(\"Weet je zeker dat je deze gebruiker wilt verwijderen?\")'>Delete</a>
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
