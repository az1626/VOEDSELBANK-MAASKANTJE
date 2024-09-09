<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
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
        <!-- Display links based on user role -->
        <?php if ($_SESSION['role'] == 1): ?>
            <a href="families.php">Manage Families</a>
            <a href="product.php">Producten</a>
            <a href="leveranciers.php">Leveranciers</a>
            <a href="medewerkers.php">Manage Medewerkers</a> <!-- Admin-only link -->
            <a href="extra.php">Manage Extra Information</a> <!-- Admin-only link -->
        <?php else: ?>
            <a href="families.php">Manage Families</a>
            <a href="product.php">Producten</a>
            <a href="leveranciers.php">Leveranciers</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
