<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$naam = $_POST['naam'];
$volwassenen = (int)$_POST['volwassenen'];
$kinderen = (int)$_POST['kinderen'];
$postcode = $_POST['postcode'];
$mail = $_POST['mail'];
$telefoonnummer = $_POST['telefoonnummer'];
$wensen = $_POST['wensen'];
$pakket = $_POST['pakket'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO gezinnen (naam, volwassenen, kinderen, postcode, mail, telefoonnummer, wensen, pakket) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siiissss", $naam, $volwassenen, $kinderen, $postcode, $mail, $telefoonnummer, $wensen, $pakket);

if ($stmt->execute()) {
    header("Location: index.php"); // Redirect to the list of families
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
