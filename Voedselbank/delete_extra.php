<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: extra.php");
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM extra WHERE beschikbare_allergieën=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);

if ($stmt->execute()) {
    $success_message = "Extra information deleted successfully!";
} else {
    $error_message = "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: extra.php");
exit;
?>
