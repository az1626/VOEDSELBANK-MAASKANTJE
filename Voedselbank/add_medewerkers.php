<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = $_POST['name']; // Corrected the form input name
    $role = $_POST['role'];

    // Adjusted the SQL query to match the actual column names in the database
    $sql = "INSERT INTO gebruikers (Email, Wachtwoord, Gebruikersnaam, Rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $error_message = "Error preparing statement: " . $conn->error;
    } else {
        $stmt->bind_param("sssi", $email, $password, $name, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "New medewerker added successfully!";
            header("Location: medewerkers.php");
            exit;
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medewerker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Add New Medewerker</h1>

    <!-- Display success or error messages -->
    <?php
    if (isset($success_message)) {
        echo "<div class='message success'>$success_message</div>";
    }
    if (isset($error_message)) {
        echo "<div class='message error'>$error_message</div>";
    }
    ?>

    <form action="add_medewerkers.php" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="name">Username:</label><br>
        <input type="text" id="name" name="name" required><br><br>

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
