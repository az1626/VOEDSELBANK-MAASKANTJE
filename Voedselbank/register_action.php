<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gebruikersnaam = trim($_POST['gebruikersnaam']);
    $password = trim($_POST['wachtwoord']);
    $rol = trim($_POST['rol']);

    if (empty($gebruikersnaam) || empty($password) || empty($rol)) {
        echo "Please fill in all fields.";
        exit;
    }

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT idGebruikers FROM Gebruikers WHERE Gebruikersnaam = ?");
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username already taken.";
        $stmt->close();
        exit;
    }

    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO Gebruikers (Gebruikersnaam, Wachtwoord, Rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $gebruikersnaam, $hashed_password, $rol);

    if ($stmt->execute()) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Ongeldige aanvraagmethode.";
}

$conn->close();
?>
