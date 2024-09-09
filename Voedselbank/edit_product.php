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
    $beschrijving = $_POST['beschrijving'];
    $categorie = $_POST['categorie'];
    $voorraad = intval($_POST['voorraad']);
    $ean_nummer = $_POST['ean_nummer'];

    // Update the product details in the database
    $sql = "UPDATE producten SET naam = ?, beschrijving = ?, categorie = ?, voorraad = ?, EAN_Nummer = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $naam, $beschrijving, $categorie, $voorraad, $ean_nummer, $id);

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
$sql = "SELECT * FROM producten WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

// Check if the product was found
if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="products.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Edit Product</h1>
    <form action="edit_product.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">

        <label for="naam">Name:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($product['naam']); ?>" required><br><br>

        <label for="beschrijving">Description:</label>
        <input type="text" id="beschrijving" name="beschrijving" value="<?php echo htmlspecialchars($product['beschrijving']); ?>" required><br><br>

        <label for="categorie">Category:</label>
        <input type="text" id="categorie" name="categorie" value="<?php echo htmlspecialchars($product['categorie']); ?>" required><br><br>

        <label for="voorraad">Stock:</label>
        <input type="number" id="voorraad" name="voorraad" value="<?php echo htmlspecialchars($product['voorraad']); ?>" required><br><br>

        <label for="ean_nummer">EAN Number:</label>
        <input type="text" id="ean_nummer" name="ean_nummer" value="<?php echo htmlspecialchars($product['EAN_Nummer']); ?>" required><br><br>

        <button type="submit" name="update">Update Product</button>
    </form>
</div>
</body>
</html>
