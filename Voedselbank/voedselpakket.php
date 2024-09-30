<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin, medewerker, or vrijwilliger role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}

// Function to get all voedselpakketen with product details and klant name
function getVoedselpakketen($conn) {
    $sql = "SELECT v.idVoedselpakketen AS id, 
                   v.Samenstellingsdatum AS samenstellingsdatum, 
                   v.Uitgiftedatum AS ophaaldatum,
                   p.naam AS product_naam, 
                   vp.Aantal AS product_aantal,
                   k.naam AS klant_naam
            FROM Voedselpakketen v
            LEFT JOIN Producten_has_Voedselpakketen vp ON v.idVoedselpakketen = vp.Voedselpakketen_idVoedselpakketen
            LEFT JOIN Producten p ON vp.Producten_idProducten = p.idProducten
            LEFT JOIN Klanten k ON v.Klanten_idKlanten = k.idKlanten
            ORDER BY v.idVoedselpakketen, p.naam";

    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $voedselpakketen = [];
        $current_id = null;
        $current_pakket = null;

        while ($row = $result->fetch_assoc()) {
            if ($current_id !== $row['id']) {
                if ($current_pakket !== null) {
                    $voedselpakketen[] = $current_pakket;
                }
                $current_id = $row['id'];
                $current_pakket = [
                    'id' => $row['id'],
                    'samenstellingsdatum' => $row['samenstellingsdatum'],
                    'ophaaldatum' => $row['ophaaldatum'],
                    'klant_naam' => $row['klant_naam'],
                    'producten' => []
                ];
            }
            if ($row['product_naam'] !== null) {
                // Append product name and quantity to the producten array
                $current_pakket['producten'][] = $row['product_naam'] . " (Aantal: " . $row['product_aantal'] . ")"; // Show product quantity
            }
        }
        if ($current_pakket !== null) {
            $voedselpakketen[] = $current_pakket;
        }
        return $voedselpakketen;
    } else {
        return [];
    }
}

// Get all voedselpakketen for display
$voedselpakketen = getVoedselpakketen($conn);

// Check if an ID is provided for deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    deleteVoedselpakket($conn, $id);
}

// Function to delete voedselpakket and related products
function deleteVoedselpakket($conn, $id) {
    // Fetch product quantities before deletion
    $sql = "SELECT Producten_idProducten, Aantal FROM Producten_has_Voedselpakketen WHERE Voedselpakketen_idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Array to hold the quantities to restore
    $products_to_restore = [];

    while ($row = $result->fetch_assoc()) {
        $products_to_restore[$row['Producten_idProducten']] = $row['Aantal'];
    }

    // Delete related records first
    $sql = "DELETE FROM Producten_has_Voedselpakketen WHERE Voedselpakketen_idVoedselpakketen = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Now delete the voedselpakket
    $sql = "DELETE FROM Voedselpakketen WHERE idVoedselpakketen = ?";
    $stmt->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Restore the product quantities
    foreach ($products_to_restore as $product_id => $quantity) {
        restoreProductQuantity($conn, $product_id, $quantity);
    }

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Voedselpakket successfully deleted.'); window.location.href='voedselpakket.php';</script>";
    } else {
        echo "<script>alert('Failed to delete voedselpakket.'); window.location.href='voedselpakket.php';</script>";
    }
}

// Function to restore product quantity
function restoreProductQuantity($conn, $product_id, $quantity) {
    $sql = "UPDATE Producten SET aantal = aantal + ? WHERE idProducten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $product_id);
    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakketen Beheer</title>
    <link rel="stylesheet" href="CSS/voedselpakket.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Voedselpakketen Beheer</h1>

    <a href="add_voedselpakket.php"><button>Voeg een nieuw Voedselpakket toe</button></a>

    <h2>Alle Voedselpakketen</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Samenstellingsdatum</th>
            <th>Ophaaldatum</th>
            <th>Klant</th> <!-- Added Klant -->
            <th>Producten</th>
            <th>Acties</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($voedselpakketen)): ?>
            <tr>
                <td colspan="6">Geen voedselpakketen gevonden.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($voedselpakketen as $pakket) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($pakket['id']); ?></td>
                    <td><?php echo htmlspecialchars($pakket['samenstellingsdatum']); ?></td>
                    <td><?php echo htmlspecialchars($pakket['ophaaldatum']); ?></td>
                    <td><?php echo htmlspecialchars($pakket['klant_naam']); ?></td> <!-- Display Klant Name -->
                    <td><?php echo htmlspecialchars(implode(', ', $pakket['producten'])); ?></td> <!-- Display Products with Quantities -->
                    <td class="action-links">
                        <a href="edit_voedselpakket.php?id=<?php echo htmlspecialchars($pakket['id']); ?>">Bewerken</a>
                        <a href="?delete_id=<?php echo htmlspecialchars($pakket['id']); ?>" onclick="return confirm('Weet je zeker dat je dit voedselpakket wilt verwijderen?');">Verwijderen</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
