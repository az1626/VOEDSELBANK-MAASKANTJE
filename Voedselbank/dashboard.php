<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user role from session
$user_role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Welkom op het Dashboard</h1>
    <div class="dashboard-links">
        <?php if ($user_role == 1): ?> <!-- Admin -->
            <a href="families.php">Beheer Gezinnen</a>
            <a href="product.php">Beheer Voorraad</a>
            <a href="voedselpakket.php">Voedselpakketten</a>
            <a href="leveranciers.php">Leveranciers</a>
        <?php elseif ($user_role == 2): ?> <!-- Medewerker -->
            <a href="product.php">Beheer Voorraad</a>
            <a href="voedselpakket.php">Voedselpakketten</a>
            <a href="leveranciers.php">Leveranciers</a>
        <?php elseif ($user_role == 3): ?> <!-- Vrijwilliger -->
            <a href="extra.php">Bekijk Dieetwensen</a>
            <a href="voedselpakket.php">Voedselpakketten</a>
        <?php else: ?>
            <p>Ongeldige rol gedetecteerd. Neem contact op met ondersteuning.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
