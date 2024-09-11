<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Sanitize and validate input data
$naam = filter_var($_POST['naam'], FILTER_SANITIZE_STRING);
$adres = filter_var($_POST['adres'], FILTER_SANITIZE_STRING);
$telefoonnummer = filter_var($_POST['telefoonnummer'], FILTER_SANITIZE_NUMBER_INT);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$aantal_volwassenen = filter_var($_POST['aantal_volwassenen'], FILTER_SANITIZE_NUMBER_INT);
$aantal_kinderen = filter_var($_POST['aantal_kinderen'], FILTER_SANITIZE_NUMBER_INT);
$aantal_babys = filter_var($_POST['aantal_babys'], FILTER_SANITIZE_NUMBER_INT);
$dieetwensen = isset($_POST['dieetwensen']) ? array_map('intval', $_POST['dieetwensen']) : [];

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: add_family.php");
    exit;
}

// Insert data into Klanten
$sql = "INSERT INTO Klanten (naam, adres, telefoonnummer, email, aantal_volwassenen, aantal_kinderen, aantal_babys) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisiii", $naam, $adres, $telefoonnummer, $email, $aantal_volwassenen, $aantal_kinderen, $aantal_babys);

if ($stmt->execute()) {
    $klant_id = $stmt->insert_id;

    // Insert dieetwensen into Klanten_has_Dieetwensen
    foreach ($dieetwensen as $wens_id) {
        $sql = "INSERT INTO Klanten_has_Dieetwensen (Klanten_idKlanten, Dieetwensen_idDieetwensen) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $klant_id, $wens_id);
        $stmt->execute();
    }

    // Handle manually entered dietary preference
    if (!empty($_POST['handmatig_input'])) {
        $handmatig = filter_var($_POST['handmatig_input'], FILTER_SANITIZE_STRING);

        // Insert the new dietary preference
        $sql = "INSERT INTO Dieetwensen (naam) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $handmatig);
        $stmt->execute();

        $handmatig_id = $stmt->insert_id;

        // Associate the new dietary preference with the customer
        $sql = "INSERT INTO Klanten_has_Dieetwensen (Klanten_idKlanten, Dieetwensen_idDieetwensen) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $klant_id, $handmatig_id);
        $stmt->execute();
    }

    $_SESSION['success'] = "Family added successfully!";
    header("Location: families.php");
    exit;
} else {
    $_SESSION['error'] = "Error: " . $stmt->error;
    header("Location: add_family.php");
    exit;
}

$stmt->close();
$conn->close();
?>