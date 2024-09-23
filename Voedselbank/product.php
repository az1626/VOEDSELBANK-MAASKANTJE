<?php
session_start();
include 'db_connect.php';

// Controleer of de gebruiker is ingelogd en admin- of medewerker-rechten heeft
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Check if a product ID is provided in the URL for deletion
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure the ID is treated as an integer to prevent SQL injection

    // Prepare the SQL statement to delete the product
    $sql = "DELETE FROM Producten WHERE idProducten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product succesvol verwijderd!";
        header("Location: product.php");
        exit;
    } else {
        $_SESSION['error'] = "Fout: " . htmlspecialchars($stmt->error);
    }

    // Close the statement
    $stmt->close();
}

// Zoekopdracht op barcode of productnaam
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Sortering ophalen
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'idProducten'; // Default sort by product ID
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Haal producten en leveranciers op uit de database, eventueel met zoekopdracht en sortering
$sql = "SELECT p.idProducten, p.naam, p.aantal, p.ean, c.naam AS categorie, 
               GROUP_CONCAT(l.idLeveranciers SEPARATOR ', ') AS leveranciers
        FROM Producten p
        LEFT JOIN Categorieen c ON p.Categorieen_idCategorieen = c.idCategorieen
        LEFT JOIN Producten_has_Leveranciers phl ON p.idProducten = phl.Producten_idProducten
        LEFT JOIN Leveranciers l ON phl.Leveranciers_idLeveranciers = l.idLeveranciers
        WHERE p.naam LIKE ? OR p.ean LIKE ?
        GROUP BY p.idProducten
        ORDER BY $sort_column $sort_order";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$search_param = "%" . $search . "%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Producten</title>
    <link rel="stylesheet" href="CSS/product.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Beheer Producten</h1>

    <!-- Zoekformulier -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Zoek op naam of EAN" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Zoeken</button>
    </form>

    <!-- Knop voor product toevoegen -->
    <div class="add-product">
        <a href="add_product.php">
            <button type="button">Voeg Nieuw Product Toe</button>
        </a>
    </div>

    <?php
    // Display success or error messages
    if (isset($_SESSION['success'])) {
        echo "<p class='success'>{$_SESSION['success']}</p>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<p class='error'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }

    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th><a href='?sort=idProducten&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>ID</a></th>
            <th><a href='?sort=naam&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Naam</a></th>
            <th><a href='?sort=categorie&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Categorie</a></th>
            <th><a href='?sort=aantal&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Voorraad</a></th>
            <th><a href='?sort=ean&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>EAN Nummer</a></th>
            <th>Leverancier ID</th>
            <th>Acties</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['idProducten']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['categorie']}</td>
                <td>{$row['aantal']}</td>
                <td>{$row['ean']}</td>
                <td>{$row['leveranciers']}</td>
                <td>
                    <a href='edit_product.php?id={$row['idProducten']}'>Bewerken</a> | 
                    <a href='?id={$row['idProducten']}' onclick='return confirm(\"Weet je zeker dat je dit product wilt verwijderen?\")'>Verwijderen</a>
                </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Geen producten gevonden.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>
