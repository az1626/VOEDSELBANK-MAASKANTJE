<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .dashboard-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .dashboard-item i {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #3498db;
        }
        .dashboard-item h2 {
            font-size: 1.2em;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .dashboard-item p {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        .dashboard-item a {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            background-color: #27ae60;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .dashboard-item a:hover {
            background-color: #2ecc71;
        }
    </style>
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

    // Get user role from session
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
                    <p>Beheer management</p>
                    <a href="management_report.php">Ga naar Management</a>
                </div>
            <?php elseif ($user_role == 2): ?> <!-- Medewerker -->
                <!-- (Medewerker items remain the same) -->
            <?php elseif ($user_role == 3): ?> <!-- Vrijwilliger -->
                <!-- (Vrijwilliger items remain the same) -->
            <?php else: ?>
                <p>Ongeldige rol gedetecteerd. Neem contact op met ondersteuning.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>