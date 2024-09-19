<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
// Redirect if not admin (role 1), medewerker (role 2), or vrijwilliger (role 3)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
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
$sql = "SELECT idProducten, naam, categorie_id, ean, aantal, Categorieen_idCategorieen, created_at FROM producten";
$result = $conn->query($sql);
$producten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
} else {
    echo "No products found.";
}

// Fetch all clients for displaying radio
$sql = "SELECT idKlanten, naam, adres, telefoonnummer, email, aantal_volwassenen, aantal_kinderen, aantal_babys, idGebruikers, postcode FROM klanten";

$result = $conn->query($sql);
$klanten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $klanten[] = $row;
    }
} else {
    echo "No clients found.";
}

// Fetch dietary preferences for each client
$dieetwensen = [];
foreach ($klanten as $klant) {
    $sql = "SELECT d.naam FROM Klanten_has_Dieetwensen k_d INNER JOIN Dieetwensen d ON k_d.Dieetwensen_idDieetwensen = d.idDieetwensen WHERE k_d.Klanten_idKlanten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $klant['idKlanten']);
    $stmt->execute();
    $result = $stmt->get_result();
    $preferences = [];
    while ($row = $result->fetch_assoc()) {
        $preferences[] = $row['naam'];
    }
    $dieetwensen[$klant['idKlanten']] = $preferences;
}
$stmt->close();
$conn->close();
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
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 600;
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
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="date"]:focus, input[type="number"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .client-group, .product-group {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }

        .client-group > label, .product-group > label {
            font-size: 18px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .client-option, .product-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .client-option input[type="radio"], .product-option input[type="checkbox"] {
            margin-right: 10px;
        }

        .product-option {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .product-option label {
            font-size: 16px;
            color: #2c3e50;
        }

        .product-option p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .quantity {
            width: 60px;
            margin-left: 10px;
        }

        .dieetwensen {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 4px;
        }

        @media (max-width: 600px) {
            .container {
                width: 95%;
                padding: 1rem;
            }

            .product-option {
                flex-direction: column;
                align-items: flex-start;
            }

            .quantity {
                margin-left: 0;
                margin-top: 5px;
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
                    <input type="radio" id="klant_<?php echo $klant['idKlanten']; ?>" name="klant_id" value="<?php echo $klant['idKlanten']; ?>" required onchange="showDieetwensen(<?php echo $klant['idKlanten']; ?>)">
                    <label for="klant_<?php echo $klant['idKlanten']; ?>"><?php echo htmlspecialchars($klant['naam']); ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="dieetwensen" id="dieetwensen_display"></div>

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

<script>
    const dieetwensen = <?php echo json_encode($dieetwensen); ?>;

    function showDieetwensen(klantId) {
        const display = document.getElementById('dieetwensen_display');
        if (dieetwensen[klantId]) {
            display.innerHTML = '<strong>Dieetwensen:</strong> ' + dieetwensen[klantId].join(', ');
        } else {
            display.innerHTML = '';
        }
    }
</script>
</body>
</html>