<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Manage Users</h1>
        <?php
        // Fetch users from the database and display them
        $sql = "SELECT * FROM user";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>
            <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
            </tr>";

            while($row = $result->fetch_assoc()) {
                echo "<tr>
                <td>{$row['AccountID']}</td>
                <td>{$row['Email']}</td>
                <td>{$row['Naam']}</td>
                <td>{$row['Telefoonnummer']}</td>
                <td>{$row['role']}</td>
                <td>
                    <a href='edit_user.php?id={$row['AccountID']}'>Edit</a> | 
                    <a href='delete_user.php?id={$row['AccountID']}' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "No users found.";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
