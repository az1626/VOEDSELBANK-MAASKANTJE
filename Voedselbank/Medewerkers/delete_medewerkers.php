<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the user is trying to delete themselves
    if ($id == $_SESSION['user_id']) {
        echo "You cannot delete your own account.";
        exit;
    }

    // Delete the user from the database
    $sql = "DELETE FROM gebruikers WHERE idGebruikers=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: medewerkers.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "No user ID provided.";
}

$conn->close();
?>
