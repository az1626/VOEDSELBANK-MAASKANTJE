<?php
session_start();
include 'db_connect.php'; // Include your database connection script

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Function to delete a voedselpakket
    function deleteVoedselpakket($conn, $id) {
        $sql = "DELETE FROM voedselpakket WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        return $stmt->affected_rows > 0;
    }

    // Attempt to delete the voedselpakket
    if (deleteVoedselpakket($conn, $id)) {
        echo "<script>alert('Voedselpakket successfully deleted.'); window.location.href='voedselpakket.php';</script>";
    } else {
        echo "<script>alert('Failed to delete voedselpakket.'); window.location.href='voedselpakket.php';</script>";
    }
} else {
    // Redirect back if no ID is provided
    header("Location: voedselpakket.php");
    exit;
}

$conn->close();
?>
