<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Function to add a new voedselpakket
function addVoedselpakket($conn, $klant_id, $gebruiker_id, $samenstellingsdatum, $uitgiftedatum) {
    $sql = "INSERT INTO voedselpakketen (Klant_id, Gebruiker_id, Samenstellingsdatum, Uitgiftedatum, Klanten_idKlanten) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssi", $klant_id, $gebruiker_id, $samenstellingsdatum, $uitgiftedatum, $klant_id);
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

// Function to add products to the voedselpakket
function addVoedselpakketProducts($conn, $voedselpakket_id, $product_id, $categorie_id, $klant_id) {
    $sql = "INSERT INTO producten_has_voedselpakketen (Producten_idProducten, Producten_Categorieen_idCategorieen, Voedselpakketen_idVoedselpakketen, Voedselpakketen_Klanten_idKlanten) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iiii", $product_id, $categorie_id, $voedselpakket_id, $klant_id);
    return $stmt->execute();
}

// Function to update product stock
function updateProductStock($conn, $product_id, $quantity) {
    $sql = "UPDATE producten SET aantal = aantal - ? WHERE idProducten = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $quantity, $product_id);
    return $stmt->execute();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_voedselpakket'])) {
    $klant_id = $_POST['klant_id'];
    $gebruiker_id = $_SESSION['user_id']; // Assuming user ID is stored in the session
    $samenstellingsdatum = $_POST['samenstellingsdatum'];
    $uitgiftedatum = $_POST['uitgiftedatum'];

    // Begin transaction
    $conn->begin_transaction();

    $voedselpakket_id = addVoedselpakket($conn, $klant_id, $gebruiker_id, $samenstellingsdatum, $uitgiftedatum);

    if ($voedselpakket_id) {
        $all_success = true;

        foreach ($_POST['producten'] as $index => $product_id) {
            $quantity = $_POST['quantities'][$index];
            $categorie_id = $_POST['categorie_ids'][$index];
            if (!empty($product_id) && !empty($quantity)) {
                if (!addVoedselpakketProducts($conn, $voedselpakket_id, $product_id, $categorie_id, $klant_id) || !updateProductStock($conn, $product_id, $quantity)) {
                    $all_success = false;
                    break;
                }
            }
        }

        if ($all_success) {
            $conn->commit();
            header("Location: voedselpakket.php");
            exit;
        } else {
            $conn->rollback();
            echo "<script>alert('Failed to add voedselpakket or update stock.'); window.location.href='add_voedselpakket.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to add voedselpakket.'); window.location.href='add_voedselpakket.php';</script>";
    }
}

// Fetch all products for displaying checkboxes
$sql = "SELECT * FROM producten";
$result = $conn->query($sql);
$producten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
} else {
    echo "No products found.";
}

// Fetch all clients for displaying radio buttons
$sql = "SELECT * FROM klanten";
$result = $conn->query($sql);
$klanten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $klanten[] = $row;
    }
} else {
    echo "No clients found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg een nieuw Voedselpakket toe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        input[type="text"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="date"] {
            width: auto;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .client-group, .product-group {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .client-group > label, .product-group > label {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .client-option, .product-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .client-option label, .product-option label {
            margin-left: 10px;
            font-weight: normal;
        }

        .quantity {
            width: 60px;
            margin-left: 10px;
        }

        @media (max-width: 600px) {
            .container {
                width: 95%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Voeg een nieuw Voedselpakket toe</h2>
    <form method="POST" action="">
        <div class="client-group">
            <label>Selecteer een klant:</label>
            <?php foreach ($klanten as $klant): ?>
                <div class="client-option">
                    <input type="radio" id="klant_<?php echo $klant['idKlanten']; ?>" name="klant_id" value="<?php echo $klant['idKlanten']; ?>" required>
                    <label for="klant_<?php echo $klant['idKlanten']; ?>"><?php echo htmlspecialchars($klant['naam']); ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="product-group">
            <label>Producten:</label>
            <?php foreach ($producten as $index => $product): ?>
                <div class="product-option">
                    <input type="checkbox" id="product_<?php echo $product['idProducten']; ?>" name="producten[]" value="<?php echo $product['idProducten']; ?>">
                    <label for="product_<?php echo $product['idProducten']; ?>">
                        <?php echo htmlspecialchars($product['naam']) . " (Voorraad: " . $product['aantal'] . ")"; ?>
                    </label>
                    <input type="number" name="quantities[]" min="1" max="<?php echo $product['aantal']; ?>" class="quantity" placeholder="Aantal">
                    <input type="hidden" name="categorie_ids[]" value="<?php echo $product['Categorieen_idCategorieen']; ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <label for="samenstellingsdatum">Samenstellingsdatum:</label>
        <input type="date" id="samenstellingsdatum" name="samenstellingsdatum" required>

        <label for="uitgiftedatum">Uitgiftedatum:</label>
        <input type="date" id="uitgiftedatum" name="uitgiftedatum" required>

        <button type="submit" name="add_voedselpakket">Voeg toe</button>
    </form>
</div>
</body>
</html>