<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Check if a product ID is provided in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ensure the ID is treated as an integer to prevent SQL injection

    // Prepare the SQL statement to delete the product
    $sql = "DELETE FROM Producten WHERE idProducten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        // Redirect to the products page with a success message
        header("Location: product.php?deleted=success");
        exit;
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>"; // Display error message securely
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "<p>No product ID specified.</p>"; // Display an error message if no ID is provided
}
?>
