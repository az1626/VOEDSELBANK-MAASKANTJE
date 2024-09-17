<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $klant_id = intval($_GET['id']);

    // First, delete related voedselpakketen records
    $sql = "DELETE FROM voedselpakketen WHERE Klanten_idKlanten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $klant_id);
    $stmt->execute();
    $stmt->close();

    // Then, delete related dieetwensen records (if needed)
    $sql = "DELETE FROM Klanten_has_Dieetwensen WHERE Klanten_idKlanten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $klant_id);
    $stmt->execute();
    $stmt->close();

    // Finally, delete the klant record
    $sql = "DELETE FROM klanten WHERE idKlanten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $klant_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Family deleted successfully!";
    } else {
        $_SESSION['error'] = "Error: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "No ID provided.";
}

header("Location: families.php");
exit;

$conn->close();
?>
