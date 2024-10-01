<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Handle deletion if an ID is provided
if (isset($_POST['delete_id'])) {
    $klant_id = intval($_POST['delete_id']);
    
    // Controleren of er voedselpakketten aan de klant zijn gekoppeld
    $sql = "SELECT COUNT(*) AS pakket_count FROM voedselpakketen WHERE Klanten_idKlanten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $klant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['pakket_count'] > 0) {
        $_SESSION['error'] = "Je kan geen klanten verwijderen die al een voedselpakket hebben.";
    } else {
        // Verwijder de gekoppelde dieetwensen van de klant
        $sql = "DELETE FROM Klanten_has_Dieetwensen WHERE Klanten_idKlanten = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $klant_id);
        $stmt->execute();
        $stmt->close();

        // Verwijder de klant zelf
        $sql = "DELETE FROM Klanten WHERE idKlanten = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $klant_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Klant succesvol verwijderd!";
        } else {
            $_SESSION['error'] = "Error: " . htmlspecialchars($stmt->error);
        }
    }

    $stmt->close();
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
    <title>Beheer Families</title>
    <link rel="stylesheet" href="CSS/families.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Beheer Klanten Data</h1>
    <a href="add_family.php" class="btn">Voeg Familie</a>

    <?php
    if (isset($_SESSION['success'])) {
        echo "<p class='success'>{$_SESSION['success']}</p>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<p class='error'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }

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
                    <a href='edit_family.php?id={$row['idKlanten']}' class='action-link'>Bewerken</a>
                    <form method='post' action='' style='display:inline;'>
                        <input type='hidden' name='delete_id' value='{$row['idKlanten']}'>
                        <button type='submit' class='action-link' onclick='return confirm(\"Weet je zeker dat je dit record wilt verwijderen?\");'>Verwijder</button>
                    </form>
                </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Geen data gevonden.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>
