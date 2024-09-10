<?php
// Start the session
session_start();

// Include your database connection
include 'db_connect.php'; // Ensure this file contains your database connection logic

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the username and password from the form
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM Gebruikers WHERE Gebruikersnaam = ?");
    $stmt->bind_param("s", $gebruikersnaam);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($wachtwoord, $user['Wachtwoord'])) {
            // Password is correct, start the session and redirect
            $_SESSION['user_id'] = $user['idGebruikers'];
            $_SESSION['username'] = $user['Gebruikersnaam'];
            $_SESSION['role'] = $user['Rol'];

            // Redirect to a secure page
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid password
            echo "Invalid password. Please try again.";
        }
    } else {
        // User not found
        echo "No user found with that username.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the form was not submitted, redirect to the login page
    header("Location: login.php");
    exit();
}
?>
