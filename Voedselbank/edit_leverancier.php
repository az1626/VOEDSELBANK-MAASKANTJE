<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Check if an ID is provided for editing
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: leveranciers.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch the supplier's current details
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM Leveranciers WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating the supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $email = $_POST['email'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $eerstevolgende_levering = $_POST['eerstevolgende_levering'];

    $sql = "UPDATE Leveranciers SET naam = ?, email = ?, telefoonnummer = ?, eerstevolgende_levering = ? WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $naam, $email, $telefoonnummer, $eerstevolgende_levering, $id);

    if ($stmt->execute()) {
        header("Location: leveranciers.php?updated=success");
        exit;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <link rel="stylesheet" href="suppliers.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Edit Supplier</h1>
    <form action="edit_leverancier.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
        <label for="naam">Name:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($supplier['naam']); ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email']); ?>" required><br><br>

        <label for="telefoonnummer">Phone:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($supplier['telefoonnummer']); ?>" required><br><br>

        <label for="eerstevolgende_levering">Next Delivery:</label>
        <input type="text" id="eerstevolgende_levering" name="eerstevolgende_levering" value="<?php echo htmlspecialchars($supplier['eerstevolgende_levering']); ?>" required><br><br>

        <button type="submit">Update Supplier</button>
    </form>
</div>
</body>
</html>
