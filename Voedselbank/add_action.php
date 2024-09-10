<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = trim($_POST['naam']);
    $adres = trim($_POST['adres']);
    $telefoonnummer = (int)$_POST['telefoonnummer'];
    $email = trim($_POST['email']);
    $aantal_volwassenen = (int)$_POST['aantal_volwassenen'];
    $aantal_kinderen = (int)$_POST['aantal_kinderen'];
    $aantal_babys = (int)$_POST['aantal_babys'];

    // Basic input validation
    if (empty($naam) || empty($adres) || empty($email) || empty($telefoonnummer)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: add_family.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: add_family.php");
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Klanten (naam, adres, telefoonnummer, email, aantal_volwassenen, aantal_kinderen, aantal_babys) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissii", $naam, $adres, $telefoonnummer, $email, $aantal_volwassenen, $aantal_kinderen, $aantal_babys);

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
