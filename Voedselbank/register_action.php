<?php
// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];

    // Set role to 0 (klant) by default
    $rol = 0; // Rol ID voor klant

    // Hash the password before storing it in the database
    $hashed_password = password_hash($wachtwoord, PASSWORD_DEFAULT);

    // Prepare an SQL statement to insert the user into the database
    $stmt = $conn->prepare("INSERT INTO Gebruikers (Gebruikersnaam, Wachtwoord, Rol) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $gebruikersnaam, $hashed_password, $rol);

    // Execute the statement and check if the insertion was successful
    if ($stmt->execute()) {
        // Redirect to the login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        // Handle errors (e.g., username already taken)
        echo "Error: Could not register user. Please try again.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the form was not submitted, redirect to the register page
    header("Location: register.php");
    exit();
}
?>
