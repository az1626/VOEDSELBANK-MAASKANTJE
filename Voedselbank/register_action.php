<?php
session_start();
include 'db_connect.php';

$name = $_POST['naam'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$phone = $_POST['telefoonnummer'];
$role = $_POST['role'];

// Insert user into the database
$sql = "INSERT INTO user (Email, Wachtwoord, Naam, Telefoonnummer, role) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $email, $password, $name, $phone, $role);

if ($stmt->execute()) {
    echo "Registration successful!";
    header("Location: login.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
