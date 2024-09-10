<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM Leveranciers WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to avoid showing message on page refresh
        header("Location: leveranciers.php?deleted=success");
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch suppliers from the database
$sql = "SELECT * FROM Leveranciers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="suppliers.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Manage Suppliers</h1>

    <!-- Add Supplier Button -->
    <div style="margin-bottom: 20px;">
        <a href="add_leverancier.php">
            <button type="button">Add New Supplier</button>
        </a>
    </div>

    <?php
    // Show success message if the 'deleted' parameter is present
    if (isset($_GET['deleted']) && $_GET['deleted'] == 'success') {
        echo "<p>Supplier deleted successfully!</p>";
    }

    if ($result->num_rows > 0) {
        echo "<table>
            <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact Person</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Next Delivery</th>
            <th>Actions</th>
            </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['idLeveranciers']}</td>
                <td>{$row['naam']}</td>
                <td>{$row['contactpersoon']}</td>
                <td>{$row['telefoonnummer']}</td>
                <td>{$row['email']}</td>
                <td>{$row['eerstevolgende_levering']}</td>
                <td>
                    <a href='edit_leverancier.php?id={$row['idLeveranciers']}'>Edit</a> | 
                    <a href='leveranciers.php?delete={$row['idLeveranciers']}' onclick='return confirm(\"Are you sure you want to delete this supplier?\")'>Delete</a>
                </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No suppliers found.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</body>
</html>