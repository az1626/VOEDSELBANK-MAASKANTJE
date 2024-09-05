<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1>Welcome to the Dashboard</h1>
        <?php if ($_SESSION['role'] == 1): ?>
            <a href="medewerkers.php">Medewerkers</a><br>
            <a href="families.php">Families</a><br>
        <?php else: ?>
            <a href="view_data.php">View Data</a><br>
        <?php endif; ?>
    </div>
</body>
</html>
