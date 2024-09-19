<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
// Redirect if not admin (role 1), medewerker (role 2), or vrijwilliger (role 3)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}


// Function to get all voedselpakketen with product details
function getVoedselpakketen($conn) {
    $sql = "SELECT v.idVoedselpakketen AS id, v.Samenstellingsdatum AS samenstellingsdatum, v.Uitgiftedatum AS ophaaldatum,
                   p.naam AS product_naam
            FROM Voedselpakketen v
            LEFT JOIN Producten_has_Voedselpakketen vp ON v.idVoedselpakketen = vp.Voedselpakketen_idVoedselpakketen
            LEFT JOIN Producten p ON vp.Producten_idProducten = p.idProducten AND vp.Producten_Categorieen_idCategorieen = p.Categorieen_idCategorieen
            ORDER BY v.idVoedselpakketen, p.naam";

    $result = $conn->query($sql);

    // Error handling
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
                    'producten' => []
                ];
            }
            if ($row['product_naam'] !== null) {
                $current_pakket['producten'][] = $row['product_naam'];
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
            <th>Producten</th>
            <th>Acties</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($voedselpakketen)): ?>
            <tr>
                <td colspan="5">Geen voedselpakketen gevonden.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($voedselpakketen as $pakket) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($pakket['id']); ?></td>
                    <td><?php echo htmlspecialchars($pakket['samenstellingsdatum']); ?></td>
                    <td><?php echo htmlspecialchars($pakket['ophaaldatum']); ?></td>
                    <td><?php echo htmlspecialchars(implode(', ', $pakket['producten'])); ?></td>
                    <td class="action-links">
                        <a href="edit_voedselpakket.php?id=<?php echo htmlspecialchars($pakket['id']); ?>">Bewerken</a>
                        <a href="delete_voedselpakket.php?id=<?php echo htmlspecialchars($pakket['id']); ?>" onclick="return confirm('Weet je zeker dat je dit voedselpakket wilt verwijderen?');">Verwijderen</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
