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
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $eerstevolgende_levering = $_POST['eerstevolgende_levering'];

    // Prepare the SQL statement to insert the new supplier
    $sql = "INSERT INTO Leveranciers (naam, contactpersoon, telefoonnummer, email, eerstevolgende_levering) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $naam, $contactpersoon, $telefoonnummer, $email, $eerstevolgende_levering);

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

        <label for="contactpersoon">Contact Person:</label>
        <input type="text" id="contactpersoon" name="contactpersoon" required><br><br>

        <label for="telefoonnummer">Phone:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="eerstevolgende_levering">Next Delivery:</label>
        <input type="datetime-local" id="eerstevolgende_levering" name="eerstevolgende_levering" required><br><br>

        <button type="submit">Add Supplier</button>
    </form>
</div>
</body>
</html>