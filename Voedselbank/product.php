<?php
session_start();
include 'db_connect.php';

// Controleer of de gebruiker is ingelogd en admin-rechten heeft
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Haal producten op uit de database
$sql = "SELECT * FROM Producten"; // Zorg ervoor dat dit overeenkomt met de naam van je tabel
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beheer Producten</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="products.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Beheer Producten</h1>

    <!-- Knop voor product toevoegen -->
    <div class="add-product">
        <a href="add_product.php">
            <button type="button">Voeg Nieuw Product Toe</button>
        </a>
    </div>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Beschrijving</th>
            <th>Categorie</th>
            <th>Voorraad</th>
            <th>EAN Nummer</th>
            <th>Acties</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['idProducten']}</td> <!-- Pas aan als de kolomnaam anders is -->
                <td>{$row['naam']}</td>
                <td>{$row['beschrijving']}</td> <!-- Pas aan als de kolomnaam anders is -->
                <td>{$row['Categorieen_idCategorieen']}</td> <!-- Pas aan als de kolomnaam anders is -->
                <td>{$row['aantal']}</td> <!-- Pas aan als de kolomnaam anders is -->
                <td>{$row['ean']}</td> <!-- Pas aan als de kolomnaam anders is -->
                <td>
                    <a href='edit_product.php?id={$row['idProducten']}'>Bewerken</a> | 
                    <a href='delete_product.php?id={$row['idProducten']}' onclick='return confirm(\"Weet je zeker dat je dit product wilt verwijderen?\")'>Verwijderen</a>
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
