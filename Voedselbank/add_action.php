<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = trim($_POST['naam']);
    $volwassenen = (int)$_POST['volwassenen'];
    $kinderen = (int)$_POST['kinderen'];
    $postcode = trim($_POST['postcode']);
    $mail = trim($_POST['mail']);
    $telefoonnummer = trim($_POST['telefoonnummer']);
    $wensen = trim($_POST['wensen']);
    $pakket = trim($_POST['pakket']);

    // Basic input validation
    if (empty($naam) || empty($postcode) || empty($mail) || empty($telefoonnummer)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: add_family.php");
        exit;
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: add_family.php");
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO gezinnen (naam, volwassenen, kinderen, postcode, mail, telefoonnummer, wensen, pakket) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiissss", $naam, $volwassenen, $kinderen, $postcode, $mail, $telefoonnummer, $wensen, $pakket);

    if ($stmt->execute()) {
        $_SESSION['success'] = "New family added successfully.";
        header("Location: families.php"); // Redirect to the list of families
        exit;
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: add_family.php");
        exit;
    }

    $stmt->close();
} else {
    header("Location: add_family.php");
    exit;
}

$conn->close();
?>