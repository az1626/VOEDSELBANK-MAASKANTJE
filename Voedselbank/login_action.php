<?php
// Start the session
session_start();

// Include your database connection
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Haal de gebruikersnaam en wachtwoord uit het formulier
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];

    // Bereid een SQL-statement voor om SQL-injectie te voorkomen
    $stmt = $conn->prepare("SELECT idGebruikers, Gebruikersnaam, Wachtwoord, Rol, Email FROM gebruikers WHERE Gebruikersnaam = ?");
    $stmt->bind_param("s", $gebruikersnaam);

    // Voer de query uit
    $stmt->execute();
    $result = $stmt->get_result();

    // Controleer of de gebruiker bestaat
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifieer het wachtwoord
        if (password_verify($wachtwoord, $user['Wachtwoord'])) {
            // Wachtwoord is correct, start de sessie en zet sessie-variabelen
            $_SESSION['user_id'] = $user['idGebruikers'];
            $_SESSION['Gebruikersnaam'] = $user['Gebruikersnaam']; // Updated to match HTML
            $_SESSION['role'] = $user['Rol'];

            // Redirect op basis van de rol van de gebruiker
            if ($user['Rol'] == 1) {
                // Admin
                header("Location: dashboard.php");
            } elseif ($user['Rol'] == 2) {
                // Medewerker
                header("Location: dashboard.php");
            } elseif ($user['Rol'] == 3) {
                // Vrijwilliger
                header("Location: dashboard.php");
            } elseif ($user['Rol'] == 0) {
                // Klant
                header("Location: klantdashboard.php");
            }
            exit();
        } else {
            // Ongeldig wachtwoord
            $_SESSION['error_message'] = "Wachtwoord of gebruikersnaam is incorrect.";
            header("Location: login.php");
            exit();
        }
    } else {
        // Gebruiker niet gevonden
        $_SESSION['error_message'] = "Wachtwoord of gebruikersnaam is incorrect.";
        header("Location: login.php");
        exit();
    }

    // Sluit de statement en de connectie
    $stmt->close();
    $conn->close();
} else {
    // Als het formulier niet is ingediend, redirect naar de login pagina
    header("Location: login.php");
    exit();
}
?>
