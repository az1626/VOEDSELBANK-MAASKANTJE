<?php 
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Handle form submission to add a new supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    // Retrieve and sanitize input values
    $naam = $_POST['naam'];
    $adres = $_POST['adres'];
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $eerstevolgende_levering = $_POST['eerstevolgende_levering'];

    // Prepare the SQL statement to insert the new supplier
    $sql = "INSERT INTO Leveranciers (naam, adres, contactpersoon, telefoonnummer, email, eerstevolgende_levering) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $naam, $adres, $contactpersoon, $telefoonnummer, $email, $eerstevolgende_levering);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        header("Location: leveranciers.php?added=success");
        exit;
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = intval($_POST['id']);
    $naam = $_POST['naam'];
    $adres = $_POST['adres'];
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $eerstevolgende_levering = $_POST['eerstevolgende_levering'];

    $sql = "UPDATE Leveranciers SET naam = ?, adres = ?, contactpersoon = ?, telefoonnummer = ?, email = ?, eerstevolgende_levering = ? WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $naam, $adres, $contactpersoon, $telefoonnummer, $email, $eerstevolgende_levering, $id);

    if ($stmt->execute()) {
        header("Location: leveranciers.php?updated=success");
        exit;
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM Leveranciers WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
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
    <link rel="stylesheet" href="CSS/leveranciers.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Beheer Leveranciers</h1>

    <!-- Add Supplier Button -->
    <div>
        <button id="openAddModalBtn" class="btn">Voeg Leverancier</button>
    </div>

    <!-- Success/Error Messages -->
    <?php
    if (isset($_GET['added']) && $_GET['added'] == 'success') {
        echo "<div class='success-message'>Leverancier succesvol toegevoegd!</div>";
    }
    if (isset($_GET['deleted']) && $_GET['deleted'] == 'success') {
        echo "<div class='success-message'>Leverancier succesvol verwijderd!</div>";
    }
    if (isset($_GET['updated']) && $_GET['updated'] == 'success') {
        echo "<div class='success-message'>Leverancier succesvol bijgewerkt!</div>";
    }
    if (isset($error_message)) {
        echo "<div class='error-message'>{$error_message}</div>";
    }
    ?>

    <!-- The Add Supplier Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeAddModal">&times;</span>
            <h2>Voeg nieuwe leverancier toe</h2>
            <form action="leveranciers.php" method="POST">
                <input type="hidden" name="action" value="add">
                <label for="naam">Bedrijf:</label>
                <input type="text" id="naam" name="naam" required>
                <label for="adres">Adres:</label>
                <input type="text" id="adres" name="adres" required>
                <label for="contactpersoon">Contact Persoon:</label>
                <input type="text" id="contactpersoon" name="contactpersoon" required>
                <label for="telefoonnummer">Telefoon:</label>
                <input type="text" id="telefoonnummer" name="telefoonnummer" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="eerstevolgende_levering">Volgende Bezorging:</label>
                <input type="datetime-local" id="eerstevolgende_levering" name="eerstevolgende_levering" required>
                <button type="submit" class="btn">Voeg Leverancier</button>
            </form>
        </div>
    </div>

    <!-- The Edit Supplier Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeEditModal">&times;</span>
            <h2>Wijzig Leverancier</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId" value="">
                <label for="editNaam">Bedrijf:</label>
                <input type="text" id="editNaam" name="naam" required>
                <label for="editAdres">Adres:</label>
                <input type="text" id="editAdres" name="adres" required>
                <label for="editContactpersoon">Contact Persoon:</label>
                <input type="text" id="editContactpersoon" name="contactpersoon" required>
                <label for="editTelefoonnummer">Telefoon:</label>
                <input type="text" id="editTelefoonnummer" name="telefoonnummer" required>
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" required>
                <label for="editEerstevolgende_levering">Volgende Bezorging:</label>
                <input type="datetime-local" id="editEerstevolgende_levering" name="eerstevolgende_levering" required>
                <button type="submit" class="btn">Bijwerken</button>
            </form>
        </div>
    </div>

    <!-- Supplier Table -->
    <?php
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
                    <button class='editBtn btn' data-id='{$row['idLeveranciers']}' data-naam='{$row['naam']}' data-adres='{$row['adres']}' data-contactpersoon='{$row['contactpersoon']}' data-telefoonnummer='{$row['telefoonnummer']}' data-email='{$row['email']}' data-eerstevolgende_levering='{$row['eerstevolgende_levering']}'>Wijzig</button>
                    <form action='leveranciers.php' method='GET' style='display:inline;' onsubmit='return confirm(\"Weet je zeker dat je deze leverancier wilt verwijderen?\");'>
                        <input type='hidden' name='delete' value='{$row['idLeveranciers']}'>
                        <button type='submit' class='btn delete-btn'>Verwijder</button>
                    </form>
                </td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Geen leveranciers gevonden.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>

<script src="JS/leveranciers.js"></script>
</body>
</html>
