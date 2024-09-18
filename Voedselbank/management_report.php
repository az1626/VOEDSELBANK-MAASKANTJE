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
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 2rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="datetime-local"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Management Report</h1>

    <form method="post">
        <label for="month">Month:</label>
        <input type="number" id="month" name="month" min="1" max="12" value="<?php echo htmlspecialchars($month ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($year ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label for="postcode">Postcode:</label>
        <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($postcode ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit">Generate Report</button>
    </form>

    <h2>Monthly Report by Product Category</h2>
    <?php if (!empty($category_data)): ?>
        <table>
            <thead>
            <tr>
                <th>Product Name</th>
                <th>Supplier Name</th>
                <th>Quantity</th>
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
        <p>No data available for the selected month and year.</p>
    <?php endif; ?>

    <h2>Monthly Report by Postcode</h2>
    <?php if (!empty($postcode_data)): ?>
        <table>
            <thead>
            <tr>
                <th>Category Name</th>
                <th>Quantity</th>
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
        <p>No data available for the selected postcode, month, and year.</p>
    <?php endif; ?>
</div>
</body>
</html>
