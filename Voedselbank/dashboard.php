<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link rel="stylesheet" href="CSS/Dashboard.css">
</head>
<body>
<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//
$user_role = $_SESSION['role'];
?>

<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Welkom op het Dashboard</h1>
    <div class="dashboard-grid">
        <?php if ($user_role == 1): ?> <!-- Admin -->
            <div class="dashboard-item">
                <i class="fas fa-users"></i>
                <h2>Beheer Gezinnen</h2>
                <p>Beheer en organiseer gezinsinformatie</p>
                <a href="families.php">Ga naar Gezinnen</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-box-open"></i>
                <h2>Beheer Voorraad</h2>
                <p>Beheer de voorraad van producten</p>
                <a href="product.php">Ga naar Voorraad</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-shopping-basket"></i>
                <h2>Voedselpakketten</h2>
                <p>Stel voedselpakketten samen</p>
                <a href="voedselpakket.php">Ga naar Pakketten</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-plus-circle"></i>
                <h2>Extra</h2>
                <p>Aanvullende functies en opties</p>
                <a href="extra.php">Ga naar Extra</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-truck"></i>
                <h2>Leveranciers</h2>
                <p>Beheer leveranciersinformatie</p>
                <a href="leveranciers.php">Ga naar Leveranciers</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-briefcase"></i>
                <h2>Management</h2>
                <p>Beheer Mannagement Rapport</p>
                <a href="management_report.php">Ga naar Rapportage</a>
            </div>
        <?php elseif ($user_role == 2): ?> <!-- Medewerker -->
            <div class="dashboard-item">
                <i class="fas fa-shopping-basket"></i>
                <h2>Voedselpakketten</h2>
                <p>Stel voedselpakketten samen</p>
                <a href="voedselpakket.php">Ga naar Pakketten</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-box-open"></i>
                <h2>Beheer Voorraad</h2>
                <p>Beheer de voorraad van producten</p>
                <a href="product.php">Ga naar Voorraad</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-users"></i>
                <h2>Beheer Gezinnen</h2>
                <p>Beheer en organiseer gezinsinformatie</p>
                <a href="families.php">Ga naar Gezinnen</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-plus-circle"></i>
                <h2>Extra</h2>
                <p>Aanvullende functies en opties</p>
                <a href="extra.php">Ga naar Extra</a>
            </div>
        <?php elseif ($user_role == 3): ?> <!-- Vrijwilliger -->
            <div class="dashboard-item">
                <i class="fas fa-shopping-basket"></i>
                <h2>Voedselpakketten</h2>
                <p>Stel voedselpakketten samen</p>
                <a href="voedselpakket.php">Ga naar Pakketten</a>
            </div>
            <div class="dashboard-item">
                <i class="fas fa-plus-circle"></i>
                <h2>Extra</h2>
                <p>Aanvullende functies en opties</p>
                <a href="extra.php">Ga naar Extra</a>
            </div>
        <?php else: ?>
            <p>Ongeldige rol gedetecteerd. Neem contact op met ondersteuning.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
