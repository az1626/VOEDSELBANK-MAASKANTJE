<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission for adding a new medewerker
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $sql = "INSERT INTO user (Email, Wachtwoord, Naam, Telefoonnummer, role) VALUES ('$email', '$password', '$name', '$phone', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "New medewerker added successfully!";
        header("Location: medewerkers.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medewerker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Add New Medewerker</h1>
        <form action="add_medewerkers.php" method="POST">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>
            
            <label for="phone">Phone:</label><br>
            <input type="text" id="phone" name="phone" required><br><br>
            
            <label for="role">Role:</label><br>
            <select id="role" name="role" required>
                <option value="0">Medewerker</option>
                <option value="1">Admin</option>
            </select><br><br>
            
            <input type="submit" value="Add Medewerker">
        </form>
    </div>
</body>
</html>
