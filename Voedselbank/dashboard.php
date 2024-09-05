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
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1>Welcome to the Dashboard</h1>
        <div class="dashboard-links">
            <?php if ($_SESSION['role'] == 1): ?>
                <a href="medewerkers.php">Manage Medewerkers</a>
                <a href="families.php">Manage Families</a>
            <?php else: ?>
                <a href="view_data.php">View Your Data</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
