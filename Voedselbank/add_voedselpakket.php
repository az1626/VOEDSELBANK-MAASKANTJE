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
    <link rel="stylesheet" href="CSS/Add_Voedselpakket.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .dropdown-content div {
            padding: 12px;
            cursor: pointer;
        }

        .dropdown-content div:hover {
            background-color: #f1f1f1;
        }

        .dropdown input {
            padding: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .quantity-input {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Voeg een nieuw Voedselpakket toe</h2>
    <form method="POST" action="">

        <!-- Client Dropdown with Search -->
        <div class="client-group">
            <label>Selecteer een klant:</label>
            <div class="dropdown">
                <input type="text" id="clientSearch" placeholder="Zoek klant..." autocomplete="off" onclick="showClientList()" onkeyup="filterClients()">
                <div class="dropdown-content" id="clientList">
                    <?php foreach ($klanten as $klant): ?>
                        <div class="client-option" data-id="<?php echo $klant['idKlanten']; ?>" onclick="selectClient('<?php echo $klant['idKlanten']; ?>', '<?php echo htmlspecialchars($klant['naam']); ?>')">
                            <?php echo htmlspecialchars($klant['naam']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" name="klant_id" id="selectedClientId" required>
        </div>

        <!-- Dieetwensen Display -->
        <div class="dieetwensen" id="dieetwensen_display"></div>

        <!-- Product Dropdown with Search -->
        <div class="product-group">
            <label>Producten:</label>
            <div class="dropdown">
                <input type="text" id="productSearch" placeholder="Zoek product..." autocomplete="off" onclick="showProductList()" onkeyup="filterProducts()">
                <div class="dropdown-content" id="productList">
                    <?php foreach ($producten as $product): ?>
                        <div class="product-option" data-id="<?php echo $product['idProducten']; ?>" data-naam="<?php echo htmlspecialchars($product['naam']); ?>" data-max="<?php echo $product['aantal']; ?>" data-category="<?php echo $product['Categorieen_idCategorieen']; ?>" onclick="selectProduct('<?php echo $product['idProducten']; ?>', '<?php echo htmlspecialchars($product['naam']); ?>', '<?php echo $product['aantal']; ?>', '<?php echo $product['Categorieen_idCategorieen']; ?>')">
                            <?php echo htmlspecialchars($product['naam']) . " (Voorraad: " . $product['aantal'] . ")"; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" id="selectedProductId">
            <div class="quantity-input">
                <label for="productQuantity">Aantal:</label>
                <input type="number" id="productQuantity" min="1" max="" placeholder="Aantal">
                <input type="hidden" id="selectedCategoryId">
            </div>
            <button type="button" onclick="addProduct()">Voeg product toe</button>
        </div>

        <!-- Selected Products List -->
        <div class="selected-products">
            <h3>Geselecteerde producten:</h3>
            <div id="selectedProductsContainer"></div>
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

    // Show all clients when clicking on search box
    function showClientList() {
        document.getElementById('clientList').style.display = 'block';
    }

    // Filter clients based on search input
    function filterClients() {
        const input = document.getElementById('clientSearch');
        const filter = input.value.toLowerCase();
        const clientList = document.getElementById('clientList');
        const clientOptions = clientList.getElementsByTagName('div');

        for (let i = 0; i < clientOptions.length; i++) {
            const txtValue = clientOptions[i].textContent || clientOptions[i].innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                clientOptions[i].style.display = '';
            } else {
                clientOptions[i].style.display = 'none';
            }
        }
    }

    // Select client when clicked, hide dropdown and show dieetwensen
    function selectClient(id, name) {
        document.getElementById('selectedClientId').value = id;
        document.getElementById('clientSearch').value = name;
        document.getElementById('clientList').style.display = 'none';
        showDieetwensen(id); // Show dietary preferences
    }

    // Show dietary preferences for the selected client
    function showDieetwensen(klantId) {
        const display = document.getElementById('dieetwensen_display');
        if (dieetwensen[klantId]) {
            display.innerHTML = '<strong>Dieetwensen:</strong> ' + dieetwensen[klantId].join(', ');
        } else {
            display.innerHTML = '';
        }
    }

    // Show all products when clicking on search box
    function showProductList() {
        document.getElementById('productList').style.display = 'block';
    }

    // Filter products based on search input
    function filterProducts() {
        const input = document.getElementById('productSearch');
        const filter = input.value.toLowerCase();
        const productList = document.getElementById('productList');
        const productOptions = productList.getElementsByTagName('div');

        for (let i = 0; i < productOptions.length; i++) {
            const txtValue = productOptions[i].textContent || productOptions[i].innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                productOptions[i].style.display = '';
            } else {
                productOptions[i].style.display = 'none';
            }
        }
    }

    // Select product and show quantity field
    function selectProduct(id, name, max, category) {
        document.getElementById('selectedProductId').value = id;
        document.getElementById('productSearch').value = name;
        document.getElementById('productQuantity').max = max;
        document.getElementById('selectedCategoryId').value = category;
        document.getElementById('productList').style.display = 'none';
    }

    // Add product to the list
    function addProduct() {
        const productId = document.getElementById('selectedProductId').value;
        const productName = document.getElementById('productSearch').value;
        const quantity = document.getElementById('productQuantity').value;
        const categoryId = document.getElementById('selectedCategoryId').value;
        
        if (!productId || !quantity) {
            alert('Selecteer een product en vul een aantal in.');
            return;
        }

        const selectedProductsContainer = document.getElementById('selectedProductsContainer');
        const productHtml = `
            <div class="selected-product">
                <span>${productName} (Aantal: ${quantity})</span>
                <input type="hidden" name="producten[]" value="${productId}">
                <input type="hidden" name="quantities[]" value="${quantity}">
                <input type="hidden" name="categorie_ids[]" value="${categoryId}">
            </div>
        `;
        selectedProductsContainer.innerHTML += productHtml;

        // Clear input fields after adding product
        document.getElementById('productSearch').value = '';
        document.getElementById('productQuantity').value = '';
    }

    // Hide dropdown if clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('#clientSearch')) {
            document.getElementById('clientList').style.display = 'none';
        }
        if (!event.target.matches('#productSearch')) {
            document.getElementById('productList').style.display = 'none';
        }
    }
</script>
</body>
</html>

