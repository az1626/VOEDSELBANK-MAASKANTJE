<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $naam = $_POST['naam'];
    $beschrijving = $_POST['beschrijving'];
    $categorie = intval($_POST['categorie']); // Ensure this is an integer
    $voorraad = intval($_POST['voorraad']); // Convert stock to integer
    $ean_nummer = $_POST['ean_nummer'];

    // Prepare and execute the SQL statement to insert the new product
    $sql = "INSERT INTO Producten (naam, beschrijving, categorie_id, ean, aantal, Categorieen_idCategorieen) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ssisis", $naam, $beschrijving, $categorie, $ean_nummer, $voorraad, $categorie);

    if ($stmt->execute()) {
        header("Location: product.php?added=success");
        exit;
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>"; // Display error message securely
    }

    $stmt->close();
}

// Fetch categories from the database
$sql = "SELECT idCategorieen, naam FROM Categorieen";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->execute();
$categories = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg Product Toe</title>
    <link rel="stylesheet" href="products.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Voeg Nieuw Product Toe</h1>
    <form action="add_product.php" method="POST">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" required><br><br>

        <label for="beschrijving">Beschrijving:</label>
        <input type="text" id="beschrijving" name="beschrijving" required><br><br>

        <label for="categorie">Categorie:</label>
        <select id="categorie" name="categorie" required>
            <?php
            // Populate dropdown with categories
            while ($row = $categories->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['idCategorieen']) . "'>" . htmlspecialchars($row['naam']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="voorraad">Voorraad:</label>
        <input type="number" id="voorraad" name="voorraad" required><br><br>

        <label for="ean_nummer">EAN Nummer:</label>
        <input type="text" id="ean_nummer" name="ean_nummer" required><br><br>

        <button type="submit">Voeg Toe</button>
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
