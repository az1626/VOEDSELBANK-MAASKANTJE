<?php
session_start();
include 'db_connect.php'; // Include your database connection script

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}

$voedselpakket_id = $_GET['id']; // Assume voedselpakket ID is passed via GET

// Function to fetch the details of the voedselpakket
function getVoedselpakketDetails($conn, $voedselpakket_id) {
    $sql = "SELECT * FROM voedselpakketen WHERE idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voedselpakket_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to fetch the products already in the voedselpakket
function getVoedselpakketProducts($conn, $voedselpakket_id) {
    $sql = "SELECT p.idProducten, p.naam, phv.Aantal, p.Categorieen_idCategorieen FROM producten p 
            INNER JOIN producten_has_voedselpakketen phv ON p.idProducten = phv.Producten_idProducten
            WHERE phv.Voedselpakketen_idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voedselpakket_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to update the voedselpakket
function updateVoedselpakket($conn, $voedselpakket_id, $samenstellingsdatum, $uitgiftedatum) {
    $sql = "UPDATE voedselpakketen SET Samenstellingsdatum = ?, Uitgiftedatum = ? WHERE idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $samenstellingsdatum, $uitgiftedatum, $voedselpakket_id);
    return $stmt->execute();
}

// Function to get the current quantities of products in a voedselpakket
function getCurrentVoedselpakketQuantities($conn, $voedselpakket_id) {
    $sql = "SELECT Producten_idProducten, Aantal FROM producten_has_voedselpakketen WHERE Voedselpakketen_idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voedselpakket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_quantities = array();
    while ($row = $result->fetch_assoc()) {
        $current_quantities[$row['Producten_idProducten']] = $row['Aantal'];
    }
    return $current_quantities;
}

// Function to update product inventory
function updateProductInventory($conn, $product_id, $quantity_change) {
    $sql = "UPDATE producten SET aantal = aantal + ? WHERE idProducten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity_change, $product_id);
    return $stmt->execute();
}

function updateVoedselpakketProducts($conn, $voedselpakket_id, $products, $quantities, $categorie_ids, $klant_id) {
    // Get current quantities
    $current_quantities = getCurrentVoedselpakketQuantities($conn, $voedselpakket_id);

    // Start by removing all existing products for the voedselpakket
    $sql = "DELETE FROM producten_has_voedselpakketen WHERE Voedselpakketen_idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voedselpakket_id);
    $stmt->execute();

    // Prepare to insert new products and quantities
    $sql = "INSERT INTO producten_has_voedselpakketen (Producten_idProducten, Producten_Categorieen_idCategorieen, Voedselpakketen_idVoedselpakketen, Voedselpakketen_Klanten_idKlanten, Aantal) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Track which products have been processed for inventory updates
    $processed_products = [];

    foreach ($products as $index => $product_id) {
        $aantal = isset($quantities[$index]) ? $quantities[$index] : 0; // Ensure quantity is set
        $categorie_id = $categorie_ids[$index];

        if (!empty($product_id) && !empty($aantal)) {
            $stmt->bind_param("iiiii", $product_id, $categorie_id, $voedselpakket_id, $klant_id, $aantal);
            if (!$stmt->execute()) {
                return false; // Failed to insert product
            }

            // Update inventory
            $old_quantity = isset($current_quantities[$product_id]) ? $current_quantities[$product_id] : 0;
            $quantity_change = $old_quantity - $aantal;
            if (!updateProductInventory($conn, $product_id, $quantity_change)) {
                return false; // Failed to update inventory
            }
            $processed_products[] = $product_id; // Track processed product
        }
    }

    // Handle products that were removed from the voedselpakket
    foreach ($current_quantities as $product_id => $old_quantity) {
        if (!in_array($product_id, $processed_products)) {
            // Update inventory to add back the old quantity
            if (!updateProductInventory($conn, $product_id, $old_quantity)) {
                return false; // Failed to update inventory for removed products
            }
        }
    }
    
    return true;
}

// Fetch the current voedselpakket details and products
$voedselpakket = getVoedselpakketDetails($conn, $voedselpakket_id);
$voedselpakket_products = getVoedselpakketProducts($conn, $voedselpakket_id);

// Fetch the client's name based on the klant_id in the voedselpakket
$klant_id = $voedselpakket['Klanten_idKlanten'];
$sql = "SELECT naam FROM klanten WHERE idKlanten = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $klant_id);
$stmt->execute();
$result = $stmt->get_result();
$klant = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_voedselpakket'])) {
    $samenstellingsdatum = $_POST['samenstellingsdatum'];
    $uitgiftedatum = $_POST['uitgiftedatum'];
    $klant_id = $_POST['klant_id'];
    
    // Begin transaction
    $conn->begin_transaction();

    // Update voedselpakket details
    $voedselpakket_updated = updateVoedselpakket($conn, $voedselpakket_id, $samenstellingsdatum, $uitgiftedatum);

    // Update the products in the voedselpakket and manage inventory
    $products_updated = updateVoedselpakketProducts($conn, $voedselpakket_id, $_POST['producten'], $_POST['quantities'], $_POST['categorie_ids'], $klant_id);

    // Commit transaction if all succeeded, otherwise rollback
    if ($products_updated && $voedselpakket_updated) {
        $conn->commit();
        header("Location: voedselpakket.php");
        exit;
    } else {
        $conn->rollback();
        echo "<script>alert('Failed to update voedselpakket.');</script>";
    }
}

// Fetch all products for displaying checkboxes
$sql = "SELECT idProducten, naam, Categorieen_idCategorieen as categorie_id, aantal FROM producten";
$result = $conn->query($sql);
$producten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
}

// Fetch all clients for displaying radio options
$sql = "SELECT idKlanten, naam FROM klanten";
$result = $conn->query($sql);
$klanten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $klanten[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakket bewerken</title>
    <link rel="stylesheet" href="CSS/Add_Voedselpakket.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Bewerk Voedselpakket</h2>
    <form method="POST" action="">

        <!-- Client (show name instead of ID) -->
        <div class="client-group">
            <label>Klant:</label>
            <input type="text" value="<?php echo htmlspecialchars($klant['naam']); ?>" readonly>
            <input type="hidden" name="klant_id" value="<?php echo $klant_id; ?>">
        </div>

        <!-- Food Package Details -->
        <label for="samenstellingsdatum">Samenstellingsdatum:</label>
        <input type="date" id="samenstellingsdatum" name="samenstellingsdatum" value="<?php echo $voedselpakket['Samenstellingsdatum']; ?>" required>

        <label for="uitgiftedatum">Uitgiftedatum:</label>
        <input type="date" id="uitgiftedatum" name="uitgiftedatum" value="<?php echo $voedselpakket['Uitgiftedatum']; ?>" required>

        <!-- Pre-populate products and quantities -->
        <label>Selecteer Producten:</label>
        <div id="product-list">
            <?php foreach ($producten as $product): ?>
                <div class="product-item">
                    <label>
                        <input type="checkbox" name="producten[]" value="<?php echo $product['idProducten']; ?>"
                            <?php 
                            // Check if the product is already part of the voedselpakket
                            $voedselpakket_products->data_seek(0);
                            while ($vp_product = $voedselpakket_products->fetch_assoc()) {
                                if ($vp_product['idProducten'] == $product['idProducten']) {
                                    echo 'checked';
                                    break;
                                }
                            }
                            ?>>
                        <?php echo htmlspecialchars($product['naam']); ?> (Voorraad: <?php echo $product['aantal']; ?>)
                    </label>
                    <input type="hidden" name="categorie_ids[]" value="<?php echo $product['categorie_id']; ?>">

                    <!-- Pre-populate the quantity -->
                    <?php
                    $quantity = 0;
                    $voedselpakket_products->data_seek(0);
                    while ($vp_product = $voedselpakket_products->fetch_assoc()) {
                        if ($vp_product['idProducten'] == $product['idProducten']) {
                            $quantity = $vp_product['Aantal'];
                            break;
                        }
                    }
                    ?>
                    <input type="number" name="quantities[]" value="<?php echo $quantity; ?>" min="0" max="<?php echo $product['aantal']; ?>" class="quantity-input">
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" name="update_voedselpakket" class="btn btn-primary">Update Voedselpakket</button>
    </form>
</div>

</body>
</html>
