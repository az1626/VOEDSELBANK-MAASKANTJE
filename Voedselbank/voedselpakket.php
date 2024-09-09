<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Function to get all voedselpakketten with product details
function getVoedselpakketten($conn) {
    $sql = "SELECT v.id, v.naam, v.samenstellingsdatum, v.ophaaldatum, 
                   p.naam AS product_naam, vp.quantity
            FROM voedselpakket v
            LEFT JOIN voedselpakket_producten vp ON v.id = vp.voedselpakket_id
            LEFT JOIN producten p ON vp.product_id = p.id
            ORDER BY v.id, p.naam";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $voedselpakketten = [];
        $current_id = null;
        $current_pakket = null;

        while ($row = $result->fetch_assoc()) {
            if ($current_id !== $row['id']) {
                if ($current_pakket !== null) {
                    $voedselpakketten[] = $current_pakket;
                }
                $current_id = $row['id'];
                $current_pakket = [
                    'id' => $row['id'],
                    'naam' => $row['naam'],
                    'producten' => [],
                    'samenstellingsdatum' => $row['samenstellingsdatum'],
                    'ophaaldatum' => $row['ophaaldatum']
                ];
            }
            if ($row['product_naam'] !== null) {
                $current_pakket['producten'][] = $row['product_naam'] . ' x' . $row['quantity'];
            }
        }
        if ($current_pakket !== null) {
            $voedselpakketten[] = $current_pakket;
        }
        return $voedselpakketten;
    } else {
        return [];
    }
}

// Get all voedselpakketten for display
$voedselpakketten = getVoedselpakketten($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakketten Beheer</title>
    <!-- Your existing CSS styles here -->
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1, h2 {
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
    }

    button:hover {
        background-color: #0056b3;
    }

    .action-links a {
        margin-right: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .action-links a:hover {
        text-decoration: underline;
    }

    .action-links a.delete {
        color: #dc3545;
    }

    .action-links a.delete:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Voedselpakketten Beheer</h1>

        <a href="add_voedselpakket.php"><button>Voeg een nieuw Voedselpakket toe</button></a>

        <h2>Alle Voedselpakketten</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Naam</th>
                    <th>Producten</th>
                    <th>Samenstellingsdatum</th>
                    <th>Ophaaldatum</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($voedselpakketten)): ?>
                    <tr>
                        <td colspan="6">Geen voedselpakketten gevonden.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($voedselpakketten as $pakket) : ?>
                        <tr>
                            <td><?php echo $pakket['id']; ?></td>
                            <td><?php echo $pakket['naam']; ?></td>
                            <td><?php echo implode(', ', $pakket['producten']); ?></td>
                            <td><?php echo $pakket['samenstellingsdatum']; ?></td>
                            <td><?php echo $pakket['ophaaldatum']; ?></td>
                            <td class="action-links">
                                <a href="edit_voedselpakket.php?id=<?php echo $pakket['id']; ?>">Bewerken</a>
                                <a href="delete_voedselpakket.php?id=<?php echo $pakket['id']; ?>" onclick="return confirm('Weet je zeker dat je dit voedselpakket wilt verwijderen?');">Verwijderen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>