<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="products.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Manage Products</h1>

    <!-- Add Product Button -->
    <div class="add-product">
        <a href="add_product.php">
            <button type="button">Add New Product</button>
        </a>
    </div>

    <?php
    // Fetch products from the database and display them
    $sql = "SELECT * FROM producten";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Stock</th>
            <th>EAN Number</th>
            <th>Actions</th>
            </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['beschrijving']}</td>
                <td>{$row['categorie']}</td>
                <td>{$row['voorraad']}</td>
                <td>{$row['EAN_Nummer']}</td>
                <td>
                    <a href='edit_product.php?id={$row['id']}'>Edit</a> | 
                    <a href='delete_product.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No products found.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>
