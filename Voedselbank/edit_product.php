<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Handle the form submission for updating the product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $naam = $_POST['naam'];
    $categorie = intval($_POST['categorie']); // Convert to integer
    $voorraad = intval($_POST['voorraad']);
    $ean_nummer = $_POST['ean_nummer'];

    // Update the product details in the database
    $sql = "UPDATE Producten SET naam = ?, Categorieen_idCategorieen = ?, aantal = ?, ean = ? WHERE idProducten = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Corrected the number of bind parameters
    $stmt->bind_param("sisis", $naam, $categorie, $voorraad, $ean_nummer, $id);

    if ($stmt->execute()) {
        header("Location: product.php?updated=success");
        exit;
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}

// Fetch the product data for the given ID
$id = intval($_GET['id']);
$sql = "SELECT idProducten, naam, categorie_id, ean, aantal, Categorieen_idCategorieen, created_at FROM Producten WHERE idProducten = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if the product was found
if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

// Fetch categories for the dropdown
$sql = "SELECT idCategorieen, naam FROM Categorieen";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Bewerken</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            padding: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Product Bewerken</h1>
    <form action="edit_product.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['idProducten']); ?>">

        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($product['naam']); ?>" required><br><br>

        <label for="categorie">Categorie:</label>
        <select id="categorie" name="categorie" required>
            <?php
            // Populate dropdown with categories
            while ($row = $categories->fetch_assoc()) {
                $selected = $row['idCategorieen'] == $product['categorie_id'] ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($row['idCategorieen']) . "' $selected>" . htmlspecialchars($row['naam']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="voorraad">Voorraad:</label>
        <input type="number" id="voorraad" name="voorraad" value="<?php echo htmlspecialchars($product['aantal']); ?>" required><br><br>

        <label for="ean_nummer">EAN Nummer:</label>
        <input type="text" id="ean_nummer" name="ean_nummer" value="<?php echo htmlspecialchars($product['ean']); ?>" required><br><br>

        <button type="submit" name="update">Update Product</button>
    </form>
</div>
</body>
</html>

<?php
// Close the statement and connection only if they are still open
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
