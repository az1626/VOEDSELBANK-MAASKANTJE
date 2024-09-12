<?php
session_start();
include 'db_connect.php';

// Check of de gebruiker is ingelogd en de admin-rol heeft
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Verwerk formulier indien ingediend
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = $_POST['name'];
    $role = $_POST['role'];

    // Pas de SQL-query aan naar de werkelijke kolomnamen in de database
    $sql = "INSERT INTO gebruikers (Email, Wachtwoord, Gebruikersnaam, Rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $error_message = "Fout bij voorbereiden van statement: " . $conn->error;
    } else {
        $stmt->bind_param("sssi", $email, $password, $name, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Nieuwe gebruiker succesvol toegevoegd!";
            header("Location: medewerkers.php");
            exit;
        } else {
            $error_message = "Fout: " . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg Medewerker of Vrijwilliger Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Voeg Nieuwe Medewerker of Vrijwilliger Toe</h1>

    <!-- Toon succes- of foutmeldingen -->
    <?php
    if (isset($success_message)) {
        echo "<div class='message success'>$success_message</div>";
    }
    if (isset($error_message)) {
        echo "<div class='message error'>$error_message</div>";
    }
    ?>

    <form action="add_medewerkers.php" method="POST">
        <label for="email">E-mail:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Wachtwoord:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="name">Gebruikersnaam:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="role">Rol:</label><br>
        <select id="role" name="role" required>
            <option value="2">Medewerker</option>
            <option value="3">Vrijwilliger</option>
            <option value="1">Admin</option>
        </select><br><br>

        <input type="submit" value="Voeg Toe">
    </form>
</div>
</body>
</html>

Voedselbank\<db_connect class="php"></db_connect>