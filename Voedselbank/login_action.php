<?php
global $conn;
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gebruikersnaam = trim($_POST['gebruikersnaam']);
    $password = trim($_POST['wachtwoord']);

    if (empty($gebruikersnaam) || empty($password)) {
        echo "Please enter both username and password.";
        exit;
    }

    $stmt = $conn->prepare("SELECT idGebruikers, Gebruikersnaam, Wachtwoord, Rol FROM gebruikers WHERE Gebruikersnaam = ?");
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_gebruikersnaam, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_naam'] = $db_gebruikersnaam;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Ongeldig wachtwoord.";
        }
    } else {
        echo "Geen gebruiker gevonden met deze gebruikersnaam.";
    }

    $stmt->close();
} else {
    echo "Ongeldige aanvraagmethode.";
}

$conn->close();
?>
