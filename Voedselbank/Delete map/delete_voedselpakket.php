<?php
session_start();
include 'db_connect.php'; // Include your database connection script

// Check if the user is logged in and has the admin role
// Redirect if not admin (role 1), medewerker (role 2), of vrijwilliger (role 3)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    // Function to restore the stock of products related to a voedselpakket
    function restoreProductStock($conn, $voedselpakket_id) {
        // Get all products and their quantities from producten_has_voedselpakketen related to the voedselpakket
        $sql = "SELECT Producten_idProducten, Aantal FROM producten_has_voedselpakketen WHERE Voedselpakketen_idVoedselpakketen = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $voedselpakket_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Loop through each product and restore its stock
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['Producten_idProducten'];
            $aantal = $row['Aantal'];

            // Update the stock in the producten table (add the aantal back to the stock)
            $updateSql = "UPDATE producten SET aantal = aantal + ? WHERE idProducten = ?";
            $updateStmt = $conn->prepare($updateSql);
            if (!$updateStmt) {
                die("Prepare failed: " . $conn->error);
            }
            $updateStmt->bind_param("ii", $aantal, $product_id);
            if (!$updateStmt->execute()) {
                $conn->rollback(); // Rollback transaction if stock update fails
                die("Failed to restore stock: " . $updateStmt->error);
            }
        }
        $stmt->close();
    }

    // Function to delete records from the producten_has_voedselpakketen table
    function deleteVoedselpakketProducts($conn, $id) {
        // Restore the stock before deleting the products
        restoreProductStock($conn, $id);

        // Delete the products linked to the voedselpakket
        $sql = "DELETE FROM producten_has_voedselpakketen WHERE Voedselpakketen_idVoedselpakketen = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $conn->rollback(); // Rollback transaction if deletion fails
            die("Execute failed: " . $stmt->error);
        }
        return $stmt->affected_rows > 0;
    }

    // Function to delete the voedselpakket
    function deleteVoedselpakket($conn, $id) {
        // Delete related records first and restore stock
        if (!deleteVoedselpakketProducts($conn, $id)) {
            return false;
        }

        // Now delete the voedselpakket itself
        $sql = "DELETE FROM voedselpakketen WHERE idVoedselpakketen = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $conn->rollback(); // Rollback transaction if voedselpakket deletion fails
            die("Execute failed: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    // Attempt to delete the voedselpakket
    if (deleteVoedselpakket($conn, $id)) {
        // Commit the transaction if everything is successful
        $conn->commit();
        echo "<script>alert('Voedselpakket succesvol verwijderd en voorraad hersteld.'); window.location.href='voedselpakket.php';</script>";
    } else {
        echo "<script>alert('Het verwijderen van het voedselpakket is mislukt.'); window.location.href='voedselpakket.php';</script>";
    }
} else {
    // Redirect back if no ID is provided
    header("Location: voedselpakket.php");
    exit;
}

$conn->close();
?>
