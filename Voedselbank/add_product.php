<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $beschrijving = $_POST['beschrijving'];
    $categorie = $_POST['categorie'];
    $voorraad = $_POST['voorraad'];
    $ean_nummer = $_POST['ean_nummer'];

    $sql = "INSERT INTO producten (naam, beschrijving, categorie, voorraad, EAN_Nummer) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $naam, $beschrijving, $categorie, $voorraad, $ean_nummer);

    if ($stmt->execute()) {
        header("Location: product.php"); // Redirect to producten.php after adding
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="products.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Add New Product</h1>
        <form action="add_product.php" method="POST">
            <label for="naam">Name:</label>
            <input type="text" id="naam" name="naam" required><br><br>

            <label for="beschrijving">Description:</label>
            <input type="text" id="beschrijving" name="beschrijving" required><br><br>

            <label for="categorie">Category:</label>
            <input type="text" id="categorie" name="categorie" required><br><br>

            <label for="voorraad">Stock:</label>
            <input type="number" id="voorraad" name="voorraad" required><br><br>

            <label for="ean_nummer">EAN Number:</label>
            <input type="text" id="ean_nummer" name="ean_nummer" required><br><br>

            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
