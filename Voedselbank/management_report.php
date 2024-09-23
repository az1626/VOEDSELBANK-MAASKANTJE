<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Define variables and initialize with empty values
$month = $year = $postcode = "";
$category_data = $postcode_data = array();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $month = isset($_POST['month']) ? $_POST['month'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';
    $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';

    // Prepare and execute query for product category report
    $stmt = $conn->prepare("SELECT p.naam AS product_naam, l.naam AS leverancier_naam, COUNT(*) AS aantal
                             FROM producten p
                             JOIN producten_has_leveranciers phl ON p.idProducten = phl.Producten_idProducten
                             JOIN leveranciers l ON phl.Leveranciers_idLeveranciers = l.idLeveranciers
                             WHERE MONTH(p.created_at) = ? AND YEAR(p.created_at) = ?
                             GROUP BY p.naam, l.naam");
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $category_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Prepare and execute query for postcode report
    if ($postcode) {
        $stmt = $conn->prepare("SELECT c.naam AS categorie_naam, COUNT(*) AS aantal
                                 FROM producten p
                                 JOIN categorieen c ON p.Categorieen_idCategorieen = c.idCategorieen
                                 JOIN klanten k ON k.idKlanten = p.idProducten
                                 WHERE MONTH(p.created_at) = ? AND YEAR(p.created_at) = ? AND k.postcode = ?
                                 GROUP BY c.naam");
        $stmt->bind_param("iis", $month, $year, $postcode);
        $stmt->execute();
        $postcode_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Report</title>
    <link rel="stylesheet" href="CSS/management.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Management Report</h1>

    <form method="post">
        <label for="month">Maand:</label>
        <input type="number" id="month" name="month" min="1" max="12" value="<?php echo htmlspecialchars($month ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label for="year">Jaar:</label>
        <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($year ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label for="postcode">Postcode:</label>
        <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($postcode ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit">Genereer Report</button>
    </form>

    <h2>Maandelijks Report by Product Category</h2>
    <?php if (!empty($category_data)): ?>
        <table>
            <thead>
            <tr>
                <th>Product Naam</th>
                <th>Leverancier Naam</th>
                <th>Hoeveelheid</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($category_data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_naam'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['leverancier_naam'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['aantal'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Er zijn geen gegevens beschikbaar voor de geselecteerde maand en jaar.</p>
    <?php endif; ?>

    <h2>Maandelijks Report bij Postcode</h2>
    <?php if (!empty($postcode_data)): ?>
        <table>
            <thead>
            <tr>
                <th>Categorie Naam</th>
                <th>Hoeveelheid</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($postcode_data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['categorie_naam'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['aantal'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Er zijn geen gegevens beschikbaar voor de geselecteerde maand en jaar.</p>
    <?php endif; ?>
</div>
</body>
</html>
