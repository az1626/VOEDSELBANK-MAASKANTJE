<?php
session_start();
include 'db_connect.php';

// Function to generate a random EAN-13 number
function generateEAN13() {
    $ean = '';
    for ($i = 0; $i < 12; $i++) {
        $ean .= rand(0, 9);
    }

    // Calculate the checksum digit
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $digit = (int)$ean[$i];
        if ($i % 2 == 0) {
            $sum += $digit;
        } else {
            $sum += $digit * 3;
        }
    }
    $checksum = (10 - ($sum % 10)) % 10;

    return $ean . $checksum;
}

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

    // If EAN number is not provided, generate one
    if (empty($ean_nummer)) {
        $ean_nummer = generateEAN13();
    }

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

        <label for="ean_nummer">EAN Nummer (leave blank to generate automatically):</label>
        <input type="text" id="ean_nummer" name="ean_nummer"><br><br>

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
