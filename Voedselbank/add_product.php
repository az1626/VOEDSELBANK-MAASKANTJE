<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle the form submission for adding a new product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $naam = $_POST['naam'];
    $beschrijving = $_POST['beschrijving'];
    $categorie = $_POST['categorie'];
    $voorraad = intval($_POST['voorraad']); // Convert stock to integer
    $ean_nummer = $_POST['ean_nummer'];

    // Prepare the SQL statement to insert the new product
    $sql = "INSERT INTO producten (Naam, Beschrijving, Categorie, Voorraad, EAN_Nummer) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $naam, $beschrijving, $categorie, $voorraad, $ean_nummer);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        // Redirect to product.php with a success message
        header("Location: product.php?added=success");
        exit;
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>"; // Display error message securely
    }

    // Close the statement and the connection
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
