<?php
session_start();
include 'db_connect.php';

// Controleer of de gebruiker is ingelogd en admin- of medewerker-rechten heeft
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Zoekopdracht op barcode of productnaam
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Sortering ophalen
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'idProducten'; // Default sort by product ID
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Haal producten op uit de database, eventueel met zoekopdracht en sortering
$sql = "SELECT * FROM Producten 
        WHERE naam LIKE ? OR ean LIKE ? 
        ORDER BY $sort_column $sort_order";
$stmt = $conn->prepare($sql);
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
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            margin-bottom: 20px;
        }
        input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .add-product {
            margin-bottom: 20px;
        }
        .add-product button {
            width: 100%;
            border-radius: 4px;
            background-color: #2ecc71;
        }
        .add-product button:hover {
            background-color: #27ae60;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #2c3e50;
        }
        th a {
            color: #2c3e50;
            text-decoration: none;
        }
        th a:hover {
            text-decoration: underline;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        td a {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
        }
        td a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }
            table {
                font-size: 14px;
            }
            th, td {
                padding: 8px;
            }
        }
    </style>
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
    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th><a href='?sort=idProducten&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>ID</a></th>
            <th><a href='?sort=naam&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Naam</a></th>
            <th><a href='?sort=beschrijving&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Beschrijving</a></th>
            <th><a href='?sort=Categorieen_idCategorieen&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Categorie</a></th>
            <th><a href='?sort=aantal&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>Voorraad</a></th>
            <th><a href='?sort=ean&order=" . ($sort_order === 'ASC' ? 'desc' : 'asc') . "'>EAN Nummer</a></th>
            <th>Acties</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['idProducten']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['beschrijving']}</td>
                <td>{$row['Categorieen_idCategorieen']}</td>
                <td>{$row['aantal']}</td>
                <td>{$row['ean']}</td>
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
