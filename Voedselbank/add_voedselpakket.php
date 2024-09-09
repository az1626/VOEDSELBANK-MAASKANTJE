<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

function addVoedselpakket($conn, $naam, $samenstellingsdatum, $ophaaldatum) {
    $sql = "INSERT INTO voedselpakket (naam, samenstellingsdatum, ophaaldatum) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $naam, $samenstellingsdatum, $ophaaldatum);
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

function addVoedselpakketProducts($conn, $voedselpakket_id, $product_id, $quantity) {
    $sql = "INSERT INTO voedselpakket_producten (voedselpakket_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $voedselpakket_id, $product_id, $quantity);
    return $stmt->execute();
}

function updateProductStock($conn, $product_id, $quantity) {
    $sql = "UPDATE producten SET voorraad = voorraad - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $product_id);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_voedselpakket'])) {
    $naam = $_POST['naam'];
    $samenstellingsdatum = $_POST['samenstellingsdatum'];
    $ophaaldatum = $_POST['ophaaldatum'];

    // Begin transaction
    $conn->begin_transaction();

    $voedselpakket_id = addVoedselpakket($conn, $naam, $samenstellingsdatum, $ophaaldatum);

    if ($voedselpakket_id) {
        $all_success = true;

        foreach ($_POST['producten'] as $index => $product_id) {
            $quantity = $_POST['quantities'][$index];
            if (!empty($product_id) && !empty($quantity)) {
                if (addVoedselpakketProducts($conn, $voedselpakket_id, $product_id, $quantity) && updateProductStock($conn, $product_id, $quantity)) {
                    continue;
                } else {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg een nieuw Voedselpakket toe</title>
    <style>
        /* Add your existing CSS styles here */
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Voeg een nieuw Voedselpakket toe</h2>
        <form method="POST" action="">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>

            <label>Producten:</label>
            <div class="checkbox-group">
                <?php foreach ($producten as $index => $product): ?>
                    <label>
                        <input type="checkbox" name="producten[]" value="<?php echo $product['id']; ?>">
                        <?php echo $product['naam'] . " (Voorraad: " . $product['voorraad'] . ")"; ?>
                        <input type="number" name="quantities[]" min="1" max="<?php echo $product['voorraad']; ?>" class="quantity" placeholder="Aantal">
                    </label>
                <?php endforeach; ?>
            </div>

            <label for="samenstellingsdatum">Samenstellingsdatum:</label>
            <input type="date" id="samenstellingsdatum" name="samenstellingsdatum" required>

            <label for="ophaaldatum">Ophaaldatum:</label>
            <input type="date" id="ophaaldatum" name="ophaaldatum" required>

            <button type="submit" name="add_voedselpakket">Voeg toe</button>
        </form>
    </div>
</body>
</html>
