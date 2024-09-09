<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle the form submission to add a new supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $naam = $_POST['naam'];
    $mail = $_POST['mail'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $postcode = $_POST['postcode'];
    $bezorgingsdatum = $_POST['bezorgingsdatum'];
    $bezorgingstijd = $_POST['bezorgingstijd'];

    // Prepare the SQL statement to insert the new supplier
    $sql = "INSERT INTO leveranciers (Naam, Email, Telefoonnummer, Postcode, Bezorgingsdatum, Bezorgingstijd) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $naam, $mail, $telefoonnummer, $postcode, $bezorgingsdatum, $bezorgingstijd);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        // Redirect to leveranciers.php with a success message
        header("Location: leveranciers.php?added=success");
        exit;
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>"; // Display error message securely
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="suppliers.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Add New Supplier</h1>
    <form action="add_leverancier.php" method="POST">
        <label for="naam">Name:</label>
        <input type="text" id="naam" name="naam" required><br><br>

        <label for="mail">Email:</label>
        <input type="email" id="mail" name="mail" required><br><br>

        <label for="telefoonnummer">Phone:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" required><br><br>

        <label for="postcode">Postal Code:</label>
        <input type="text" id="postcode" name="postcode" required><br><br>

        <label for="bezorgingsdatum">Delivery Date:</label>
        <input type="date" id="bezorgingsdatum" name="bezorgingsdatum" required><br><br>

        <label for="bezorgingstijd">Delivery Time:</label>
        <input type="text" id="bezorgingstijd" name="bezorgingstijd" required><br><br>

        <button type="submit">Add Supplier</button>
    </form>
</div>
</body>
</html>
