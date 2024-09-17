<?php
session_start();
include '../db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: extra.php");
    exit;
}

$id = $_GET['id'];

// Prepare and execute the deletion query
$sql = "DELETE FROM dieetwensen WHERE idDieetwensen=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $success_message = "Dietary wish deleted successfully!";
} else {
    $error_message = "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: extra.php");
exit;
?>
