<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and is a customer (role 0)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klant Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Welkom op het Klant Dashboard</h1>
    <div class="dashboard-links">
        <a href="klant_family.php">Voeg een familie toe</a>
    </div>
</div>
</body>
</html>
